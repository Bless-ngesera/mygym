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

        .confirm-btn {
            background: linear-gradient(135deg, #7e22ce 0%, #1e293b 100%);
            transition: all 0.2s ease;
            box-shadow: 0 10px 15px -3px rgba(126, 34, 206, 0.3);
            color: white;
            border: none;
            cursor: pointer;
        }
        .confirm-btn:hover {
            background: linear-gradient(135deg, #1e293b 0%, #7e22ce 100%);
            box-shadow: 0 20px 25px -5px rgba(126, 34, 206, 0.4);
            transform: translateY(-2px);
        }
        .confirm-btn:active { transform: translateY(0); }

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

        .info-box {
            background: rgba(126, 34, 206, 0.05);
            border-left: 4px solid #7e22ce;
        }
    </style>

    <!-- Background Image -->
    <div class="fixed inset-0 bg-cover bg-center"
         style="background-image: url('{{ asset('images/background2.jpg') }}');"></div>

    <!-- Simple dark overlay -->
    <div class="fixed inset-0 bg-black/30"></div>

    <!-- Centered Content -->
    <div class="relative flex items-center justify-center min-h-screen px-4">
        <div class="card-glass card-wrap rounded-2xl p-8 w-full max-w-md">

            {{-- Brand - Logo --}}
            <div class="text-center mb-8">
                <div class="flex justify-center mb-3">
                    <img src="{{ asset('images/Project_Logo.png') }}"
                         alt="MyGym Logo"
                         class="h-16 w-auto object-contain">
                </div>
                <p class="text-sm text-gray-600">
                    Confirm your password
                </p>
            </div>

            <!-- Info Box -->
            <div class="info-box mb-6 p-4 rounded-xl">
                <div class="flex gap-3">
                    <svg class="h-5 w-5 text-purple-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span class="text-sm text-gray-700 leading-relaxed">
                        {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
                    </span>
                </div>
            </div>

            <form method="POST" action="{{ route('password.confirm') }}">
                @csrf

                <!-- Password -->
                <div class="mb-6">
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
                            autocomplete="current-password"
                            placeholder="Enter your password" />
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-xs text-red-500" />
                </div>

                <!-- Confirm Button -->
                <button type="submit"
                        class="confirm-btn w-full py-2.5 text-white text-sm font-semibold rounded-xl tracking-wide uppercase">
                    Confirm Password
                </button>
            </form>

            {{-- Footer --}}
            <div class="mt-8 pt-5 border-t divider-line text-center">
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
