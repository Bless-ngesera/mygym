<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Receipt - MyGym</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .content {
            padding: 30px;
        }
        .receipt-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: center;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>💰 Payment Receipt</h1>
            <p>Your payment has been confirmed</p>
        </div>

        <div class="content">
            <h2>Hello {{ $receipt->user->name }}!</h2>
            <p>Thank you for your payment. Your transaction has been completed successfully.</p>

            <div class="receipt-box">
                <p><strong>Receipt Number:</strong></p>
                <h3>{{ $receipt->reference_number }}</h3>
                <p><strong>Amount Paid:</strong> UGX {{ number_format($receipt->amount, 0) }}</p>
                <p><strong>Payment Method:</strong> {{ $receipt->payment_method }}</p>
                <p><strong>Date:</strong> {{ $receipt->created_at->format('F j, Y g:i A') }}</p>
            </div>

            <div style="text-align: center;">
                <a href="{{ route('receipts.show', $receipt) }}" style="display: inline-block; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; text-decoration: none; padding: 12px 30px; border-radius: 6px;">View Receipt</a>
            </div>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} MyGym. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
