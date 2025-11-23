<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Receipt;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class ReportsController extends Controller
{
    /**
     * Show the reports page.
     */
    public function index()
    {
        return view('admin.reports.index');
    }

    /**
     * Generate report data based on type and date range.
     */
    public function generate(Request $request)
    {
        $type = $request->input('type');
        $from = $request->input('from');
        $to   = $request->input('to');

        $query = Receipt::query();

        if ($from && $to) {
            $query->whereBetween('created_at', [$from, $to]);
        }

        $data = match ($type) {
            // Income by year
            'income_year' => $query->selectRaw('YEAR(created_at) as Year, SUM(amount) as Total')
                                   ->groupBy('Year')
                                   ->orderBy('Year')
                                   ->get(),

            // Income by month
            'income_month' => $query->selectRaw('YEAR(created_at) as Year, MONTH(created_at) as Month, SUM(amount) as Total')
                                    ->groupBy('Year','Month')
                                    ->orderBy('Year')
                                    ->orderBy('Month')
                                    ->get(),

            // Instructors earnings
            'instructors' => $query->with('scheduledClass.instructor')
                                   ->get()
                                   ->groupBy('scheduled_class_id')
                                   ->map(function ($group) {
                                       $first = $group->first();
                                       return [
                                           'Instructor Name'  => optional(optional($first->scheduledClass)->instructor)->name ?? 'Unknown',
                                           'Instructor Email' => optional(optional($first->scheduledClass)->instructor)->email ?? '-',
                                           'Scheduled Class'  => optional($first->scheduledClass)->title ?? 'Class #' . $first->scheduled_class_id,
                                           'Total (UGX)'      => $group->sum('amount'),
                                       ];
                                   })
                                   ->values(),

            // Income by plan
            'plans' => $query->selectRaw('plan_id as Plan, SUM(amount) as Total')
                             ->groupBy('Plan')
                             ->get(),

            // Members growth
            'members' => $query->selectRaw('YEAR(created_at) as Year, COUNT(DISTINCT user_id) as Members')
                               ->groupBy('Year')
                               ->orderBy('Year')
                               ->get(),

            default => collect(),
        };

        return response()->json($data);
    }

    /**
     * Download report as PDF.
     */
    public function downloadPdf(Request $request)
{
    $type = $request->input('type');

    // Get dataset from generate()
    $rows = $this->generate($request)->getData();

    // Convert stdClass objects to arrays for Blade
    $rows = collect($rows)->map(fn($r) => (array) $r)->values()->all();

    // Use a custom Blade for instructors, dynamic for others
    $view = $type === 'instructors'
        ? 'admin.reports.instructors-pdf'
        : 'admin.reports.dynamic-pdf';

    $pdf = Pdf::loadView($view, [
        'rows' => $rows,
        'type' => $type
    ]);

    return $pdf->download("{$type}-report.pdf");
}


    /**
     * Download report as Excel.
     */
    public function downloadExcel(Request $request)
    {
        $type = $request->input('type');

        // Get dataset from generate()
        $rows = $this->generate($request)->getData();

        // Convert stdClass objects to arrays
        $rows = collect($rows)->map(fn($r) => (array) $r)->values()->all();

        $export = new class($rows) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
            private $rows;
            public function __construct($rows) { $this->rows = $rows; }
            public function collection() { return collect($this->rows); }
            public function headings(): array
            {
                return array_keys($this->rows[0] ?? []);
            }
        };

        return Excel::download($export, "{$type}-report.xlsx");
    }


   public function downloadInstructorPdf()
{
    $receipts = \App\Models\Receipt::with('scheduledClass.instructor')->get();

    $rows = $receipts->groupBy('scheduled_class_id')
        ->map(function ($group) {
            $first = $group->first();
            return [
                'Instructor Name'  => optional(optional($first->scheduledClass)->instructor)->name ?? 'Unknown',
                'Instructor Email' => optional(optional($first->scheduledClass)->instructor)->email ?? '-',
                'Scheduled Class'  => optional($first->scheduledClass)->title ?? 'Class #' . $first->scheduled_class_id,
                'Total (UGX)'      => $group->sum('amount'),
            ];
        })
        ->values()
        ->all();

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reports.instructors-pdf', [
        'rows' => $rows
    ]);

    return $pdf->download('instructors-report.pdf');
}


}
