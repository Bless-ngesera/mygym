<x-guest-layout>
    <div class="fixed inset-0">
        {{-- Fonts --}}
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

        <style>
            * { font-family: 'DM Sans', sans-serif; }
            .brand-font { font-family: 'Playfair Display', serif; }

            .card-glass {
                background: rgba(255, 255, 255, 0.88);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 255, 255, 0.55);
            }

            .input-field {
                background: #ffffff !important;
                border: 1.5px solid #e2e8f0;
                color: #1e293b;
                transition: border-color 0.2s, box-shadow 0.2s;
            }
            .input-field:focus {
                border-color: #6366f1;
                box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12);
                outline: none;
            }
            .input-field::placeholder { color: #a0aec0; }

            .register-btn {
                background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
                transition: opacity 0.2s, transform 0.2s, box-shadow 0.2s;
                box-shadow: 0 4px 18px rgba(99, 102, 241, 0.38);
            }
            .register-btn:hover {
                opacity: 0.93;
                transform: translateY(-1px);
                box-shadow: 0 8px 24px rgba(99, 102, 241, 0.45);
            }
            .register-btn:active { transform: translateY(0); }

            .card-wrap {
                transition: box-shadow 0.2s, transform 0.2s;
                box-shadow: 0 8px 40px rgba(0,0,0,0.18);
            }
            .card-wrap:hover {
                box-shadow: 0 16px 56px rgba(0,0,0,0.22);
                transform: translateY(-2px);
            }

            .label-text {
                font-size: 0.8rem;
                font-weight: 600;
                letter-spacing: 0.04em;
                text-transform: uppercase;
                color: #475569;
            }

            .divider-line { border-color: #e9edf5; }

            .login-link {
                color: #6366f1;
                font-weight: 600;
                transition: color 0.15s;
            }
            .login-link:hover { color: #4338ca; text-decoration: underline; }

            .accent-orb {
                position: absolute;
                border-radius: 50%;
                filter: blur(72px);
                opacity: 0.22;
                animation: drift 9s ease-in-out infinite alternate;
            }
            @keyframes drift {
                from { transform: translateY(0px) scale(1); }
                to   { transform: translateY(18px) scale(1.06); }
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

            /* Step indicators */
            .step-dot {
                width: 6px; height: 6px;
                border-radius: 50%;
                background: #e2e8f0;
                transition: background 0.3s;
            }
            .step-dot.active { background: #6366f1; width: 18px; border-radius: 3px; }
        </style>

        <!-- Background Image -->
        <div class="absolute inset-0 bg-cover bg-center"
             style="background-image: url('{{ asset('images/background2.jpg') }}');"></div>

        <!-- Overlay -->
        <div class="absolute inset-0"
             style="background: linear-gradient(135deg, rgba(17,24,55,0.55) 0%, rgba(49,30,80,0.42) 100%);"></div>

        <!-- Accent orbs -->
        <div class="accent-orb" style="width:420px;height:420px;background:#6366f1;top:-80px;right:-100px;animation-duration:11s;"></div>
        <div class="accent-orb" style="width:300px;height:300px;background:#8b5cf6;bottom:-60px;left:-60px;animation-duration:8s;animation-delay:1.5s;"></div>

        <!-- Centered Content -->
        <div class="relative flex items-center justify-center min-h-screen px-4 py-10">

            <div class="card-glass card-wrap rounded-2xl p-8 w-full max-w-sm">

                {{-- Brand --}}
                <div class="flex flex-col items-center mb-6">
                    <img src="{{ asset('images/Project_Logo.png') }}" alt="MyGym Logo" class="h-14 w-auto mb-3">
                    <h1 class="brand-font text-2xl text-slate-800 font-semibold tracking-tight text-center">
                        Create your account
                    </h1>
                    <p class="text-slate-500 text-sm mt-1 text-center" style="font-weight:300;">
                        Join MyGym Uganda and start your journey
                    </p>
                </div>

                {{-- Progress dots --}}
                <div class="flex items-center justify-center gap-1.5 mb-6">
                    <div class="step-dot active"></div>
                    <div class="step-dot"></div>
                    <div class="step-dot"></div>
                </div>

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <!-- Full Name -->
                    <div class="mb-4">
                        <label for="name" class="block label-text mb-1.5">Full Name</label>
                        <div class="relative">
                            <span class="input-icon">
                                <svg class="h-4 w-4" style="color:#8b5cf6;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </span>
                            <x-text-input id="name"
                                class="input-field w-full pl-9 pr-4 py-2.5 rounded-xl text-sm"
                                type="text"
                                name="name"
                                :value="old('name')"
                                required
                                autofocus
                                autocomplete="name"
                                placeholder="John Doe" />
                        </div>
                        <x-input-error :messages="$errors->get('name')" class="mt-1.5 text-xs text-red-500" />
                    </div>

                    <!-- Email -->
                    <div class="mb-4">
                        <label for="email" class="block label-text mb-1.5">Email Address</label>
                        <div class="relative">
                            <span class="input-icon">
                                <svg class="h-4 w-4" style="color:#8b5cf6;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                autocomplete="username"
                                placeholder="your@email.com" />
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs text-red-500" />
                    </div>

                    <!-- Password -->
                    <div class="mb-4">
                        <label for="password" class="block label-text mb-1.5">Password</label>
                        <div class="relative">
                            <span class="input-icon">
                                <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </span>
                            <x-text-input id="password"
                                class="input-field w-full pl-9 pr-4 py-2.5 rounded-xl text-sm"
                                type="password"
                                name="password"
                                required
                                autocomplete="new-password"
                                placeholder="Min. 8 characters" />
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs text-red-500" />
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-6">
                        <label for="password_confirmation" class="block label-text mb-1.5">Confirm Password</label>
                        <div class="relative">
                            <span class="input-icon">
                                <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </span>
                            <x-text-input id="password_confirmation"
                                class="input-field w-full pl-9 pr-4 py-2.5 rounded-xl text-sm"
                                type="password"
                                name="password_confirmation"
                                required
                                autocomplete="new-password"
                                placeholder="Re-enter your password" />
                        </div>
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5 text-xs text-red-500" />
                    </div>

                    <!-- Register Button -->
                    <button type="submit"
                            class="register-btn w-full py-2.5 text-white text-sm font-semibold rounded-xl tracking-wide">
                        Create My Account
                    </button>

                    <!-- Login Link -->
                    <p class="mt-5 text-center text-sm text-slate-500">
                        Already have an account?
                        <a href="{{ route('login') }}" class="login-link ml-1">Sign in →</a>
                    </p>
                </form>

                {{-- Footer --}}
                <div class="mt-7 pt-5 border-t divider-line flex items-center justify-center gap-2">
                    <svg class="h-3 w-3 text-slate-300" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-xs text-slate-400">
                        &copy; {{ date('Y') }} MyGym Uganda · Secure Registration
                    </p>
                </div>

            </div>
        </div>
    </div>
</x-guest-layout>
