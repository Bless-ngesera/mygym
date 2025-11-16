<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
    }

    public function export(Request $request)
    {
        $type = $request->get('type', 'pdf');
        $report = $request->get('report', 'earnings');
        $content = "Report: $report\nType: $type\nGenerated: ".now();
        $filename = "report_{$report}_" . now()->format('Ymd_His') . ($type === 'excel' ? '.xlsx' : '.pdf');

        return response($content, 200)
            ->header('Content-Type', $type === 'excel' ? 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' : 'application/pdf')
            ->header('Content-Disposition', "attachment; filename=\"$filename\"");
    }
}
