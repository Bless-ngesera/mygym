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
            margin: 20px;
        }
        header {
            text-align: center;
            margin-bottom: 20px;
        }
        header h1 {
            font-size: 20px;
            margin: 0;
            color: #2c3e50;
        }
        header p {
            font-size: 12px;
            color: #777;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px 10px;
            text-align: left;
        }
        th {
            background-color: #6366f1;
            color: #fff;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 11px;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #eef2ff;
        }
        td {
            font-size: 11px;
        }
        .highlight {
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
        }
    </style>
</head>
<body>
    <header>
        <h2>MyGym Instructors Report</h2>
        <p>Generated on {{ now()->format('Y-m-d H:i') }}</p>
    </header>


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
