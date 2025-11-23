<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Earnings</h2>
                <p class="text-sm text-gray-500 mt-1">Overview of revenue and payouts</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.dashboard') }}"
                   class="px-3 py-2 text-sm bg-white rounded shadow hover:bg-gray-50 transition">
                   Back to Dashboard
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- KPI Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white rounded-lg shadow p-4 border-l-4 border-indigo-500 hover:shadow-md transition">
                    <div class="text-sm text-gray-500">Total Earnings (All time)</div>
                    <div class="text-2xl font-bold mt-2">UGX {{ number_format($totalEarnings ?? 0, 2) }}</div>
                </div>
                <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500 hover:shadow-md transition">
                    <div class="text-sm text-gray-500">This Month</div>
                    <div class="text-2xl font-bold mt-2">UGX {{ number_format($monthEarnings ?? 0, 2) }}</div>
                </div>
                <div class="bg-white rounded-lg shadow p-4 border-l-4 border-yellow-500 hover:shadow-md transition">
                    <div class="text-sm text-gray-500">Pending Payouts</div>
                    <div class="text-2xl font-bold mt-2">UGX {{ number_format($pendingPayouts ?? 0, 2) }}</div>
                </div>
            </div>

            <!-- Chart Section -->
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-4">
                        <h3 class="font-semibold">Monthly Earnings</h3>
                        <select id="earnings-range"
                                class="px-2 py-1 border rounded text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="12">Last 12 months</option>
                            <option value="6">Last 6 months</option>
                            <option value="3">Last 3 months</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('earnings.pdf') }}"
                           class="px-3 py-1 bg-indigo-600 text-white rounded text-sm hover:bg-indigo-700 transition">
                           Export PDF
                        </a>
                        <a href="{{ route('earnings.csv') }}"
                           class="px-3 py-1 bg-green-600 text-white rounded text-sm hover:bg-green-700 transition">
                           Export CSV
                        </a>
                        <a href="{{ route('earnings.excel') }}"
                           class="px-3 py-1 bg-yellow-600 text-white rounded text-sm hover:bg-yellow-700 transition">
                           Export Excel
                        </a>
                    </div>
                </div>

                <div class="relative h-64 w-full">
                    <canvas id="earningsChart"></canvas>
                </div>
            </div>

            <!-- Transactions Table -->
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-semibold text-lg text-gray-800">Recent Transactions</h3>
                    <input id="tx-search" type="search"
                        class="px-3 py-2 border rounded text-sm w-64 focus:ring focus:ring-indigo-200 focus:border-indigo-400"
                        placeholder="Search receipts, members..." />
                </div>

                <div class="overflow-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Reference</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Member</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Instructor</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Amount</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Date</th>
                            </tr>
                        </thead>
                        <tbody id="tx-tbody" class="divide-y divide-gray-200 bg-white">
                            @forelse($recentTransactions as $t)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-2 text-sm font-mono text-gray-500">
                                        {{ $t->reference_number ?? '-' }}
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-900">
                                        {{ optional($t->user)->name ?? $t->member_name ?? '-' }}
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-900">
                                        {{ optional($t->scheduledClass->instructor)->name ?? 'Unknown Instructor' }}
                                    </td>
                                    <td class="px-4 py-2 text-sm font-medium text-green-600">
                                        UGX {{ number_format($t->amount ?? 0, 2) }}
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-600">
                                        {{ optional($t->created_at)->format('M d, Y') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-6 text-center text-gray-500">No recent transactions.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 text-right">
                    <a href="{{ route('admin.earnings.all') }}"
                       class="text-sm text-indigo-600 hover:text-indigo-800">
                       View All Transactions →
                    </a>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Full 12-month dataset from the controller (chronological order)
        const fullLabels = @js($monthlyLabels ?? []);
        const fullData   = @js($monthlyEarnings ?? []);

        const ctx = document.getElementById('earningsChart')?.getContext('2d');
        let earningsChart = null;

        function renderChart(range = 12) {
            // Use the last N months
            const labels = fullLabels.slice(-range);
            const data   = fullData.slice(-range);

            if (earningsChart) earningsChart.destroy();

            earningsChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels,
                    datasets: [{
                        label: 'Earnings (UGX)',
                        data,
                        borderColor: '#6366f1',
                        backgroundColor: 'rgba(99,102,241,0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Earnings: ' + new Intl.NumberFormat(
                                        'en-UG', { style: 'currency', currency: 'UGX' }
                                    ).format(context.parsed.y);
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'UGX ' + Number(value).toLocaleString();
                                }
                            },
                            grid: { borderDash: [2,4] }
                        },
                        x: { grid: { display: false } }
                    }
                }
            });
        }

        // Initial render
        if (ctx) renderChart(12);

        // Range selector
        document.getElementById('earnings-range')?.addEventListener('change', function () {
            const range = parseInt(this.value, 10) || 12;
            renderChart(range);
        });

        // Search filter for table
        document.getElementById('tx-search')?.addEventListener('input', function() {
            const q = this.value.toLowerCase();
            document.querySelectorAll('#tx-tbody tr').forEach(tr => {
                tr.style.display = tr.innerText.toLowerCase().includes(q) ? '' : 'none';
            });
        });
    </script>
</x-app-layout>
