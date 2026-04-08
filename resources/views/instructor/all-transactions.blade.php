<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('All Transactions') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Complete history of all your earnings</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('instructor.earnings.index') }}"
                   class="px-4 py-2 bg-purple-100 text-purple-700 rounded-xl text-sm font-semibold hover:bg-purple-200 transition-all duration-200">
                    Earnings Dashboard
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <form method="GET" class="flex gap-4 flex-wrap">
                        <div class="flex-1 min-w-[200px]">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date From</label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                        </div>
                        <div class="flex-1 min-w-[200px]">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date To</label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                        </div>
                        <div class="flex-1 min-w-[250px]">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                            <input type="text" name="search" value="{{ request('search') }}"
                                   placeholder="Reference, member, or class name..."
                                   class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                        </div>
                        <div class="flex items-end gap-2">
                            <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                                Filter
                            </button>
                            <a href="{{ route('instructor.earnings.transactions') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Date</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Class</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Member</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Amount</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Reference</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($receipts as $receipt)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $receipt->created_at->format('M d, Y h:i A') }}
                                 </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-semibold text-gray-800">
                                        {{ $receipt->scheduledClass->classType->name ?? 'N/A' }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $receipt->scheduledClass->date_time->format('M d, Y h:i A') ?? 'N/A' }}
                                    </div>
                                 </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $receipt->user->name ?? 'N/A' }}
                                 </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm font-bold text-emerald-600">
                                        UGX {{ number_format($receipt->amount ?? 0, 0) }}
                                    </span>
                                 </td>
                                <td class="px-6 py-4">
                                    <span class="text-xs font-mono text-gray-500">
                                        {{ $receipt->reference_number ?? 'N/A' }}
                                    </span>
                                 </td>
                             </tr>
                            @empty
                             <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    No transactions found
                                 </td>
                             </tr>
                            @endforelse
                        </tbody>
                     </table>
                </div>

                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $receipts->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
