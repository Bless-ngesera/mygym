<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Receipt;
use App\Models\User;
use App\Models\ScheduledClass;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function dashboard()
    {
        return view('admin.analytics', [
            'memberGrowth' => $this->getMemberGrowth(),
            'popularClasses' => $this->getPopularClasses(),
            'revenueStats' => $this->getRevenueStats(),
            'bookingTrends' => $this->getBookingTrends(),
            'instructorPerformance' => $this->getInstructorPerformance(),
            'attendanceRate' => $this->getAttendanceRate(),
        ]);
    }

    private function getMemberGrowth()
    {
        return User::where('role', 'member')
            ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'), DB::raw('count(*) as total'))
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->take(12)
            ->get();
    }

    private function getPopularClasses()
    {
        return ScheduledClass::with('classType')
            ->withCount('members')
            ->orderBy('members_count', 'desc')
            ->take(5)
            ->get();
    }

    private function getRevenueStats()
    {
        return Receipt::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('SUM(amount) as total')
        )
        ->groupBy('month')
        ->orderBy('month', 'desc')
        ->take(6)
        ->get();
    }

    private function getBookingTrends()
    {
        return Booking::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d") as date'),
            DB::raw('count(*) as total')
        )
        ->where('created_at', '>=', Carbon::now()->subDays(30))
        ->groupBy('date')
        ->orderBy('date', 'asc')
        ->get();
    }

    private function getInstructorPerformance()
    {
        return User::where('role', 'instructor')
            ->withCount(['scheduledClasses as total_classes'])
            ->withCount(['scheduledClasses as completed_classes' => function($q) {
                $q->where('date_time', '<', now());
            }])
            ->withCount(['scheduledClasses as upcoming_classes' => function($q) {
                $q->where('date_time', '>', now());
            }])
            ->get();
    }

    private function getAttendanceRate()
    {
        $totalBookings = Booking::count();
        $completedBookings = Booking::whereHas('scheduledClass', function($q) {
            $q->where('date_time', '<', now());
        })->count();

        return $totalBookings > 0 ? round(($completedBookings / $totalBookings) * 100) : 0;
    }
}
