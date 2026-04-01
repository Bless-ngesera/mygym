<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ScheduledClass;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    /**
     * Show the form for creating a new booking.
     */
    public function create()
    {
        $user = Auth::user();

        // Get upcoming bookings for the user
        $upcomingBookings = Booking::with(['scheduledClass.classType'])
            ->where('user_id', $user->id)
            ->whereHas('scheduledClass', function ($q) {
                $q->where('date_time', '>=', now());
            })
            ->orderBy('id', 'desc')
            ->get();

        // Get available classes that are not booked by the user
        $scheduledClasses = ScheduledClass::upcoming()
            ->with(['classType', 'instructor'])
            ->whereDoesntHave('members', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->orderBy('date_time', 'asc')
            ->paginate(12);

        return view('member.book', compact('scheduledClasses', 'upcomingBookings'));
    }

    /**
     * Store a newly created booking in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'scheduled_class_id' => 'required|exists:scheduled_classes,id',
        ]);

        $user = Auth::user();
        $class = ScheduledClass::findOrFail($validated['scheduled_class_id']);

        // Check if class is in the past
        if ($class->date_time->isPast()) {
            return redirect()->back()
                ->withErrors(['error' => 'Cannot book past classes.']);
        }

        // Check if already booked
        if ($user->bookings()->where('scheduled_class_id', $class->id)->exists()) {
            return redirect()->back()
                ->withErrors(['error' => 'You already booked this class.']);
        }

        // Attach booking
        $user->bookings()->attach($class->id);

        // Create payment record automatically
        Payment::create([
            'member_id'         => $user->id,
            'instructor_id'     => $class->instructor_id,
            'scheduled_class_id'=> $class->id,
            'amount'            => $class->price,
            'paid_at'           => now(),
            'status'            => 'completed',
            'reference'         => 'PAY-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6)),
        ]);

        return redirect()->route('member.bookings')
            ->with('success', 'Class booked successfully!');
    }

    /**
     * Display a listing of the user's upcoming bookings.
     */
    public function index()
    {
        $user = Auth::user();

        // Get upcoming bookings with pagination
        $bookings = $user->bookings()
            ->with(['classType', 'instructor'])
            ->where('date_time', '>', now())
            ->orderBy('date_time', 'asc')
            ->paginate(10);

        return view('member.upcoming', compact('bookings'));
    }

    /**
     * Remove the specified booking from storage.
     */
    public function destroy($id)
    {
        $user = Auth::user();

        // Check if booking exists
        $booking = $user->bookings()->where('scheduled_class_id', $id)->first();

        if (!$booking) {
            return redirect()->route('member.bookings')
                ->withErrors(['error' => 'Booking not found.']);
        }

        // Detach the booking
        $user->bookings()->detach($id);

        return redirect()->route('member.bookings')
            ->with('success', 'Booking cancelled successfully.');
    }
}
