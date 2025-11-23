<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>MyGym {{ ucfirst(str_replace('_',' ', $type)) }} Report</title>
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
        <h1>MyGym {{ ucfirst(str_replace('_',' ', $type)) }} Report</h1>
        <p>Generated on {{ now()->format('Y-m-d H:i') }}</p>
    </header>

    <table>
    <thead>
        <tr>
            @foreach(array_keys($rows[0] ?? []) as $heading)
                <th>{{ $heading }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $row)
            <tr>
                @foreach($row as $value)
                    <td>{{ $value }}</td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>


    <footer>
        Page <span class="page-number"></span>
    </footer>
</body>
</html>
