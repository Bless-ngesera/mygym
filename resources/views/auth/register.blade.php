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

        .register-btn {
            background: linear-gradient(135deg, #7e22ce 0%, #1e293b 100%);
            transition: all 0.2s ease;
            box-shadow: 0 10px 15px -3px rgba(126, 34, 206, 0.3);
            color: white;
            border: none;
            cursor: pointer;
        }
        .register-btn:hover {
            background: linear-gradient(135deg, #1e293b 0%, #7e22ce 100%);
            box-shadow: 0 20px 25px -5px rgba(126, 34, 206, 0.4);
            transform: translateY(-2px);
        }
        .register-btn:active { transform: translateY(0); }

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

        .login-link {
            color: #7e22ce;
            font-weight: 600;
            transition: color 0.15s;
        }
        .login-link:hover { color: #6b21a8; text-decoration: underline; }

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
    </style>

    <!-- Background Image -->
    <div class="fixed inset-0 bg-cover bg-center"
         style="background-image: url('{{ asset('images/background2.jpg') }}');"></div>

    <!-- Simple dark overlay -->
    <div class="fixed inset-0 bg-black/30"></div>

    <!-- Centered Content - EXACT SAME WIDTH AS LOGIN PAGE (max-w-md) -->
    <div class="relative flex items-center justify-center min-h-screen px-4">
        <div class="card-glass card-wrap rounded-2xl p-8 w-full max-w-md">

            {{-- Brand - Logo (EXACT SAME AS LOGIN PAGE) --}}
            <div class="text-center mb-8">
                <div class="flex justify-center mb-3">
                    <img src="{{ asset('images/Project_Logo.png') }}"
                         alt="MyGym Logo"
                         class="h-16 w-auto object-contain">
                </div>
                <p class="text-sm text-gray-600">
                    Create your fitness account
                </p>
            </div>

            <!-- Validation Errors -->
            @if ($errors->any())
                <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-xl">
                    <div class="flex items-center gap-2 mb-1">
                        <svg class="h-4 w-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-xs font-semibold text-red-700">Please fix the following errors:</span>
                    </div>
                    <ul class="list-disc list-inside text-xs text-red-600">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Name - SAME SPACING AS LOGIN PAGE -->
                <div class="mb-4">
                    <label for="name" class="block label-text mb-1.5">Full Name</label>
                    <div class="relative">
                        <span class="input-icon">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </span>
                        <input id="name"
                            class="input-field w-full pl-9 pr-4 py-2.5 rounded-xl text-sm"
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            required
                            autofocus
                            autocomplete="name"
                            placeholder="Blessing Ngesera" />
                    </div>
                </div>

                <!-- Email Address - SAME SPACING AS LOGIN PAGE -->
                <div class="mb-4">
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
                            autocomplete="username"
                            placeholder="blessingngesera@email.com" />
                    </div>
                </div>

                <!-- Password - SAME SPACING AS LOGIN PAGE -->
                <div class="mb-4">
                    <label for="password" class="block label-text mb-1.5">Password</label>
                    <div class="relative">
                        <span class="input-icon">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </span>
                        <input id="password"
                            class="input-field w-full pl-9 pr-4 py-2.5 rounded-xl text-sm"
                            type="password"
                            name="password"
                            required
                            autocomplete="new-password"
                            placeholder="••••••••" />
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Minimum 8 characters</p>
                </div>

                <!-- Confirm Password - SAME SPACING AS LOGIN PAGE -->
                <div class="mb-5">
                    <label for="password_confirmation" class="block label-text mb-1.5">Confirm Password</label>
                    <div class="relative">
                        <span class="input-icon">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </span>
                        <input id="password_confirmation"
                            class="input-field w-full pl-9 pr-4 py-2.5 rounded-xl text-sm"
                            type="password"
                            name="password_confirmation"
                            required
                            autocomplete="new-password"
                            placeholder="••••••••" />
                    </div>
                </div>

                <!-- Terms - SAME SPACING AS REMEMBER ME ON LOGIN PAGE -->
                <div class="flex items-start mb-6">
                    <input type="checkbox" id="terms" name="terms" required
                           class="mt-1 w-4 h-4 rounded border-gray-300 text-purple-700 focus:ring-purple-300">
                    <label for="terms" class="ml-2 text-xs text-gray-600 leading-relaxed">
                        I agree to the <a href="#" class="text-purple-700 hover:text-purple-900 font-medium">Terms of Service</a> and
                        <a href="#" class="text-purple-700 hover:text-purple-900 font-medium">Privacy Policy</a>
                    </label>
                </div>

                <!-- Register Button - SAME AS LOGIN BUTTON -->
                <button type="submit"
                        class="register-btn w-full py-2.5 text-white text-sm font-semibold rounded-xl tracking-wide uppercase">
                    Create Account
                </button>

                <!-- Login Link - SAME AS LOGIN PAGE STYLING -->
                <p class="mt-5 text-center text-sm text-gray-600">
                    Already have an account?
                    <a href="{{ route('login') }}" class="login-link ml-1 font-semibold">Sign in →</a>
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
</x-guest-layout>
