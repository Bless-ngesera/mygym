<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ScheduledClass;
use App\Models\Receipt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Mail\BookingConfirmation;
use App\Mail\BookingCancellation;

class BookingController extends Controller
{
    /**
     * Display a listing of the user's bookings.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->role !== 'member') {
            return redirect()->route('dashboard')->with('error', 'Only members can view their bookings.');
        }

        $filter = request('filter', 'upcoming');

        // Get all scheduled classes the user is booked into
        $allScheduledClasses = $user->bookings()
            ->with(['classType', 'instructor'])
            ->get();

        // Filter by upcoming / past in PHP
        $filtered = $allScheduledClasses->filter(function (ScheduledClass $sc) use ($filter) {
            if (!$sc->date_time) {
                return false;
            }
            if ($filter === 'upcoming') {
                return $sc->date_time->isFuture();
            } elseif ($filter === 'past') {
                return $sc->date_time->isPast();
            }
            return true;
        });

        // Sort
        $sorted = ($filter === 'past')
            ? $filtered->sortByDesc(fn($sc) => $sc->date_time)
            : $filtered->sortBy(fn($sc) => $sc->date_time);

        // Manual pagination
        $perPage = 10;
        $currentPage = (int) request('page', 1);
        $items = $sorted->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $bookings = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $sorted->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('member.upcoming', compact('bookings', 'filter'));
    }

    /**
     * Show available (upcoming, not-yet-booked) classes.
     * This displays the booking form/page.
     */
    public function create()
    {
        $user = Auth::user();

        // Check if user is logged in
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to book classes.');
        }

        if ($user->role !== 'member') {
            return redirect()->route('dashboard')->with('error', 'Only members can book classes.');
        }

        // Get upcoming classes that the user hasn't booked yet
        $classes = ScheduledClass::upcoming()
            ->with(['classType', 'instructor'])
            ->whereDoesntHave('members', fn($q) => $q->where('user_id', $user->id))
            ->orderBy('date_time', 'asc')
            ->paginate(12);

