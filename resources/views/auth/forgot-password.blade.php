<x-guest-layout>
    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * { font-family: 'Inter', sans-serif; }

        .card-glass {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .input-field {
            background: #ffffff !important;
            border: 1.5px solid #e2e8f0;
            color: #1e293b;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .input-field:focus {
            border-color: #7e22ce;
            box-shadow: 0 0 0 3px rgba(126, 34, 206, 0.12);
            outline: none;
        }
        .input-field::placeholder { color: #a0aec0; }

        .reset-btn {
            background: linear-gradient(135deg, #7e22ce 0%, #1e293b 100%);
            transition: all 0.2s ease;
            box-shadow: 0 10px 15px -3px rgba(126, 34, 206, 0.3);
            color: white;
            border: none;
            cursor: pointer;
        }
        .reset-btn:hover {
            background: linear-gradient(135deg, #1e293b 0%, #7e22ce 100%);
            box-shadow: 0 20px 25px -5px rgba(126, 34, 206, 0.4);
            transform: translateY(-2px);
        }
        .reset-btn:active { transform: translateY(0); }

        .card-wrap {
            transition: box-shadow 0.2s, transform 0.2s;
        }
        .card-wrap:hover {
            box-shadow: 0 30px 60px -15px rgba(0, 0, 0, 0.3);
            transform: translateY(-4px);
        }

        .label-text {
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: #4b5563;
        }

        .back-link {
            color: #7e22ce;
            font-weight: 600;
            transition: color 0.15s;
        }
        .back-link:hover { color: #6b21a8; text-decoration: underline; }

        .divider-line {
            border-color: #e5e7eb;
        }

        .input-icon {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            left: 12px;
            display: flex;
            align-items: center;
            pointer-events: none;
        }

        @keyframes fadeOut {
            from { opacity: 1; transform: translateY(0); }
            to { opacity: 0; transform: translateY(-10px); }
        }
        .fade-out {
            animation: fadeOut 0.5s ease-out forwards;
        }
    </style>

    <!-- Background Image -->
    <div class="fixed inset-0 bg-cover bg-center"
         style="background-image: url('{{ asset('images/background2.jpg') }}');"></div>

    <!-- Simple dark overlay -->
    <div class="fixed inset-0 bg-black/30"></div>

    <!-- Centered Content - EXACT SAME WIDTH AND POSITIONING AS LOGIN PAGE -->
    <div class="relative flex items-center justify-center min-h-screen px-4">
        <div class="card-glass card-wrap rounded-2xl p-8 w-full max-w-md">

            {{-- Brand - Logo instead of text (EXACT SAME AS LOGIN PAGE) --}}
            <div class="text-center mb-8">
                <div class="flex justify-center mb-3">
                    <img src="{{ asset('images/Project_Logo.png') }}"
                         alt="MyGym Logo"
                         class="h-16 w-auto object-contain">
                </div>
                <p class="text-sm text-gray-600">
                    Reset your password
                </p>
            </div>

            <!-- Description - SAME SPACING AS LOGIN PAGE -->
            <div class="mb-6 p-4 bg-purple-50/50 border border-purple-100 rounded-xl text-sm text-gray-600 leading-relaxed">
                <div class="flex gap-3">
                    <svg class="h-5 w-5 text-purple-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>
                        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
                    </span>
                </div>
            </div>

            <!-- Session Status with Auto-Dismiss -->
            @if(session('status'))
                <div id="successMessage" class="mb-4 text-sm text-emerald-700 bg-emerald-50 border border-emerald-200 p-3 rounded-xl">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <!-- Email Address - SAME SPACING AS LOGIN PAGE -->
                <div class="mb-6">
                    <label for="email" class="block label-text mb-1.5">Email Address</label>
                    <div class="relative">
                        <span class="input-icon">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </span>
                        <input id="email"
                            class="input-field w-full pl-9 pr-4 py-2.5 rounded-xl text-sm"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            placeholder="your@email.com" />
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-xs text-red-500" />
                </div>

                <!-- Reset Button - SAME AS LOGIN BUTTON -->
                <button type="submit"
                        class="reset-btn w-full py-2.5 text-white text-sm font-semibold rounded-xl tracking-wide uppercase">
                    Send Reset Link
                </button>

                <!-- Back to Login - SAME STYLING AS LOGIN PAGE -->
                <p class="mt-5 text-center text-sm text-gray-600">
                    <a href="{{ route('login') }}" class="back-link font-semibold">
                        ← Back to login
                    </a>
                </p>
            </form>

            {{-- Footer - EXACT SAME AS LOGIN PAGE --}}
            <div class="mt-7 pt-5 border-t divider-line text-center">
                <div class="flex justify-center gap-6 mb-3">
                    <a href="#" class="text-xs text-gray-400 hover:text-purple-600 transition-colors">About</a>
                    <a href="#" class="text-xs text-gray-400 hover:text-purple-600 transition-colors">Terms</a>
                    <a href="#" class="text-xs text-gray-400 hover:text-purple-600 transition-colors">Privacy</a>
                    <a href="#" class="text-xs text-gray-400 hover:text-purple-600 transition-colors">Contact</a>
                </div>
                <p class="text-xs text-gray-400">
                    &copy; {{ date('Y') }} MyGym. All rights reserved.
                </p>
            </div>
        </div>
    </div>

    <script>
        // Auto-dismiss success message after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const successMessage = document.getElementById('successMessage');
            if (successMessage) {
                setTimeout(function() {
                    successMessage.style.opacity = '0';
                    successMessage.style.transform = 'translateY(-10px)';
                    successMessage.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                    setTimeout(function() {
                        if (successMessage.parentNode) {
                            successMessage.remove();
                        }
                    }, 500);
                }, 5000);
            }
        });
    </script>
</x-guest-layout>
