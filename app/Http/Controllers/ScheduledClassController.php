<?php

namespace App\Http\Controllers;

use App\Events\ClassCanceled;
use App\Models\ClassType;
use App\Models\ScheduledClass;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class ScheduledClassController extends Controller
{
    /**
     * Display a listing of upcoming classes for the instructor.
     */
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        $scheduledClasses = $user->scheduledClasses()
            ->upcoming()
            ->with('classType')
            ->oldest('date_time')
            ->paginate(10);

        return view('instructor.upcoming', compact('scheduledClasses'));
    }

    /**
     * Show the form for creating a new scheduled class.
     */
    public function create()
    {
        $classTypes = ClassType::all();
        return view('instructor.schedule', compact('classTypes'));
    }

    /**
     * Store a newly created class in storage.
     * Redirects back to the schedule page with success message.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_type_id' => 'required|exists:class_types,id',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|date_format:H:i',
        ]);

        // Combine date and time
        $dateTime = Carbon::parse($validated['date'] . ' ' . $validated['time']);

        // Additional validation
        if ($dateTime->isPast()) {
            return back()->withErrors(['time' => 'The class time cannot be in the past.'])->withInput();
        }

        /** @var User $user */
        $user = Auth::user();

        // Check for duplicate
        $exists = ScheduledClass::where('date_time', $dateTime)
            ->where('instructor_id', $user->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['date_time' => 'You already have a class scheduled at this time.'])->withInput();
        }

        // Create the class
        ScheduledClass::create([
            'class_type_id' => $validated['class_type_id'],
            'instructor_id' => $user->id,
            'date_time' => $dateTime,
        ]);

        // Redirect back to the schedule page with success message
        return redirect()->back()->with('success', 'Class scheduled successfully!');
    }

    /**
     * Display all classes for the logged-in instructor with filtering.
     */
    public function instructorClasses()
    {
        /** @var User $user */
        $user = Auth::user();

        // Get the filter from the request, default to 'all'
        $filter = request('filter', 'all');

        // Start the query
        $query = ScheduledClass::where('instructor_id', $user->id)
            ->with('classType');

        // Apply filter based on the selected tab
        if ($filter === 'upcoming') {
            $query->where('date_time', '>', Carbon::now());
        } elseif ($filter === 'past') {
            $query->where('date_time', '<', Carbon::now());
        }
        // 'all' shows everything, no date filter

        // Order by date_time descending (newest first) and paginate
        $classes = $query->orderBy('date_time', 'desc')
            ->paginate(10)
            ->withQueryString(); // Preserve filter in pagination links

        return view('instructor.classes', compact('classes'));
    }

    /**
     * Display the specified resource.
     */
    public function show(ScheduledClass $scheduledClass)
    {
        // Check if the instructor owns this class
        if ($scheduledClass->instructor_id !== Auth::id()) {
            abort(403);
        }

        $scheduledClass->load(['classType', 'instructor']);

        return view('instructor.show', compact('scheduledClass'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ScheduledClass $scheduledClass)
    {
        /** @var User $user */
        $user = Auth::user();

        // Check authorization
        if ($scheduledClass->instructor_id !== $user->id) {
            abort(403);
        }

        // Don't allow editing past classes
        if ($scheduledClass->date_time->isPast()) {
            return redirect()->route('instructor.upcoming')
                ->withErrors(['error' => 'Cannot edit past classes.']);
        }

        $classTypes = ClassType::all();

        // Prepare date and time for form
        $date = $scheduledClass->date_time->format('Y-m-d');
        $time = $scheduledClass->date_time->format('H:i');

        return view('instructor.edit', compact('scheduledClass', 'classTypes', 'date', 'time'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ScheduledClass $scheduledClass)
    {
        /** @var User $user */
        $user = Auth::user();

        // Check authorization
        if ($scheduledClass->instructor_id !== $user->id) {
            abort(403);
        }

        // Don't allow updating past classes
        if ($scheduledClass->date_time->isPast()) {
            return redirect()->route('instructor.upcoming')
                ->withErrors(['error' => 'Cannot update past classes.']);
        }

        $validated = $request->validate([
            'class_type_id' => 'required|exists:class_types,id',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|date_format:H:i',
        ]);

        // Combine date and time
        $dateTime = Carbon::parse($validated['date'] . ' ' . $validated['time']);

        if ($dateTime->isPast()) {
            return back()->withErrors(['time' => 'The class time cannot be in the past.'])->withInput();
        }

        // Check for duplicate (excluding current class)
        $exists = ScheduledClass::where('date_time', $dateTime)
            ->where('instructor_id', $user->id)
            ->where('id', '!=', $scheduledClass->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['date_time' => 'You already have another class scheduled at this time.'])->withInput();
        }

        // Update the class
        $scheduledClass->update([
            'class_type_id' => $validated['class_type_id'],
            'date_time' => $dateTime,
        ]);

        return redirect()->route('instructor.upcoming')
            ->with('success', 'Class updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ScheduledClass $schedule)
    {
        /** @var User $user */
        $user = Auth::user();

        // Check authorization
        if ($schedule->instructor_id !== $user->id) {
            abort(403);
        }

        // Don't allow deletion of past classes
        if ($schedule->date_time->isPast()) {
            return back()->withErrors(['error' => 'Cannot delete past classes.']);
        }

        // Dispatch event
        ClassCanceled::dispatch($schedule);

        // Detach members and delete
        $schedule->members()->detach();
        $schedule->delete();

        return redirect()->route('instructor.upcoming')
            ->with('success', 'Class cancelled successfully!');
    }

    /**
     * Get upcoming classes for members to view and book.
     */
    public function upcoming()
    {
        $classes = ScheduledClass::upcoming()
            ->with(['classType', 'instructor'])
            ->orderBy('date_time', 'asc')
            ->paginate(12);

        return view('member.classes', compact('classes'));
    }

    /**
     * Book a class for a member.
     */
    public function book(ScheduledClass $scheduledClass)
    {
        /** @var User $user */
        $user = Auth::user();

        // Check if user is a member
        if ($user->role !== 'member') {
            abort(403);
        }

        if ($scheduledClass->date_time->isPast()) {
            return back()->withErrors(['error' => 'Cannot book past classes.']);
        }

        // Check if already booked
        if ($user->bookings()->where('scheduled_class_id', $scheduledClass->id)->exists()) {
            return back()->withErrors(['error' => 'You already booked this class.']);
        }

        $user->bookings()->attach($scheduledClass->id);

        return redirect()->route('member.bookings')
            ->with('success', 'Class booked successfully!');
    }
}
