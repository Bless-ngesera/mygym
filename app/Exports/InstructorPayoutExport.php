<?php

namespace App\Exports;

use App\Models\Payment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class InstructorPayoutExport implements FromCollection, WithHeadings
{
    /**
     * Return a collection of rows to be exported.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Adjust the query to match the instructor payout logic you need.
        // This example exports id, instructor (user) id, amount, status and created_at.
        return Payment::select('id', 'user_id', 'amount', 'status', 'created_at')->get();
    }

    /**
     * Column headings for the exported file.
     *
     * @return array
     */
    public function headings(): array
    {
        return ['ID', 'Instructor ID', 'Amount', 'Status', 'Created At'];
    }
}
