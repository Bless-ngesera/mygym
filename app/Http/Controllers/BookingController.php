<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ScheduledClass;

class BookingController extends Controller
{
    public function create() {
    $scheduledClasses = ScheduledClass::upcoming()
    ->with('classType', 'instructor')
    ->notBooked()
    ->oldest()->get();
    return view('member.book', ['scheduledClasses' => $scheduledClasses]);
    }

    public function store(Request $request){
        auth()->user()->bookings()->attach($request->scheduled_class_id);

        return redirect()->route('booking.create')->with('success', 'Class booked successfully.');
    }

    public function index(){
        $bookings = auth()->user()->bookings()->upcoming()->get();

        return view('member.upcoming')->with('bookings', $bookings);
    }
    public function destroy(int $id){
        auth()->user()->bookings()->detach($id);

        return redirect()->route('booking.index')->with('success', 'Booking cancelled successfully.');
    }
}
