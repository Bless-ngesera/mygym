<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Membership Plans
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Choose the perfect plan for your fitness journey
                </p>
            </div>
            <a href="{{ route('member.dashboard') }}"
               class="px-4 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white rounded-xl text-sm font-semibold transition-all duration-200 shadow-md hover:shadow-lg inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-12 min-h-screen"
        style="background-image: url('{{ asset('images/background2.jpg') }}');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Current Subscription Status --}}
            @if(isset($currentSubscription) && $currentSubscription)
                @php
                    $daysRemaining = max(0, (int)Carbon\Carbon::now()->diffInDays($currentSubscription->end_date, false));
                    $totalDays = (int)Carbon\Carbon::parse($currentSubscription->start_date)->diffInDays($currentSubscription->end_date);
                    $daysUsed = max(0, $totalDays - $daysRemaining);
                    $progressPercent = $totalDays > 0 ? min(100, max(0, ($daysUsed / $totalDays) * 100)) : 0;
                @endphp
                <div class="mb-8 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-2xl p-6 text-white shadow-xl">
                    <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold">Your Current Plan</h3>
                                <p class="text-2xl font-bold mt-1">{{ $currentSubscription->plan_name }}</p>
                            </div>
                        </div>

                        <div class="flex-1 max-w-md">
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-emerald-100">{{ $daysUsed }} days used</span>
                                <span class="text-emerald-100">{{ $daysRemaining }} days remaining</span>
                            </div>
                            <div class="h-2 w-full overflow-hidden rounded-full bg-white/20">
                                <div class="h-full rounded-full bg-white transition-all duration-700" style="width: {{ $progressPercent }}%"></div>
                            </div>
                            <p class="text-xs text-emerald-100 mt-2">
                                Valid until {{ Carbon\Carbon::parse($currentSubscription->end_date)->format('F j, Y') }}
                            </p>
                        </div>

                        <form method="POST" action="{{ route('plans.cancel') }}" onsubmit="return confirm('Are you sure you want to cancel your subscription? This action cannot be undone.');">
                            @csrf
                            <button type="submit" class="px-5 py-2.5 bg-white/20 hover:bg-white/30 text-white text-sm font-semibold rounded-xl transition-all duration-200 backdrop-blur-sm inline-flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Cancel Subscription
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            @if(session('success'))
                <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-xl shadow-md">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl shadow-2xl overflow-hidden">
                <div class="p-6 md:p-8">

                    {{-- Section Title --}}
                    <div class="text-center mb-12">
                        <h2 class="text-3xl font-bold text-gray-900 mb-3">Choose Your Plan</h2>
                        <p class="text-gray-500 max-w-2xl mx-auto">Select the perfect membership plan that fits your fitness goals and lifestyle</p>
                    </div>

                    {{-- Plans Grid --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        {{-- Basic Plan --}}
                        <div class="bg-white/80 backdrop-blur-sm border border-white/40 rounded-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 flex flex-col h-full">
                            <div class="p-6 flex-1">
                                <h3 class="text-2xl font-bold text-gray-900">Basic</h3>
                                <div class="mt-4">
                                    <span class="text-4xl font-bold text-gray-900">100,000 UGX</span>
                                    <span class="text-gray-500">/monthly</span>
                                </div>

                                <p class="mt-4 text-gray-600 text-sm">Perfect for beginners starting their fitness journey</p>

                                <div class="mt-6 pt-4 border-t border-gray-100">
                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">What's included:</p>
                                    <div class="space-y-2.5">
                                        <div class="flex items-center gap-2.5">
                                            <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            <span class="text-sm text-gray-600">🏋️ Access to gym facilities (6 AM - 6 PM)</span>
                                        </div>
                                        <div class="flex items-center gap-2.5">
                                            <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            <span class="text-sm text-gray-600">📅 2 Group classes per week</span>
                                        </div>
                                        <div class="flex items-center gap-2.5">
                                            <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            <span class="text-sm text-gray-600">📱 Basic workout tracking</span>
                                        </div>
                                        <div class="flex items-center gap-2.5">
                                            <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            <span class="text-sm text-gray-600">📧 Email support (48hr response)</span>
                                        </div>
                                        <div class="flex items-center gap-2.5">
                                            <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            <span class="text-sm text-gray-600">🔒 Locker room access</span>
                                        </div>
                                        <div class="flex items-center gap-2.5">
                                            <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            <span class="text-sm text-gray-600">💧 Free drinking water</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="p-6 pt-0">
                                @if(!isset($currentSubscription) || !$currentSubscription)
                                    <button onclick="openSubscribeModal({{ $plans[0]->id ?? 1 }}, 'Basic', '100,000 UGX')"
                                            class="w-full py-3 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-semibold rounded-xl transition-all duration-200 shadow-md hover:shadow-lg">
                                        Subscribe Now
                                    </button>
                                @elseif($currentSubscription->plan_name !== 'Basic')
                                    <button disabled
                                            class="w-full py-3 bg-gray-200 text-gray-500 font-semibold rounded-xl cursor-not-allowed">
                                        Already Subscribed
                                    </button>
                                @else
                                    <button disabled
                                            class="w-full py-3 bg-emerald-100 text-emerald-600 font-semibold rounded-xl cursor-default">
                                        Your Current Plan
                                    </button>
                                @endif
                            </div>
                        </div>

                        {{-- Standard Plan --}}
                        <div class="bg-white/80 backdrop-blur-sm border border-white/40 rounded-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 flex flex-col h-full">
                            <div class="p-6 flex-1">
                                <h3 class="text-2xl font-bold text-gray-900">Standard</h3>
                                <div class="mt-4">
                                    <span class="text-4xl font-bold text-gray-900">150,000 UGX</span>
                                    <span class="text-gray-500">/monthly</span>
                                </div>

                                <p class="mt-4 text-gray-600 text-sm">Most popular choice for regular gym-goers</p>

                                <div class="mt-6 pt-4 border-t border-gray-100">
                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">What's included:</p>
                                    <div class="space-y-2.5">
                                        <div class="flex items-center gap-2.5">
                                            <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            <span class="text-sm text-gray-600">🏋️ 24/7 Access to gym facilities</span>
                                        </div>
                                        <div class="flex items-center gap-2.5">
                                            <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            <span class="text-sm text-gray-600">📅 5 Group classes per week</span>
                                        </div>
                                        <div class="flex items-center gap-2.5">
                                            <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            <span class="text-sm text-gray-600">📱 Advanced workout tracking</span>
                                        </div>
                                        <div class="flex items-center gap-2.5">
                                            <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            <span class="text-sm text-gray-600">💬 Priority support (12hr response)</span>
                                        </div>
                                        <div class="flex items-center gap-2.5">
                                            <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            <span class="text-sm text-gray-600">🔒 Premium locker room access</span>
                                        </div>
                                        <div class="flex items-center gap-2.5">
                                            <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            <span class="text-sm text-gray-600">💧 Free water & protein shake</span>
                                        </div>
                                        <div class="flex items-center gap-2.5">
                                            <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            <span class="text-sm text-gray-600">🥗 Basic nutrition guidance</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="p-6 pt-0">
                                @if(!isset($currentSubscription) || !$currentSubscription)
                                    <button onclick="openSubscribeModal({{ $plans[1]->id ?? 2 }}, 'Standard', '150,000 UGX')"
                                            class="w-full py-3 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-semibold rounded-xl transition-all duration-200 shadow-md hover:shadow-lg">
                                        Subscribe Now
                                    </button>
                                @elseif($currentSubscription->plan_name !== 'Standard')
                                    <button disabled
                                            class="w-full py-3 bg-gray-200 text-gray-500 font-semibold rounded-xl cursor-not-allowed">
                                        Already Subscribed
                                    </button>
                                @else
                                    <button disabled
                                            class="w-full py-3 bg-emerald-100 text-emerald-600 font-semibold rounded-xl cursor-default">
                                        Your Current Plan
                                    </button>
                                @endif
                            </div>
                        </div>

                        {{-- Premium Plan --}}
                        <div class="bg-white/80 backdrop-blur-sm border border-white/40 rounded-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 flex flex-col h-full">
                            <div class="p-6 flex-1">
                                <h3 class="text-2xl font-bold text-gray-900">Premium</h3>
                                <div class="mt-4">
                                    <span class="text-4xl font-bold text-gray-900">200,000 UGX</span>
                                    <span class="text-gray-500">/monthly</span>
                                </div>

                                <p class="mt-4 text-gray-600 text-sm">Ultimate fitness experience with personal trainer</p>

                                <div class="mt-6 pt-4 border-t border-gray-100">
                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">What's included:</p>
                                    <div class="space-y-2.5">
                                        <div class="flex items-center gap-2.5">
                                            <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            <span class="text-sm text-gray-600">🏋️ 24/7 VIP Access to gym facilities</span>
                                        </div>
                                        <div class="flex items-center gap-2.5">
                                            <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            <span class="text-sm text-gray-600">🎓 Dedicated personal trainer (1-on-1)</span>
                                        </div>
                                        <div class="flex items-center gap-2.5">
                                            <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            <span class="text-sm text-gray-600">🏆 Unlimited group classes</span>
                                        </div>
                                        <div class="flex items-center gap-2.5">
                                            <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            <span class="text-sm text-gray-600">📱 Premium workout tracking with AI</span>
                                        </div>
                                        <div class="flex items-center gap-2.5">
                                            <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            <span class="text-sm text-gray-600">💬 24/7 Priority support (2hr response)</span>
                                        </div>
                                        <div class="flex items-center gap-2.5">
                                            <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            <span class="text-sm text-gray-600">🔒 VIP Locker room with sauna</span>
                                        </div>
                                        <div class="flex items-center gap-2.5">
                                            <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            <span class="text-sm text-gray-600">💧 Free water & protein shakes</span>
                                        </div>
                                        <div class="flex items-center gap-2.5">
                                            <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            <span class="text-sm text-gray-600">🥗 Custom nutrition plan by expert</span>
                                        </div>
                                        <div class="flex items-center gap-2.5">
                                            <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            <span class="text-sm text-gray-600">🧘 Free yoga & meditation classes</span>
                                        </div>
                                        <div class="flex items-center gap-2.5">
                                            <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            <span class="text-sm text-gray-600">💪 Body composition analysis monthly</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="p-6 pt-0">
                                @if(!isset($currentSubscription) || !$currentSubscription)
                                    <button onclick="openSubscribeModal({{ $plans[2]->id ?? 3 }}, 'Premium', '200,000 UGX')"
                                            class="w-full py-3 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-semibold rounded-xl transition-all duration-200 shadow-md hover:shadow-lg">
                                        Subscribe Now
                                    </button>
                                @elseif($currentSubscription->plan_name !== 'Premium')
                                    <button disabled
                                            class="w-full py-3 bg-gray-200 text-gray-500 font-semibold rounded-xl cursor-not-allowed">
                                        Already Subscribed
                                    </button>
                                @else
                                    <button disabled
                                            class="w-full py-3 bg-emerald-100 text-emerald-600 font-semibold rounded-xl cursor-default">
                                        Your Current Plan
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Subscribe Modal --}}
    <div id="subscribeModal" class="fixed inset-0 z-[1000] hidden items-center justify-center bg-black/60 backdrop-blur-sm p-4" onclick="if(event.target===this) closeSubscribeModal()">
        <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden transform transition-all duration-300 scale-95 opacity-0" id="modalContent">
            <div class="relative">
                <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-purple-500 to-indigo-500"></div>

                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 bg-gradient-to-br from-purple-100 to-indigo-100 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/>
                            </svg>
                        </div>
                        <h3 class="font-bold text-gray-900 text-lg" id="modalPlanName">Subscribe to Plan</h3>
                    </div>
                    <button onclick="closeSubscribeModal()" class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form id="subscribeForm" method="POST" class="p-6 space-y-5">
                    @csrf
                    <div class="bg-gradient-to-r from-purple-50 to-indigo-50 rounded-xl p-4 text-center">
                        <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Plan Price</p>
                        <p class="text-3xl font-bold text-gray-900" id="modalPlanPrice">UGX 0</p>
                    </div>

                    <div>
                        <label class="block mb-2 text-xs font-bold text-gray-600 uppercase tracking-wider">Select Payment Method</label>
                        <select name="payment_method" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-800 focus:ring-2 focus:ring-purple-200 focus:border-purple-400 focus:bg-white outline-none transition-all duration-200">
                            <option value="credit_card">💳 Credit / Debit Card</option>
                            <option value="mobile_money">📱 Mobile Money (MTN, Airtel)</option>
                            <option value="bank_transfer">🏦 Bank Transfer</option>
                        </select>
                    </div>

                    <div class="bg-amber-50 rounded-xl p-3 border border-amber-200">
                        <div class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-amber-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-xs text-amber-700">This is a demo payment. No actual charges will be made to your account.</p>
                        </div>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="button" onclick="closeSubscribeModal()" class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition-colors">Cancel</button>
                        <button type="submit" class="flex-1 px-4 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white text-sm font-semibold rounded-xl shadow-lg shadow-purple-200 transition-all duration-200 flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Confirm Subscription
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>

    <script>
        let currentPlanId = null;

        function openSubscribeModal(planId, planName, planPrice) {
            currentPlanId = planId;
            document.getElementById('modalPlanName').innerHTML = `Subscribe to ${planName}`;
            document.getElementById('modalPlanPrice').innerHTML = planPrice;
            document.getElementById('subscribeForm').action = `/plans/${planId}/subscribe`;

            const modal = document.getElementById('subscribeModal');
            const content = document.getElementById('modalContent');

            modal.classList.remove('hidden');
            modal.classList.add('flex');

            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);

            document.body.style.overflow = 'hidden';
        }

        function closeSubscribeModal() {
            const modal = document.getElementById('subscribeModal');
            const content = document.getElementById('modalContent');

            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');

            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.style.overflow = '';
            }, 200);
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeSubscribeModal();
        });
    </script>

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

                {{-- Column 2: Quick Links --}}
                <div>
                    <h5 class="text-lg font-semibold text-white mb-4">Quick Links</h5>
                    <ul class="space-y-3">
                        <li><a href="{{ route('member.dashboard') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">Dashboard</a></li>
                        <li><a href="{{ route('classes.index') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">Browse Classes</a></li>
                        <li><a href="{{ route('member.bookings.index') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">My Bookings</a></li>
                        <li><a href="{{ route('profile.edit') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">Manage Profile</a></li>
                        <li><a href="{{ route('plans.index') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">Membership Plans</a></li>
                    </ul>
                </div>

                {{-- Column 3: Popular Classes --}}
                <div>
                    <h5 class="text-lg font-semibold text-white mb-4">Popular Classes</h5>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">🧘 Yoga</a></li>
                        <li><a href="#" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">💪 HIIT</a></li>
                        <li><a href="#" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">💃 Zumba</a></li>
                        <li><a href="#" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">🥊 Boxing</a></li>
                        <li><a href="#" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">🧘 Pilates</a></li>
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
                            <span><a href="mailto:info@mygym.com" class="hover:text-purple-400">info@mygym.com</a></span>
                        </li>
                    </ul>
                    <div class="mt-6">
                        <h5 class="text-sm font-semibold text-white mb-2">Support Hours</h5>
                        <p class="text-xs text-gray-400">Mon-Fri: 9AM - 6PM</p>
                        <p class="text-xs text-gray-400">Sat: 10AM - 4PM</p>
                        <p class="text-xs text-gray-400">Sun: Closed</p>
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
</x-app-layout>
