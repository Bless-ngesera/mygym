<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ScheduledClass;
use App\Models\Receipt;
use App\Models\Booking;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Mail\BookingConfirmation;
use App\Mail\BookingCancellation;
use App\Mail\ClassReminder;

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

        // Get all bookings first, then filter in PHP to avoid the relationship issue
        $allBookings = $user->bookings()
            ->with(['scheduledClass.classType', 'scheduledClass.instructor'])
            ->get();

        // Filter in PHP
        $filteredBookings = $allBookings->filter(function($booking) use ($filter) {
            if (!$booking->scheduledClass) {
                return false;
            }

            if ($filter === 'upcoming') {
                return $booking->scheduledClass->date_time > now();
            } elseif ($filter === 'past') {
                return $booking->scheduledClass->date_time < now();
            }
            return true;
        });

        // Paginate manually
        $currentPage = request('page', 1);
        $perPage = 10;
        $bookings = new \Illuminate\Pagination\LengthAwarePaginator(
            $filteredBookings->forPage($currentPage, $perPage),
            $filteredBookings->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('member.upcoming', compact('bookings', 'filter'));
    }

    /**
     * Show the form for creating a new booking (available classes).
     */
    public function create()
    {
        $user = Auth::user();

        if ($user->role !== 'member') {
            return redirect()->route('dashboard')->with('error', 'Only members can book classes.');
        }

        // Get available classes that are not booked by the user
        $classes = ScheduledClass::upcoming()
            ->with(['classType', 'instructor'])
            ->whereDoesntHave('members', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->orderBy('date_time', 'asc')
            ->paginate(12);

        return view('member.classes', compact('classes'));
    }

    /**
     * Store a newly created booking and receipt.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'scheduled_class_id' => 'required|exists:scheduled_classes,id',
            'payment_method' => 'nullable|string',
            'payment_contact' => 'nullable|string',
        ]);

        $user = Auth::user();
        $class = ScheduledClass::findOrFail($validated['scheduled_class_id']);

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

        try {
            DB::beginTransaction();

            // Create the booking
            $user->bookings()->attach($class->id);

            // Get the booking record
            $booking = $user->bookings()->where('scheduled_class_id', $class->id)->first();

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
            try {
                Mail::to($user->email)->send(new BookingConfirmation($booking, $receipt));
                $emailSent = true;
            } catch (\Exception $e) {
                Log::error('Booking confirmation email failed: ' . $e->getMessage());
                $emailSent = false;
            }

            // Redirect to bookings page with success message
            $message = 'Class booked successfully! Receipt #' . $receipt->reference_number;
            if ($emailSent) {
                $message .= ' A confirmation email has been sent to your email address.';
            }

            return redirect()->route('member.bookings')->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Booking error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to book class. Please try again.');
        }
    }

    /**
     * Remove the specified booking from storage.
     */
    public function destroy($id)
    {
        $user = Auth::user();

        if ($user->role !== 'member') {
            return redirect()->back()->with('error', 'Only members can cancel bookings.');
        }

        // Get the class
        $class = ScheduledClass::findOrFail($id);

        // Check if class is in the past
        if ($class->date_time->isPast()) {
            return redirect()->back()->with('error', 'Cannot cancel past classes.');
        }

        // Check if the class is starting soon (within 2 hours)
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

            // Get the booking before detaching
            $booking = $user->bookings()->where('scheduled_class_id', $id)->first();

            // Remove the booking
            $user->bookings()->detach($id);

            DB::commit();

            // Send cancellation email
            try {
                Mail::to($user->email)->send(new BookingCancellation($booking));
                $emailSent = true;
            } catch (\Exception $e) {
                Log::error('Cancellation email failed: ' . $e->getMessage());
                $emailSent = false;
            }

            $message = 'Booking cancelled successfully!';
            if ($emailSent) {
                $message .= ' A confirmation email has been sent to your email address.';
            }

            return redirect()->route('member.bookings')->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Cancel booking error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to cancel booking. Please try again.');
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
     * Show the receipt for a specific booking.
     */
    public function receipt($bookingId)
    {
        $user = Auth::user();

        if ($user->role !== 'member') {
            return redirect()->route('dashboard')->with('error', 'Only members can view receipts.');
        }

        // Find the receipt for this booking
        $receipt = Receipt::where('user_id', $user->id)
            ->where('scheduled_class_id', $bookingId)
            ->with(['scheduledClass.classType', 'scheduledClass.instructor'])
            ->first();

        if (!$receipt) {
            return redirect()->route('member.bookings')
                ->with('error', 'Receipt not found.');
        }

        return view('receipts.show', compact('receipt'));
    }

    /**
     * Get upcoming bookings for the member.
     */
    public function upcoming()
    {
        $user = Auth::user();

        if ($user->role !== 'member') {
            return redirect()->route('dashboard')->with('error', 'Only members can view their bookings.');
        }

        $bookings = $user->bookings()
            ->with(['scheduledClass.classType', 'scheduledClass.instructor'])
            ->get()
            ->filter(function($booking) {
                return $booking->scheduledClass && $booking->scheduledClass->date_time > now();
            })
            ->sortBy(function($booking) {
                return $booking->scheduledClass->date_time;
            })
            ->paginate(10);

        return view('member.upcoming', compact('bookings'));
    }

    /**
     * Get past bookings for the member.
     */
    public function past()
    {
        $user = Auth::user();

        if ($user->role !== 'member') {
            return redirect()->route('dashboard')->with('error', 'Only members can view their bookings.');
        }

        $bookings = $user->bookings()
            ->with(['scheduledClass.classType', 'scheduledClass.instructor'])
            ->get()
            ->filter(function($booking) {
                return $booking->scheduledClass && $booking->scheduledClass->date_time < now();
            })
            ->sortByDesc(function($booking) {
                return $booking->scheduledClass->date_time;
            })
            ->paginate(10);

        return view('member.upcoming', compact('bookings'));
    }

    /**
     * Check if a class is bookable.
     */
    public function checkAvailability($classId)
    {
        $user = Auth::user();

        if ($user->role !== 'member') {
            return response()->json(['available' => false, 'message' => 'Unauthorized'], 403);
        }

        $class = ScheduledClass::findOrFail($classId);

        $isBooked = $user->bookings()->where('scheduled_class_id', $classId)->exists();
        $isPast = $class->date_time->isPast();

        $available = !$isBooked && !$isPast;

        return response()->json([
            'available' => $available,
            'message' => $available ? 'Class is available for booking' : 'Class is not available',
            'price' => $class->price,
            'date_time' => $class->date_time->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Resend booking confirmation email.
     */
    public function resendConfirmation($bookingId)
    {
        $user = Auth::user();

        if ($user->role !== 'member') {
            return redirect()->back()->with('error', 'Only members can access this.');
        }

        $booking = $user->bookings()->where('scheduled_class_id', $bookingId)->first();

        if (!$booking) {
            return redirect()->back()->with('error', 'Booking not found.');
        }

        $receipt = Receipt::where('user_id', $user->id)
            ->where('scheduled_class_id', $bookingId)
            ->first();

        try {
            Mail::to($user->email)->send(new BookingConfirmation($booking, $receipt));
            return redirect()->back()->with('success', 'Confirmation email resent successfully!');
        } catch (\Exception $e) {
            Log::error('Resend confirmation email failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to send email. Please try again.');
        }
    }
}
