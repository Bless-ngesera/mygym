<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Receipt Details
                </h2>
                <p class="text-sm text-gray-500 mt-1">{{ $receipt->reference_number }}</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('receipts.index') }}"
                   class="no-print px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-semibold transition-all duration-200">
                    ← Back to Receipts
                </a>
                <a href="{{ route('receipts.download', $receipt) }}"
                   class="no-print px-4 py-2 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white rounded-xl text-sm font-semibold transition-all duration-200 shadow-md hover:shadow-lg">
                    📥 Download PDF
                </a>
                <button onclick="window.print()"
                        class="no-print px-4 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white rounded-xl text-sm font-semibold transition-all duration-200 shadow-md hover:shadow-lg">
                    🖨️ Print
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12 min-h-screen"
        style="background-image: url('{{ asset('images/background2.jpg') }}');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;">

        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div id="receipt-card" class="bg-white rounded-2xl shadow-xl overflow-hidden">

                {{-- ── HEADER ── --}}
                <div class="receipt-header relative px-8 pt-8 pb-6 border-b border-gray-100">
                    <div class="deco-tr absolute top-0 right-0 w-32 h-32 bg-purple-100 rounded-full opacity-30"
                         style="margin-right:-4rem;margin-top:-4rem;"></div>
                    <div class="deco-bl absolute bottom-0 left-0 w-24 h-24 bg-indigo-100 rounded-full opacity-30"
                         style="margin-left:-3rem;margin-bottom:-3rem;"></div>

                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 relative z-10">
                        <div class="flex items-center gap-4">
                            <img src="{{ asset('images/Project_Logo.png') }}" alt="MyGym Logo" class="h-12 w-auto">
                            <div class="border-l-2 border-gray-200 pl-4">
                                <h1 class="text-2xl font-black text-gray-900">Official <span class="text-purple-600">Receipt</span></h1>
                                <p class="text-xs text-gray-500 uppercase tracking-wider">Payment Confirmation</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-gray-100 rounded-full">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <span class="text-xs font-mono font-semibold text-gray-700">{{ $receipt->reference_number }}</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">{{ $receipt->created_at->format('jS M Y, g:i a') }}</p>
                        </div>
                    </div>
                </div>

                {{-- ── BODY ── --}}
                <div class="px-8 py-8">

                    {{-- Row 1: Member + Payment --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="card-member bg-gradient-to-br from-gray-50 to-white rounded-2xl p-6 border border-gray-100">
                            <div class="flex items-center gap-2 mb-4">
                                <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Member Information</h3>
                            </div>
                            <p class="text-lg font-bold text-gray-900">{{ $receipt->user->name }}</p>
                            <p class="text-sm text-gray-500 mt-1">{{ $receipt->user->email ?? '' }}</p>
                            <p class="text-xs text-gray-400 mt-3">Member ID: #{{ $receipt->user->id }}</p>
                        </div>

                        <div class="card-payment bg-gradient-to-br from-gray-50 to-white rounded-2xl p-6 border border-gray-100">
                            <div class="flex items-center gap-2 mb-4">
                                <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                    </svg>
                                </div>
                                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Payment Details</h3>
                            </div>
                            <div class="flex justify-between items-end">
                                <div>
                                    <p class="text-sm text-gray-600">{{ $receipt->payment_method }}</p>
                                    <p class="text-xs text-gray-400 mt-1">{{ $receipt->paid_at ? $receipt->paid_at->format('M d, Y h:i A') : 'N/A' }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-2xl font-black text-gray-900">UGX {{ number_format($receipt->amount, 0) }}</p>
                                    <div class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 bg-green-100 rounded-full">
                                        <svg class="w-3 h-3 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="text-xs font-semibold text-green-700">Paid</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Row 2: Class + Schedule --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="border border-gray-100 rounded-2xl p-6">
                            <div class="flex items-center gap-2 mb-4">
                                <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                </div>
                                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Class Details</h3>
                            </div>
                            <p class="text-lg font-bold text-gray-900">{{ $receipt->scheduledClass->classType->name ?? 'N/A' }}</p>
                            <div class="flex items-center gap-3 mt-2">
                                <span class="text-xs text-gray-500">{{ $receipt->scheduledClass->classType->minutes ?? 0 }} minutes</span>
                                <span class="text-xs text-gray-300">•</span>
                                <span class="text-xs text-gray-500">Level: All Levels</span>
                            </div>
                            @if($receipt->scheduledClass->classType->description ?? false)
                                <p class="text-xs text-gray-500 mt-3">{{ Str::limit($receipt->scheduledClass->classType->description, 100) }}</p>
                            @endif
                        </div>

                        <div class="border border-gray-100 rounded-2xl p-6">
                            <div class="flex items-center gap-2 mb-4">
                                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Schedule</h3>
                            </div>
                            <p class="text-base font-semibold text-gray-800">
                                {{ $receipt->scheduledClass->date_time ? $receipt->scheduledClass->date_time->format('l, jS M Y') : 'N/A' }}
                            </p>
                            <p class="text-lg font-bold text-purple-600 mt-1">
                                {{ $receipt->scheduledClass->date_time ? $receipt->scheduledClass->date_time->format('g:i A') : 'N/A' }}
                            </p>
                            <div class="flex items-center gap-2 mt-3 pt-3 border-t border-gray-100">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <span class="text-sm text-gray-600">{{ $receipt->scheduledClass->instructor->name ?? 'TBA' }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- QR & Verification --}}
                    <div class="qr-strip rounded-2xl p-5 mb-6">
                        <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                            <div class="flex items-center gap-4">
                                @php
                                    $receiptUrl = route('receipts.show', $receipt->id);
                                    $qrCodeUrl  = 'https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=' . urlencode($receiptUrl);
                                @endphp
                                <div class="w-20 h-20 bg-white rounded-xl flex items-center justify-center shadow-sm p-2">
                                    <img src="{{ $qrCodeUrl }}" alt="QR Code" class="w-full h-full object-contain"
                                         onerror="this.src='https://chart.googleapis.com/chart?chs=120x120&cht=qr&chl={{ urlencode($receiptUrl) }}&chld=L|1';">
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wider">Verify Receipt</p>
                                    <p class="text-sm font-semibold text-gray-800 mt-1">Scan QR code to verify</p>
                                    <p class="text-xs text-gray-400 mt-1">or use reference number</p>
                                </div>
                            </div>
                            <div class="text-center md:text-right">
                                <p class="text-xs text-gray-500">Reference Number</p>
                                <p class="text-sm font-mono font-bold text-gray-800">{{ $receipt->reference_number }}</p>
                                <div class="mt-2">
                                    <span class="inline-flex items-center gap-1 px-3 py-1 bg-green-100 rounded-full">
                                        <svg class="w-3 h-3 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="text-xs font-semibold text-green-700">Verified</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="text-center pt-4 border-t border-gray-100">
                        <p class="text-xs text-gray-400">This is a computer-generated receipt and requires no signature.</p>
                        <p class="text-xs text-gray-400 mt-1">© {{ date('Y') }} MyGym - All rights reserved.</p>
                        <p class="text-xs text-gray-400 mt-2">For inquiries, contact support@mygym.com</p>
                    </div>
                </div>
            </div>{{-- end #receipt-card --}}

        </div>
    </div>

    <footer class="bg-gradient-to-r from-gray-900 to-gray-800 border-t border-purple-500/30">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 gap-8 md:grid-cols-4 lg:grid-cols-5">
                {{-- Column 1: Logo/Brand Info --}}
                <div class="col-span-2 md:col-span-1 lg:col-span-2">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-gradient-to-br from-purple-600 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
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

                {{-- Column 2: Quick Links --}}
                <div>
                    <h5 class="text-lg font-semibold text-white mb-4">Quick Links</h5>
                    <ul class="space-y-3">
                        <li><a href="{{ route('member.dashboard') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">Dashboard</a></li>
                        <li><a href="{{ route('classes.index') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">Browse Classes</a></li>
                        <li><a href="{{ route('member.bookings.index') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">My Bookings</a></li>
                        <li><a href="{{ route('profile.edit') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">Manage Profile</a></li>
                    </ul>
                </div>

                {{-- Column 3: Classes --}}
                <div>
                    <h5 class="text-lg font-semibold text-white mb-4">Popular Classes</h5>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">Pilates</a></li>
                        <li><a href="#" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">Yoga</a></li>
                        <li><a href="#" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">Dance Fitness</a></li>
                        <li><a href="#" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">Boxing</a></li>
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
                            <span><a href="mailto:info@mygym.com" class="hover:text-purple-400">info@mygym.com</a></span>
                        </li>
                    </ul>
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
        /* Screen: QR strip background */
        .qr-strip {
            background: linear-gradient(to right, #f9fafb, #f3f4f6);
        }
        /* Screen: receipt header gradient */
        .receipt-header {
            background: linear-gradient(135deg, #f8fafc 0%, #ffffff 50%, #f8fafc 100%);
        }

        /* ════════════════════════════════════════
           PRINT — visibility trick:
           hide the whole body, then reveal only
           #receipt-card and everything inside it.
           This is the most reliable cross-browser
           approach and avoids blank-page bugs.
           ════════════════════════════════════════ */
        @media print {
            @page {
                size: A4 portrait;
                margin: 1cm;
            }

            /* Force colours & backgrounds to print */
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            /* 1. Make everything invisible */
            body * {
                visibility: hidden !important;
            }

            /* 2. Make the receipt and its children visible */
            #receipt-card,
            #receipt-card * {
                visibility: visible !important;
            }

            /* 3. Pull the card to the top-left corner of the page */
            #receipt-card {
                position: absolute !important;
                top: 0 !important;
                left: 0 !important;
                width: 100% !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                overflow: visible !important;
            }

            /* Hide the nav buttons (they are inside body so need explicit hide) */
            .no-print {
                display: none !important;
                visibility: hidden !important;
            }

            /* ── Named element colours ── */
            .receipt-header {
                background: linear-gradient(135deg, #f8fafc 0%, #ffffff 50%, #f8fafc 100%) !important;
                border-bottom: 1px solid #eef2f8 !important;
            }
            .deco-tr { background: #ede9fe !important; }
            .deco-bl { background: #e0e7ff !important; }
            .card-member,
            .card-payment { background: #f8fafc !important; }
            .qr-strip     { background: #f1f5f9 !important; }

            /* ── Tailwind colour overrides ── */
            .bg-purple-100  { background-color: #f5f3ff !important; }
            .bg-emerald-100 { background-color: #ecfdf5 !important; }
            .bg-orange-100  { background-color: #fff7ed !important; }
            .bg-blue-100    { background-color: #eff6ff !important; }
            .bg-green-100   { background-color: #dcfce7 !important; }
            .bg-gray-100    { background-color: #f3f4f6 !important; }

            .text-purple-600  { color: #7c3aed !important; }
            .text-emerald-600 { color: #059669 !important; }
            .text-orange-600  { color: #ea580c !important; }
            .text-blue-600    { color: #2563eb !important; }
            .text-green-600,
            .text-green-700   { color: #15803d !important; }
            .text-gray-900    { color: #0f172a !important; }
            .text-gray-800    { color: #1e293b !important; }
            .text-gray-700    { color: #374151 !important; }
            .text-gray-600    { color: #475569 !important; }
            .text-gray-500    { color: #64748b !important; }
            .text-gray-400    { color: #94a3b8 !important; }
            .text-gray-300    { color: #cbd5e1 !important; }

            /* ── Layout ── */
            .grid            { display: grid !important; }
            .md\:grid-cols-2 { grid-template-columns: 1fr 1fr !important; }
            .gap-6           { gap: 14px !important; }
            .mb-6            { margin-bottom: 12px !important; }
            .shadow-xl,
            .shadow-md,
            .shadow-sm       { box-shadow: none !important; }
        }
    </style>
</x-app-layout>
