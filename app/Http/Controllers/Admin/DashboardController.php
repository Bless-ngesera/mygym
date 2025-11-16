<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Instructor;
use App\Models\Member;
use App\Models\Payment;
use App\Models\Plan;
use Carbon\Carbon;
use DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Totals
        $totalUsers = User::count();
        $totalInstructors = Instructor::count();
        $totalMembers = Member::count();
        $totalEarnings = Payment::sum('amount');

        // Recent items
        $recentInstructors = Instructor::latest()->limit(6)->get();
        $recentMembers = Member::with('plan')->latest()->limit(6)->get();

        // Monthly earnings for last 12 months
        $months = collect();
        $monthlyEarnings = collect();
        for ($i = 11; $i >= 0; $i--) {
            $dt = Carbon::now()->subMonths($i);
            $label = $dt->format('M');
            $months->push($label);

            $sum = Payment::whereYear('paid_at', $dt->year)
                ->whereMonth('paid_at', $dt->month)
                ->sum('amount');
            $monthlyEarnings->push((float) $sum);
        }

        // Earnings by instructor
        $instructorEarnings = Instructor::get()->map(function ($ins) {
            $sum = Payment::where('instructor_id', $ins->id)->sum('amount');
            return [
                'name' => $ins->name,
                'amount' => (float) $sum,
            ];
        })->filter(function ($row) {
            return $row['amount'] > 0;
        })->values()->all();

        // Earnings by plan
        $planEarnings = Plan::get()->map(function ($plan) {
            $sum = $plan->members()->with('payments')->get()->flatMap->payments->sum('amount');
            return [
                'plan' => $plan->name,
                'amount' => (float) $sum,
            ];
        })->filter(fn($r) => $r['amount'] > 0)->values()->all();

        return view('admin.dashboard', [
            'totalUsers' => $totalUsers,
            'totalInstructors' => $totalInstructors,
            'totalMembers' => $totalMembers,
            'totalEarnings' => $totalEarnings,
            'recentInstructors' => $recentInstructors,
            'recentMembers' => $recentMembers,
            'monthlyLabels' => $months->all(),
            'monthlyEarnings' => $monthlyEarnings->all(),
            'instructorEarnings' => $instructorEarnings,
            'planEarnings' => $planEarnings,
        ]);
    }
}
