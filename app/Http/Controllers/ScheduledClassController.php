<?php

namespace App\Http\Controllers;

use App\Events\ClassCanceled;
use App\Models\ClassType; // <-- ADDED: Import your ClassType model
use App\Models\ScheduledClass; // ADDED: Assuming you have this model for the resource
use Illuminate\Http\Request;

class ScheduledClassController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $scheduledClasses = auth()->user()->scheduledClasses()->upcoming()->oldest('date_time')->get();
        return view('instructor.upcoming')->with('scheduledClasses', $scheduledClasses); 
    }

    /**
     * Show the form for creating a new scheduled class.
     * This method resolves the 'Undefined variable $classTypes' error.
     */
    public function create()
    {
        // 1. Fetch all available class types from the database.
        $classTypes = ClassType::all();

        // 2. Pass the data to the view.
        return view('instructor.schedule')->with('classTypes', $classTypes);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $date_time = $request->input('date')." ".$request->input('time');

        $request->merge([
            'date_time' => $date_time,
            'instructor_id' => auth()->user()->id,
        ]);

        $validated = $request->validate([
            'class_type_id' => 'required',
            'instructor_id' => 'required',
            'date_time' => 'required|unique:scheduled_classes,date_time|after:now',
        ]);

        ScheduledClass::create($validated);

        return redirect()->route('schedule.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(ScheduledClass $scheduledClass)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ScheduledClass $scheduledClass)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ScheduledClass $scheduledClass)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ScheduledClass $schedule)
    {
        if(auth()->user()->cannot('delete', $schedule)){
            abort(403);
        }
        // if(auth()->user()->id !== $schedule->instructor_id){
        //     abort(403);
        // }
        
        ClassCanceled::dispatch($schedule);

        $schedule->members()->detach();
        $schedule->delete();


        return redirect()->route('schedule.index');
    }
}


