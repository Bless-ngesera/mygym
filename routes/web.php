<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ScheduledClassController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EarningsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth'])
    ->name('dashboard');

Route::get('/instructor/dashboard', function () {
    return view('instructor.dashboard');
})->middleware(['auth','role:instructor'])->name('instructor.dashboard');

Route::resource('/instructor/schedule', ScheduledClassController::class)
    ->only(['index','create','store','destroy'])
    ->middleware(['auth','role:instructor']);

/* Member routes */
Route::middleware(['auth','role:member'])->group(function() {
    Route::get('/member/dashboard', function () {
        return view('member.dashboard');
    })->name('member.dashboard');

    Route::get('/member/book', [BookingController::class, 'create'])->name('booking.create');
    Route::post('/member/bookings', [BookingController::class, 'store'])->name('booking.store');
    Route::get('/member/bookings', [BookingController::class, 'index'])->name('booking.index');
    Route::delete('/member/bookings/{id}', [BookingController::class, 'destroy'])->name('booking.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/receipts', [ReceiptController::class, 'store'])->name('receipts.store');
    Route::get('/receipts/{receipt}', [ReceiptController::class, 'show'])->name('receipts.show');
});

/* ===========================
   ADMIN ROUTES
   =========================== */
Route::prefix('admin')->middleware(['auth','role:admin'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // Instructors CRUD
    Route::get('/instructors', [AdminController::class, 'indexInstructors'])->name('admin.instructors.index');
    Route::get('/instructors/create', [AdminController::class, 'createInstructor'])->name('admin.instructors.create');
    Route::post('/instructors', [AdminController::class, 'storeInstructor'])->name('admin.instructors.store');
    Route::get('/instructors/{id}/edit', [AdminController::class, 'editInstructor'])->name('admin.instructors.edit');
    Route::put('/instructors/{id}', [AdminController::class, 'updateInstructor'])->name('admin.instructors.update');
    Route::delete('/instructors/{id}', [AdminController::class, 'destroyInstructor'])->name('admin.instructors.destroy');

    // Members
    Route::get('/members', function () {
        $members = \App\Models\User::where('role', 'member')->orderByDesc('created_at')->get();
        return view('admin.members.index', compact('members'));
    })->name('admin.members.index');

    // Earnings
    Route::get('/earnings', [AdminController::class, 'earnings'])->name('admin.earnings');
    Route::get('/earnings-data', [AdminController::class, 'earningsData'])->name('admin.earningsData');
    Route::get('/earnings/pdf', [AdminController::class, 'exportPdf'])->name('admin.earnings.pdf');
    Route::get('/earnings/csv', [AdminController::class, 'exportCsv'])->name('admin.earnings.csv');

    // Reports
    Route::get('/reports', [ReportsController::class, 'index'])->name('admin.reports.index');
    Route::post('/reports/generate', [ReportsController::class, 'generate'])->name('reports.generate');
    Route::get('/reports/pdf', [ReportsController::class, 'downloadPdf'])->name('reports.pdf');
    Route::get('/reports/excel', [ReportsController::class, 'downloadExcel'])->name('reports.excel');

    // If you want a separate Instructor PDF, keep this route ONLY if you add the method in ReportsController
    // Otherwise, remove it and use reports.pdf?type=instructors
    Route::get('/reports/instructor-pdf', [ReportsController::class, 'downloadInstructorPdf'])
        ->name('admin.reports.instructor-pdf');

    // Settings
    Route::get('/settings', function () {
        return view('admin.settings');
    })->name('admin.settings.index');
});

Route::get('/instructor/payouts', [EarningsController::class, 'payoutReport'])->name('instructors.payouts');

require __DIR__.'/auth.php';


Route::prefix('admin')->middleware(['auth','role:admin'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])
        ->name('admin.dashboard');

    // Earnings overview page
    Route::get('/earnings', [EarningsController::class, 'index'])
        ->name('admin.earnings');

    // Export routes
    Route::get('/earnings/pdf', [EarningsController::class, 'exportPdf'])
        ->name('earnings.pdf');
    Route::get('/earnings/csv', [EarningsController::class, 'exportCsv'])
        ->name('earnings.csv');
    Route::get('/earnings/excel', [EarningsController::class, 'exportExcel'])
        ->name('earnings.excel');

    // View all transactions
    Route::get('/earnings/all', [EarningsController::class, 'allTransactions'])
        ->name('admin.earnings.all');
});

