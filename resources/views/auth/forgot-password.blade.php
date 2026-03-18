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

            .submit-btn {
                background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
                transition: opacity 0.2s, transform 0.2s, box-shadow 0.2s;
                box-shadow: 0 4px 18px rgba(99, 102, 241, 0.38);
            }
            .submit-btn:hover {
                opacity: 0.93;
                transform: translateY(-1px);
                box-shadow: 0 8px 24px rgba(99, 102, 241, 0.45);
            }
            .submit-btn:active { transform: translateY(0); }

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

            .back-link {
                color: #6366f1;
                font-weight: 600;
                font-size: 0.875rem;
                transition: color 0.15s;
            }
            .back-link:hover { color: #4338ca; text-decoration: underline; }

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
                top: 50%; left: 12px;
                transform: translateY(-50%);
                display: flex; align-items: center;
                pointer-events: none;
            }

            /* Animated envelope icon */
            .envelope-wrap {
                width: 56px; height: 56px;
                border-radius: 16px;
                background: linear-gradient(135deg, rgba(99,102,241,0.12) 0%, rgba(139,92,246,0.12) 100%);
                border: 1px solid rgba(99,102,241,0.2);
                display: flex; align-items: center; justify-content: center;
                margin: 0 auto 16px;
                animation: pulse-soft 3s ease-in-out infinite;
            }
            @keyframes pulse-soft {
                0%, 100% { box-shadow: 0 0 0 0 rgba(99,102,241,0.15); }
                50%       { box-shadow: 0 0 0 8px rgba(99,102,241,0); }
            }
        </style>

        <!-- Background -->
        <div class="absolute inset-0 bg-cover bg-center"
             style="background-image: url('{{ asset('images/background2.jpg') }}');"></div>

        <!-- Overlay -->
        <div class="absolute inset-0"
             style="background: linear-gradient(135deg, rgba(17,24,55,0.55) 0%, rgba(49,30,80,0.42) 100%);"></div>

        <!-- Accent orbs -->
        <div class="accent-orb" style="width:380px;height:380px;background:#6366f1;bottom:-60px;left:-80px;animation-duration:10s;"></div>
        <div class="accent-orb" style="width:260px;height:260px;background:#8b5cf6;top:-50px;right:-50px;animation-duration:7s;animation-delay:2s;"></div>

        <!-- Centered Content -->
        <div class="relative flex items-center justify-center min-h-screen px-4">

            <div class="card-glass card-wrap rounded-2xl p-8 w-full max-w-sm">

                {{-- Logo --}}
                <div class="flex justify-center mb-5">
                    <img src="{{ asset('images/Project_Logo.png') }}" alt="MyGym Logo" class="h-12 w-auto">
                </div>

                {{-- Animated envelope icon --}}
                <div class="envelope-wrap">
                    <svg class="h-6 w-6" style="color:#6366f1;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>

                {{-- Heading --}}
                <h1 class="brand-font text-2xl text-slate-800 font-semibold tracking-tight text-center mb-2">
                    Reset your password
                </h1>
                <p class="text-slate-500 text-sm text-center leading-relaxed mb-6" style="font-weight:300;">
                    Enter your email and we'll send you a secure link to choose a new password.
                </p>

                <!-- Session Status (success message) -->
                <x-auth-session-status
                    class="mb-5 text-sm text-emerald-700 bg-emerald-50 border border-emerald-200 p-3 rounded-xl"
                    :status="session('status')" />

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <!-- Email -->
                    <div class="mb-6">
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
                                autofocus
                                placeholder="your@email.com" />
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs text-red-500" />
                    </div>

                    <!-- Submit -->
                    <button type="submit"
                            class="submit-btn w-full py-2.5 text-white text-sm font-semibold rounded-xl tracking-wide">
                        Send Reset Link
                    </button>

                    <!-- Back to login -->
                    <p class="mt-5 text-center text-sm text-slate-500">
                        Remembered your password?
                        <a href="{{ route('login') }}" class="back-link ml-1">Back to sign in →</a>
                    </p>
                </form>

                {{-- Footer --}}
                <div class="mt-7 pt-5 border-t divider-line flex items-center justify-center gap-2">
                    <svg class="h-3 w-3 text-slate-300" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-xs text-slate-400">
                        &copy; {{ date('Y') }} MyGym Uganda · Secure Reset
                    </p>
                </div>

            </div>
        </div>
    </div>
</x-guest-layout>
