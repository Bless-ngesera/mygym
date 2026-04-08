<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use App\Models\ScheduledClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InstructorEarningsController extends Controller
{
    public function earnings(Request $request)
    {
        $instructor = auth()->user();

        // Get receipts using JOIN (more reliable)
        $receipts = Receipt::join('scheduled_classes', 'receipts.scheduled_class_id', '=', 'scheduled_classes.id')
            ->where('scheduled_classes.instructor_id', $instructor->id)
            ->select('receipts.*')
            ->with(['scheduledClass.classType', 'user'])
            ->orderBy('receipts.created_at', 'desc')
            ->paginate(15);

        // Calculate total earnings
        $totalEarnings = Receipt::join('scheduled_classes', 'receipts.scheduled_class_id', '=', 'scheduled_classes.id')
            ->where('scheduled_classes.instructor_id', $instructor->id)
            ->sum('receipts.amount');

        // Calculate this month's earnings
        $monthEarnings = Receipt::join('scheduled_classes', 'receipts.scheduled_class_id', '=', 'scheduled_classes.id')
            ->where('scheduled_classes.instructor_id', $instructor->id)
            ->whereMonth('receipts.created_at', now()->month)
            ->whereYear('receipts.created_at', now()->year)
            ->sum('receipts.amount');

        // Calculate average per class
        $totalClasses = ScheduledClass::where('instructor_id', $instructor->id)
            ->whereExists(function($query) {
                $query->select(DB::raw(1))
                    ->from('receipts')
                    ->whereColumn('receipts.scheduled_class_id', 'scheduled_classes.id');
            })
            ->count();

        $avgEarnings = $totalClasses > 0 ? $totalEarnings / $totalClasses : 0;

        // Calculate growth percentage
        $lastMonthEarnings = Receipt::join('scheduled_classes', 'receipts.scheduled_class_id', '=', 'scheduled_classes.id')
            ->where('scheduled_classes.instructor_id', $instructor->id)
            ->whereMonth('receipts.created_at', now()->subMonth()->month)
            ->whereYear('receipts.created_at', now()->subMonth()->year)
            ->sum('receipts.amount');

        $growthPercentage = $lastMonthEarnings > 0
            ? (($monthEarnings - $lastMonthEarnings) / $lastMonthEarnings) * 100
            : ($monthEarnings > 0 ? 100 : 0);

        // Prepare monthly data for the last 12 months
        $monthlyLabels = [];
        $monthlyEarnings = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthStart = $date->copy()->startOfMonth();
            $monthEnd = $date->copy()->endOfMonth();

            $monthlyLabels[] = $date->format('M Y');

            $earning = Receipt::join('scheduled_classes', 'receipts.scheduled_class_id', '=', 'scheduled_classes.id')
                ->where('scheduled_classes.instructor_id', $instructor->id)
                ->whereBetween('receipts.created_at', [$monthStart, $monthEnd])
                ->sum('receipts.amount');

            $monthlyEarnings[] = $earning;
        }

        return view('instructor.earnings', compact(
            'receipts',
            'totalEarnings',
            'monthEarnings',
            'avgEarnings',
            'monthlyLabels',
            'monthlyEarnings',
            'growthPercentage'
        ));
    }

    public function exportTransactions(Request $request)
    {
        $instructor = auth()->user();

        $transactions = Receipt::join('scheduled_classes', 'receipts.scheduled_class_id', '=', 'scheduled_classes.id')
            ->where('scheduled_classes.instructor_id', $instructor->id)
            ->with(['scheduledClass.classType', 'user'])
            ->select('receipts.*')
            ->orderBy('receipts.created_at', 'desc')
            ->get();

        $csvData = [];
        $csvData[] = ['Date', 'Class', 'Member', 'Amount (UGX)', 'Reference Number', 'Status'];

        foreach ($transactions as $transaction) {
            $csvData[] = [
                $transaction->created_at->format('Y-m-d H:i:s'),
                $transaction->scheduledClass->classType->name ?? 'N/A',
                $transaction->user->name ?? 'N/A',
                number_format($transaction->amount, 0),
                $transaction->reference_number ?? 'N/A',
                'Completed'
            ];
        }

        $filename = 'transactions-' . now()->format('Y-m-d') . '.csv';

        return response()->stream(
            function () use ($csvData) {
                $handle = fopen('php://output', 'w');
                foreach ($csvData as $row) {
                    fputcsv($handle, $row);
                }
                fclose($handle);
            },
            200,
            [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]
        );
    }

    public function getAllTransactions(Request $request)
    {
        $instructor = auth()->user();

        $query = Receipt::join('scheduled_classes', 'receipts.scheduled_class_id', '=', 'scheduled_classes.id')
            ->where('scheduled_classes.instructor_id', $instructor->id)
            ->select('receipts.*')
            ->with(['scheduledClass.classType', 'user']);

        // Apply date filters
        if ($request->filled('date_from')) {
            $query->whereDate('receipts.created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('receipts.created_at', '<=', $request->date_to);
        }

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('receipts.reference_number', 'like', "%{$search}%")
                  ->orWhere('users.name', 'like', "%{$search}%")
                  ->orWhere('class_types.name', 'like', "%{$search}%");
            });
        }

        $receipts = $query->orderBy('receipts.created_at', 'desc')->paginate(25);

        return view('instructor.all-transactions', compact('receipts'));
    }
}
