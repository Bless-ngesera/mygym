<x-guest-layout>
    <div class="fixed inset-0">
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

            .login-btn {
                /* Direction (to bottom right), Start Color, End Color */
                background: linear-gradient(135deg, #7e22ce 0%, #1e293b 100%);

                /* Keep your existing styles */
                transition: all 0.2s ease;
                box-shadow: 0 10px 15px -3px rgba(126, 34, 206, 0.3);

                /* Important: Ensure text is readable */
                color: white;
                border: none;
                cursor: pointer;
            }
            .login-btn:hover {
                 /* Direction (to bottom right), Start Color, End Color */
                background: linear-gradient(135deg, #1e293b 0%, #7e22ce 100%);

                /* Keep your existing styles */
                transition: all 0.2s ease;
                box-shadow: 0 20px 25px -5px rgba(126, 34, 206, 0.4);

                /* Important: Ensure text is readable */
                color: white;
                border: none;
                cursor: pointer;
                transform: translateY(-2px);
            }
            .login-btn:active { transform: translateY(0); }

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

            .forgot-link {
                color: #7e22ce;
                font-size: 0.82rem;
                font-weight: 500;
                transition: color 0.15s;
            }
            .forgot-link:hover { color: #6b21a8; text-decoration: underline; }

            .signup-link {
                color: #7e22ce;
                font-weight: 600;
                transition: color 0.15s;
            }
            .signup-link:hover { color: #6b21a8; text-decoration: underline; }

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
        <div class="absolute inset-0 bg-cover bg-center"
             style="background-image: url('{{ asset('images/background2.jpg') }}');"></div>

        <!-- Simple dark overlay like welcome page -->
        <div class="absolute inset-0 bg-black/30"></div>

        <!-- Centered Content -->
        <div class="relative flex items-center justify-center min-h-screen px-4">

            <div class="card-glass card-wrap rounded-2xl p-8 w-full max-w-md">

                {{-- Brand - Clean like welcome page --}}
                <div class="text-center mb-8">
                    <h1 class="text-5xl font-extrabold tracking-tight">
                        <span class="text-purple-700">My</span><span class="text-gray-900">Gym</span>
                    </h1>
                    <p class="mt-2 text-sm text-gray-600">
                        Sign in to your fitness dashboard
                    </p>
                </div>

                <!-- Session Status -->
                <x-auth-session-status class="mb-4 text-sm text-emerald-700 bg-emerald-50 border border-emerald-200 p-3 rounded-xl" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email -->
                    <div class="mb-4">
                        <label for="email" class="block label-text mb-1.5">Email</label>
                        <div class="relative">
                            <span class="input-icon">
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </span>
                            <x-text-input id="email"
                                class="input-field w-full pl-9 pr-4 py-2.5 rounded-xl text-sm"
                                type="email"
                                name="email"
                                :value="old('email')"
                                required
                                autofocus
                                placeholder="your@email.com" />
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs text-red-500" />
                    </div>

                    <!-- Password -->
                    <div class="mb-5">
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="label-text">Password</label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="forgot-link text-xs">Forgot?</a>
                            @endif
                        </div>
                        <div class="relative">
                            <span class="input-icon">
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </span>
                            <x-text-input id="password"
                                class="input-field w-full pl-9 pr-4 py-2.5 rounded-xl text-sm"
                                type="password"
                                name="password"
                                required
                                placeholder="••••••••" />
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs text-red-500" />
                    </div>

                    <!-- Remember Me - Simplified -->
                    <div class="flex items-center mb-6">
                        <input type="checkbox" id="remember" name="remember"
                               class="w-4 h-4 rounded border-gray-300 text-purple-700 focus:ring-purple-300">
                        <label for="remember" class="ml-2 text-sm text-gray-600 cursor-pointer select-none">
                            Remember me
                        </label>
                    </div>

                    <!-- Login Button - Purple like welcome page -->
                    <button type="submit"
                            class="login-btn w-full py-2.5 text-white text-sm font-semibold rounded-xl tracking-wide uppercase">
                        Log In
                    </button>

                    <!-- Sign Up - Clean link -->
                    <p class="mt-5 text-center text-sm text-gray-600">
                        New to MyGym?
                        <a href="{{ route('register') }}" class="signup-link ml-1 font-semibold">Create account →</a>
                    </p>
                </form>

                {{-- Simple Footer --}}
                <div class="mt-7 pt-5 border-t divider-line text-center">
                    <p class="text-xs text-gray-400">
                        &copy; {{ date('Y') }} MyGym. All rights reserved.
                    </p>
                </div>
            </div>

        </div>
    </div>
</x-guest-layout>
