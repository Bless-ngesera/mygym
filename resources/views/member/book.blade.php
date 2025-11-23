<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            Book a Class
        </h2>
    </x-slot>

    {{-- Show payment success message --}}
    @if(session('success'))
        <div class="max-w-2xl mx-auto mt-6 mb-4 px-4 py-3 bg-green-100 text-green-800 rounded-lg shadow text-center font-semibold">
            {{ session('success') }}
        </div>
    @endif

    <div class="py-12 bg-gray-50 min-h-screen"
        style="background-image: url('{{ asset('images/background2.jpg') }}'); 
        background-size: cover; 
        background-position: center; 
        background-attachment: fixed;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @forelse ($scheduledClasses as $class)
                <div class="bg-white/75 backdrop-blur-sm border border-white/20 rounded-2xl shadow-2xl ring-1 ring-white/30 overflow-hidden p-8">
                    
                    <div class="flex flex-col md:flex-row justify-between gap-6">
                        <!-- Class Info -->
                        <div>
                            <p class="text-2xl font-semibold text-purple-700">{{ $class->classType->name }}</p>
                            <p class="text-sm text-gray-600">{{ $class->instructor->name }}</p>
                            <p class="mt-2 text-gray-700">{{ $class->classType->description }}</p>
                            <span class="text-gray-500 text-sm mt-1 block">{{ $class->classType->minutes }} minutes</span>
                        </div>

                        <!-- Date & Time -->
                        <div class="text-right flex-shrink-0">
                            <p class="text-lg font-bold text-gray-800">{{ $class->date_time->format('g:i a') }}</p>
                            <p class="text-sm text-gray-500">{{ $class->date_time->format('jS M') }}</p>
                        </div>
                    </div>

                    <!-- Booking Button -->
                    <div class="mt-4 text-right">
                        <form method="post" action="{{ route('booking.store') }}">
                            @csrf
                            {{-- Replace submission form/button with modal-opening button --}}
                            <button 
                                type="button"
                                data-id="{{ $class->id }}"
                                data-name="{{ $class->classType->name }}"
                                data-price="{{ $class->classType->price ?? 10000 }}"
                                class="open-payment px-4 py-2 bg-purple-600 hover:bg-purple-700 rounded-lg text-white font-semibold transition">
                                Book Class
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <!-- Empty State -->
                <div class="bg-white shadow-lg rounded-xl p-6 text-center space-y-4">
                    <p class="text-gray-700 text-lg font-medium">No classes are currently scheduled.</p>
                    <p class="text-gray-500">Please check back later or add a new class schedule.</p>
                    <a href="{{ route('schedule.create') }}" 
                       class="inline-block bg-purple-600 hover:bg-purple-700 text-white font-semibold px-5 py-2 rounded-lg transition">
                        Create a Schedule
                    </a>
                </div>
            @endforelse

        </div>
    </div>

    {{-- Payment Modal --}}
    <div id="paymentModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">
        <div class="bg-white rounded-xl max-w-lg w-full p-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-2">Confirm Payment</h3>
            <p id="modalClassName" class="text-gray-600 mb-4"></p>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Price</label>
                <div id="modalPrice" class="text-2xl font-bold text-gray-900">UGX 0</div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                <div class="space-y-2">
                    <label class="flex items-center space-x-2">
                        <input type="radio" name="payment_method" value="MTN Mobile Money" checked>
                        <span class="text-sm">MTN Mobile Money (Uganda)</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="radio" name="payment_method" value="Airtel Money">
                        <span class="text-sm">Airtel Money</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="radio" name="payment_method" value="PayPal">
                        <span class="text-sm">PayPal</span>
                    </label>
                </div>
            </div>

            {{-- Payment contact (phone or email) --}}
            <div id="paymentContactWrap" class="mb-4 hidden">
                <label id="paymentContactLabel" class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                <input id="paymentContactInput" type="text" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="">
                <p id="paymentContactHelp" class="mt-2 text-xs text-gray-400 hidden"></p>
            </div>

            <div id="paymentStatus" class="text-sm text-gray-600 mb-4 hidden">Processing payment...</div>

            <div class="flex justify-end gap-3">
                <button id="cancelPayment" class="px-4 py-2 rounded-lg bg-gray-200">Cancel</button>
                <button id="confirmPayment" class="px-4 py-2 rounded-lg bg-purple-600 text-white font-semibold">Pay</button>
            </div>
        </div>
    </div>

    {{-- Payment Success / Redirect will be handled after POST --}}
    <script>
        (function(){
            const openButtons = document.querySelectorAll('.open-payment');
            const modal = document.getElementById('paymentModal');
            const modalClassName = document.getElementById('modalClassName');
            const modalPrice = document.getElementById('modalPrice');
            const paymentStatus = document.getElementById('paymentStatus');
            const cancelBtn = document.getElementById('cancelPayment');
            const confirmBtn = document.getElementById('confirmPayment');

            const contactWrap = document.getElementById('paymentContactWrap');
            const contactLabel = document.getElementById('paymentContactLabel');
            const contactInput = document.getElementById('paymentContactInput');
            const contactHelp = document.getElementById('paymentContactHelp');

            // Ensure CSRF token is available
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
            const storeUrl = "{{ route('receipts.store') }}";

            let currentClassId = null;
            let currentAmount = 0;

            // formatter for UGX display
            const fmt = (n) => {
                try {
                    return new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(n);
                } catch (e) {
                    return Number(n).toFixed(2);
                }
            };

            openButtons.forEach(btn => {
                btn.addEventListener('click', () => {
                    currentClassId = btn.getAttribute('data-id');
                    const name = btn.getAttribute('data-name');
                    currentAmount = parseFloat(btn.getAttribute('data-price') || 0);
                    modalClassName.textContent = name;
                    modalPrice.textContent = 'UGX ' + fmt(currentAmount);
                    paymentStatus.classList.add('hidden');
                    // reset contact input
                    contactInput.value = '';
                    contactHelp.classList.add('hidden');
                    contactWrap.classList.add('hidden');

                    // ensure default method selection -> show contact for mobile money by default
                    const defaultMethod = document.querySelector('input[name="payment_method"]:checked').value;
                    handleMethodChange(defaultMethod);

                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                });
            });

            // handle payment method changes (show contact input and adjust placeholder)
            const paymentRadios = document.querySelectorAll('input[name="payment_method"]');
            paymentRadios.forEach(r => r.addEventListener('change', (e) => handleMethodChange(e.target.value)));

            function handleMethodChange(method) {
                if (!contactWrap) return;
                if (method === 'MTN Mobile Money' || method === 'Airtel Money') {
                    contactWrap.classList.remove('hidden');
                    contactLabel.textContent = 'Mobile Number';
                    contactInput.type = 'tel';
                    contactInput.placeholder = '+256 7XX XXX XXX';
                    contactHelp.textContent = 'Enter the mobile money number to receive a confirmation SMS/payment prompt.';
                    contactHelp.classList.remove('hidden');
                } else if (method === 'PayPal') {
                    contactWrap.classList.remove('hidden');
                    contactLabel.textContent = 'PayPal Email';
                    contactInput.type = 'email';
                    contactInput.placeholder = 'you@example.com';
                    contactHelp.textContent = 'Enter the PayPal email where payment will be associated.';
                    contactHelp.classList.remove('hidden');
                } else {
                    contactWrap.classList.add('hidden');
                    contactHelp.classList.add('hidden');
                }
            }

            cancelBtn.addEventListener('click', () => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            });

            confirmBtn.addEventListener('click', async () => {
                paymentStatus.textContent = 'Processing payment...';
                paymentStatus.classList.remove('hidden');
                confirmBtn.disabled = true;
                cancelBtn.disabled = true;

                // Simulate processing delay
                await new Promise(r => setTimeout(r, 1200));

                // read selected method
                const method = document.querySelector('input[name="payment_method"]:checked').value;

                // validate contact input when shown
                if (!contactWrap.classList.contains('hidden')) {
                    const contactVal = contactInput.value.trim();
                    if (!contactVal) {
                        paymentStatus.textContent = 'Please enter the required contact (phone or PayPal email).';
                        confirmBtn.disabled = false;
                        cancelBtn.disabled = false;
                        return;
                    }
                }

                // Create and submit a standard POST form to let Laravel handle CSRF and redirects
                try {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = storeUrl;
                    form.style.display = 'none';

                    // helper to add hidden inputs
                    const addInput = (name, value) => {
                        const i = document.createElement('input');
                        i.type = 'hidden';
                        i.name = name;
                        i.value = value;
                        form.appendChild(i);
                    };

                    addInput('_token', csrfToken);
                    addInput('scheduled_class_id', currentClassId);
                    addInput('payment_method', method);
                    addInput('amount', currentAmount);
                    // include contact (if present)
                    if (!contactWrap.classList.contains('hidden')) {
                        addInput('payment_contact', contactInput.value.trim());
                    }

                    document.body.appendChild(form);
                    form.submit();
                    // navigation will follow server response; no further UI changes needed here
                } catch (err) {
                    paymentStatus.textContent = 'Payment failed. Please try again.';
                    confirmBtn.disabled = false;
                    cancelBtn.disabled = false;
                    console.error(err);
                }
            });
        })();
    </script>

    <footer class="bg-gray-900 border-t border-indigo-500/30">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 gap-8 md:grid-cols-4 lg:grid-cols-5">
                {{-- Column 1: Logo/Brand Info --}}
                <div class="col-span-2 md:col-span-1 lg:col-span-2">
                    <h4 class="text-2xl font-bold text-white mb-4 tracking-wider">My<span class="text-yellow-400">Gym</span></h4>
                    <p class="text-sm text-gray-400 leading-relaxed">
                        Train smart, stay consistent, and celebrate your growth. We're a community rooted in African strength and unity.
                    </p>
                    <div class="flex space-x-4 mt-4">
                        <a href="#" class="text-gray-400 hover:text-indigo-400 transition duration-300" aria-label="Facebook">
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.77l-.44 2.89h-2.33v6.987A10 10 0 0022 12z" clip-rule="evenodd" /></svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-indigo-400 transition duration-300" aria-label="Instagram">
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M12.315 2c2.43 0 2.715.01 3.67.058 1.036.05 1.745.21 2.37.456.684.276 1.258.74 1.717 1.259.46.52.825 1.094 1.102 1.717.246.625.407 1.334.456 2.37.048.955.058 1.23.058 3.67s-.01 2.715-.058 3.67c-.05.97-.21 1.745-.456 2.37-.276.684-.74 1.258-1.259 1.717-.52.46-1.094.825-1.717 1.102-.625.246-1.334.407-2.37.456-.955.048-1.23.058-3.67.058s-2.715-.01-3.67-.058c-.97-.05-1.745-.21-2.37-.456-.684-.276-1.258-.74-1.717-1.259-.46-.52-.825-1.094-1.102-1.717-.246-.625-.407-1.334-.456-2.37-.048-.955-.058-1.23-.058-3.67s.01-2.715.058-3.67c.05-.97.21-1.745.456-2.37.276-.684.74-1.258 1.259-1.717.46-.52 1.094-.825 1.717-1.102.625-.246 1.334-.407 2.37-.456C9.59 2.01 9.875 2 12.315 2zm0 1.637c-2.35 0-2.6.01-3.535.056-.983.05-1.503.21-1.85.347-.417.164-.78.384-1.095.698-.315.315-.534.678-.698 1.095-.137.347-.297.867-.347 1.85-.046.935-.056 1.185-.056 3.535s.01 2.6.056 3.535c.05.983.21 1.503.347 1.85.164.417.384.78.698 1.095.315.315.678.534 1.095.698.347.137.867.297 1.85.347.935.046 1.185.056 3.535.056s2.6-.01 3.535-.056c.983-.05 1.503-.21 1.85-.347.417-.164.78-.384 1.095-.698.315-.315.534-.678.698-1.095.137-.347.297-.867.347-1.85.046-.935.056-1.185.056-3.535s-.01-2.6-.056-3.535c-.05-.983-.21-1.503-.347-1.85-.164-.417-.384-.78-.698-1.095-.315-.315-.678-.534-1.095-.698-.347-.137-.867-.297-1.85-.347-.935-.046-1.185-.056-3.535-.056zM12.315 5.564c-3.714 0-6.75 3.036-6.75 6.75s3.036 6.75 6.75 6.75 6.75-3.036 6.75-6.75-3.036-6.75-6.75-6.75zm0 11.235c-2.476 0-4.485-2.009-4.485-4.485S9.839 7.828 12.315 7.828s4.485 2.009 4.485 4.485-2.009 4.485-4.485 4.485zm4.991-9.982c-.52 0-.942-.423-.942-.942s.422-.942.942-.942.942.423.942.942-.422.942-.942.942z" clip-rule="evenodd" /></svg>
                        </a>
                    </div>
                </div>

                {{-- Column 2: Quick Links --}}
                <div>
                    <h5 class="text-lg font-semibold text-white mb-4">Quick Links</h5>
                    <ul class="space-y-3">
                        <li><a href="{{ route('member.dashboard') }}" class="text-sm text-gray-400 hover:text-indigo-400 transition duration-300">Dashboard</a></li>
                        <li><a href="{{ route('booking.create') }}" class="text-sm text-gray-400 hover:text-indigo-400 transition duration-300">Book a Class</a></li>
                        <li><a href="{{ route('profile.edit') }}" class="text-sm text-gray-400 hover:text-indigo-400 transition duration-300">Manage Profile</a></li>
                        <li><a href="#" class="text-sm text-gray-400 hover:text-indigo-400 transition duration-300">Contact Support</a></li>
                    </ul>
                </div>

                {{-- Column 3: Classes --}}
                <div>
                    <h5 class="text-lg font-semibold text-white mb-4">Popular Classes</h5>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-sm text-gray-400 hover:text-indigo-400 transition duration-300">Pilates</a></li>
                        <li><a href="#" class="text-sm text-gray-400 hover:text-indigo-400 transition duration-300">Yoga</a></li>
                        <li><a href="#" class="text-sm text-gray-400 hover:text-indigo-400 transition duration-300">Dance Fitness</a></li>
                        <li><a href="#" class="text-sm text-gray-400 hover:text-indigo-400 transition duration-300">Boxing</a></li>
                    </ul>
                </div>

                {{-- Column 4: Contact Info --}}
                <div class="col-span-2 md:col-span-1">
                    <h5 class="text-lg font-semibold text-white mb-4">Get In Touch</h5>
                    <ul class="space-y-3 text-sm text-gray-400">
                        <li class="flex items-start">
                            <span class="mr-2 text-indigo-400">📍</span>
                            <span>Ggaba road, Kampala, UGANDA</span>
                        </li>
                        <li class="flex items-start">
                            <span class="mr-2 text-indigo-400">📞</span>
                            <span>+256 700 123 456</span>
                        </li>
                        <li class="flex items-start">
                            <span class="mr-2 text-indigo-400">📧</span>
                            <span><a href="mailto:info@mygym.com" class="hover:text-indigo-400">info@mygym.com</a></span>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Copyright Section --}}
            <div class="mt-12 pt-8 border-t border-indigo-500/30 text-center">
                <p class="text-sm text-gray-500">
                    &copy; {{ date('Y') }} MyGym. All rights reserved. Powered by Passion.
                </p>
            </div>
        </div>
    </footer>
</x-app-layout>
