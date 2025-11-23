<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Generic Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        th { background: #f5f5f5; }
    </style>
</head>
<body>
    <h2>Generic Receipts Report</h2>
    <p>Generated on {{ now()->format('Y-m-d H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>Reference</th>
                <th>Member</th>
                <th>Instructor</th>
                <th>Amount (UGX)</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($receipts as $r)
                <tr>
                    <td>{{ $r->reference_number ?? '-' }}</td>
                    <td>{{ optional($r->user)->name ?? 'Unknown Member' }}</td>
                    <td>{{ optional(optional($r->scheduledClass)->instructor)->name ?? 'Unknown Instructor' }}</td>
                    <td>{{ number_format($r->amount ?? 0, 2) }}</td>
                    <td>{{ optional($r->created_at)->format('Y-m-d') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

