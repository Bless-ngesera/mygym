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
        <footer class="bg-gradient-to-r from-gray-900 to-gray-800 border-t border-purple-500/30">
            <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 gap-8 md:grid-cols-4 lg:grid-cols-5">
                    {{-- Column 1: Logo/Brand Info --}}
                    <div class="col-span-2 md:col-span-1 lg:col-span-2">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="flex items-center">
                                <img src="{{ asset('images/Project_Logo.png') }}" alt="Gym Logo" class="h-7 w-auto object-contain ml-1">
                            </div>
                        </div>
                        <p class="text-sm text-gray-400 leading-relaxed">
                            Train smart, stay consistent, and celebrate your growth. We're a community rooted in African strength and unity.
                        </p>
                        <div class="flex space-x-4 mt-4">
                            <a href="#" class="text-gray-400 hover:text-purple-400 transition duration-300">
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.77l-.44 2.89h-2.33v6.987A10 10 0 0022 12z" clip-rule="evenodd" /></svg>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-purple-400 transition duration-300">
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M12.315 2c2.43 0 2.715.01 3.67.058 1.036.05 1.745.21 2.37.456.684.276 1.258.74 1.717 1.259.46.52.825 1.094 1.102 1.717.246.625.407 1.334.456 2.37.048.955.058 1.23.058 3.67s-.01 2.715-.058 3.67c-.05.97-.21 1.745-.456 2.37-.276.684-.74 1.258-1.259 1.717-.52.46-1.094.825-1.717 1.102-.625.246-1.334.407-2.37.456-.955.048-1.23.058-3.67.058s-2.715-.01-3.67-.058c-.97-.05-1.745-.21-2.37-.456-.684-.276-1.258-.74-1.717-1.259-.46-.52-.825-1.094-1.102-1.717-.246-.625-.407-1.334-.456-2.37-.048-.955-.058-1.23-.058-3.67s.01-2.715.058-3.67c.05-.97.21-1.745.456-2.37.276-.684.74-1.258 1.259-1.717.46-.52 1.094-.825 1.717-1.102.625-.246 1.334-.407 2.37-.456C9.59 2.01 9.875 2 12.315 2zm0 1.637c-2.35 0-2.6.01-3.535.056-.983.05-1.503.21-1.85.347-.417.164-.78.384-1.095.698-.315.315-.534.678-.698 1.095-.137.347-.297.867-.347 1.85-.046.935-.056 1.185-.056 3.535s.01 2.6.056 3.535c.05.983.21 1.503.347 1.85.164.417.384.78.698 1.095.315.315.678.534 1.095.698.347.137.867.297 1.85.347.935.046 1.185.056 3.535.056s2.6-.01 3.535-.056c.983-.05 1.503-.21 1.85-.347.417-.164.78-.384 1.095-.698.315-.315.534-.678.698-1.095.137-.347.297-.867.347-1.85.046-.935.056-1.185.056-3.535s-.01-2.6-.056-3.535c-.05-.983-.21-1.503-.347-1.85-.164-.417-.384-.78-.698-1.095-.315-.315-.678-.534-1.095-.698-.347-.137-.867-.297-1.85-.347-.935-.046-1.185-.056-3.535-.056zM12.315 5.564c-3.714 0-6.75 3.036-6.75 6.75s3.036 6.75 6.75 6.75 6.75-3.036 6.75-6.75-3.036-6.75-6.75-6.75zm0 11.235c-2.476 0-4.485-2.009-4.485-4.485S9.839 7.828 12.315 7.828s4.485 2.009 4.485 4.485-2.009 4.485-4.485 4.485zm4.991-9.982c-.52 0-.942-.423-.942-.942s.422-.942.942-.942.942.423.942.942-.422.942-.942.942z" clip-rule="evenodd" /></svg>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-purple-400 transition duration-300">
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                            </a>
                        </div>
                    </div>

                    {{-- Column 2: Admin Quick Links --}}
                    <div>
                        <h5 class="text-lg font-semibold text-white mb-4">Admin Panel</h5>
                        <ul class="space-y-3">
                            <li><a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">📊 Dashboard</a></li>
                            <li><a href="{{ route('admin.members.index') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">👥 Manage Members</a></li>
                            <li><a href="{{ route('admin.instructors.index') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">👨‍🏫 Manage Instructors</a></li>
                            <li><a href="{{ route('admin.earnings.index') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">💰 Earnings Overview</a></li>
                            <li><a href="{{ route('admin.reports.index') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">📈 Reports</a></li>
                            <li><a href="{{ route('admin.settings.index') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">⚙️ Settings</a></li>
                        </ul>
                    </div>

                    {{-- Column 3: System Management --}}
                    <div>
                        <h5 class="text-lg font-semibold text-white mb-4">System</h5>
                        <ul class="space-y-3">
                            <li><a href="{{ route('admin.system.health') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">🩺 System Health</a></li>
                            <li><a href="{{ route('admin.system.logs') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">📋 System Logs</a></li>
                            <li><a href="{{ route('admin.database.backup') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">💾 Database Backup</a></li>
                            <li>
                                <form method="POST" action="{{ route('admin.system.clear-cache') }}" class="inline" onsubmit="return confirm('Are you sure you want to clear the system cache?');">
                                    @csrf
                                    <button type="submit" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">🗑️ Clear Cache</button>
                                </form>
                            </li>
                            <li><a href="{{ route('admin.system.queue-status') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">⏳ Queue Status</a></li>
                        </ul>
                    </div>

                    {{-- Column 4: Contact & Support --}}
                    <div class="col-span-2 md:col-span-1">
                        <h5 class="text-lg font-semibold text-white mb-4">Get In Touch</h5>
                        <ul class="space-y-3 text-sm text-gray-400">
                            <li class="flex items-start">
                                <span class="mr-2 text-purple-400">📍</span>
                                <span>Ggaba Road, Kampala, UGANDA</span>
                            </li>
                            <li class="flex items-start">
                                <span class="mr-2 text-purple-400">📞</span>
                                <span>+256 700 123 456</span>
                            </li>
                            <li class="flex items-start">
                                <span class="mr-2 text-purple-400">📧</span>
                                <span><a href="mailto:admin@mygym.com" class="hover:text-purple-400">admin@mygym.com</a></span>
                            </li>
                        </ul>
                        <div class="mt-6">
                            <h5 class="text-sm font-semibold text-white mb-2">Support Hours</h5>
                            <p class="text-xs text-gray-400">Monday - Friday: 9AM - 6PM</p>
                            <p class="text-xs text-gray-400">Saturday: 10AM - 4PM</p>
                            <p class="text-xs text-gray-400">Sunday: Closed</p>
                        </div>
                    </div>
                </div>

                {{-- Copyright Section with Links --}}
                <div class="mt-12 pt-8 border-t border-purple-500/30">
                    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                        <div class="flex gap-6">
                            <a href="#" class="text-xs text-gray-500 hover:text-purple-400 transition-colors">About Us</a>
                            <a href="#" class="text-xs text-gray-500 hover:text-purple-400 transition-colors">Terms of Service</a>
                            <a href="#" class="text-xs text-gray-500 hover:text-purple-400 transition-colors">Privacy Policy</a>
                            <a href="#" class="text-xs text-gray-500 hover:text-purple-400 transition-colors">Cookie Policy</a>
                        </div>
                        <div class="flex items-center gap-2">
                            <p class="text-sm text-gray-500">
                                &copy; {{ date('Y') }} MyGym. All rights reserved. Powered by Passion.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </footer>

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
