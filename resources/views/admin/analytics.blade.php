<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Analytics Dashboard
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-gray-500 text-sm">Total Revenue</h3>
                    <p class="text-3xl font-bold text-green-600">UGX {{ number_format($revenueStats->sum('total'), 0) }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-gray-500 text-sm">Attendance Rate</h3>
                    <p class="text-3xl font-bold text-blue-600">{{ $attendanceRate }}%</p>
                </div>
            </div>

            <!-- Charts can be added using Chart.js or similar libraries -->
        </div>
    </div>
</x-app-layout>
