<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use App\Exports\InstructorPayoutExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class EarningsController extends Controller
{
    public function index(Request $request)
    {
        $query = Receipt::with(['user', 'scheduledClass.classType', 'scheduledClass.instructor']); // eager load relations

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        // Paginated list for full receipts table
        $recentReceipts = $query->latest()->paginate(10);

        // Last 5 transactions for summary table
        $recentTransactions = Receipt::with(['user', 'scheduledClass.instructor'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.earnings.index', [
            'recentReceipts'    => $recentReceipts,
            'recentTransactions'=> $recentTransactions,
            'totalEarnings'     => Receipt::sum('amount'),
            'monthEarnings'     => Receipt::whereYear('created_at', now()->year)
                                          ->whereMonth('created_at', now()->month)
                                          ->sum('amount'),
            'pendingPayouts'    => 0, // no status column in receipts
            'monthlyLabels'     => $this->getMonthlyLabels(),
            'monthlyEarnings'   => $this->getMonthlyEarnings(),
        ]);
    }

    public function exportPdf()
    {
        $receipts = Receipt::with(['user','scheduledClass.classType','scheduledClass.instructor'])->latest()->get();
        $pdf = Pdf::loadView('admin.exports.earnings-pdf', compact('receipts'));

        return $pdf->download('earnings_report.pdf');
    }

    public function exportCsv()
    {
        $receipts = Receipt::with(['user','scheduledClass.classType','scheduledClass.instructor'])->latest()->get();

        $export = new class($receipts) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
            private $receipts;
            public function __construct($receipts) { $this->receipts = $receipts; }
            public function collection()
            {
                return $this->receipts->map(function ($r) {
                    return [
                        'Reference'  => $r->reference_number ?? '-',
                        'Member'     => $r->user->name ?? 'Unknown Member',
                        'Instructor' => optional($r->scheduledClass->instructor)->name ?? 'Unknown Instructor',
                        'Amount'     => $r->amount,
                        'Date'       => $r->created_at->format('Y-m-d'),
                    ];
                });
            }
            public function headings(): array
            {
                return ['Reference', 'Member', 'Instructor', 'Amount', 'Date'];
            }
        };

        return Excel::download($export, 'earnings_report.csv');
    }

    public function exportExcel()
    {
        $receipts = Receipt::with(['user','scheduledClass.classType','scheduledClass.instructor'])->latest()->get();

        $export = new class($receipts) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
            private $receipts;
            public function __construct($receipts) { $this->receipts = $receipts; }
            public function collection()
            {
                return $this->receipts->map(function ($r) {
                    return [
                        'Reference'  => $r->reference_number ?? '-',
                        'Member'     => $r->user->name ?? 'Unknown Member',
                        'Instructor' => optional($r->scheduledClass->instructor)->name ?? 'Unknown Instructor',
                        'Amount'     => $r->amount,
                        'Date'       => $r->created_at->format('Y-m-d'),
                    ];
                });
            }
            public function headings(): array
            {
                return ['Reference', 'Member', 'Instructor', 'Amount', 'Date'];
            }
        };

        return Excel::download($export, 'earnings_report.xlsx');
    }

    public function instructorPayoutReport()
    {
        return Excel::download(new InstructorPayoutExport(), 'payout_report.xlsx');
    }

    private function getMonthlyLabels()
    {
        return collect(range(1, 12))
            ->map(fn($m) => now()->subMonths(12 - $m)->format('M'))
            ->toArray();
    }

    private function getMonthlyEarnings()
    {
        return collect(range(1, 12))->map(fn($m) =>
            Receipt::whereYear('created_at', now()->subMonths(12 - $m)->year)
                   ->whereMonth('created_at', now()->subMonths(12 - $m)->month)
                   ->sum('amount')
        )->toArray();
    }

    public function all(Request $request)
    {
        $query = Receipt::with(['user', 'scheduledClass.classType', 'scheduledClass.instructor']);

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        $recentReceipts = $query->latest()->paginate(20);

        return view('admin.earnings.all', [
            'recentReceipts' => $recentReceipts,
        ]);
    }

}
