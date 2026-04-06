<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\EarningsController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\ScheduledClassController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\MemberDashboardController;
use App\Http\Controllers\Api\MemberDashboardApiController;
use App\Models\ScheduledClass;
use App\Models\Receipt;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', fn() => view('welcome'))->name('home');

// Public class listing (no auth required)
Route::get('/classes', [ScheduledClassController::class, 'publicIndex'])->name('classes.index');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    /*
    |----------------------------------------------------------------------
    | Shared Dashboard — redirects based on role
    |----------------------------------------------------------------------
    */
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    /*
    |----------------------------------------------------------------------
    | Profile
    |----------------------------------------------------------------------
    */
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/',              [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/',            [ProfileController::class, 'update'])->name('update');
        Route::put('/password',      [ProfileController::class, 'passwordUpdate'])->name('password.update');
        Route::delete('/',           [ProfileController::class, 'destroy'])->name('destroy');
    });

    /*
    |----------------------------------------------------------------------
    | User Settings
    |----------------------------------------------------------------------
    */
    Route::prefix('settings')->name('user.settings.')->group(function () {
        Route::get('/',  [SettingsController::class, 'userIndex'])->name('index');
        Route::post('/', [SettingsController::class, 'userUpdate'])->name('update');
    });

    /*
    |----------------------------------------------------------------------
    | Receipts (shared — members AND admins can access)
    |----------------------------------------------------------------------
    */
    Route::prefix('receipts')->name('receipts.')->group(function () {
        Route::get('/',                          [ReceiptController::class, 'index'])->name('index');
        Route::get('/{receipt}',                 [ReceiptController::class, 'show'])->name('show');
        Route::get('/{receipt}/download',        [ReceiptController::class, 'download'])->name('download');
        Route::get('/{receipt}/preview',         [ReceiptController::class, 'preview'])->name('preview');
        Route::get('/{receipt}/print',           [ReceiptController::class, 'print'])->name('print');
        Route::get('/reference/{reference}',     [ReceiptController::class, 'findByReference'])->name('find-by-reference');
        Route::post('/{receipt}/resend',         [ReceiptController::class, 'resendReceipt'])->name('resend');
    });

    /*
    |======================================================================
    | MEMBER ROUTES
    |======================================================================
    */
    Route::prefix('member')->name('member.')->middleware('role:member')->group(function () {

        // ==================== MEMBER DASHBOARD (NEW PREMIUM DASHBOARD) ====================
        Route::get('/dashboard', [MemberDashboardController::class, 'index'])->name('dashboard');

        // Workout routes
        Route::post('/workouts/{workout}/start', [MemberDashboardController::class, 'startWorkout'])->name('workouts.start');
        Route::post('/workouts/{workout}/complete', [MemberDashboardController::class, 'completeWorkout'])->name('workouts.complete');
        Route::post('/workout-exercises/{workoutExercise}/complete', [MemberDashboardController::class, 'completeExercise'])->name('workout-exercises.complete');

        // Attendance routes
        Route::post('/check-in', [MemberDashboardController::class, 'checkIn'])->name('check-in');
        Route::post('/check-out', [MemberDashboardController::class, 'checkOut'])->name('check-out');

        // Nutrition routes
        Route::post('/nutrition', [MemberDashboardController::class, 'addNutrition'])->name('nutrition.store');

        // Progress routes
        Route::post('/progress', [MemberDashboardController::class, 'addProgress'])->name('progress.store');
        Route::post('/goals', [MemberDashboardController::class, 'createGoal'])->name('goals.store');

        // Notification routes
        Route::post('/notifications/{notification}/read', [MemberDashboardController::class, 'markNotificationRead'])->name('notifications.read');
        Route::post('/notifications/mark-all-read', [MemberDashboardController::class, 'markAllNotificationsRead'])->name('notifications.mark-all-read');

        // Message routes
        Route::post('/messages', [MemberDashboardController::class, 'sendMessage'])->name('messages.send');
        Route::get('/messages/{userId}', [MemberDashboardController::class, 'getMessages'])->name('messages.get');

        /*
        | Classes (browse & book) - Existing booking functionality
        */
        Route::get('/classes', [BookingController::class, 'create'])->name('classes');
        Route::post('/classes/book', [BookingController::class, 'store'])->name('book');

        // Cancel a booking (DELETE by ScheduledClass ID)
        Route::delete('/bookings/{id}', [BookingController::class, 'destroy'])->name('cancel-booking');

        // Bookings list with optional ?filter=upcoming|past
        Route::get('/bookings', [BookingController::class, 'index'])->name('bookings');
        Route::get('/bookings/upcoming', [BookingController::class, 'upcoming'])->name('bookings.upcoming');
        Route::get('/bookings/past', [BookingController::class, 'past'])->name('bookings.past');

        // Receipts (member's own)
        Route::get('/receipts', [BookingController::class, 'receipts'])->name('receipts');
        Route::get('/receipts/{receiptId}', [BookingController::class, 'receipt'])->name('receipt');

        // Resend booking confirmation email
        Route::post('/resend-confirmation/{scheduledClassId}', [BookingController::class, 'resendConfirmation'])->name('resend-confirmation');

        // AJAX helpers
        Route::get('/check-availability/{classId}', [BookingController::class, 'checkAvailability'])->name('check-availability');
        Route::get('/statistics', [BookingController::class, 'statistics'])->name('statistics');
    });

    /*
    |======================================================================
    | API Routes for Member Dashboard
    |======================================================================
    */
    Route::prefix('api/member')->middleware('role:member')->group(function () {
        Route::get('/dashboard/stats', [MemberDashboardApiController::class, 'getStats']);
        Route::get('/dashboard/weight-progress', [MemberDashboardApiController::class, 'getWeightProgress']);
        Route::get('/dashboard/workout-frequency', [MemberDashboardApiController::class, 'getWorkoutFrequency']);
    });

    /*
    |======================================================================
    | INSTRUCTOR ROUTES
    |======================================================================
    */
    Route::prefix('instructor')->name('instructor.')->middleware('role:instructor')->group(function () {

        // Dashboard (inline — keeps all stats in one place)
        Route::get('/dashboard', function () {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            $totalUniqueClients = ScheduledClass::where('instructor_id', $user->id)
                ->with('members')
                ->get()
                ->pluck('members')
                ->flatten()
                ->unique('id')
                ->count();

            $totalBookings = ScheduledClass::where('instructor_id', $user->id)
                ->withCount('members')
                ->get()
                ->sum('members_count');

            $totalClasses = ScheduledClass::where('instructor_id', $user->id)->count();

            $upcomingClasses = ScheduledClass::where('instructor_id', $user->id)
                ->where('date_time', '>', Carbon::now())
                ->count();

            $pastClasses = ScheduledClass::where('instructor_id', $user->id)
                ->where('date_time', '<', Carbon::now())
                ->count();

            $totalStudents = ScheduledClass::where('instructor_id', $user->id)
                ->withCount('members')
                ->get()
                ->sum('members_count');

            $totalEarnings = Receipt::whereHas('scheduledClass', fn($q) =>
                $q->where('instructor_id', $user->id)
            )->sum('amount');

            $recentClasses = ScheduledClass::where('instructor_id', $user->id)
                ->with(['classType', 'instructor'])
                ->withCount('members')
                ->latest()
                ->take(5)
                ->get();

            $topClasses = ScheduledClass::where('instructor_id', $user->id)
                ->with('classType')
                ->withCount('members')
                ->orderByDesc('members_count')
                ->take(5)
                ->get();

            return view('instructor.dashboard', compact(
                'totalUniqueClients', 'totalBookings', 'totalClasses',
                'upcomingClasses', 'pastClasses', 'totalStudents',
                'totalEarnings', 'recentClasses', 'topClasses'
            ));
        })->name('dashboard');

        // Upcoming classes
        Route::get('/upcoming', [ScheduledClassController::class, 'index'])->name('upcoming');

        // Create / store a class
        Route::get('/classes/create', [ScheduledClassController::class, 'create'])->name('create');
        Route::post('/schedule', [ScheduledClassController::class, 'store'])->name('schedule.store');

        // All instructor classes
        Route::get('/classes', [ScheduledClassController::class, 'instructorClasses'])->name('classes');

        // Individual schedule management
        Route::get('/schedule/{scheduledClass}', [ScheduledClassController::class, 'show'])->name('schedule.show');
        Route::get('/schedule/{scheduledClass}/edit', [ScheduledClassController::class, 'edit'])->name('schedule.edit');
        Route::put('/schedule/{scheduledClass}', [ScheduledClassController::class, 'update'])->name('schedule.update');
        Route::delete('/schedule/{scheduledClass}', [ScheduledClassController::class, 'destroy'])->name('schedule.destroy');

        // Earnings, calendar, stats
        Route::get('/earnings', [EarningsController::class, 'instructorEarnings'])->name('earnings');
        Route::get('/calendar', [ScheduledClassController::class, 'calendar'])->name('calendar');
        Route::get('/statistics', [ScheduledClassController::class, 'statistics'])->name('statistics');
    });

    /*
    |----------------------------------------------------------------------
    | Schedule resource (instructor middleware applied on resource)
    |----------------------------------------------------------------------
    */
    Route::resource('schedule', ScheduledClassController::class)
        ->parameters(['schedule' => 'scheduledClass'])
        ->middleware('role:instructor');

    Route::prefix('schedule')->name('schedule.')->middleware('role:instructor')->group(function () {
        Route::get('/upcoming', [ScheduledClassController::class, 'index'])->name('upcoming');
        Route::get('/all', [ScheduledClassController::class, 'instructorClasses'])->name('all');
    });

    /*
    |======================================================================
    | ADMIN ROUTES
    |======================================================================
    */
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {

        Route::get('/', [AdminController::class, 'dashboard'])->name('index');
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Members
        Route::prefix('members')->name('members.')->group(function () {
            Route::get('/', [AdminController::class, 'members'])->name('index');
            Route::get('/create', [AdminController::class, 'createMember'])->name('create');
            Route::post('/', [AdminController::class, 'storeMember'])->name('store');
            Route::get('/{id}/edit', [AdminController::class, 'editMember'])->name('edit');
            Route::put('/{id}', [AdminController::class, 'updateMember'])->name('update');
            Route::delete('/{id}', [AdminController::class, 'destroyMember'])->name('destroy');
        });

        // Instructors
        Route::prefix('instructors')->name('instructors.')->group(function () {
            Route::get('/', [AdminController::class, 'indexInstructors'])->name('index');
            Route::get('/create', [AdminController::class, 'createInstructor'])->name('create');
            Route::post('/', [AdminController::class, 'storeInstructor'])->name('store');
            Route::get('/{id}/edit', [AdminController::class, 'editInstructor'])->name('edit');
            Route::put('/{id}', [AdminController::class, 'updateInstructor'])->name('update');
            Route::delete('/{id}', [AdminController::class, 'destroyInstructor'])->name('destroy');
            Route::get('/download-pdf', [AdminController::class, 'downloadInstructorPdf'])->name('download.pdf');
        });

        // Earnings
        Route::prefix('earnings')->name('earnings.')->group(function () {
            Route::get('/', [EarningsController::class, 'index'])->name('index');
            Route::get('/data', [EarningsController::class, 'earningsData'])->name('data');
            Route::get('/all', [EarningsController::class, 'all'])->name('all');
            Route::get('/transactions', [EarningsController::class, 'allTransactions'])->name('transactions');
            Route::get('/instructor-payout', [EarningsController::class, 'instructorPayoutReport'])->name('instructor.payout');
            Route::get('/export-pdf', [EarningsController::class, 'exportPdf'])->name('export.pdf');
            Route::get('/export-csv', [EarningsController::class, 'exportCsv'])->name('export.csv');
            Route::get('/export-excel', [EarningsController::class, 'exportExcel'])->name('export.excel');
        });

        // Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportsController::class, 'index'])->name('index');
            Route::post('/generate', [ReportsController::class, 'generate'])->name('generate');
            Route::get('/download-pdf', [ReportsController::class, 'downloadPdf'])->name('download.pdf');
            Route::get('/download-excel', [ReportsController::class, 'downloadExcel'])->name('download.excel');
            Route::get('/download-instructor-pdf', [ReportsController::class, 'downloadInstructorPdf'])->name('download.instructor.pdf');
        });

        // Settings
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [SettingsController::class, 'index'])->name('index');
            Route::post('/', [SettingsController::class, 'update'])->name('update');
            Route::post('/logo', [SettingsController::class, 'updateLogo'])->name('logo');
            Route::post('/favicon', [SettingsController::class, 'updateFavicon'])->name('favicon');
            Route::post('/reset', [SettingsController::class, 'reset'])->name('reset');
            Route::post('/clear-cache', [SettingsController::class, 'clearCache'])->name('clear-cache');
        });
    });

});

require __DIR__.'/auth.php';
