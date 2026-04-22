<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('My Earnings') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Track your revenue and payouts</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('instructor.classes') }}"
                   class="px-3 py-2 bg-purple-100 text-purple-700 rounded-xl text-sm font-semibold hover:bg-purple-200 transition-all duration-200">
                    My Classes
                </a>
                <a href="{{ route('instructor.upcoming') }}"
                   class="px-3 py-2 bg-indigo-100 text-indigo-700 rounded-xl text-sm font-semibold hover:bg-indigo-200 transition-all duration-200">
                    Upcoming
                </a>
                <a href="{{ route('instructor.earnings.transactions') }}"
                   class="px-3 py-2 bg-emerald-100 text-emerald-700 rounded-xl text-sm font-semibold hover:bg-emerald-200 transition-all duration-200">
                    Transactions
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

            {{-- Stats Cards with Animations --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-200 overflow-hidden group">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
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

                <div class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-200 overflow-hidden group">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div class="flex flex-col items-end">
                                <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">{{ now()->format('M Y') }}</span>
                                @if(isset($growthPercentage))
                                    <span class="text-xs {{ $growthPercentage >= 0 ? 'text-green-600' : 'text-red-600' }} mt-1">
                                        {{ $growthPercentage >= 0 ? '↑' : '↓' }} {{ number_format(abs($growthPercentage), 1) }}%
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="text-2xl font-bold text-gray-900 tracking-tight">UGX {{ number_format($monthEarnings ?? 0, 0) }}</div>
                        <div class="text-xs font-medium text-gray-500 mt-0.5">This Month</div>
                    </div>
                </div>

                <div class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-200 overflow-hidden group">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
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

                <div class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-200 overflow-hidden group">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                            </div>
                            <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">Total</span>
                        </div>
                        <div class="text-2xl font-bold text-gray-900 tracking-tight">{{ $receipts->total() ?? 0 }}</div>
                        <div class="text-xs font-medium text-gray-500 mt-0.5">Transactions</div>
                    </div>
                </div>
            </div>

            {{-- Premium Chart Card with Advanced Features --}}
            <div class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl shadow-lg p-6 mb-6">
                <div class="flex justify-between items-center mb-5 flex-wrap gap-4">
                    <div>
                        <h3 class="font-bold text-gray-900 text-lg">Monthly Earnings Trend</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Last 12 months revenue overview</p>
                    </div>
                    <div class="flex gap-2">
                        <button id="toggleDataLabels" class="text-xs px-3 py-1.5 bg-purple-100 hover:bg-purple-200 text-purple-700 rounded-lg transition-colors font-medium">
                            Show Values
                        </button>
                        <button id="exportPdfBtn" class="text-xs px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors font-medium">
                            Export PDF
                        </button>
                        <button id="exportCsvBtn" class="text-xs px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors font-medium">
                            Export CSV
                        </button>
                    </div>
                </div>
                <div style="height: 400px; position: relative;">
                    <canvas id="earningsChart"></canvas>
                </div>
            </div>

            {{-- Enhanced Earnings Table with Export --}}
            <div class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center flex-wrap gap-4">
                    <div>
                        <h3 class="font-bold text-gray-900 text-lg">Recent Transactions</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Your latest transactions</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('instructor.earnings.transactions') }}"
                           class="text-sm px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors font-medium">
                            View All Transactions
                        </a>
                        <a href="{{ route('instructor.earnings.export') }}"
                           class="text-sm px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition-colors font-medium">
                            Export All
                        </a>
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
                            @forelse($receipts ?? [] as $receipt)
                            <tr class="hover:bg-purple-50/30 transition-colors duration-150 group">
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $receipt->created_at->format('M d, Y') }}
                                    <div class="text-xs text-gray-400">{{ $receipt->created_at->format('h:i A') }}</div>
                                 </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-lg flex items-center justify-center shadow-md group-hover:shadow-lg transition-shadow">
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-sm font-semibold text-gray-800">{{ $receipt->scheduledClass->classType->name ?? 'N/A' }}</div>
                                            <div class="text-xs text-gray-500">{{ optional($receipt->scheduledClass->date_time)->format('M d, Y h:i A') ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                 </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($receipt->user->name ?? 'Member') }}&background=10b981&color=fff&bold=true&size=32&length=2"
                                             alt="" class="w-6 h-6 rounded-full">
                                        <span class="text-sm text-gray-600 font-medium">{{ $receipt->user->name ?? 'N/A' }}</span>
                                    </div>
                                 </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm font-bold text-emerald-600">UGX {{ number_format($receipt->amount ?? 0, 0) }}</span>
                                 </td>
                                <td class="px-6 py-4">
                                    <span class="text-xs font-mono text-gray-500 bg-gray-100 px-2 py-1 rounded">{{ $receipt->reference_number ?? 'N/A' }}</span>
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
                                    <a href="{{ route('instructor.create') }}" class="inline-flex items-center gap-2 mt-4 text-purple-600 hover:text-purple-800 text-sm font-semibold">
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
                @if(isset($receipts) && $receipts->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                        {{ $receipts->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

        <footer class="bg-gradient-to-r from-gray-900 to-gray-800 border-t border-purple-500/30">
    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 gap-8 md:grid-cols-4 lg:grid-cols-5">
            {{-- Column 1: Logo/Brand Info --}}
            <div class="col-span-2 md:col-span-1 lg:col-span-2">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-14 rounded-xl flex items-center justify-center shadow-lg overflow-hidden">
                        <img src="{{ asset('images/logo.png') }}" alt="MyGym Logo" class="w-full h-full object-cover">
                    </div>
                    <h4 class="text-2xl font-bold text-white tracking-wider">My<span class="text-purple-400">Gym</span></h4>
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

            {{-- Column 2: Instructor Quick Links --}}
            <div>
                <h5 class="text-lg font-semibold text-white mb-4">Instructor Hub</h5>
                <ul class="space-y-3">
                    <li><a href="{{ route('instructor.dashboard') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">📊 Dashboard</a></li>
                    <li><a href="{{ route('instructor.create') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">🗓️ Schedule Class</a></li>
                    <li><a href="{{ route('instructor.classes') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">👥 My Classes</a></li>
                    <li><a href="{{ route('instructor.upcoming') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">📊 Upcoming Classes</a></li>
                    <li><a href="{{ route('instructor.calendar') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">📅 Calendar View</a></li>
                    <li><a href="{{ route('instructor.earnings.index') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">💵 My Earnings</a></li>
                </ul>
            </div>

            {{-- Column 3: Resources & Support --}}
            <div>
                <h5 class="text-lg font-semibold text-white mb-4">Resources</h5>
                <ul class="space-y-3">
                    <li><a href="{{ route('instructor.members.index') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">👥 My Members</a></li>
                    <li><a href="#" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">📚 Training Guides</a></li>
                    <li><a href="#" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">💡 Tips & Tricks</a></li>
                    <li><a href="#" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">🎓 Certification</a></li>
                    <li><a href="#" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">❓ Help Center</a></li>
                    <li><a href="#" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">📧 Support</a></li>
                </ul>
            </div>

            {{-- Column 4: Contact Info --}}
            <div class="col-span-2 md:col-span-1">
                <h5 class="text-lg font-semibold text-white mb-4">Get In Touch</h5>
                <ul class="space-y-3 text-sm text-gray-400">
                    <li class="flex items-start">
                        <span class="mr-2 text-purple-400">📍</span>
                        <span>Ggaba road, Kampala, UGANDA</span>
                    </li>
                    <li class="flex items-start">
                        <span class="mr-2 text-purple-400">📞</span>
                        <span>+256 700 123 456</span>
                    </li>
                    <li class="flex items-start">
                        <span class="mr-2 text-purple-400">📧</span>
                        <span><a href="mailto:instructors@mygym.com" class="hover:text-purple-400">instructors@mygym.com</a></span>
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

        {{-- Copyright Section --}}
        <div class="mt-12 pt-8 border-t border-purple-500/30 text-center">
            <p class="text-sm text-gray-500">
                &copy; {{ date('Y') }} MyGym. All rights reserved. Powered by Passion.
            </p>
        </div>
    </div>
    </footer>

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

        /* Custom Chart.js styling */
        canvas {
            max-height: 100%;
            width: 100%;
        }

        /* Premium scrollbar */
        .overflow-x-auto::-webkit-scrollbar {
            height: 6px;
        }

        .overflow-x-auto::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .overflow-x-auto::-webkit-scrollbar-thumb {
            background: #c084fc;
            border-radius: 10px;
        }

        .overflow-x-auto::-webkit-scrollbar-thumb:hover {
            background: #a855f7;
        }

        /* Print styles */
        @media print {
            .bg-white\/85 {
                background: white !important;
            }
            .backdrop-blur-md {
                backdrop-filter: none !important;
            }
            button, a, .flex.gap-3, .flex.gap-2 {
                display: none !important;
            }
            body {
                background: white !important;
            }
        }
    </style>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let earningsChart = null;
            let showDataLabels = false;

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

            // Premium Chart Configuration
            const ctx = document.getElementById('earningsChart')?.getContext('2d');
            if (ctx) {
                const monthlyLabels = @json($monthlyLabels ?? []);
                const monthlyEarnings = @json($monthlyEarnings ?? []);

                earningsChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: monthlyLabels,
                        datasets: [{
                            label: 'Monthly Earnings (UGX)',
                            data: monthlyEarnings,
                            borderColor: '#7e22ce',
                            backgroundColor: function(context) {
                                const chart = context.chart;
                                const {ctx, chartArea} = chart;
                                if (!chartArea) return 'rgba(126, 34, 206, 0.1)';

                                const gradient = ctx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
                                gradient.addColorStop(0, 'rgba(126, 34, 206, 0.3)');
                                gradient.addColorStop(1, 'rgba(126, 34, 206, 0.02)');
                                return gradient;
                            },
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#7e22ce',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 5,
                            pointHoverRadius: 8,
                            pointHoverBackgroundColor: '#7e22ce',
                            pointHoverBorderColor: '#fff',
                            pointHoverBorderWidth: 2,
                            pointStyle: 'circle',
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                                labels: {
                                    font: {
                                        size: 12,
                                        weight: 'bold'
                                    },
                                    padding: 15,
                                    usePointStyle: true,
                                    pointStyle: 'circle'
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(17,24,39,0.95)',
                                titleColor: '#fff',
                                bodyColor: '#e5e7eb',
                                cornerRadius: 8,
                                padding: 12,
                                borderColor: '#7e22ce',
                                borderWidth: 1,
                                callbacks: {
                                    label: function(context) {
                                        return 'Earnings: UGX ' + Number(context.parsed.y).toLocaleString();
                                    },
                                    afterBody: function(tooltipItems) {
                                        if (!tooltipItems.length) return '';
                                        const value = tooltipItems[0].parsed.y;
                                        const total = tooltipItems[0].dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                        return `Percentage: ${percentage}% of total`;
                                    }
                                }
                            },
                            datalabels: {
                                display: false,
                                color: '#7e22ce',
                                anchor: 'end',
                                align: 'top',
                                offset: 8,
                                font: {
                                    weight: 'bold',
                                    size: 11
                                },
                                formatter: function(value) {
                                    if (value === 0) return '';
                                    return 'UGX ' + value.toLocaleString();
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0,0,0,0.05)',
                                    drawBorder: true,
                                    borderDash: [5, 5]
                                },
                                ticks: {
                                    callback: function(value) {
                                        return 'UGX ' + Number(value).toLocaleString();
                                    }
                                },
                                title: {
                                    display: true,
                                    text: 'Amount (UGX)',
                                    font: {
                                        size: 12,
                                        weight: 'bold'
                                    },
                                    color: '#6b7280'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        size: 11
                                    },
                                    rotation: 0,
                                    autoSkip: true,
                                    maxRotation: 45,
                                    minRotation: 0
                                },
                                title: {
                                    display: true,
                                    text: 'Month',
                                    font: {
                                        size: 12,
                                        weight: 'bold'
                                    },
                                    color: '#6b7280'
                                }
                            }
                        },
                        interaction: {
                            intersect: false,
                            mode: 'index'
                        },
                        elements: {
                            line: {
                                borderJoin: 'round',
                                borderCap: 'round'
                            },
                            point: {
                                hitRadius: 10,
                                hoverRadius: 8,
                                radius: 5
                            }
                        },
                        layout: {
                            padding: {
                                top: 20,
                                bottom: 20,
                                left: 10,
                                right: 10
                            }
                        }
                    }
                });
            }

            // Toggle data labels
            const toggleBtn = document.getElementById('toggleDataLabels');
            if (toggleBtn && earningsChart) {
                toggleBtn.addEventListener('click', function() {
                    showDataLabels = !showDataLabels;
                    const plugin = earningsChart.options.plugins.datalabels;
                    if (plugin) {
                        plugin.display = showDataLabels;
                        earningsChart.update();
                        this.textContent = showDataLabels ? 'Hide Values' : 'Show Values';
                        this.classList.toggle('bg-purple-200', showDataLabels);
                    }
                });
            }

            // Export CSV function
            function downloadCSV(filename, rows) {
                const csv = rows.map(r => r.map(v => `"${String(v).replace(/"/g, '""')}"`).join(',')).join('\n');
                const blob = new Blob(["\uFEFF" + csv], { type: 'text/csv;charset=utf-8;' });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = filename;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
            }

            // Export monthly CSV
            const exportCsvBtn = document.getElementById('exportCsvBtn');
            if (exportCsvBtn) {
                exportCsvBtn.addEventListener('click', function() {
                    const labels = @json($monthlyLabels ?? []);
                    const earnings = @json($monthlyEarnings ?? []);
                    const rows = [
                        ['Month', 'Earnings (UGX)'],
                        ...labels.map((label, index) => [label, earnings[index]])
                    ];
                    downloadCSV('monthly-earnings-' + new Date().toISOString().split('T')[0] + '.csv', rows);
                });
            }

            // Export PDF (print)
            const exportPdfBtn = document.getElementById('exportPdfBtn');
            if (exportPdfBtn) {
                exportPdfBtn.addEventListener('click', function() {
                    window.print();
                });
            }

            // Add animation to stats cards on scroll
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '0';
                        entry.target.style.transform = 'translateY(20px)';
                        setTimeout(() => {
                            entry.target.style.transition = 'all 0.6s ease-out';
                            entry.target.style.opacity = '1';
                            entry.target.style.transform = 'translateY(0)';
                        }, 100);
                        observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            document.querySelectorAll('.grid > div').forEach(card => {
                observer.observe(card);
            });
        });
    </script>
    @endpush
</x-app-layout>
