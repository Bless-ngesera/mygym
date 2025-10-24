<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ScheduledClass;

class BookingController extends Controller
{
    public function create() {
    $scheduledClasses = ScheduledClass::where('date_time', '>', now())->oldest()->get();
    return view('member.book', ['scheduledClasses' => $scheduledClasses]);
    }
}
