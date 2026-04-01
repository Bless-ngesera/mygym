<?php

namespace App\Http\Controllers;

use App\Events\ClassCanceled;
use App\Models\ClassType;
use App\Models\ScheduledClass;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\UniqueConstraintViolationException;

class ScheduledClassController extends Controller
{
    /**
     * Display upcoming classes for the logged-in instructor.
     */
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        // Check if user is an instructor
        if ($user->role !== 'instructor') {
            return redirect()->route('dashboard')->with('error', 'You are not authorized to view this page.');
        }

        $scheduledClasses = ScheduledClass::where('instructor_id', $user->id)
            ->upcoming()
            ->with('classType')
            ->withCount('members')
            ->orderBy('date_time', 'asc')
            ->paginate(10);

        return view('instructor.upcoming', compact('scheduledClasses'));
    }

    /**
     * Show the form for creating a new scheduled class.
     */
    public function create()
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->role !== 'instructor') {
            return redirect()->route('dashboard')->with('error', 'You are not authorized to view this page.');
        }

        $classTypes = ClassType::all();
        return view('instructor.schedule', compact('classTypes'));
    }

    /**
     * Store a newly created scheduled class.
     */
    public function store(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->role !== 'instructor') {
            return redirect()->route('dashboard')->with('error', 'You are not authorized to perform this action.');
        }

        $validated = $request->validate([
            'class_type_id' => 'required|exists:class_types,id',
            'date'          => 'required|date|after_or_equal:today',
            'time'          => 'required|date_format:H:i',
            'price'         => 'required|numeric|min:0',
        ]);

        $dateTime = Carbon::parse($validated['date'] . ' ' . $validated['time']);

        if ($dateTime->isPast()) {
            return back()
                ->withErrors(['time' => 'The class time cannot be in the past.'])
                ->withInput();
        }

        try {
            $scheduledClass = ScheduledClass::create([
                'class_type_id' => $validated['class_type_id'],
                'instructor_id' => $user->id,
                'date_time'     => $dateTime,
                'price'         => $validated['price'],
            ]);

            return redirect()
                ->route('instructor.upcoming')
                ->with('success', 'Class scheduled successfully!');

        } catch (UniqueConstraintViolationException $e) {
            return back()
                ->withErrors(['date_time' => 'You already have a class scheduled at this date and time.'])
                ->withInput();
        }
    }

    /**
     * Display all classes for the logged-in instructor with filtering.
     */
    public function instructorClasses()
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->role !== 'instructor') {
            return redirect()->route('dashboard')->with('error', 'You are not authorized to view this page.');
        }

        $filter = request('filter', 'all');

        $query = ScheduledClass::where('instructor_id', $user->id)
            ->with(['classType', 'members']) // Load members for avatars
            ->withCount('members');

        if ($filter === 'upcoming') {
            $query->where('date_time', '>', Carbon::now());
        } elseif ($filter === 'past') {
            $query->where('date_time', '<', Carbon::now());
        }

        $classes = $query->orderBy('date_time', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('instructor.classes', compact('classes', 'filter'));
    }

    /**
     * Display a single scheduled class (instructor view).
     */
    public function show(ScheduledClass $scheduledClass)
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->role !== 'instructor') {
            return redirect()->route('dashboard')->with('error', 'You are not authorized to view this page.');
        }

        if ($scheduledClass->instructor_id !== $user->id) {
            return redirect()->route('instructor.upcoming')
                ->with('error', 'You do not own this class.');
        }

        $scheduledClass->load(['classType', 'instructor', 'members']);
        $scheduledClass->loadCount('members');

        // Get total unique clients who have booked any class with this instructor
        $totalClients = ScheduledClass::where('instructor_id', $user->id)
            ->with('members')
            ->get()
            ->pluck('members')
            ->flatten()
            ->unique('id')
            ->count();

        // Get total bookings across all classes
        $totalBookings = ScheduledClass::where('instructor_id', $user->id)
            ->withCount('members')
            ->get()
            ->sum('members_count');

        return view('instructor.show', compact('scheduledClass', 'totalClients', 'totalBookings'));
    }

    /**
     * Show the edit form for a scheduled class.
     */
    public function edit(ScheduledClass $scheduledClass)
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->role !== 'instructor') {
            return redirect()->route('dashboard')->with('error', 'You are not authorized to view this page.');
        }

        if ($scheduledClass->instructor_id !== $user->id) {
            return redirect()->route('instructor.upcoming')
                ->with('error', 'You do not own this class.');
        }

        if ($scheduledClass->date_time->isPast()) {
            return redirect()->route('instructor.upcoming')
                ->with('error', 'Cannot edit past classes.');
        }

        $classTypes = ClassType::all();
        $date       = $scheduledClass->date_time->format('Y-m-d');
        $time       = $scheduledClass->date_time->format('H:i');

        return view('instructor.edit', compact('scheduledClass', 'classTypes', 'date', 'time'));
    }

    /**
     * Update a scheduled class.
     */
    public function update(Request $request, ScheduledClass $scheduledClass)
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->role !== 'instructor') {
            return redirect()->route('dashboard')->with('error', 'You are not authorized to perform this action.');
        }

        if ($scheduledClass->instructor_id !== $user->id) {
            return redirect()->route('instructor.upcoming')
                ->with('error', 'You do not own this class.');
        }

        if ($scheduledClass->date_time->isPast()) {
            return redirect()->route('instructor.upcoming')
                ->with('error', 'Cannot update past classes.');
        }

        $validated = $request->validate([
            'class_type_id' => 'required|exists:class_types,id',
            'date'          => 'required|date|after_or_equal:today',
            'time'          => 'required|date_format:H:i',
            'price'         => 'required|numeric|min:0',
        ]);

        $dateTime = Carbon::parse($validated['date'] . ' ' . $validated['time']);

        if ($dateTime->isPast()) {
            return back()
                ->withErrors(['time' => 'The class time cannot be in the past.'])
                ->withInput();
        }

        try {
            $scheduledClass->update([
                'class_type_id' => $validated['class_type_id'],
                'date_time'     => $dateTime,
                'price'         => $validated['price'],
            ]);

            return redirect()->route('instructor.upcoming')
                ->with('success', 'Class updated successfully!');

        } catch (UniqueConstraintViolationException $e) {
            return back()
                ->withErrors(['date_time' => 'You already have another class scheduled at this date and time.'])
                ->withInput();
        }
    }

    /**
     * Cancel (delete) a scheduled class.
     */
    public function destroy(ScheduledClass $schedule)
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->role !== 'instructor') {
            return redirect()->route('dashboard')->with('error', 'You are not authorized to perform this action.');
        }

        if ($schedule->instructor_id !== $user->id) {
            return redirect()->route('instructor.upcoming')
                ->with('error', 'You do not own this class.');
        }

        if ($schedule->date_time->isPast()) {
            return redirect()->route('instructor.upcoming')
                ->with('error', 'Cannot delete past classes.');
        }

        // Check if there are members booked
        $memberCount = $schedule->members()->count();

        if ($memberCount > 0) {
            return redirect()->route('instructor.upcoming')
                ->with('error', 'Cannot cancel class with booked members. Please notify them first.');
        }

        try {
            // Dispatch event to notify any members
            ClassCanceled::dispatch($schedule);

            // Detach all members
            $schedule->members()->detach();

            // Delete the class
            $schedule->delete();

            return redirect()->route('instructor.upcoming')
                ->with('success', 'Class cancelled successfully!');

        } catch (\Exception $e) {
            Log::error('Error cancelling class: ' . $e->getMessage());
            return redirect()->route('instructor.upcoming')
                ->with('error', 'Unable to cancel class. Please try again.');
        }
    }

    /**
     * Get instructor statistics including total clients and bookings.
     */
    public function statistics()
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->role !== 'instructor') {
            return redirect()->route('dashboard')->with('error', 'You are not authorized to view this page.');
        }

        // Total number of unique clients who have booked any class
        $totalUniqueClients = ScheduledClass::where('instructor_id', $user->id)
            ->with('members')
            ->get()
            ->pluck('members')
            ->flatten()
            ->unique('id')
            ->count();

        // Total number of bookings across all classes
        $totalBookings = ScheduledClass::where('instructor_id', $user->id)
            ->withCount('members')
            ->get()
            ->sum('members_count');

        // Total number of classes
        $totalClasses = ScheduledClass::where('instructor_id', $user->id)->count();

        // Upcoming classes count
        $upcomingClasses = ScheduledClass::where('instructor_id', $user->id)
            ->where('date_time', '>', Carbon::now())
            ->count();

        // Past classes count
        $pastClasses = ScheduledClass::where('instructor_id', $user->id)
            ->where('date_time', '<', Carbon::now())
            ->count();

        // Classes with most bookings
        $topClasses = ScheduledClass::where('instructor_id', $user->id)
            ->with('classType')
            ->withCount('members')
            ->orderBy('members_count', 'desc')
            ->take(5)
            ->get();

        return response()->json([
            'total_unique_clients' => $totalUniqueClients,
            'total_bookings' => $totalBookings,
            'total_classes' => $totalClasses,
            'upcoming_classes' => $upcomingClasses,
            'past_classes' => $pastClasses,
            'top_classes' => $topClasses,
        ]);
    }

    /**
     * Display calendar view for instructor's classes.
     */
    public function calendar()
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->role !== 'instructor') {
            return redirect()->route('dashboard')->with('error', 'You are not authorized to view this page.');
        }

        $classes = ScheduledClass::where('instructor_id', $user->id)
            ->with(['classType', 'instructor', 'members'])
            ->withCount('members')
            ->orderBy('date_time', 'asc')
            ->get();

        return view('instructor.calendar', compact('classes'));
    }

    /**
     * Get class details for API/JSON response (for AJAX calendar).
     */
    public function getClassDetails(ScheduledClass $scheduledClass)
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->role !== 'instructor' || $scheduledClass->instructor_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json([
            'id' => $scheduledClass->id,
            'class_type' => $scheduledClass->classType->name,
            'date_time' => $scheduledClass->date_time->toDateTimeString(),
            'price' => $scheduledClass->price,
            'member_count' => $scheduledClass->members()->count(),
        ]);
    }
}