        return view('member.classes', compact('classes'));
    }

    /**
     * Store a newly created booking and receipt.
     * This handles the POST request from the booking form.
     */
    public function store(Request $request)
    {
        // Validate the request - expecting scheduled_class_id from the form
        $validated = $request->validate([
            'scheduled_class_id' => 'required|exists:scheduled_classes,id',
            'payment_method' => 'nullable|string|in:MTN Mobile Money,Airtel Money,Card,Cash',
            'payment_contact' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();

        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to book a class.');
        }

        $class = ScheduledClass::with(['classType', 'instructor'])->findOrFail($validated['scheduled_class_id']);

        // Check if user is a member
        if ($user->role !== 'member') {
            return redirect()->back()->with('error', 'Only members can book classes.');
        }

        // Check if class is in the past
        if ($class->date_time->isPast()) {
            return redirect()->back()->with('error', 'Cannot book past classes.');
        }

        // Check if already booked
        if ($user->bookings()->where('scheduled_class_id', $class->id)->exists()) {
            return redirect()->back()->with('error', 'You have already booked this class.');
        }

        // Check if class is full (if there's a capacity limit)
        if ($class->capacity && $class->members()->count() >= $class->capacity) {
            return redirect()->back()->with('error', 'This class is fully booked.');
        }

        try {
            DB::beginTransaction();

            // FIXED: Create the booking WITHOUT extra columns (just attach the class)
            // This works for a standard many-to-many pivot table with only user_id and scheduled_class_id
            $user->bookings()->attach($class->id);

            // Generate unique receipt number
            $receiptNumber = 'RCP-' . strtoupper(uniqid()) . '-' . date('Ymd');

            // Create receipt
            $receipt = Receipt::create([
                'reference_number' => $receiptNumber,
                'user_id' => $user->id,
                'scheduled_class_id' => $class->id,
                'payment_method' => $validated['payment_method'] ?? 'MTN Mobile Money',
                'amount' => $class->price,
                'payment_contact' => $validated['payment_contact'] ?? null,
                'status' => 'completed',
                'paid_at' => now(),
            ]);

            DB::commit();

            // Send email confirmation
            $emailSent = false;
            try {
                Mail::to($user->email)->send(new BookingConfirmation($user, $class, $receipt));
                $emailSent = true;
            } catch (\Exception $e) {
                Log::error('Booking confirmation email failed: ' . $e->getMessage());
            }

            $message = '✓ Class booked successfully! Receipt #' . $receipt->reference_number;
            if ($emailSent) {
                $message .= ' 📧 A confirmation email has been sent to ' . $user->email;
            }

            return redirect()->route('member.bookings')->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Booking error: ' . $e->getMessage());
            return redirect()->back()->with('error', '❌ Unable to book class. Please try again. Error: ' . $e->getMessage());
        }
    }

    /**
     * Cancel a booking.
     */
    public function destroy($id)
    {
        $user = Auth::user();

        if ($user->role !== 'member') {
            return redirect()->back()->with('error', 'Only members can cancel bookings.');
        }

        $class = ScheduledClass::findOrFail($id);

        // Check if class is in the past
        if ($class->date_time->isPast()) {
            return redirect()->back()->with('error', 'Cannot cancel past classes.');
        }

        // Check if within 2 hours
        $hoursUntilClass = Carbon::now()->diffInHours($class->date_time, false);
        if ($hoursUntilClass < 2 && $hoursUntilClass > 0) {
            return redirect()->back()->with('error', 'Cannot cancel class within 2 hours of start time.');
        }

        // Check if actually booked
        if (!$user->bookings()->where('scheduled_class_id', $id)->exists()) {
            return redirect()->back()->with('error', 'You have not booked this class.');
        }

        try {
            DB::beginTransaction();

            // Remove the booking
            $user->bookings()->detach($id);

            // Mark receipt as cancelled/refunded
            $receipt = Receipt::where('user_id', $user->id)
                ->where('scheduled_class_id', $id)
                ->first();

            if ($receipt) {
                $receipt->status = 'cancelled';
                $receipt->save();
            }

            DB::commit();

            // Send cancellation email
            $emailSent = false;
            try {
                Mail::to($user->email)->send(new BookingCancellation($user, $class));
                $emailSent = true;
            } catch (\Exception $e) {
                Log::error('Cancellation email failed: ' . $e->getMessage());
            }

            $message = '✓ Booking cancelled successfully!';
            if ($emailSent) {
                $message .= ' 📧 A confirmation email has been sent to ' . $user->email;
            }

            return redirect()->route('member.bookings')->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Cancel booking error: ' . $e->getMessage());
            return redirect()->back()->with('error', '❌ Unable to cancel booking. Please try again.');
        }
    }

    /**
     * Display all receipts for the logged-in member.
     */
    public function receipts()
    {
        $user = Auth::user();

        if ($user->role !== 'member') {
            return redirect()->route('dashboard')->with('error', 'Only members can view receipts.');
        }

        $receipts = Receipt::where('user_id', $user->id)
            ->with(['scheduledClass.classType', 'scheduledClass.instructor'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('receipts.index', compact('receipts'));
    }

    /**
     * Show a single receipt.
     */
    public function receipt($receiptId)
    {
        $user = Auth::user();

        if ($user->role !== 'member') {
            return redirect()->route('dashboard')->with('error', 'Only members can view receipts.');
        }

        $receipt = Receipt::where('user_id', $user->id)
            ->where('id', $receiptId)
            ->with(['scheduledClass.classType', 'scheduledClass.instructor'])
            ->firstOrFail();

        return view('receipts.show', compact('receipt'));
    }

    /**
     * Get upcoming bookings only.
     */
    public function upcoming()
    {
        $user = Auth::user();

        if ($user->role !== 'member') {
            return redirect()->route('dashboard')->with('error', 'Only members can view their bookings.');
        }

        $allBookings = $user->bookings()
            ->with(['classType', 'instructor'])
            ->get();

        $bookings = $allBookings->filter(function($booking) {
            return $booking->date_time && $booking->date_time->isFuture();
        })->sortBy(function($booking) {
            return $booking->date_time;
        });

        return view('member.upcoming', compact('bookings'));
    }

    /**
     * Get past bookings only.
     */
    public function past()
    {
        $user = Auth::user();

        if ($user->role !== 'member') {
            return redirect()->route('dashboard')->with('error', 'Only members can view their bookings.');
        }

        $allBookings = $user->bookings()
            ->with(['classType', 'instructor'])
            ->get();

        $bookings = $allBookings->filter(function($booking) {
            return $booking->date_time && $booking->date_time->isPast();
        })->sortByDesc(function($booking) {
            return $booking->date_time;
        });

        return view('member.upcoming', compact('bookings'));
    }

    /**
     * Check if a class is bookable.
     */
    public function checkAvailability($classId)
    {
        $user = Auth::user();

        if (!Auth::check()) {
            return response()->json(['available' => false, 'message' => 'Please login to book'], 401);
        }

        if ($user->role !== 'member') {
            return response()->json(['available' => false, 'message' => 'Unauthorized'], 403);
        }

        $class = ScheduledClass::findOrFail($classId);

        $isBooked = $user->bookings()->where('scheduled_class_id', $classId)->exists();
        $isPast = $class->date_time->isPast();
        $isFull = $class->capacity && $class->members()->count() >= $class->capacity;

        $available = !$isBooked && !$isPast && !$isFull;

        return response()->json([
            'success' => true,
            'available' => $available,
            'message' => $available ? 'Class is available for booking' : ($isFull ? 'Class is full' : 'Class is not available'),
            'price' => $class->price,
            'formatted_price' => 'UGX ' . number_format($class->price, 0),
            'date_time' => $class->date_time->format('Y-m-d H:i:s'),
            'formatted_date' => $class->date_time->format('l, F j, Y \a\t g:i A'),
            'spots_left' => $class->capacity ? max(0, $class->capacity - $class->members()->count()) : null,
        ]);
    }

    /**
     * Resend booking confirmation email.
     */
    public function resendConfirmation($scheduledClassId)
    {
        $user = Auth::user();

        if ($user->role !== 'member') {
            return redirect()->back()->with('error', 'Only members can access this.');
        }

        $class = $user->bookings()
            ->with(['classType', 'instructor'])
            ->where('scheduled_class_id', $scheduledClassId)
            ->first();

        if (!$class) {
            return redirect()->back()->with('error', 'Booking not found.');
        }

        $receipt = Receipt::where('user_id', $user->id)
            ->where('scheduled_class_id', $scheduledClassId)
            ->first();

        if (!$receipt) {
            return redirect()->back()->with('error', 'Receipt not found for this booking.');
        }

        try {
            Mail::to($user->email)->send(new BookingConfirmation($user, $class, $receipt));
            return redirect()->back()->with('success', '✓ Confirmation email resent successfully to ' . $user->email);
        } catch (\Exception $e) {
            Log::error('Resend confirmation email failed: ' . $e->getMessage());
            return redirect()->back()->with('error', '❌ Unable to send email. Please try again.');
        }
    }

    /**
     * Get booking statistics for the member dashboard.
     */
    public function statistics()
    {
        $user = Auth::user();

        if ($user->role !== 'member') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $allClasses = $user->bookings()->get();

        $totalBookings = $allClasses->count();
        $upcomingBookings = $allClasses->filter(fn($sc) => $sc->date_time && $sc->date_time->isFuture())->count();
        $pastBookings = $allClasses->filter(fn($sc) => $sc->date_time && $sc->date_time->isPast())->count();
        $totalSpent = (float) $user->receipts()->sum('amount');

        return response()->json([
            'success' => true,
            'total_bookings' => $totalBookings,
            'upcoming_bookings' => $upcomingBookings,
            'past_bookings' => $pastBookings,
            'total_spent' => $totalSpent,
            'formatted_total_spent' => 'UGX ' . number_format($totalSpent, 0),
        ]);
    }
}
