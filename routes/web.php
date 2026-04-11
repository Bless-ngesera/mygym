<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\EarningsController;
use App\Http\Controllers\InstructorEarningsController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\ScheduledClassController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\MemberDashboardController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\AIController;
use App\Http\Controllers\AIChatController;
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

// Language switcher route
Route::get('/lang/{locale}', function ($locale) {
    $supportedLocales = ['en', 'es', 'fr', 'de', 'it', 'pt', 'sw', 'ar'];
    if (in_array($locale, $supportedLocales)) {
        session()->put('locale', $locale);
        app()->setLocale($locale);

        // Update user preference if logged in
        if (auth()->check()) {
            auth()->user()->update(['language' => $locale]);
        }
    }
    return redirect()->back();
})->name('lang.switch');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    /*
    |----------------------------------------------------------------------
    | Profile
    |----------------------------------------------------------------------
    */
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::put('/password', [ProfileController::class, 'passwordUpdate'])->name('password.update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

    /*
    |----------------------------------------------------------------------
    | User Settings
    |----------------------------------------------------------------------
    */
    Route::prefix('settings')->name('user.settings.')->group(function () {
        Route::get('/', [SettingsController::class, 'userIndex'])->name('index');
        Route::post('/', [SettingsController::class, 'userUpdate'])->name('update');
    });

    /*
    |----------------------------------------------------------------------
    | Receipts (shared — members AND admins can access)
    |----------------------------------------------------------------------
    */
    Route::prefix('receipts')->name('receipts.')->group(function () {
        Route::get('/', [ReceiptController::class, 'index'])->name('index');
        Route::get('/{receipt}', [ReceiptController::class, 'show'])->name('show');
        Route::get('/{receipt}/download', [ReceiptController::class, 'download'])->name('download');
        Route::get('/{receipt}/preview', [ReceiptController::class, 'preview'])->name('preview');
        Route::get('/{receipt}/print', [ReceiptController::class, 'print'])->name('print');
        Route::get('/reference/{reference}', [ReceiptController::class, 'findByReference'])->name('find-by-reference');
        Route::post('/{receipt}/resend', [ReceiptController::class, 'resendReceipt'])->name('resend');
    });

    /*
    |======================================================================
    | AI CHAT SYSTEM (Available for ALL authenticated users)
    |======================================================================
    */
    Route::prefix('chat')->name('chat.')->group(function () {
        // Main chat endpoints
        Route::post('/send', [AIChatController::class, 'sendMessage'])->name('send');
        Route::get('/history', [AIChatController::class, 'getHistory'])->name('history');
        Route::delete('/clear', [AIChatController::class, 'clearHistory'])->name('clear');
        Route::delete('/message/{messageId}', [AIChatController::class, 'deleteMessage'])->name('message.delete');
        Route::get('/message/{messageId}', [AIChatController::class, 'getMessage'])->name('message.show');

        // Suggestions and utilities
        Route::get('/suggestions', [AIChatController::class, 'getSuggestions'])->name('suggestions');
        Route::get('/export', [AIChatController::class, 'exportHistory'])->name('export');
        Route::get('/statistics', [AIChatController::class, 'getStatistics'])->name('statistics');
    });

    /*
    |======================================================================
    | MEMBER ROUTES
    |======================================================================
    */
    Route::prefix('member')->name('member.')->middleware(['role:member'])->group(function () {

        // ==================== MEMBER DASHBOARD ====================
        Route::get('/dashboard', [MemberDashboardController::class, 'index'])->name('dashboard');

        // AI Chat - Main endpoint (legacy, keep for compatibility)
        Route::post('/ai-chat', [MemberDashboardController::class, 'aiChat'])->name('ai.chat');

        // Workout Templates (for scheduling)
        Route::get('/workout-templates', [MemberDashboardController::class, 'getWorkoutTemplates'])->name('workout-templates');

        // Workout routes
        Route::prefix('workouts')->name('workouts.')->group(function () {
            Route::post('/schedule', [MemberDashboardController::class, 'scheduleWorkout'])->name('schedule');
            Route::post('/{workout}/start', [MemberDashboardController::class, 'startWorkout'])->name('start');
            Route::post('/{workout}/complete', [MemberDashboardController::class, 'completeWorkout'])->name('complete');
            Route::put('/{workout}/reschedule', [MemberDashboardController::class, 'rescheduleWorkout'])->name('reschedule');
            Route::delete('/{workout}', [MemberDashboardController::class, 'cancelWorkout'])->name('cancel');
            Route::get('/history', [MemberDashboardController::class, 'workoutHistory'])->name('history');
            Route::get('/upcoming', [MemberDashboardController::class, 'upcomingWorkouts'])->name('upcoming');
        });

        // Workout exercises
        Route::post('/workout-exercises/{workoutExercise}/complete', [MemberDashboardController::class, 'completeExercise'])->name('workout-exercises.complete');

        // Attendance routes
        Route::prefix('attendance')->name('attendance.')->group(function () {
            Route::post('/check-in', [MemberDashboardController::class, 'checkIn'])->name('check-in');
            Route::post('/check-out', [MemberDashboardController::class, 'checkOut'])->name('check-out');
        });

        // Nutrition routes
        Route::prefix('nutrition')->name('nutrition.')->group(function () {
            Route::post('/store', [MemberDashboardController::class, 'addNutrition'])->name('store');
            Route::get('/today', [MemberDashboardController::class, 'getTodayNutrition'])->name('today');
        });

        // Progress routes (weight tracking)
        Route::prefix('progress')->name('progress.')->group(function () {
            Route::post('/weight', [MemberDashboardController::class, 'addProgress'])->name('weight');
        });

        // Goals routes
        Route::prefix('goals')->name('goals.')->group(function () {
            Route::post('/store', [MemberDashboardController::class, 'createGoal'])->name('store');
            Route::get('/', [MemberDashboardController::class, 'getGoals'])->name('index');
        });

        // Notification routes
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::post('/{notification}/read', [MemberDashboardController::class, 'markNotificationRead'])->name('read');
            Route::post('/mark-all-read', [MemberDashboardController::class, 'markAllNotificationsRead'])->name('mark-all-read');
        });

        // Message routes (for instructor communication)
        Route::prefix('messages')->name('messages.')->group(function () {
            Route::post('/send', [MemberDashboardController::class, 'sendMessage'])->name('send');
            Route::get('/{userId}', [MemberDashboardController::class, 'getMessages'])->name('get');
        });

        // Class booking routes
        Route::prefix('classes')->name('classes.')->group(function () {
            Route::get('/', [BookingController::class, 'create'])->name('index');
            Route::post('/book', [BookingController::class, 'store'])->name('book');
            Route::get('/{classId}/availability', [BookingController::class, 'checkAvailability'])->name('availability');
            Route::get('/{classId}', [BookingController::class, 'show'])->name('show');
        });

        // Bookings
        Route::prefix('bookings')->name('bookings.')->group(function () {
            Route::get('/', [BookingController::class, 'index'])->name('index');
            Route::delete('/{id}', [BookingController::class, 'destroy'])->name('cancel');
            Route::get('/upcoming', [BookingController::class, 'upcoming'])->name('upcoming');
            Route::get('/past', [BookingController::class, 'past'])->name('past');
            Route::get('/{id}', [BookingController::class, 'show'])->name('show');
        });

        // Receipts (member's own)
        Route::prefix('receipts')->name('receipts.')->group(function () {
            Route::get('/', [BookingController::class, 'receipts'])->name('index');
            Route::get('/{receiptId}', [BookingController::class, 'receipt'])->name('show');
            Route::get('/{receiptId}/download', [BookingController::class, 'downloadReceipt'])->name('download');
        });
    });

    /*
    |======================================================================
    | INSTRUCTOR ROUTES
    |======================================================================
    */
    Route::prefix('instructor')->name('instructor.')->middleware(['role:instructor'])->group(function () {

        // Dashboard
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

        // Instructor messaging routes
        Route::prefix('messages')->name('messages.')->group(function () {
            Route::get('/conversations', [MessageController::class, 'instructorConversations'])->name('conversations');
            Route::get('/conversation/{userId}', [MessageController::class, 'getConversation'])->name('conversation');
            Route::post('/send', [MessageController::class, 'sendMessage'])->name('send');
            Route::post('/{message}/read', [MessageController::class, 'markAsRead'])->name('read');
            Route::delete('/conversation/{userId}', [MessageController::class, 'deleteConversation'])->name('delete');
        });

        // Upcoming classes
        Route::get('/upcoming', [ScheduledClassController::class, 'index'])->name('upcoming');

        // Create / store a class
        Route::get('/classes/create', [ScheduledClassController::class, 'create'])->name('create');
        Route::post('/schedule', [ScheduledClassController::class, 'store'])->name('schedule.store');

        // All instructor classes
        Route::get('/classes', [ScheduledClassController::class, 'instructorClasses'])->name('classes');

        // Individual schedule management
        Route::prefix('schedule')->name('schedule.')->group(function () {
            Route::get('/{scheduledClass}', [ScheduledClassController::class, 'show'])->name('show');
            Route::get('/{scheduledClass}/edit', [ScheduledClassController::class, 'edit'])->name('edit');
            Route::put('/{scheduledClass}', [ScheduledClassController::class, 'update'])->name('update');
            Route::delete('/{scheduledClass}', [ScheduledClassController::class, 'destroy'])->name('destroy');
        });

        // Earnings routes
        Route::prefix('earnings')->name('earnings.')->group(function () {
            Route::get('/', [InstructorEarningsController::class, 'earnings'])->name('index');
            Route::get('/export', [InstructorEarningsController::class, 'exportTransactions'])->name('export');
            Route::get('/transactions', [InstructorEarningsController::class, 'getAllTransactions'])->name('transactions');
            Route::get('/summary', [InstructorEarningsController::class, 'getEarningsSummary'])->name('summary');
            Route::get('/payouts', [InstructorEarningsController::class, 'getPayoutHistory'])->name('payouts');
        });

        // Calendar and statistics
        Route::get('/calendar', [ScheduledClassController::class, 'calendar'])->name('calendar');
        Route::get('/statistics', [ScheduledClassController::class, 'statistics'])->name('statistics');

        // Member management for instructors
        Route::prefix('members')->name('members.')->group(function () {
            Route::get('/', [ScheduledClassController::class, 'members'])->name('index');
            Route::get('/{userId}/progress', [ScheduledClassController::class, 'memberProgress'])->name('progress');
            Route::post('/{userId}/message', [ScheduledClassController::class, 'sendMessageToMember'])->name('send-message');
        });

        // Export routes
        Route::get('/export/classes', [ScheduledClassController::class, 'exportClasses'])->name('export.classes');
        Route::get('/export/earnings', [InstructorEarningsController::class, 'exportEarnings'])->name('export.earnings');
    });

    /*
    |----------------------------------------------------------------------
    | Schedule resource (instructor middleware applied on resource)
    |----------------------------------------------------------------------
    */
    Route::resource('schedule', ScheduledClassController::class)
        ->parameters(['schedule' => 'scheduledClass'])
        ->middleware(['role:instructor']);

    /*
    |======================================================================
    | ADMIN ROUTES
    |======================================================================
    */
    Route::prefix('admin')->name('admin.')->middleware(['role:admin'])->group(function () {

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
            Route::post('/{id}/assign-instructor', [AdminController::class, 'assignInstructor'])->name('assign-instructor');
            Route::get('/export', [AdminController::class, 'exportMembers'])->name('export');
            Route::post('/bulk-delete', [AdminController::class, 'bulkDeleteMembers'])->name('bulk-delete');
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
            Route::get('/{id}/members', [AdminController::class, 'instructorMembers'])->name('members');
            Route::post('/{id}/toggle-status', [AdminController::class, 'toggleInstructorStatus'])->name('toggle-status');
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
            Route::post('/process-payouts', [EarningsController::class, 'processPayouts'])->name('process-payouts');
        });

        // Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportsController::class, 'index'])->name('index');
            Route::post('/generate', [ReportsController::class, 'generate'])->name('generate');
            Route::get('/download-pdf', [ReportsController::class, 'downloadPdf'])->name('download.pdf');
            Route::get('/download-excel', [ReportsController::class, 'downloadExcel'])->name('download.excel');
            Route::get('/download-instructor-pdf', [ReportsController::class, 'downloadInstructorPdf'])->name('download.instructor.pdf');
            Route::get('/members', [ReportsController::class, 'membersReport'])->name('members');
            Route::get('/attendance', [ReportsController::class, 'attendanceReport'])->name('attendance');
            Route::get('/financial', [ReportsController::class, 'financialReport'])->name('financial');
            Route::get('/classes', [ReportsController::class, 'classesReport'])->name('classes');
        });

        // Settings
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [SettingsController::class, 'index'])->name('index');
            Route::post('/', [SettingsController::class, 'update'])->name('update');
            Route::post('/logo', [SettingsController::class, 'updateLogo'])->name('logo');
            Route::post('/favicon', [SettingsController::class, 'updateFavicon'])->name('favicon');
            Route::post('/reset', [SettingsController::class, 'reset'])->name('reset');
            Route::post('/clear-cache', [SettingsController::class, 'clearCache'])->name('clear-cache');
            Route::post('/backup', [SettingsController::class, 'createBackup'])->name('backup');
            Route::get('/backups', [SettingsController::class, 'listBackups'])->name('backups');
            Route::delete('/backups/{filename}', [SettingsController::class, 'deleteBackup'])->name('backups.delete');
        });

        // System health
        Route::prefix('system')->name('system.')->group(function () {
            Route::get('/health', [AdminController::class, 'systemHealth'])->name('health');
            Route::get('/logs', [AdminController::class, 'systemLogs'])->name('logs');
            Route::post('/cache/clear', [AdminController::class, 'clearSystemCache'])->name('clear-cache');
            Route::get('/phpinfo', [AdminController::class, 'phpInfo'])->name('phpinfo');
            Route::get('/queue-status', [AdminController::class, 'queueStatus'])->name('queue-status');
            Route::post('/queue-restart', [AdminController::class, 'restartQueue'])->name('queue-restart');
        });

        // Database management
        Route::prefix('database')->name('database.')->group(function () {
            Route::get('/backup', [AdminController::class, 'databaseBackup'])->name('backup');
            Route::post('/optimize', [AdminController::class, 'optimizeDatabase'])->name('optimize');
        });
    });

});

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';

// Fallback route for 404 errors (must be last)
Route::fallback(function () {
    if (request()->expectsJson()) {
        return response()->json(['success' => false, 'message' => 'Resource not found'], 404);
    }
    return response()->view('errors.404', [], 404);
})->name('fallback');
