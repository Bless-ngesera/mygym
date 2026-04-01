<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('My Earnings') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Track your revenue and payouts</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('instructor.classes') }}"
                   class="px-4 py-2 bg-purple-100 text-purple-700 rounded-xl text-sm font-semibold hover:bg-purple-200 transition-all duration-200">
                    My Classes
                </a>
                <a href="{{ route('instructor.upcoming') }}"
                   class="px-4 py-2 bg-indigo-100 text-indigo-700 rounded-xl text-sm font-semibold hover:bg-indigo-200 transition-all duration-200">
                    Upcoming Classes
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12 min-h-screen"
        style="background-image: url('{{ asset('images/background2.jpg') }}');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Success Message --}}
            @if(session('success'))
                <div id="successMessage" class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-xl shadow-md flex items-center justify-between animate-fade-in">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button onclick="this.parentElement.style.display='none'" class="text-green-700 hover:text-green-900">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            @endif

            {{-- Error Messages --}}
            @if($errors->any())
                <div id="errorMessage" class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-xl shadow-md">
                    <div class="flex items-center mb-2">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="font-semibold">Please fix the following errors:</span>
                    </div>
                    <ul class="list-disc pl-8 space-y-1">
                        @foreach($errors->all() as $error)
                            <li class="text-sm">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Stats Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-200 overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">All time</span>
                        </div>
                        <div class="text-2xl font-bold text-gray-900 tracking-tight">UGX {{ number_format($totalEarnings ?? 0, 0) }}</div>
                        <div class="text-xs font-medium text-gray-500 mt-0.5">Total Earnings</div>
                    </div>
                </div>

                <div class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-200 overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">{{ now()->format('M Y') }}</span>
                        </div>
                        <div class="text-2xl font-bold text-gray-900 tracking-tight">UGX {{ number_format($monthEarnings ?? 0, 0) }}</div>
                        <div class="text-xs font-medium text-gray-500 mt-0.5">This Month</div>
                    </div>
                </div>

                <div class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-200 overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <span class="text-xs font-semibold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full">Average</span>
                        </div>
                        <div class="text-2xl font-bold text-gray-900 tracking-tight">UGX {{ number_format($avgEarnings ?? 0, 0) }}</div>
                        <div class="text-xs font-medium text-gray-500 mt-0.5">Average per Class</div>
                    </div>
                </div>
            </div>

            {{-- Chart Card --}}
            <div class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl shadow-lg p-6 mb-6">
                <div class="flex justify-between items-center mb-5">
                    <div>
                        <h3 class="font-bold text-gray-900 text-sm">Monthly Earnings</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Revenue overview by month</p>
                    </div>
                    <div class="flex gap-2">
                        <button id="exportPdfBtn" class="text-xs px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors font-medium">Export PDF</button>
                        <button id="exportCsvBtn" class="text-xs px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors font-medium">Export CSV</button>
                    </div>
                </div>
                <canvas id="earningsChart" height="200"></canvas>
            </div>

            {{-- Earnings Table --}}
            <div class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <div>
                        <h3 class="font-bold text-gray-900 text-sm">Earnings History</h3>
                        <p class="text-xs text-gray-400 mt-0.5">All your transactions</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-gray-50/80">
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Class</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Member</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Reference</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($earnings ?? [] as $receipt)
                            <tr class="hover:bg-purple-50/30 transition-colors duration-150">
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $receipt->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-lg flex items-center justify-center">
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-sm font-semibold text-gray-800">{{ $receipt->scheduledClass->classType->name ?? 'N/A' }}</div>
                                            <div class="text-xs text-gray-500">{{ $receipt->scheduledClass->date_time->format('M d, Y h:i A') ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($receipt->user->name ?? 'Member') }}&background=10b981&color=fff&bold=true&size=32"
                                             alt="" class="w-6 h-6 rounded-lg">
                                        <span class="text-sm text-gray-600">{{ $receipt->user->name ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm font-semibold text-emerald-600">UGX {{ number_format($receipt->amount ?? 0, 0) }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-xs font-mono text-gray-500">{{ $receipt->reference_number ?? 'N/A' }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <p class="text-gray-500 font-medium">No earnings yet</p>
                                    <p class="text-sm text-gray-400 mt-1">Your earnings will appear here once members book your classes</p>
                                    <a href="{{ route('schedule.create') }}" class="inline-flex items-center gap-2 mt-4 text-purple-600 hover:text-purple-800 text-sm font-semibold">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Schedule a class to start earning
                                    </a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if(isset($earnings) && $earnings->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100">
                        {{ $earnings->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-fade-in {
            animation: fadeIn 0.3s ease-out;
        }
    </style>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-dismiss messages
            setTimeout(function() {
                let successMessage = document.getElementById('successMessage');
                let errorMessage = document.getElementById('errorMessage');
                if (successMessage) {
                    successMessage.style.transition = 'opacity 0.5s ease';
                    successMessage.style.opacity = '0';
                    setTimeout(function() {
                        successMessage.style.display = 'none';
                    }, 500);
                }
                if (errorMessage) {
                    errorMessage.style.transition = 'opacity 0.5s ease';
                    errorMessage.style.opacity = '0';
                    setTimeout(function() {
                        errorMessage.style.display = 'none';
                    }, 500);
                }
            }, 5000);

            // Chart
            const ctx = document.getElementById('earningsChart')?.getContext('2d');
            if (ctx) {
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: @json($monthlyLabels ?? []),
                        datasets: [{
                            label: 'Earnings (UGX)',
                            data: @json($monthlyEarnings ?? []),
                            borderColor: '#7e22ce',
                            backgroundColor: 'rgba(126, 34, 206, 0.1)',
                            borderWidth: 2.5,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#7e22ce',
                            pointRadius: 4,
                            pointHoverRadius: 6,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: 'rgba(17,24,39,0.9)',
                                cornerRadius: 8,
                                padding: 10,
                                callbacks: {
                                    label: function(context) {
                                        return 'Earnings: UGX ' + Number(context.parsed.y).toLocaleString();
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: 'rgba(0,0,0,0.04)' },
                                ticks: {
                                    callback: function(value) {
                                        return 'UGX ' + Number(value).toLocaleString();
                                    }
                                }
                            },
                            x: { grid: { display: false } }
                        }
                    }
                });
            }

            // Export functions
            function downloadCSV(filename, rows) {
                const csv = rows.map(r => r.map(v => `"${String(v).replace(/"/g,'""')}"`).join(',')).join('\n');
                const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = filename;
                document.body.appendChild(a);
                a.click();
                a.remove();
                URL.revokeObjectURL(url);
            }

            // Export buttons
            document.getElementById('exportCsvBtn')?.addEventListener('click', function() {
                const labels = @json($monthlyLabels ?? []);
                const earnings = @json($monthlyEarnings ?? []);
                const rows = [
                    ['Month', 'Earnings (UGX)'],
                    ...labels.map((label, index) => [label, earnings[index]])
                ];
                downloadCSV('monthly-earnings.csv', rows);
            });

            document.getElementById('exportPdfBtn')?.addEventListener('click', function() {
                window.print();
            });
        });
    </script>
    @endpush
</x-app-layout>

