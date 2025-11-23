<?php

namespace App\Exports;

use App\Models\Receipt;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Http\Request;

class EarningsExport implements FromView
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function view(): View
    {
        $query = Receipt::with('user');

        if ($this->request->start_date && $this->request->end_date) {
            $query->whereBetween('created_at', [
                $this->request->start_date,
                $this->request->end_date
            ]);
        }

        return view('earnings.export-table', [
            'transactions' => $query->get()
        ]);

        
    }
}
