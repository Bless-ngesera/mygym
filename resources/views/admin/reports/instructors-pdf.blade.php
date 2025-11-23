<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>MyGym Instructors Report</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        h2 {
            font-size: 18px;
            margin-bottom: 4px;
            color: #222;
        }
        p {
            font-size: 11px;
            color: #666;
            margin-top: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #f5f5f5;
            color: #444;
            font-weight: bold;
            padding: 8px;
            border: 1px solid #ccc;
            text-align: left;
        }
        td {
            padding: 8px;
            border: 1px solid #ccc;
            vertical-align: top;
        }
        tr:nth-child(even) td {
            background-color: #fafafa;
        }
        .amount {
            color: #0f5132;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h2>MyGym Instructors Report</h2>
    <p>Generated on {{ now()->format('Y-m-d H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>Instructor Name</th>
                <th>Instructor Email</th>
                <th>Scheduled Class</th>
                <th>Total (UGX)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $row)
                <tr>
                    <td>{{ $row['Instructor Name'] }}</td>
                    <td>{{ $row['Instructor Email'] }}</td>
                    <td>{{ $row['Scheduled Class'] }}</td>
                    <td class="amount">{{ number_format($row['Total (UGX)'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
