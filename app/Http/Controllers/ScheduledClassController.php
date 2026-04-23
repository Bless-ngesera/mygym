<?php

namespace App\Http\Controllers;

use App\Events\ClassCancelled;
use App\Models\ClassType;
use App\Models\ScheduledClass;
use App\Models\User;
use App\Models\Message;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Pagination\LengthAwarePaginator;

class ScheduledClassController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Display upcoming classes for the logged-in instructor.
     */
    public function index()
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'instructor') {
            return redirect()->route('home')->with('error', 'You are not authorized to view this page.');
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
        $user = Auth::user();

        if (!$user || $user->role !== 'instructor') {
            return redirect()->route('home')->with('error', 'You are not authorized to view this page.');
        }

        $classTypes = ClassType::all();
        return view('instructor.schedule.create', compact('classTypes'));
    }

    /**
     * Store a newly created scheduled class.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'instructor') {
            return redirect()->route('home')->with('error', 'You are not authorized to perform this action.');
        }

        $validated = $request->validate([
            'class_type_id' => 'required|exists:class_types,id',
            'date'          => 'required|date|after_or_equal:today',
            'time'          => 'required|date_format:H:i',
            'price'         => 'required|numeric|min:0',
            'capacity'      => 'nullable|integer|min:1|max:100',
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
                'capacity'      => $validated['capacity'] ?? null,
            ]);

            return redirect()
                ->route('instructor.schedule.show', $scheduledClass)
                ->with('success', 'Class scheduled successfully!');

        } catch (UniqueConstraintViolationException $e) {
            return back()
                ->withErrors(['date_time' => 'You already have a class scheduled at this date and time.'])
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Error scheduling class: ' . $e->getMessage());
            return back()
                ->withErrors(['error' => 'An error occurred while scheduling the class. Please try again.'])
                ->withInput();
        }
    }

    /**
     * Display all classes for the logged-in instructor with filtering.
     */
    public function instructorClasses()
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'instructor') {
            return redirect()->route('home')->with('error', 'You are not authorized to view this page.');
        }

        $filter = request('filter', 'all');

        $query = ScheduledClass::where('instructor_id', $user->id)
            ->with(['classType', 'members'])
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
        $user = Auth::user();

        if (!$user || $user->role !== 'instructor') {
            return redirect()->route('home')->with('error', 'You are not authorized to view this page.');
        }

        if ($scheduledClass->instructor_id !== $user->id) {
            return redirect()->route('instructor.upcoming')
                ->with('error', 'You do not own this class.');
        }

        $scheduledClass->load(['classType', 'instructor', 'members']);
        $scheduledClass->loadCount('members');

        // Get total unique clients who have booked any class with this instructor
        $allClasses = ScheduledClass::where('instructor_id', $user->id)->with('members')->get();
        $totalClients = $allClasses->pluck('members')->flatten()->unique('id')->count();

        // Get total bookings across all classes
        $totalBookings = ScheduledClass::where('instructor_id', $user->id)->withCount('members')->get()->sum('members_count');

        $availableSpots = $scheduledClass->capacity ? max(0, $scheduledClass->capacity - $scheduledClass->members_count) : null;
        $isPast = $scheduledClass->date_time->isPast();
        $isFull = $scheduledClass->capacity && $scheduledClass->members_count >= $scheduledClass->capacity;

        return view('instructor.schedule.show', compact('scheduledClass', 'totalClients', 'totalBookings', 'availableSpots', 'isPast', 'isFull'));
    }

    /**
     * Show the edit form for a scheduled class.
     */
    public function edit(ScheduledClass $scheduledClass)
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'instructor') {
            return redirect()->route('home')->with('error', 'You are not authorized to view this page.');
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
        $date = $scheduledClass->date_time->format('Y-m-d');
        $time = $scheduledClass->date_time->format('H:i');

        return view('instructor.schedule.edit', compact('scheduledClass', 'classTypes', 'date', 'time'));
    }

    /**
     * Update a scheduled class.
     */
    public function update(Request $request, ScheduledClass $scheduledClass)
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'instructor') {
            return redirect()->route('home')->with('error', 'You are not authorized to perform this action.');
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
            'capacity'      => 'nullable|integer|min:1|max:100',
        ]);

        $dateTime = Carbon::parse($validated['date'] . ' ' . $validated['time']);

        if ($dateTime->isPast()) {
            return back()
                ->withErrors(['time' => 'The class time cannot be in the past.'])
                ->withInput();
        }

        try {
            $oldDateTime = $scheduledClass->date_time;

            $scheduledClass->update([
                'class_type_id' => $validated['class_type_id'],
                'date_time'     => $dateTime,
                'price'         => $validated['price'],
                'capacity'      => $validated['capacity'] ?? null,
            ]);

            // Only notify members if date/time changed
            if ($oldDateTime->ne($dateTime)) {
                foreach ($scheduledClass->members as $member) {
                    $this->notificationService->sendToUser($member, [
                        'type' => 'class_updated',
                        'title' => '📅 Class Updated',
                        'message' => "The {$scheduledClass->classType->name} class has been rescheduled to {$dateTime->format('M d, h:i A')}",
                        'priority' => 'medium',
                        'action_url' => route('member.bookings.index'),
                        'data' => ['class_id' => $scheduledClass->id]
                    ]);
                }
            }

            return redirect()->route('instructor.schedule.show', $scheduledClass)
                ->with('success', 'Class updated successfully!');

        } catch (UniqueConstraintViolationException $e) {
            return back()
                ->withErrors(['date_time' => 'You already have another class scheduled at this date and time.'])
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Error updating class: ' . $e->getMessage());
            return back()
                ->withErrors(['error' => 'An error occurred while updating the class.'])
                ->withInput();
        }
    }

    /**
     * Cancel (delete) a scheduled class.
     */
    public function destroy(ScheduledClass $scheduledClass)
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'instructor') {
            return redirect()->route('home')->with('error', 'You are not authorized to perform this action.');
        }

        if ($scheduledClass->instructor_id !== $user->id) {
            return redirect()->route('instructor.upcoming')
                ->with('error', 'You do not own this class.');
        }

        if ($scheduledClass->date_time->isPast()) {
            return redirect()->route('instructor.upcoming')
                ->with('error', 'Cannot delete past classes.');
        }

        // Get members before deleting
        $members = $scheduledClass->members()->get();

        try {
            // Notify each member
            foreach ($members as $member) {
                $this->notificationService->sendToUser($member, [
                    'type' => 'class_cancelled',
                    'title' => '❌ Class Cancelled',
                    'message' => "The {$scheduledClass->classType->name} class on {$scheduledClass->date_time->format('M d, h:i A')} has been cancelled.",
                    'priority' => 'high',
                    'action_url' => route('member.bookings.index'),
                    'data' => ['class_id' => $scheduledClass->id]
                ]);
            }

            // Dispatch event for any additional handling
            event(new ClassCancelled($scheduledClass, $user->name, 'Class cancelled by instructor'));

            // Detach all members
            $scheduledClass->members()->detach();

            // Delete the class
            $scheduledClass->delete();

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
        $user = Auth::user();

        if (!$user || $user->role !== 'instructor') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            // Total number of unique clients who have booked any class
            $allClasses = ScheduledClass::where('instructor_id', $user->id)->with('members')->get();
            $totalUniqueClients = $allClasses->pluck('members')->flatten()->unique('id')->count();

            // Total number of bookings across all classes
            $totalBookings = ScheduledClass::where('instructor_id', $user->id)->withCount('members')->get()->sum('members_count');

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
                ->get()
                ->map(function($class) {
                    return [
                        'id' => $class->id,
                        'name' => $class->classType->name,
                        'bookings' => $class->members_count,
                        'date' => $class->date_time->format('M d, Y')
                    ];
                });

            return response()->json([
                'success' => true,
                'total_unique_clients' => $totalUniqueClients,
                'total_bookings' => $totalBookings,
                'total_classes' => $totalClasses,
                'upcoming_classes' => $upcomingClasses,
                'past_classes' => $pastClasses,
                'top_classes' => $topClasses,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching statistics: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch statistics'
            ], 500);
        }
    }

    /**
     * Display calendar view for instructor's classes.
     */
    public function calendar()
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'instructor') {
            return redirect()->route('home')->with('error', 'You are not authorized to view this page.');
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
        $user = Auth::user();

        if (!$user || $user->role !== 'instructor' || $scheduledClass->instructor_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            return response()->json([
                'success' => true,
                'id' => $scheduledClass->id,
                'class_type' => $scheduledClass->classType->name ?? 'Unknown',
                'date_time' => $scheduledClass->date_time->toDateTimeString(),
                'price' => $scheduledClass->price,
                'member_count' => $scheduledClass->members()->count(),
                'capacity' => $scheduledClass->capacity,
                'available_spots' => $scheduledClass->capacity ? max(0, $scheduledClass->capacity - $scheduledClass->members()->count()) : null,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch class details'], 500);
        }
    }

    /**
     * Public index for members to view available classes.
     */
    public function publicIndex()
    {
        try {
            $classes = ScheduledClass::upcoming()
                ->with(['classType', 'instructor'])
                ->withCount('members')
                ->orderBy('date_time', 'asc')
                ->paginate(12);

            return view('classes.index', compact('classes'));
        } catch (\Exception $e) {
            Log::error('Error in publicIndex: ' . $e->getMessage());
            $classes = new LengthAwarePaginator([], 0, 12);
            return view('classes.index', compact('classes'))->with('error', 'Unable to load classes at this time.');
        }
    }

    /**
     * Get members for a specific class (instructor view).
     */
    public function members(ScheduledClass $scheduledClass)
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'instructor' || $scheduledClass->instructor_id !== $user->id) {
            return redirect()->route('home')->with('error', 'Unauthorized access.');
        }

        $members = $scheduledClass->members()->paginate(20);

        return view('instructor.class-members', compact('scheduledClass', 'members'));
    }

    /**
     * Send message to a member.
     */
    public function sendMessageToMember(Request $request, $userId)
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'instructor') {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            return redirect()->route('home')->with('error', 'Unauthorized.');
        }

        $validated = $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        try {
            $member = User::findOrFail($userId);

            // Create message
            $message = Message::create([
                'sender_id' => $user->id,
                'receiver_id' => $member->id,
                'message' => $validated['message'],
                'read' => false,
            ]);

            // Send notification
            $this->notificationService->sendToUser($member, [
                'type' => 'new_message',
                'title' => '💬 New Message from Your Instructor',
                'message' => "{$user->name} sent you a message: " . substr($validated['message'], 0, 100),
                'priority' => 'high',
                'action_url' => route('member.messages.get', $user->id),
                'data' => ['message_id' => $message->id]
            ]);

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Message sent successfully!']);
            }

            return back()->with('success', 'Message sent successfully!');

        } catch (\Exception $e) {
            Log::error('Error sending message: ' . $e->getMessage());

            if ($request->wantsJson()) {
                return response()->json(['error' => 'Failed to send message'], 500);
            }

            return back()->with('error', 'Failed to send message. Please try again.');
        }
    }
}
