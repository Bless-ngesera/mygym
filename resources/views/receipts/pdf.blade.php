<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt - {{ $receipt->reference_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', 'Helvetica', 'Arial', sans-serif;
            background: #f4f4f6;
            padding: 16px;
            color: #1e293b;
            line-height: 1.3;
            font-size: 12px;
        }

        .receipt {
            max-width: 780px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }

        /* ── HEADER ── */
        .header {
            padding: 18px 24px 14px;
            border-bottom: 1px solid #eef2f8;
            background: linear-gradient(135deg, #f8fafc 0%, #ffffff 50%, #f8fafc 100%);
            position: relative;
            overflow: hidden;
        }

        .header-deco-tr {
            position: absolute;
            top: -30px;
            right: -30px;
            width: 100px;
            height: 100px;
            background: #ede9fe;
            border-radius: 50%;
            opacity: 0.35;
        }

        .header-deco-bl {
            position: absolute;
            bottom: -25px;
            left: -25px;
            width: 80px;
            height: 80px;
            background: #e0e7ff;
            border-radius: 50%;
            opacity: 0.35;
        }

        .header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo-area {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo {
            height: 42px;
            width: auto;
        }

        .title-block {
            border-left: 2px solid #e2e8f0;
            padding-left: 12px;
        }

        .title-block h1 {
            font-size: 20px;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: -0.3px;
        }

        .title-block h1 span {
            color: #7c3aed;
        }

        .title-block p {
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: #64748b;
            margin-top: 2px;
        }

        .header-right {
            text-align: right;
        }

        .ref-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #f1f5f9;
            border-radius: 30px;
            padding: 5px 14px;
        }

        .ref-badge svg {
            width: 12px;
            height: 12px;
            color: #64748b;
        }

        .ref-badge span {
            font-family: 'DejaVu Sans Mono', monospace;
            font-size: 10px;
            font-weight: 700;
            color: #1e293b;
            letter-spacing: 0.5px;
        }

        .header-date {
            font-size: 9.5px;
            color: #94a3b8;
            margin-top: 6px;
        }

        /* ── BODY ── */
        .body {
            padding: 18px 24px 14px;
        }

        /* ── TWO-COLUMN GRID ── */
        .two-col {
            width: 100%;
            margin-bottom: 16px;
        }

        .two-col table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 12px 0;
        }

        .two-col td {
            width: 50%;
            vertical-align: top;
        }

        /* ── CARDS ── */
        .card {
            background: #ffffff;
            border: 1px solid #eef2f8;
            border-radius: 14px;
            padding: 13px 15px;
            margin-bottom: 14px;
        }

        .card:last-child {
            margin-bottom: 0;
        }

        .card-header {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 10px;
        }

        .icon-box {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .icon-box svg {
            width: 14px;
            height: 14px;
        }

        .icon-purple { background: #f5f3ff; }
        .icon-purple svg { color: #7c3aed; }
        .icon-emerald { background: #ecfdf5; }
        .icon-emerald svg { color: #059669; }
        .icon-orange { background: #fff7ed; }
        .icon-orange svg { color: #ea580c; }
        .icon-blue { background: #eff6ff; }
        .icon-blue svg { color: #2563eb; }

        .card-label {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #64748b;
        }

        /* Member Card */
        .member-name {
            font-size: 15px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 2px;
        }

        .member-email {
            font-size: 11px;
            color: #64748b;
        }

        .member-id {
            font-size: 10px;
            color: #94a3b8;
            margin-top: 6px;
        }

        /* Payment Card */
        .payment-method {
            font-size: 12px;
            font-weight: 600;
            color: #334155;
        }

        .payment-date {
            font-size: 10px;
            color: #94a3b8;
            margin-bottom: 8px;
        }

        .amount {
            font-size: 22px;
            font-weight: 900;
            color: #0f172a;
            margin: 4px 0;
        }

        .paid-chip {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: #dcfce7;
            border-radius: 20px;
            padding: 3px 10px;
        }

        .paid-chip svg {
            width: 10px;
            height: 10px;
            fill: #16a34a;
        }

        .paid-chip span {
            font-size: 10px;
            font-weight: 700;
            color: #15803d;
        }

        /* Class Card */
        .class-name {
            font-size: 15px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 3px;
        }

        .class-meta {
            font-size: 10px;
            color: #64748b;
        }

        .class-desc {
            font-size: 10px;
            color: #64748b;
            margin-top: 6px;
            line-height: 1.4;
        }

        /* Schedule Card */
        .schedule-date {
            font-size: 12px;
            font-weight: 600;
            color: #334155;
        }

        .schedule-time {
            font-size: 20px;
            font-weight: 900;
            color: #7c3aed;
            margin: 3px 0;
        }

        .instructor-row {
            display: flex;
            align-items: center;
            gap: 5px;
            padding-top: 8px;
            margin-top: 8px;
            border-top: 1px solid #f0f2f5;
        }

        .instructor-row svg {
            width: 12px;
            height: 12px;
            color: #94a3b8;
            flex-shrink: 0;
        }

        .instructor-row span {
            font-size: 11px;
            color: #475569;
        }

        /* ── QR SECTION ── */
        .qr-section {
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            border-radius: 14px;
            padding: 13px 18px;
            margin-bottom: 14px;
        }

        .qr-inner {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .qr-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .qr-box {
            width: 64px;
            height: 64px;
            background: #ffffff;
            border-radius: 10px;
            padding: 5px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.06);
            flex-shrink: 0;
        }

        .qr-box img {
            width: 100%;
            height: 100%;
            display: block;
        }

        .qr-verify-label {
            font-size: 8.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #7c3aed;
        }

        .qr-verify-title {
            font-size: 12px;
            font-weight: 600;
            color: #0f172a;
            margin: 2px 0 1px;
        }

        .qr-verify-hint {
            font-size: 9.5px;
            color: #94a3b8;
        }

        .qr-right {
            text-align: right;
        }

        .ref-label {
            font-size: 9px;
            color: #64748b;
        }

        .ref-code {
            font-family: 'DejaVu Sans Mono', monospace;
            font-size: 12px;
            font-weight: 700;
            color: #1e293b;
            margin: 3px 0 5px;
        }

        .verified-chip {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: #dcfce7;
            border-radius: 20px;
            padding: 3px 10px;
        }

        .verified-chip svg {
            width: 10px;
            height: 10px;
            fill: #16a34a;
        }

        .verified-chip span {
            font-size: 10px;
            font-weight: 700;
            color: #15803d;
        }

        /* ── FOOTER ── */
        .footer {
            border-top: 1px solid #eef2f8;
            padding: 10px 24px;
            text-align: center;
            background: #fafbfc;
        }

        .footer p {
            font-size: 9px;
            color: #94a3b8;
            margin: 2px 0;
        }

        @page {
            size: A4;
            margin: 1cm 1cm;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }
            .receipt {
                border: none;
                border-radius: 0;
            }
        }
    </style>
</head>
<body>
    <div class="receipt">

        {{-- ════ HEADER ════ --}}
        <div class="header">
            <div class="header-deco-tr"></div>
            <div class="header-deco-bl"></div>
            <div class="header-row">
                <div class="logo-area">
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/Project_Logo.png'))) }}"
                         alt="MyGym Logo"
                         class="logo">
                    <div class="title-block">
                        <h1>Official <span>Receipt</span></h1>
                        <p>Payment Confirmation</p>
                    </div>
                </div>
                <div class="header-right">
                    <div class="ref-badge">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span>{{ $receipt->reference_number }}</span>
                    </div>
                    <div class="header-date">{{ $receipt->created_at->format('jS M Y, g:i a') }}</div>
                </div>
            </div>
        </div>

        {{-- ════ BODY ════ --}}
        <div class="body">

            {{-- Row 1: Member + Payment --}}
            <div class="two-col">
                <table>
                    <tr>
                        {{-- Member Info --}}
                        <td>
                            <div class="card">
                                <div class="card-header">
                                    <div class="icon-box icon-purple">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                    <span class="card-label">Member Information</span>
                                </div>
                                <div class="member-name">{{ $receipt->user->name }}</div>
                                <div class="member-email">{{ $receipt->user->email ?? '' }}</div>
                                <div class="member-id">Member ID: #{{ $receipt->user->id }}</div>
                            </div>
                        </td>

                        {{-- Payment Details --}}
                        <td>
                            <div class="card">
                                <div class="card-header">
                                    <div class="icon-box icon-emerald">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                        </svg>
                                    </div>
                                    <span class="card-label">Payment Details</span>
                                </div>
                                <div class="payment-method">{{ $receipt->payment_method }}</div>
                                <div class="payment-date">{{ $receipt->paid_at ? $receipt->paid_at->format('M d, Y h:i A') : 'N/A' }}</div>
                                <div class="amount">UGX {{ number_format($receipt->amount, 0) }}</div>
                                <div class="paid-chip">
                                    <svg viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>Paid</span>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>

            {{-- Row 2: Class + Schedule --}}
            <div class="two-col">
                <table>
                    <tr>
                        {{-- Class Details --}}
                        <td>
                            <div class="card">
                                <div class="card-header">
                                    <div class="icon-box icon-orange">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                        </svg>
                                    </div>
                                    <span class="card-label">Class Details</span>
                                </div>
                                <div class="class-name">{{ $receipt->scheduledClass->classType->name ?? 'N/A' }}</div>
                                <div class="class-meta">
                                    {{ $receipt->scheduledClass->classType->minutes ?? 0 }} minutes &nbsp;•&nbsp; Level: All Levels
                                </div>
                                @if($receipt->scheduledClass->classType->description ?? false)
                                    <div class="class-desc">{{ Str::limit($receipt->scheduledClass->classType->description, 100) }}</div>
                                @endif
                            </div>
                        </td>

                        {{-- Schedule --}}
                        <td>
                            <div class="card">
                                <div class="card-header">
                                    <div class="icon-box icon-blue">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <span class="card-label">Schedule</span>
                                </div>
                                <div class="schedule-date">
                                    {{ $receipt->scheduledClass->date_time
                                        ? $receipt->scheduledClass->date_time->format('l, jS M Y')
                                        : 'N/A' }}
                                </div>
                                <div class="schedule-time">
                                    {{ $receipt->scheduledClass->date_time
                                        ? $receipt->scheduledClass->date_time->format('g:i A')
                                        : 'N/A' }}
                                </div>
                                <div class="instructor-row">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    <span>{{ $receipt->scheduledClass->instructor->name ?? 'TBA' }}</span>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>

            {{-- QR & Verification --}}
            <div class="qr-section">
                <div class="qr-inner">
                    <div class="qr-left">
                        @php
                            $receiptUrl = route('receipts.show', $receipt->id);
                            $qrCodeUrl  = 'https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=' . urlencode($receiptUrl);
                        @endphp
                        <div class="qr-box">
                            <img src="{{ $qrCodeUrl }}" alt="QR Code">
                        </div>
                        <div>
                            <div class="qr-verify-label">Verify Receipt</div>
                            <div class="qr-verify-title">Scan QR code to verify</div>
                            <div class="qr-verify-hint">or use reference number</div>
                        </div>
                    </div>
                    <div class="qr-right">
                        <div class="ref-label">Reference Number</div>
                        <div class="ref-code">{{ $receipt->reference_number }}</div>
                        <div class="verified-chip">
                            <svg viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                      d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                      clip-rule="evenodd"/>
                            </svg>
                            <span>Verified</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>{{-- end .body --}}

        {{-- ════ FOOTER ════ --}}
        <div class="footer">
            <p>This is a computer-generated receipt and requires no signature.</p>
            <p>© {{ date('Y') }} MyGym &nbsp;|&nbsp; support@mygym.com &nbsp;|&nbsp; All rights reserved.</p>
        </div>

    </div>
    
</body>
</html>
