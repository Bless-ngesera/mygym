<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    My Receipts
                </h2>
                <p class="text-sm text-gray-500 mt-1">View your payment history and receipts</p>
            </div>
            <a href="{{ route('member.classes') }}"
               class="px-4 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white rounded-xl text-sm font-semibold transition-all duration-200 shadow-md hover:shadow-lg">
                Browse Classes
            </a>
        </div>
    </x-slot>

    <div class="py-12 min-h-screen"
        style="background-image: url('{{ asset('images/background2.jpg') }}');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-xl shadow-md">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl shadow-2xl overflow-hidden">
                <div class="p-6 md:p-8">
                    <div class="grid grid-cols-1 gap-4">
                        @forelse($receipts as $receipt)
                            <div class="bg-white/80 backdrop-blur-sm border border-white/40 rounded-xl p-4 hover:shadow-lg transition-all">
                                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                                    <div>
                                        <p class="font-bold text-gray-900">{{ $receipt->reference_number }}</p>
                                        <p class="text-xs text-gray-500">{{ $receipt->created_at->format('M d, Y h:i A') }}</p>
                                        <p class="text-sm text-gray-600 mt-1">{{ $receipt->scheduledClass->classType->name ?? 'Class' }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-xl font-bold text-emerald-600">UGX {{ number_format($receipt->amount, 0) }}</p>
                                        <p class="text-xs text-gray-500">{{ $receipt->payment_method }}</p>
                                    </div>
                                    <div>
                                        <a href="{{ route('receipts.show', $receipt) }}"
                                           class="inline-flex items-center gap-2 px-4 py-2 bg-purple-50 hover:bg-purple-100 text-purple-700 rounded-lg text-sm font-semibold transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            View Receipt
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12">
                                <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <p class="text-gray-500 text-lg mb-2">No receipts yet</p>
                                <p class="text-gray-400 text-sm">Book a class to generate your first receipt.</p>
                            </div>
                        @endforelse
                    </div>

                    {{-- Pagination --}}
                    @if($receipts->hasPages())
                        <div class="mt-6">
                            {{ $receipts->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
