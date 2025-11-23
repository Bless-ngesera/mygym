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
                        <div class="flex items-center gap-2">
                            <a href="{{ url('/admin/exports/pdf') }}"
                            class="px-3 py-1 bg-indigo-600 text-white rounded text-sm hover:bg-indigo-700 transition">
                            Export PDF
                            </a>

                            <a href="{{ url('/admin/exports/csv') }}"
                            class="px-3 py-1 bg-green-600 text-white rounded text-sm hover:bg-green-700 transition">
                            Export CSV
                            </a>
                        </div>
                    </div>
                </div>

                <div class="relative h-64 w-full">
                    <canvas id="earningsChart"></canvas>
                </div>
            </div>

            <!-- Transactions Table -->
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-semibold">Recent Transactions</h3>
                    <input id="tx-search" type="search" class="px-3 py-2 border rounded text-sm w-64" placeholder="Search receipts, members..." />
                </div>

                <div class="overflow-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Date</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Reference</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Member</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Amount</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Status</th>
                            </tr>
                        </thead>
                        <tbody id="tx-tbody" class="divide-y divide-gray-200 bg-white">
                            @forelse($recentTransactions ?? [] as $t)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-2 text-sm text-gray-600">
                                        {{ $t->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-4 py-2 text-sm font-mono text-gray-500">
                                        {{ $t->reference ?? '-' }}
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-900">
                                        {{ $t->user->name ?? $t->member_name ?? '-' }}
                                    </td>
                                    <td class="px-4 py-2 text-sm font-medium text-green-600">
                                        UGX {{ number_format($t->amount ?? 0, 2) }}
                                    </td>
                                    <td class="px-4 py-2 text-sm">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            {{ $t->status ?? 'completed' }}
                                        </span>
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
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // JSON-safe defaults (NO short arrays!)
    const labels = @js($monthlyLabels ?? ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']);
    const data   = @js($monthlyEarnings ?? [0,0,0,0,0,0,0,0,0,0,0,0]);

        // Draw the chart
        const ctx = document.getElementById('earningsChart')?.getContext('2d');
        if (ctx) {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Earnings (UGX)',
                        data: data,
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
                                    let label = 'Earnings: ';
                                    label += new Intl.NumberFormat(
                                        'en-UG',
                                        { style: 'currency', currency: 'UGX' }
                                    ).format(context.parsed.y);
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'UGX ' + value.toLocaleString();
                                }
                            },
                            grid: { borderDash: [2,4] }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });
        }

        // Search filter for table
        document.getElementById('tx-search')?.addEventListener('input', function() {
            const q = this.value.toLowerCase();
            document.querySelectorAll('#tx-tbody tr').forEach(tr => {
                tr.style.display = tr.innerText.toLowerCase().includes(q) ? '' : 'none';
            });
        });
    </script>

</x-app-layout>
