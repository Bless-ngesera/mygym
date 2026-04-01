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
use App\Models\ScheduledClass;
use App\Models\Receipt;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Authenticated routes
Route::middleware('auth')->group(function () {

    // Dashboard (uses __invoke method)
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // Bookings
    Route::prefix('bookings')->name('bookings.')->group(function () {
        Route::get('/', [BookingController::class, 'index'])->name('index');
        Route::get('/create', [BookingController::class, 'create'])->name('create');
        Route::post('/', [BookingController::class, 'store'])->name('store');
        Route::delete('/{id}', [BookingController::class, 'destroy'])->name('destroy');
    });

    // ==================== SCHEDULE / CLASSES ROUTES ====================
    // Resource controller for schedule (instructors only)
    Route::resource('schedule', ScheduledClassController::class)
        ->parameters(['schedule' => 'scheduledClass'])
        ->middleware(['role:instructor']);

    // Additional schedule routes
    Route::prefix('schedule')->name('schedule.')->middleware(['role:instructor'])->group(function () {
        Route::get('/upcoming', [ScheduledClassController::class, 'index'])->name('upcoming');
        Route::get('/all', [ScheduledClassController::class, 'instructorClasses'])->name('all');
    });

    // ==================== MEMBER ROUTES ====================
    Route::prefix('member')->name('member.')->middleware('role:member')->group(function () {
        // Dashboard
        Route::get('/dashboard', function () {
            return view('member.dashboard');
        })->name('dashboard');

        // Available classes (view only)
        Route::get('/classes', [ScheduledClassController::class, 'upcoming'])->name('classes');

        // Book a class (standard POST, redirects back)
        Route::post('/classes/{scheduledClass}/book', [ScheduledClassController::class, 'book'])->name('book');

        // My bookings
        Route::get('/bookings', [ScheduledClassController::class, 'myBookings'])->name('bookings');

        // Cancel a booking
        Route::delete('/bookings/{scheduledClass}', [ScheduledClassController::class, 'cancelBooking'])->name('cancel-booking');

        // Get available classes (AJAX filtering - optional)
        Route::get('/available-classes', [ScheduledClassController::class, 'getAvailableClasses'])->name('available-classes');
    });

    // ==================== LEGACY CLASSES ROUTES (keep for backward compatibility) ====================
    // These routes might be used elsewhere, keeping them but they will redirect to member routes
    Route::prefix('classes')->name('classes.')->group(function () {
        Route::get('/', [ScheduledClassController::class, 'upcoming'])->name('index');
        Route::post('/{scheduledClass}/book', [ScheduledClassController::class, 'book'])->name('book');
    });

    // Receipts
    Route::prefix('receipts')->name('receipts.')->group(function () {
        Route::get('/', [ReceiptController::class, 'index'])->name('index');
        Route::post('/', [ReceiptController::class, 'store'])->name('store');
        Route::get('/{receipt}', [ReceiptController::class, 'show'])->name('show');
    });

    // ==================== PROFILE ROUTES ====================
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::put('/password', [ProfileController::class, 'passwordUpdate'])->name('password.update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

    // ==================== USER SETTINGS ====================
    Route::prefix('settings')->name('user.settings.')->group(function () {
        Route::get('/', [SettingsController::class, 'userIndex'])->name('index');
        Route::post('/', [SettingsController::class, 'userUpdate'])->name('update');
    });

    // ==================== ADMIN ROUTES ====================
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/', [AdminController::class, 'dashboard'])->name('index');

        // Members Management
        Route::prefix('members')->name('members.')->group(function () {
            Route::get('/', [AdminController::class, 'members'])->name('index');
            Route::get('/create', [AdminController::class, 'createMember'])->name('create');
            Route::post('/', [AdminController::class, 'storeMember'])->name('store');
            Route::get('/{id}/edit', [AdminController::class, 'editMember'])->name('edit');
            Route::put('/{id}', [AdminController::class, 'updateMember'])->name('update');
            Route::delete('/{id}', [AdminController::class, 'destroyMember'])->name('destroy');
        });

        // Earnings
        Route::prefix('earnings')->name('earnings.')->group(function () {
            Route::get('/', [EarningsController::class, 'index'])->name('index');
            Route::get('/data', [EarningsController::class, 'earningsData'])->name('data');
            Route::get('/export-pdf', [EarningsController::class, 'exportPdf'])->name('export.pdf');
            Route::get('/export-csv', [EarningsController::class, 'exportCsv'])->name('export.csv');
            Route::get('/export-excel', [EarningsController::class, 'exportExcel'])->name('export.excel');
            Route::get('/instructor-payout', [EarningsController::class, 'instructorPayoutReport'])->name('instructor.payout');
            Route::get('/all', [EarningsController::class, 'all'])->name('all');
            Route::get('/transactions', [EarningsController::class, 'allTransactions'])->name('transactions');
        });

        // Instructor Management
        Route::prefix('instructors')->name('instructors.')->group(function () {
            Route::get('/', [AdminController::class, 'indexInstructors'])->name('index');
            Route::get('/create', [AdminController::class, 'createInstructor'])->name('create');
            Route::post('/', [AdminController::class, 'storeInstructor'])->name('store');
            Route::get('/{id}/edit', [AdminController::class, 'editInstructor'])->name('edit');
            Route::put('/{id}', [AdminController::class, 'updateInstructor'])->name('update');
            Route::delete('/{id}', [AdminController::class, 'destroyInstructor'])->name('destroy');
            Route::get('/download-pdf', [AdminController::class, 'downloadInstructorPdf'])->name('download.pdf');
        });

        // Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportsController::class, 'index'])->name('index');
            Route::post('/generate', [ReportsController::class, 'generate'])->name('generate');
            Route::get('/download-pdf', [ReportsController::class, 'downloadPdf'])->name('download.pdf');
            Route::get('/download-excel', [ReportsController::class, 'downloadExcel'])->name('download.excel');
            Route::get('/download-instructor-pdf', [ReportsController::class, 'downloadInstructorPdf'])->name('download.instructor.pdf');
        });

        // Admin Settings
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [SettingsController::class, 'index'])->name('index');
            Route::post('/', [SettingsController::class, 'update'])->name('update');
            Route::post('/logo', [SettingsController::class, 'updateLogo'])->name('logo');
            Route::post('/favicon', [SettingsController::class, 'updateFavicon'])->name('favicon');
            Route::post('/reset', [SettingsController::class, 'reset'])->name('reset');
            Route::post('/clear-cache', [SettingsController::class, 'clearCache'])->name('clear-cache');
        });
    });

    // ==================== INSTRUCTOR ROUTES ====================
    Route::prefix('instructor')->name('instructor.')->middleware('role:instructor')->group(function () {
        // Dashboard
        Route::get('/dashboard', function () {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            $totalClasses = ScheduledClass::where('instructor_id', $user->id)->count();
            $upcomingClasses = ScheduledClass::where('instructor_id', $user->id)
                ->where('date_time', '>', Carbon::now())
                ->count();
            $totalStudents = ScheduledClass::where('instructor_id', $user->id)
                ->withCount('members')
                ->get()
                ->sum('members_count');

            $totalEarnings = Receipt::whereHas('scheduledClass', function($query) use ($user) {
                $query->where('instructor_id', $user->id);
            })->sum('amount');

            $recentClasses = ScheduledClass::where('instructor_id', $user->id)
                ->with('classType')
                ->latest()
                ->take(5)
                ->get();

            return view('instructor.dashboard', compact(
                'totalClasses',
                'upcomingClasses',
                'totalStudents',
                'totalEarnings',
                'recentClasses'
            ));
        })->name('dashboard');

        // Upcoming classes
        Route::get('/upcoming', [ScheduledClassController::class, 'index'])->name('upcoming');

        // Create class
        Route::get('/classes/create', [ScheduledClassController::class, 'create'])->name('create');
        Route::post('/schedule', [ScheduledClassController::class, 'store'])->name('schedule.store');

        // All classes
        Route::get('/classes', [ScheduledClassController::class, 'instructorClasses'])->name('classes');

        // Earnings
        Route::get('/earnings', [EarningsController::class, 'instructorEarnings'])->name('earnings');

        // Calendar
        Route::get('/calendar', [ScheduledClassController::class, 'calendar'])->name('calendar');

        // Schedule management (CRUD)
        Route::get('/schedule/{scheduledClass}', [ScheduledClassController::class, 'show'])->name('schedule.show');
        Route::get('/schedule/{scheduledClass}/edit', [ScheduledClassController::class, 'edit'])->name('schedule.edit');
        Route::put('/schedule/{scheduledClass}', [ScheduledClassController::class, 'update'])->name('schedule.update');
        Route::delete('/schedule/{scheduledClass}', [ScheduledClassController::class, 'destroy'])->name('schedule.destroy');
    });
});

require __DIR__.'/auth.php';
