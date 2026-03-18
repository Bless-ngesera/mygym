<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Receipt;
use App\Models\Booking;
use App\Models\ScheduledClass;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // ==================== SUMMARY CARDS DATA ====================
        $totalUsers = User::count();
        $totalInstructors = User::where('role', 'instructor')->count();
        $totalMembers = User::where('role', 'member')->count();
        $totalEarnings = Receipt::sum('amount') ?? 0;

        // ==================== RECENT DATA FOR TABLES ====================
        $recentInstructors = User::where('role', 'instructor')
            ->latest()
            ->take(5)
            ->get();

        $recentMembers = User::where('role', 'member')
            ->latest()
            ->take(5)
            ->get();

        $recentBookings = Booking::with(['user', 'scheduledClass.classType'])
            ->latest()
            ->take(5)
            ->get();

        // ==================== ALL DATA FOR MODALS ====================
        $allInstructors = User::where('role', 'instructor')
            ->latest()
            ->get();

        $allMembers = User::where('role', 'member')
            ->latest()
            ->get();

        // ==================== CHART DATA ====================
        // Monthly earnings for the last 12 months
        $monthlyLabels = [];
        $monthlyEarnings = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthlyLabels[] = $month->format('M Y');

            $earnings = Receipt::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('amount');

            $monthlyEarnings[] = $earnings;
        }

        // Instructor earnings breakdown for doughnut chart
        $instructorEarnings = User::where('role', 'instructor')
            ->with('receipts')
            ->get()
            ->map(function ($instructor) {
                return (object) [
                    'name' => $instructor->name,
                    'amount' => $instructor->receipts->sum('amount') ?? 0
                ];
            })
            ->filter(function ($item) {
                return $item->amount > 0;
            })
            ->values();

        // Plan earnings (if you have plans - keep as empty collection for now)
        $planEarnings = collect([]);

        // ==================== ADDITIONAL STATS ====================
        $totalBookings = Booking::count();
        $totalClasses = ScheduledClass::count();
        $totalAdmins = User::where('role', 'admin')->count();

        // Monthly earnings for current month
        $monthEarnings = Receipt::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount') ?? 0;

        // Recent transactions for earnings page (if needed)
        $recentTransactions = Receipt::with('user')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            // Summary cards
            'totalUsers',
            'totalInstructors',
            'totalMembers',
            'totalEarnings',

            // Recent data
            'recentInstructors',
            'recentMembers',
            'recentBookings',

            // All data for modals
            'allInstructors',
            'allMembers',

            // Chart data
            'monthlyLabels',
            'monthlyEarnings',
            'instructorEarnings',
            'planEarnings',

            // Additional stats
            'totalBookings',
            'totalClasses',
            'totalAdmins',
            'monthEarnings',
            'recentTransactions'
        ));
    }
}
