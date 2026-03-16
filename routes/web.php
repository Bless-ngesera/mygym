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
use Illuminate\Support\Facades\Route;

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

    // Scheduled Classes (Full Resource Controller)
    Route::resource('classes', ScheduledClassController::class)->parameters([
        'classes' => 'scheduledClass'
    ]);

    // Receipts
    Route::prefix('receipts')->name('receipts.')->group(function () {
        Route::post('/', [ReceiptController::class, 'store'])->name('store');
        Route::get('/{receipt}', [ReceiptController::class, 'show'])->name('show');
    });

    // Admin routes - using your CheckUserRole middleware with 'admin' parameter
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {

        // Admin Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Admin main dashboard (legacy)
        Route::get('/', [AdminController::class, 'dashboard'])->name('index');

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
    });

    // Instructor routes - using your CheckUserRole middleware with 'instructor' parameter
    Route::prefix('instructor')->name('instructor.')->middleware('role:instructor')->group(function () {
        // Add instructor-specific routes here
        Route::get('/dashboard', function () {
            return view('instructor.dashboard');
        })->name('dashboard');

        Route::get('/classes', [ScheduledClassController::class, 'instructorClasses'])->name('classes');
        Route::get('/earnings', [EarningsController::class, 'instructorEarnings'])->name('earnings');
    });

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Member dashboard
    Route::get('/member/dashboard', function () {
        return view('member.dashboard'); // Make sure this view exists
    })->name('member.dashboard');
});

require __DIR__.'/auth.php';
