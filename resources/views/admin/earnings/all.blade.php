{{-- resources/views/admin/earnings/all.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">All Transactions</h2>
                <p class="text-sm text-gray-500 mt-1">Full receipts list with pagination</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.earnings') }}"
                   class="px-3 py-2 text-sm bg-white rounded shadow hover:bg-gray-50 transition">
                   ← Back to Earnings
                </a>
                <a href="{{ route('admin.dashboard') }}"
                   class="px-3 py-2 text-sm bg-white rounded shadow hover:bg-gray-50 transition">
                   Dashboard
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Receipts Table -->
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-semibold text-lg text-gray-800">Receipts</h3>
                    <div class="text-sm text-gray-500">
                        Showing {{ $recentReceipts->firstItem() ?? 0 }}–{{ $recentReceipts->lastItem() ?? 0 }}
                        of {{ $recentReceipts->total() ?? 0 }}
                    </div>
                </div>

                <div class="overflow-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Reference</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Member</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Instructor</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Class</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Payment method</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Amount (UGX)</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($recentReceipts as $r)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-2 text-sm font-mono text-gray-700">
                                        {{ $r->reference_number ?? '-' }}
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-900">
                                        {{ optional($r->user)->name ?? 'Unknown Member' }}
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-900">
                                        {{ optional(optional($r->scheduledClass)->instructor)->name ?? 'Unknown Instructor' }}
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-900">
                                        {{ optional(optional($r->scheduledClass)->classType)->name ?? 'Unknown Class' }}
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-900">
                                        {{ $r->payment_method ?? '-' }}
                                    </td>
                                    <td class="px-4 py-2 text-sm font-medium text-green-600">
                                        UGX {{ number_format($r->amount ?? 0, 2) }}
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-600">
                                        {{ optional($r->created_at)->format('Y-m-d') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="p-6 text-center text-gray-500">No receipts found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $recentReceipts->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
