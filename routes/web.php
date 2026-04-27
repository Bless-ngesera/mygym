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
use App\Http\Controllers\ChatSessionController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\InstructorDashboardController;
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

        if (Auth::check()) {
            Auth::user()->update(['language' => $locale]);
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
    | Plans & Subscriptions
    |----------------------------------------------------------------------
    */
    Route::prefix('plans')->name('plans.')->group(function () {
        Route::get('/', [PlanController::class, 'index'])->name('index');
        Route::post('/{plan}/subscribe', [PlanController::class, 'subscribe'])->name('subscribe');
        Route::post('/cancel', [PlanController::class, 'cancelSubscription'])->name('cancel');
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
    | NOTIFICATIONS SYSTEM (For all authenticated users)
    |======================================================================
    */
    Route::prefix('notifications')->name('notifications.')->group(function () {
        // Main views
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/settings', [NotificationController::class, 'settings'])->name('settings');
        Route::put('/settings', [NotificationController::class, 'updateSettings'])->name('update-settings');

        // AJAX endpoints
        Route::get('/recent', [NotificationController::class, 'recent'])->name('recent');
        Route::get('/stats', [NotificationController::class, 'stats'])->name('stats');
        Route::get('/unread-count', [NotificationController::class, 'unreadCount'])->name('unread-count');
        Route::get('/export', [NotificationController::class, 'export'])->name('export');

        // Read operations
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::post('/mark-multiple-read', [NotificationController::class, 'markMultipleAsRead'])->name('mark-multiple-read');

        // Delete operations
        Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');
        Route::delete('/clear-all', [NotificationController::class, 'clearAll'])->name('clear-all');
        Route::delete('/clear-read', [NotificationController::class, 'clearRead'])->name('clear-read');
        Route::delete('/destroy-multiple', [NotificationController::class, 'destroyMultiple'])->name('destroy-multiple');
    });

    /*
    |======================================================================
    | AI CHAT SYSTEM (Available for ALL authenticated users)
    |======================================================================
    */
    Route::prefix('chat')->name('chat.')->group(function () {
        Route::post('/send', [ChatSessionController::class, 'sendMessage'])->name('send');
        Route::get('/suggestions', [ChatSessionController::class, 'getSuggestions'])->name('suggestions');

        Route::get('/sessions', [ChatSessionController::class, 'index'])->name('sessions.index');
        Route::get('/sessions/current', [ChatSessionController::class, 'getCurrentSession'])->name('sessions.current');
        Route::post('/sessions', [ChatSessionController::class, 'store'])->name('sessions.store');
        Route::get('/sessions/{id}', [ChatSessionController::class, 'show'])->name('sessions.show');
        Route::put('/sessions/{id}', [ChatSessionController::class, 'update'])->name('sessions.update');
        Route::delete('/sessions/{id}', [ChatSessionController::class, 'destroy'])->name('sessions.destroy');

        Route::get('/sessions/{id}/share', [ChatSessionController::class, 'shareSession'])->name('sessions.share');
        Route::get('/share/{token}', [ChatSessionController::class, 'getSharedSession'])->name('share');

        Route::post('/message/{messageId}/regenerate', [ChatSessionController::class, 'regenerateMessage'])->name('message.regenerate');

        Route::delete('/clear-all', [ChatSessionController::class, 'clearAllHistory'])->name('clear-all');
        Route::get('/export', [ChatSessionController::class, 'exportHistory'])->name('export');
        Route::get('/statistics', [ChatSessionController::class, 'getStatistics'])->name('statistics');

        // Legacy routes
        Route::get('/history-list', [ChatSessionController::class, 'index'])->name('history-list');
        Route::get('/history', [ChatSessionController::class, 'getCurrentSession'])->name('history');
        Route::delete('/clear', [ChatSessionController::class, 'clearHistory'])->name('clear');
        Route::delete('/message/{messageId}', [ChatSessionController::class, 'deleteMessage'])->name('message.delete');
        Route::get('/message/{messageId}', [ChatSessionController::class, 'getMessage'])->name('message.show');
        Route::post('/feedback', [ChatSessionController::class, 'submitFeedback'])->name('feedback');
    });

    /*
    |======================================================================
    | MEMBER ROUTES
    |======================================================================
    */
    Route::middleware(['role:member'])->prefix('member')->name('member.')->group(function () {

        // Dashboard
        Route::get('/dashboard', [MemberDashboardController::class, 'index'])->name('dashboard');

        // AI Chat
        Route::post('/ai-chat', [MemberDashboardController::class, 'aiChat'])->name('ai.chat');

        // Workout Templates
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
            Route::get('/{workout}', [MemberDashboardController::class, 'getWorkoutDetails'])->name('details');
        });

        // Workout exercises
        Route::post('/workout-exercises/{workoutExercise}/complete', [MemberDashboardController::class, 'completeExercise'])->name('workout-exercises.complete');

        // Trainer selection
        Route::post('/select-trainer', [MemberDashboardController::class, 'selectTrainer'])->name('select-trainer');
        Route::get('/chat-messages/{trainerId}', [MemberDashboardController::class, 'getChatMessages'])->name('chat.messages');

        // MESSAGE ROUTES (Member specific - using MemberDashboardController)
        Route::prefix('messages')->name('messages.')->group(function () {
            Route::get('/conversation/{trainerId}', [MemberDashboardController::class, 'getConversation'])->name('conversation');
            Route::post('/send', [MemberDashboardController::class, 'sendMessageToTrainer'])->name('send');
            Route::put('/{messageId}', [MemberDashboardController::class, 'updateMessage'])->name('update');
            Route::delete('/{messageId}', [MemberDashboardController::class, 'deleteMessage'])->name('delete');
            Route::post('/{messageId}/pin', [MemberDashboardController::class, 'pinMessage'])->name('pin');
        });

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

        // Progress routes
        Route::prefix('progress')->name('progress.')->group(function () {
            Route::post('/weight', [MemberDashboardController::class, 'addProgress'])->name('weight');
        });

        // Goals routes
        Route::prefix('goals')->name('goals.')->group(function () {
            Route::post('/store', [MemberDashboardController::class, 'createGoal'])->name('store');
            Route::get('/', [MemberDashboardController::class, 'getGoals'])->name('index');
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
            Route::post('/{scheduledClassId}/resend', [BookingController::class, 'resendConfirmation'])->name('resend');
            Route::get('/statistics', [BookingController::class, 'statistics'])->name('statistics');
        });

        // Receipts
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
    Route::middleware(['role:instructor'])->prefix('instructor')->name('instructor.')->group(function () {

        Route::get('/dashboard', [InstructorDashboardController::class, 'index'])->name('dashboard');

        // Instructor Messages view
        Route::get('/messages', [MessageController::class, 'instructorConversations'])->name('messages.index');

        // INSTRUCTOR MESSAGE ROUTES (using MessageController for instructors)
        Route::prefix('messages')->name('messages.')->group(function () {
            Route::get('/conversation/{memberId}', [MessageController::class, 'getConversation'])->name('conversation');
            Route::post('/send', [MessageController::class, 'sendMessage'])->name('send');
            Route::put('/{messageId}', [MessageController::class, 'updateMessage'])->name('update');
            Route::delete('/{messageId}', [MessageController::class, 'deleteMessage'])->name('delete');
            Route::post('/{messageId}/pin', [MessageController::class, 'pinMessage'])->name('pin');
            Route::get('/unread-count', [MessageController::class, 'getUnreadCount'])->name('unread-count');
        });

        // Notification routes for instructors
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [NotificationController::class, 'index'])->name('index');
            Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('read');
            Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
            Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');
            Route::delete('/clear-all', [NotificationController::class, 'clearAll'])->name('clear-all');
            Route::get('/stats', [NotificationController::class, 'stats'])->name('stats');
            Route::get('/unread-count', [NotificationController::class, 'unreadCount'])->name('unread-count');
        });

        // Upcoming classes
        Route::get('/upcoming', [ScheduledClassController::class, 'index'])->name('upcoming');

        // Create / store a class
        Route::get('/classes/create', [ScheduledClassController::class, 'create'])->name('create');
        Route::post('/schedule', [ScheduledClassController::class, 'store'])->name('schedule.store');

        // All instructor classes
        Route::get('/classes', [ScheduledClassController::class, 'instructorClasses'])->name('classes');

        // Schedule management
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

        // Member management
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
    | Schedule resource
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
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {

        Route::get('/', [AdminController::class, 'dashboard'])->name('index');
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Notification routes for admins
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [NotificationController::class, 'index'])->name('index');
            Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('read');
            Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
            Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');
            Route::delete('/clear-all', [NotificationController::class, 'clearAll'])->name('clear-all');
            Route::get('/stats', [NotificationController::class, 'stats'])->name('stats');
            Route::get('/unread-count', [NotificationController::class, 'unreadCount'])->name('unread-count');
            Route::get('/export', [NotificationController::class, 'export'])->name('export');
        });

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
            Route::post('/clear-logs', [AdminController::class, 'clearLogs'])->name('clear-logs');
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

// Fallback route for 404 errors
Route::fallback(function () {
    if (request()->expectsJson()) {
        return response()->json(['success' => false, 'message' => 'Resource not found'], 404);
    }
    return response()->view('errors.404', [], 404);
})->name('fallback');
