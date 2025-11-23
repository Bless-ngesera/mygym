<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>MyGym Earnings Report</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 30px;
        }
        header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #4F46E5;
            padding-bottom: 10px;
        }
        header h2 {
            font-size: 22px;
            margin: 0;
            color: #2c3e50;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        header p {
            font-size: 12px;
            color: #555;
            margin-top: 5px;
        }
        .summary {
            margin: 20px 0;
            padding: 10px;
            background-color: #F9FAFB;
            border: 1px solid #E5E7EB;
            border-radius: 6px;
        }
        .summary div {
            margin-bottom: 6px;
            font-size: 12px;
        }
        .summary strong {
            color: #111827;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #E5E7EB;
            padding: 8px 10px;
            text-align: left;
        }
        th {
            background-color: #4F46E5;
            color: #fff;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.5px;
        }
        tr:nth-child(even) {
            background-color: #F9FAFB;
        }
        tr:hover {
            background-color: #EEF2FF;
        }
        td {
            font-size: 11px;
        }
        .amount {
            font-weight: bold;
            color: #2c3e50;
        }
        footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            color: #777;
            border-top: 1px solid #ddd;
            padding-top: 5px;
        }
        .watermark {
            position: fixed;
            bottom: 40px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9px;
            color: #aaa;
            opacity: 0.6;
        }
    </style>
</head>
<body>
    <header>
        <h2>MyGym Global Earnings Report</h2>
        <p>Generated on {{ now()->format('M d, Y H:i') }}</p>
    </header>

    <div class="summary">
        <div><strong>Total Earnings:</strong> UGX {{ number_format($receipts->sum('amount'), 2) }}</div>
        <div><strong>Number of Transactions:</strong> {{ $receipts->count() }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Class</th>
                <th>Payment Method</th>
                <th>Amount (UGX)</th>
                <th>Reference</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($receipts as $receipt)
                <tr>
                    <td>{{ $receipt->id }}</td>
                    <td>{{ $receipt->user->name ?? 'Unknown User' }}</td>
                    <td>{{ $receipt->scheduledClass->classType->name ?? 'Unknown Class' }}</td>
                    <td>{{ $receipt->payment_method }}</td>
                    <td class="amount">UGX {{ number_format($receipt->amount, 2) }}</td>
                    <td>{{ $receipt->reference_number ?? '-' }}</td>
                    <td>{{ $receipt->created_at->format('M d, Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align:center; color:#999;">No receipts found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <footer>
        Page <span class="page-number"></span>
    </footer>
    <div class="watermark">
        Confidential • Internal Use Only
    </div>
</body>
</html>
