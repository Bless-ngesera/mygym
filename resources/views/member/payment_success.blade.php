<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">Receipt</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-6 rounded-xl bg-green-50 border border-green-200 px-5 py-4 text-green-700 font-medium">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Premium Receipt Card -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <!-- Header with gradient accent -->
                <div class="relative bg-gradient-to-r from-gray-50 via-white to-gray-50 px-8 pt-8 pb-6 border-b border-gray-100">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
                        <div class="flex items-center gap-3">
                            <!-- Website Logo - using your actual logo -->
                            <div class="w-12 h-12 bg-gradient-to-br from-yellow-400 to-yellow-500 rounded-xl flex items-center justify-center shadow-sm">
                                <svg class="w-7 h-7 text-gray-900" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-2xl font-black text-gray-900">My<span class="text-yellow-500">Gym</span></h1>
                                <p class="text-xs text-gray-500 uppercase tracking-wide">Official Receipt</p>
                            </div>
                        </div>

                        <div class="text-right">
                            <div class="inline-flex items-center gap-2 px-3 py-1 bg-gray-50 rounded-full">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <span class="text-xs font-mono text-gray-600">{{ $receipt->reference_number }}</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">{{ $receipt->created_at->format('jS M Y, g:i a') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div class="px-8 py-8">
                    <!-- Member & Payment Row -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8">
                        <div class="bg-gray-50 rounded-xl p-5">
                            <div class="flex items-center gap-2 mb-3">
                                <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Member</h3>
                            </div>
                            <p class="text-lg font-bold text-gray-900">{{ $receipt->user->name }}</p>
                            <p class="text-sm text-gray-500">{{ $receipt->user->email ?? '' }}</p>
                        </div>

                        <div class="bg-gray-50 rounded-xl p-5">
                            <div class="flex items-center gap-2 mb-3">
                                <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Payment</h3>
                            </div>
                            <div class="flex justify-between items-baseline">
                                <p class="text-sm text-gray-600">{{ $receipt->payment_method }}</p>
                                <p class="text-2xl font-black text-gray-900">UGX {{ number_format($receipt->amount, 2) }}</p>
                            </div>
                            <p class="text-xs text-green-600 mt-2 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Paid in full
                            </p>
                        </div>
                    </div>

                    <!-- Class & Schedule Row -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8">
                        <div class="border border-gray-100 rounded-xl p-5 hover:shadow-sm transition-shadow">
                            <div class="flex items-center gap-2 mb-3">
                                <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Class</h3>
                            </div>
                            <p class="text-base font-bold text-gray-900">{{ optional(optional($receipt->scheduledClass)->classType)->name ?? 'N/A' }}</p>
                            @if(optional(optional($receipt->scheduledClass)->classType)->description)
                                <p class="text-sm text-gray-500 mt-1">{{ optional(optional($receipt->scheduledClass)->classType)->description }}</p>
                            @endif
                        </div>

                        <div class="border border-gray-100 rounded-xl p-5 hover:shadow-sm transition-shadow">
                            <div class="flex items-center gap-2 mb-3">
                                <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Schedule</h3>
                            </div>
                            <p class="text-base font-bold text-gray-900">
                                {{ optional($receipt->scheduledClass)->date_time ? $receipt->scheduledClass->date_time->format('jS M Y, g:i a') : 'N/A' }}
                            </p>
                            <p class="text-sm text-gray-500 mt-1">
                                Instructor: {{ optional(optional($receipt->scheduledClass)->instructor)->name ?? 'TBA' }}
                            </p>
                        </div>
                    </div>

                    <!-- QR Code & Verification -->
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-6 p-5 bg-gray-50 rounded-xl">
                        <div class="flex items-center gap-3">
                            <div class="w-20 h-20 bg-white rounded-lg shadow-sm flex items-center justify-center">
                                @php
                                    $receiptUrl = route('receipts.show', $receipt->id);
                                    $qrPrimary = 'https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=' . rawurlencode($receiptUrl);
                                    $qrFallback = 'https://chart.googleapis.com/chart?chs=120x120&cht=qr&chl=' . rawurlencode($receiptUrl) . '&chld=L|1';
                                @endphp
                                <img
                                    src="{{ $qrPrimary }}"
                                    alt="Receipt QR Code"
                                    class="w-16 h-16 object-contain"
                                    onerror="this.onerror=null; this.src='{{ $qrFallback }}';"
                                >
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Verify with QR</p>
                                <p class="text-xs text-gray-500 mt-1">Scan to view receipt details</p>
                            </div>
                        </div>

                        <div class="text-right">
                            <div class="inline-flex items-center gap-1 text-xs text-gray-500">
                                <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>Verified by MyGym</span>
                            </div>
                            <p class="text-xs text-gray-400 mt-2">Receipt ID: {{ substr($receipt->reference_number, 0, 12) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="bg-gray-50 px-8 py-5 border-t border-gray-100">
                    <div class="flex flex-col sm:flex-row justify-between items-center gap-3 text-xs text-gray-500">
                        <p class="flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            This receipt was generated by MyGym
                        </p>
                        <p>Keep this for your records & class check-in</p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-8 flex flex-wrap items-center justify-center gap-4 print:hidden">
                <button onclick="window.print()" class="inline-flex items-center gap-2 px-6 py-2.5 bg-gray-900 hover:bg-gray-800 text-white rounded-xl font-medium transition-all shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Print / Save as PDF
                </button>

                <button id="downloadReceipt" class="inline-flex items-center gap-2 px-6 py-2.5 border border-gray-300 hover:border-yellow-400 text-gray-700 hover:text-yellow-600 rounded-xl font-medium transition-all bg-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Download Receipt
                </button>

                <a href="{{ route('member.bookings.index') }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-yellow-400 hover:bg-yellow-500 text-gray-900 rounded-xl font-medium transition-all shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    View My Bookings
                </a>
            </div>
        </div>
    </div>

    <script>
        (function(){
            const btn = document.getElementById('downloadReceipt');
            if (btn) {
                btn.addEventListener('click', () => {
                    const receiptHtml = `<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyGym Receipt - {{ $receipt->reference_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #f3f4f6;
            padding: 2rem;
            line-height: 1.5;
        }

        .receipt-container {
            max-width: 700px;
            margin: 0 auto;
            background: white;
            border-radius: 1rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
            overflow: hidden;
        }

        .receipt-header {
            background: linear-gradient(135deg, #f9fafb 0%, #ffffff 100%);
            padding: 1.5rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .logo-section {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .logo-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #eab308 0%, #f59e0b 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo-icon svg {
            width: 28px;
            height: 28px;
            color: #1f2937;
        }

        .logo-text h1 {
            font-size: 1.5rem;
            font-weight: 900;
            color: #111827;
        }

        .logo-text span {
            color: #eab308;
        }

        .logo-text p {
            font-size: 0.7rem;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .reference {
            text-align: right;
        }

        .reference-code {
            background: #f3f4f6;
            padding: 0.25rem 1rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-family: monospace;
            color: #374151;
        }

        .reference-date {
            font-size: 0.7rem;
            color: #9ca3af;
            margin-top: 0.25rem;
        }

        .receipt-body {
            padding: 1.5rem;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.25rem;
            margin-bottom: 1.5rem;
        }

        .info-card {
            background: #f9fafb;
            border-radius: 0.75rem;
            padding: 1rem;
        }

        .info-title {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.75rem;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #f59e0b;
        }

        .info-title svg {
            width: 1rem;
            height: 1rem;
        }

        .info-value {
            font-size: 1rem;
            font-weight: 700;
            color: #111827;
        }

        .info-sub {
            font-size: 0.75rem;
            color: #6b7280;
            margin-top: 0.25rem;
        }

        .amount-large {
            font-size: 1.5rem;
            font-weight: 800;
            color: #111827;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            font-size: 0.7rem;
            color: #059669;
            margin-top: 0.5rem;
        }

        .qr-section {
            background: #f9fafb;
            border-radius: 0.75rem;
            padding: 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .qr-box {
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem;
        }

        .qr-box img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .receipt-footer {
            background: #f9fafb;
            padding: 1rem 1.5rem;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 0.7rem;
            color: #9ca3af;
        }

        @media (max-width: 640px) {
            body {
                padding: 1rem;
            }
            .grid-2 {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            .logo-section {
                flex-direction: column;
                align-items: flex-start;
            }
            .reference {
                text-align: left;
            }
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }
            .receipt-container {
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="receipt-header">
            <div class="logo-section">
                <div class="logo">
                    <div class="logo-icon">
                        <svg fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                        </svg>
                    </div>
                    <div class="logo-text">
                        <h1>My<span>Gym</span></h1>
                        <p>Official Receipt</p>
                    </div>
                </div>
                <div class="reference">
                    <div class="reference-code">{{ $receipt->reference_number }}</div>
                    <div class="reference-date">{{ $receipt->created_at->format('jS M Y, g:i a') }}</div>
                </div>
            </div>
        </div>

        <div class="receipt-body">
            <div class="grid-2">
                <div class="info-card">
                    <div class="info-title">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Member
                    </div>
                    <div class="info-value">{{ $receipt->user->name }}</div>
                    <div class="info-sub">{{ $receipt->user->email ?? '' }}</div>
                </div>

                <div class="info-card">
                    <div class="info-title">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                        Payment
                    </div>
                    <div class="info-value">{{ $receipt->payment_method }}</div>
                    <div class="amount-large">UGX {{ number_format($receipt->amount, 2) }}</div>
                    <div class="status-badge">
                        <svg width="12" height="12" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                        </svg>
                        Paid in full
                    </div>
                </div>

                <div class="info-card">
                    <div class="info-title">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Class
                    </div>
                    <div class="info-value">{{ optional(optional($receipt->scheduledClass)->classType)->name ?? 'N/A' }}</div>
                    @if(optional(optional($receipt->scheduledClass)->classType)->description)
                        <div class="info-sub">{{ optional(optional($receipt->scheduledClass)->classType)->description }}</div>
                    @endif
                </div>

                <div class="info-card">
                    <div class="info-title">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Schedule
                    </div>
                    <div class="info-value">{{ optional($receipt->scheduledClass)->date_time ? $receipt->scheduledClass->date_time->format('jS M Y, g:i a') : 'N/A' }}</div>
                    <div class="info-sub">Instructor: {{ optional(optional($receipt->scheduledClass)->instructor)->name ?? 'TBA' }}</div>
                </div>
            </div>

            <div class="qr-section">
                <div class="qr-box">
                    <img src="{{ $qrPrimary }}" alt="QR Code" onerror="this.onerror=null; this.src='{{ $qrFallback }}';">
                </div>
                <div>
                    <div style="font-size: 0.7rem; font-weight: 600; color: #f59e0b;">VERIFY WITH QR</div>
                    <div style="font-size: 0.7rem; color: #6b7280;">Scan to view receipt details</div>
                </div>
                <div style="font-size: 0.7rem; color: #9ca3af;">Receipt ID: {{ substr($receipt->reference_number, 0, 12) }}</div>
            </div>
        </div>

        <div class="receipt-footer">
            This receipt was generated by MyGym. Please keep it for your records.
        </div>
    </div>
</body>
</html>`;

                    const blob = new Blob([receiptHtml], { type: 'text/html' });
                    const url = URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `receipt-{{ $receipt->reference_number }}.html`;
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                    URL.revokeObjectURL(url);
                });
            }
        })();
    </script>

    <style>
        @media print {
            .print\:hidden {
                display: none !important;
            }
            body {
                background: white;
            }
        }
    </style>
</x-app-layout>
