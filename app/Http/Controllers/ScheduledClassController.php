<?php

namespace App\Http\Controllers;

use App\Events\ClassCanceled;
use App\Models\ClassType;
use App\Models\ScheduledClass;
use App\Models\Receipt;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
            ->with('classType');

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

        return view('instructor.show', compact('scheduledClass'));
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
     * List upcoming classes for members to browse and book.
     * Excludes classes already booked by the current user.
     */
    public function upcoming(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        // Get classes that are upcoming and not booked by the current user
        $classes = ScheduledClass::upcoming()
            ->with(['classType', 'instructor'])
            ->whereDoesntHave('members', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->orderBy('date_time', 'asc')
            ->paginate(12);

        return view('member.classes', compact('classes'));
    }

    /**
     * Book a class for the authenticated member and create a receipt.
     * Redirects back to the same page with a success/error message.
     */
    public function book(Request $request, ScheduledClass $scheduledClass)
    {
        /** @var User $user */
        $user = Auth::user();

        // Check if user is a member
        if ($user->role !== 'member') {
            return redirect()->back()->with('error', 'Only members can book classes.');
        }

        // Check if class is in the past
        if ($scheduledClass->date_time->isPast()) {
            return redirect()->back()->with('error', 'Cannot book past classes.');
        }

        // Check if already booked
        if ($user->bookings()->where('scheduled_class_id', $scheduledClass->id)->exists()) {
            return redirect()->back()->with('error', 'You have already booked this class.');
        }

        try {
            DB::beginTransaction();

            // Create the booking
            $user->bookings()->attach($scheduledClass->id);

            // Generate unique receipt number
            $receiptNumber = 'RCP-' . strtoupper(uniqid()) . '-' . date('Ymd');

            // Create receipt
            $receipt = Receipt::create([
                'reference_number' => $receiptNumber,
                'user_id' => $user->id,
                'scheduled_class_id' => $scheduledClass->id,
                'payment_method' => $request->payment_method ?? 'MTN Mobile Money',
                'amount' => $scheduledClass->price,
                'payment_contact' => $request->payment_contact ?? null,
                'status' => 'completed',
            ]);

            DB::commit();

            // Redirect back with success message and receipt link
            return redirect()->back()->with('success', 'Class booked successfully! Receipt #' . $receipt->reference_number . ' has been generated. <a href="' . route('receipts.show', $receipt) . '" class="text-green-800 underline font-semibold">View Receipt →</a>');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Booking error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to book class. Please try again.');
        }
    }

    /**
     * Display member's booked classes.
     */
    public function myBookings()
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->role !== 'member') {
            return redirect()->route('dashboard')->with('error', 'Only members can view their bookings.');
        }

        $bookings = $user->bookings()
            ->with(['classType', 'instructor'])
            ->orderBy('date_time', 'asc')
            ->paginate(10);

        // Pass both classes and bookings for the view
        return view('member.classes', [
            'classes' => $bookings,
            'bookings' => $bookings,
            'isBookingsPage' => true
        ]);
    }

    /**
     * Cancel a booking for a member.
     * Redirects back to the same page with a success/error message.
     */
    public function cancelBooking(Request $request, ScheduledClass $scheduledClass)
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->role !== 'member') {
            return redirect()->back()->with('error', 'Only members can cancel bookings.');
        }

        if ($scheduledClass->date_time->isPast()) {
            return redirect()->back()->with('error', 'Cannot cancel past classes.');
        }

        // Check if the class is starting soon (within 2 hours)
        $hoursUntilClass = Carbon::now()->diffInHours($scheduledClass->date_time, false);

        if ($hoursUntilClass < 2 && $hoursUntilClass > 0) {
            return redirect()->back()->with('error', 'Cannot cancel class within 2 hours of start time.');
        }

        // Check if actually booked
        if (!$user->bookings()->where('scheduled_class_id', $scheduledClass->id)->exists()) {
            return redirect()->back()->with('error', 'You have not booked this class.');
        }

        try {
            DB::beginTransaction();

            // Delete associated receipts first (optional - if you want to keep receipts for canceled bookings)
            // Receipt::where('scheduled_class_id', $scheduledClass->id)
            //     ->where('user_id', $user->id)
            //     ->delete();

            // Remove the booking
            $user->bookings()->detach($scheduledClass->id);

            DB::commit();

            return redirect()->back()->with('success', 'Booking cancelled successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Cancel booking error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to cancel booking. Please try again.');
        }
    }

    /**
     * Get upcoming classes for a member with filtering.
     */
    public function getAvailableClasses(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $query = ScheduledClass::upcoming()
            ->with(['classType', 'instructor'])
            ->whereDoesntHave('members', function($q) use ($user) {
                $q->where('user_id', $user->id);
            });

        // Apply filters
        if ($request->has('class_type_id') && $request->class_type_id) {
            $query->where('class_type_id', $request->class_type_id);
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->where('date_time', '>=', Carbon::parse($request->date_from));
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->where('date_time', '<=', Carbon::parse($request->date_to));
        }

        $classes = $query->orderBy('date_time', 'asc')->paginate(12);

        return response()->json([
            'data' => $classes->items(),
            'current_page' => $classes->currentPage(),
            'last_page' => $classes->lastPage(),
            'total' => $classes->total(),
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
            ->with('classType')
            ->get();

        // Check if the view exists, if not redirect to upcoming classes
        if (!view()->exists('instructor.calendar')) {
            return redirect()->route('instructor.upcoming')
                ->with('info', 'Calendar view is coming soon. Here are your upcoming classes.');
        }

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
