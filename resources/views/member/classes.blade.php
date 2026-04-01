<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Available Classes
                </h2>
                <p class="text-sm text-gray-500 mt-1">Browse and book fitness classes</p>
            </div>
            <a href="{{ route('member.bookings') }}"
               class="px-4 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white rounded-xl text-sm font-semibold transition-all duration-200 shadow-md hover:shadow-lg">
                My Bookings
            </a>
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

            {{-- Error Message --}}
            @if(session('error'))
                <div id="errorMessage" class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-xl shadow-md">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            {{-- Validation Errors --}}
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

            {{-- Classes Count --}}
            <div class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl shadow-2xl ring-1 ring-white/30 overflow-hidden p-4 mb-4 text-sm text-gray-500">
                Showing <span class="font-semibold text-purple-600">{{ isset($classes) && $classes instanceof \Illuminate\Pagination\LengthAwarePaginator ? $classes->total() : 0 }}</span> available class(es)
            </div>

            <div class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl shadow-2xl ring-1 ring-white/30 overflow-hidden">
                <div class="p-6 md:p-8">

                    {{-- Classes Grid --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse(($classes ?? []) as $class)
                        <div class="bg-white/80 backdrop-blur-sm border border-white/40 rounded-2xl p-5 shadow-lg hover:shadow-xl transition-all duration-200 hover:-translate-y-1">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-md">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-gray-900 text-lg">{{ optional($class->classType)->name ?? 'Class' }}</h3>
                                        <p class="text-xs text-gray-500">{{ optional($class->classType)->minutes ?? 0 }} minutes</p>
                                    </div>
                                </div>
                                <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">Available</span>
                            </div>

                            <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ optional($class->classType)->description ?? 'No description available' }}</p>

                            <div class="space-y-2 pt-3 border-t border-gray-100">
                                <div class="flex items-center gap-2 text-sm text-gray-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <span class="font-medium">Instructor:</span>
                                    <span>{{ optional($class->instructor)->name ?? 'TBA' }}</span>
                                </div>
                                <div class="flex items-center gap-2 text-sm text-gray-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <span class="font-medium">Date:</span>
                                    <span>{{ optional($class->date_time)->format('l, M d, Y') ?? 'TBA' }}</span>
                                </div>
                                <div class="flex items-center gap-2 text-sm text-gray-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="font-medium">Time:</span>
                                    <span>{{ optional($class->date_time)->format('h:i A') ?? 'TBA' }}</span>
                                </div>
                                <div class="flex items-center gap-2 text-sm text-gray-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="font-medium">Price:</span>
                                    <span class="font-semibold text-emerald-600">UGX {{ number_format($class->price ?? 0, 0) }}</span>
                                </div>
                                <div class="flex items-center gap-2 text-sm text-gray-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    <span class="font-medium">Booked:</span>
                                    <span>{{ $class->members_count ?? 0 }} / {{ optional($class->classType)->capacity ?? '∞' }}</span>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="button"
                                        data-id="{{ $class->id }}"
                                        data-name="{{ optional($class->classType)->name }}"
                                        data-price="{{ $class->price ?? 10000 }}"
                                        class="open-payment w-full px-4 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white rounded-lg text-sm font-semibold transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                                    <span class="flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Book This Class
                                    </span>
                                </button>
                            </div>
                        </div>
                        @empty
                        <div class="col-span-full text-center py-12">
                            <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <p class="text-gray-500 text-lg mb-2">No classes available</p>
                            <p class="text-gray-400 text-sm mb-4">There are no upcoming classes at the moment.</p>
                            <a href="{{ route('member.dashboard') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white rounded-xl font-semibold transition-all duration-200 shadow-md hover:shadow-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                </svg>
                                Go to Dashboard
                            </a>
                        </div>
                        @endforelse
                    </div>

                    {{-- Pagination --}}
                    @if(isset($classes) && $classes instanceof \Illuminate\Pagination\LengthAwarePaginator && $classes->hasPages())
                        <div class="mt-8 pt-4 border-t border-gray-100">
                            {{ $classes->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Payment Modal with Standard Form --}}
    <div id="paymentModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm">
        <form id="bookingForm" method="POST" action="{{ route('member.book') }}" class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl transform transition-all">
            @csrf
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-900">Confirm Booking</h3>
                <button type="button" id="cancelPaymentBtn" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <p id="modalClassName" class="text-gray-600 mb-2 font-medium text-lg"></p>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Price</label>
                <div id="modalPrice" class="text-2xl font-bold text-purple-700">UGX 0</div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                <div class="space-y-2">
                    <label class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-50 cursor-pointer">
                        <input type="radio" name="payment_method" value="MTN Mobile Money" checked class="text-purple-600 focus:ring-purple-500">
                        <span class="text-sm">MTN Mobile Money (Uganda)</span>
                    </label>
                    <label class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-50 cursor-pointer">
                        <input type="radio" name="payment_method" value="Airtel Money" class="text-purple-600 focus:ring-purple-500">
                        <span class="text-sm">Airtel Money</span>
                    </label>
                    <label class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-50 cursor-pointer">
                        <input type="radio" name="payment_method" value="PayPal" class="text-purple-600 focus:ring-purple-500">
                        <span class="text-sm">PayPal</span>
                    </label>
                </div>
            </div>

            <div id="paymentContactWrap" class="mb-4 hidden">
                <label id="paymentContactLabel" class="block text-sm font-medium text-gray-700 mb-2">Mobile Number</label>
                <input id="paymentContactInput" name="payment_contact" type="tel" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500" placeholder="+256 7XX XXX XXX">
                <p id="paymentContactHelp" class="mt-2 text-xs text-gray-500">Enter the mobile money number to receive a confirmation SMS.</p>
            </div>

            {{-- Hidden input for class ID - CRITICAL for booking --}}
            <input type="hidden" name="scheduled_class_id" id="scheduled_class_id" value="">

            <div class="flex justify-end gap-3 mt-6">
                <button type="button" id="closePaymentModal" class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold transition">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-semibold transition shadow-md">Confirm Payment</button>
            </div>
        </form>
    </div>

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
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>

    <script>
        (function() {
            // DOM elements
            let currentClassId = null;

            // Handle payment method change
            function handleMethodChange(method) {
                const contactWrap = document.getElementById('paymentContactWrap');
                const contactLabel = document.getElementById('paymentContactLabel');
                const contactInput = document.getElementById('paymentContactInput');
                const contactHelp = document.getElementById('paymentContactHelp');

                if (!contactWrap) return;

                if (method === 'MTN Mobile Money' || method === 'Airtel Money') {
                    contactWrap.classList.remove('hidden');
                    contactLabel.textContent = 'Mobile Number';
                    contactInput.type = 'tel';
                    contactInput.placeholder = '+256 7XX XXX XXX';
                    if (contactHelp) contactHelp.textContent = 'Enter the mobile money number to receive a confirmation SMS.';
                } else if (method === 'PayPal') {
                    contactWrap.classList.remove('hidden');
                    contactLabel.textContent = 'PayPal Email';
                    contactInput.type = 'email';
                    contactInput.placeholder = 'you@example.com';
                    if (contactHelp) contactHelp.textContent = 'Enter the PayPal email for payment confirmation.';
                } else {
                    contactWrap.classList.add('hidden');
                }
            }

            // Open modal when clicking book button
            document.querySelectorAll('.open-payment').forEach(btn => {
                btn.addEventListener('click', () => {
                    currentClassId = btn.getAttribute('data-id');
                    const name = btn.getAttribute('data-name');
                    const price = parseFloat(btn.getAttribute('data-price') || 0);

                    const modalClassName = document.getElementById('modalClassName');
                    const modalPrice = document.getElementById('modalPrice');
                    const contactInput = document.getElementById('paymentContactInput');
                    const contactWrap = document.getElementById('paymentContactWrap');
                    const hiddenClassId = document.getElementById('scheduled_class_id');

                    if (modalClassName) modalClassName.textContent = name;
                    if (modalPrice) modalPrice.textContent = 'UGX ' + price.toLocaleString();
                    if (contactInput) contactInput.value = '';
                    if (contactWrap) contactWrap.classList.add('hidden');

                    // Set the hidden input value with the class ID
                    if (hiddenClassId) {
                        hiddenClassId.value = currentClassId;
                        console.log('Class ID set to:', hiddenClassId.value); // Debug log
                    }

                    const defaultMethod = document.querySelector('input[name="payment_method"]:checked')?.value || 'MTN Mobile Money';
                    handleMethodChange(defaultMethod);

                    const modal = document.getElementById('paymentModal');
                    if (modal) {
                        modal.classList.remove('hidden');
                        modal.classList.add('flex');
                        document.body.style.overflow = 'hidden';
                    }
                });
            });

            // Add form submit validation
            const bookingForm = document.getElementById('bookingForm');
            if (bookingForm) {
                bookingForm.addEventListener('submit', function(e) {
                    const hiddenInput = document.getElementById('scheduled_class_id');
                    if (!hiddenInput || !hiddenInput.value) {
                        e.preventDefault();
                        alert('Please select a class first.');
                        return false;
                    }
                    console.log('Submitting booking for class ID:', hiddenInput.value);
                });
            }

            // Payment method radio listeners
            const paymentRadios = document.querySelectorAll('input[name="payment_method"]');
            paymentRadios.forEach(r => r.addEventListener('change', (e) => handleMethodChange(e.target.value)));

            // Close modal functions
            function closeModal() {
                const modal = document.getElementById('paymentModal');
                if (modal) {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    document.body.style.overflow = '';
                }
            }

            const cancelBtn = document.getElementById('closePaymentModal');
            const cancelPaymentBtn = document.getElementById('cancelPaymentBtn');

            if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
            if (cancelPaymentBtn) cancelPaymentBtn.addEventListener('click', closeModal);

            // Click outside to close
            const modal = document.getElementById('paymentModal');
            if (modal) {
                modal.addEventListener('click', (e) => {
                    if (e.target === modal) closeModal();
                });
            }

            // Auto-dismiss messages after 5 seconds
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
        })();
    </script>
</x-app-layout>
