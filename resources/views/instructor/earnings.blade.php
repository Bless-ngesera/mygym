cat > resources/views/instructor/earnings.blade.php << 'EOF'
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Earnings') }}
        </h2>
    </x-slot>

    <div class="py-12"
        style="background-image: url('{{ asset('images/background2.jpg') }}');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white/55 backdrop-blur-sm border border-white/20 rounded-2xl shadow-2xl ring-1 ring-white/30 overflow-hidden p-6">
                    <div class="text-sm text-gray-600">Total Earnings</div>
                    <div class="text-2xl font-bold mt-2">UGX {{ number_format($totalEarnings, 0) }}</div>
                </div>

                <div class="bg-white/55 backdrop-blur-sm border border-white/20 rounded-2xl shadow-2xl ring-1 ring-white/30 overflow-hidden p-6">
                    <div class="text-sm text-gray-600">This Month</div>
                    <div class="text-2xl font-bold mt-2">UGX {{ number_format($monthEarnings, 0) }}</div>
                </div>

                <div class="bg-white/55 backdrop-blur-sm border border-white/20 rounded-2xl shadow-2xl ring-1 ring-white/30 overflow-hidden p-6">
                    <div class="text-sm text-gray-600">Average per Class</div>
                    <div class="text-2xl font-bold mt-2">UGX {{ $earnings->count() > 0 ? number_format($totalEarnings / $earnings->count(), 0) : 0 }}</div>
                </div>
            </div>

            <!-- Chart Card -->
            <div class="bg-white/55 backdrop-blur-sm border border-white/20 rounded-2xl shadow-2xl ring-1 ring-white/30 overflow-hidden p-6 mb-6">
                <h3 class="text-lg font-semibold mb-4">Monthly Earnings</h3>
                <canvas id="earningsChart" height="100"></canvas>
            </div>

            <!-- Earnings Table -->
            <div class="bg-white/55 backdrop-blur-sm border border-white/20 rounded-2xl shadow-2xl ring-1 ring-white/30 overflow-hidden p-6">
                <h3 class="text-lg font-semibold mb-4">Earnings History</h3>

                @if(session('success'))
                    <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white/80 rounded-lg">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="py-3 px-4 text-left">Date</th>
                                <th class="py-3 px-4 text-left">Class</th>
                                <th class="py-3 px-4 text-left">Member</th>
                                <th class="py-3 px-4 text-left">Amount</th>
                                <th class="py-3 px-4 text-left">Reference</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($earnings as $receipt)
                            <tr class="border-t hover:bg-gray-50">
                                <td class="py-3 px-4">{{ $receipt->created_at->format('M d, Y') }}</td>
                                <td class="py-3 px-4">{{ $receipt->scheduledClass->classType->name ?? 'N/A' }}</td>
                                <td class="py-3 px-4">{{ $receipt->user->name ?? 'N/A' }}</td>
                                <td class="py-3 px-4 font-semibold text-green-600">UGX {{ number_format($receipt->amount, 0) }}</td>
                                <td class="py-3 px-4 text-sm text-gray-500">{{ $receipt->reference_number ?? 'N/A' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-gray-500">No earnings yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $earnings->links() }}
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('earningsChart').getContext('2d');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($monthlyLabels),
                    datasets: [{
                        label: 'Earnings (UGX)',
                        data: @json($monthlyEarnings),
                        borderColor: '#7e22ce',
                        backgroundColor: 'rgba(126, 34, 206, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'UGX ' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
EOF
