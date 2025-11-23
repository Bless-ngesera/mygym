<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">Receipt</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto bg-white p-8 rounded-lg shadow-md">
            @if(session('success'))
                <div class="mb-4 px-4 py-3 bg-green-100 text-green-800 rounded font-semibold">
                    {{ session('success') }}
                </div>
            @endif

            <div class="flex items-start justify-between gap-6">
                <div>
                    <h1 class="text-2xl font-extrabold text-gray-900">My<span class="text-yellow-400">Gym</span></h1>
                    <p class="text-sm text-gray-500">Receipt</p>
                    <p class="mt-4 text-sm text-gray-600">Reference: <span class="font-medium">{{ $receipt->reference_number }}</span></p>
                    <p class="text-sm text-gray-600">Date: <span class="font-medium">{{ $receipt->created_at->format('jS M Y, g:i a') }}</span></p>
                </div>

                <div class="w-40 h-40 bg-gray-50 border rounded-md flex items-center justify-center p-2">
                    {{-- QR points to the public receipt URL --}}
                    @php
                        $receiptUrl = route('receipts.show', $receipt->id);
                        $qrPrimary = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . rawurlencode($receiptUrl);
                        $qrFallback = 'https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=' . rawurlencode($receiptUrl) . '&chld=L|1';
                    @endphp
                    <img
                        src="{{ $qrPrimary }}"
                        alt="Receipt QR"
                        class="w-full h-full object-contain bg-white p-1"
                        onerror="this.onerror=null; this.src='{{ $qrFallback }}';"
                    >
                </div>
            </div>

            <hr class="my-6">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-gray-700">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Member</h3>
                    <p class="text-sm">{{ $receipt->user->name }}</p>
                    <p class="text-sm text-gray-500">{{ $receipt->user->email ?? '' }}</p>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Payment</h3>
                    <p class="text-sm"><span class="font-medium">Method:</span> {{ $receipt->payment_method }}</p>
                    <p class="text-sm"><span class="font-medium">Amount:</span> UGX {{ number_format($receipt->amount, 2) }}</p>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Class</h3>
                    <p class="text-sm font-medium">{{ optional(optional($receipt->scheduledClass)->classType)->name ?? 'N/A' }}</p>
                    <p class="text-sm text-gray-500">{{ optional(optional($receipt->scheduledClass)->classType)->description ?? '' }}</p>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Schedule</h3>
                    <p class="text-sm font-medium">
                        {{-- formatted class time --}}
                        {{ optional($receipt->scheduledClass)->date_time ? $receipt->scheduledClass->date_time->format('jS M Y, g:i a') : 'N/A' }}
                    </p>
                    <p class="text-sm text-gray-500">
                        Instructor: {{ optional(optional($receipt->scheduledClass)->instructor)->name ?? 'TBA' }}
                    </p>
                </div>
            </div>

            <div class="mt-8 flex flex-wrap items-center gap-3 print:hidden">
                <button onclick="window.print()" class="px-4 py-2 bg-indigo-600 text-white rounded-md shadow">Print / Save as PDF</button>

                <button id="downloadReceipt" class="px-4 py-2 bg-gray-800 text-white rounded-md shadow">Download (HTML)</button>

                @if (Route::has('member.upcoming'))
                    <a href="{{ route('member.bookings') }}" class="px-4 py-2 bg-green-600 text-white rounded">View Upcoming Classes</a>
                @else
                    <a href="{{ url('/member/bookings') }}" class="px-4 py-2 bg-green-600 text-white rounded">View Upcoming Classes</a>
                @endif
            </div>

            <p class="mt-6 text-xs text-gray-400">This receipt is auto-generated. Keep it for your records.</p>
        </div>
    </div>

    <script>
        (function(){
            const btn = document.getElementById('downloadReceipt');
            btn?.addEventListener('click', () => {
                const receiptHtml = `
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Receipt - {{ $receipt->reference_number }}</title>
<style>
body{font-family: Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial; color:#111; padding:24px;}
.container{max-width:700px;margin:0 auto;border:1px solid #e5e7eb;padding:24px;border-radius:8px;}
.header{display:flex;justify-content:space-between;align-items:center;}
h1{margin:0;font-size:20px;}
.meta{color:#6b7280;font-size:13px;margin-top:6px;}
.grid{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:18px;}
.label{font-weight:600;color:#374151;}
.value{color:#111;}
.qr{margin-top:18px;}
.footer{margin-top:18px;color:#6b7280;font-size:13px;}
</style>
</head>
<body>
<div class="container">
  <div class="header">
    <div>
      <h1>MyGym - Receipt</h1>
      <div class="meta">Reference: {{ $receipt->reference_number }} • {{ $receipt->created_at->format('jS M Y, g:i a') }}</div>
    </div>
    <div><img src="{{ $qrPrimary }}" alt="QR" style="width:120px;height:120px;border:1px solid #e5e7eb;padding:6px;background:#fff"></div>
  </div>

  <div class="grid">
    <div>
      <div class="label">Member</div>
      <div class="value">{{ $receipt->user->name }} {{ $receipt->user->email ? '• ' . $receipt->user->email : '' }}</div>
    </div>

    <div>
      <div class="label">Payment</div>
      <div class="value">{{ $receipt->payment_method }} • UGX {{ number_format($receipt->amount, 2) }}</div>
    </div>

    <div>
      <div class="label">Class</div>
      <div class="value">{{ optional(optional($receipt->scheduledClass)->classType)->name ?? 'N/A' }}</div>
    </div>

    <div>
      <div class="label">Schedule</div>
      <div class="value">
        {{ optional($receipt->scheduledClass)->date_time ? $receipt->scheduledClass->date_time->format('jS M Y, g:i a') : 'N/A' }}
      </div>
    </div>
  </div>

  <div class="footer">This receipt was generated by MyGym. Please keep it for your records.</div>
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
        })();
    </script>

    <style>
        @media print {
            .print\\:hidden { display: none !important; }
        }
    </style>
</x-app-layout>
