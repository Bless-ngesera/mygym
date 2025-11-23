<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ScheduledClass;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    public function create()
    {
        $user = auth()->user();

        $upcomingBookings = Booking::with(['scheduledClass.classType'])
            ->where('user_id', $user->id)
            ->whereHas('scheduledClass', function ($q) {
                $q->where('date_time', '>=', now());
            })
            ->orderBy('id', 'desc')
            ->get();

        $scheduledClasses = ScheduledClass::upcoming()
            ->with('classType', 'instructor')
            ->notBooked()
            ->oldest()
            ->get();

        return view('member.book', compact('scheduledClasses', 'upcomingBookings'));
    }

public function store(Request $request)
{
    $user = auth()->user();
    $class = ScheduledClass::findOrFail($request->scheduled_class_id);

    // Attach booking
    $user->bookings()->attach($class->id);

    // Insert payment automatically
    Payment::create([
        'member_id'         => $user->id,
        'instructor_id'     => $class->instructor_id,
        'scheduled_class_id'=> $class->id,
        'amount'            => $class->price,
        'paid_at'           => now(),
        'status'            => 'completed',
        'reference'         => 'PAY-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6)),
    ]);

    return redirect()->route('booking.create')
        ->with('success', 'Class booked and payment recorded.');
}




    public function index()
    {
        $bookings = auth()->user()->bookings()->upcoming()->get();

        return view('member.upcoming')->with('bookings', $bookings);
    }

    public function destroy(int $id)
    {
        auth()->user()->bookings()->detach($id);

        return redirect()->route('booking.index')
            ->with('success', 'Booking cancelled successfully.');
    }
}




