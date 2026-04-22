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

        .verify-btn {
            background: linear-gradient(135deg, #7e22ce 0%, #1e293b 100%);
            transition: all 0.2s ease;
            box-shadow: 0 10px 15px -3px rgba(126, 34, 206, 0.3);
            color: white;
            border: none;
            cursor: pointer;
        }
        .verify-btn:hover {
            background: linear-gradient(135deg, #1e293b 0%, #7e22ce 100%);
            box-shadow: 0 20px 25px -5px rgba(126, 34, 206, 0.4);
            transform: translateY(-2px);
        }
        .verify-btn:active { transform: translateY(0); }

        .card-wrap {
            transition: box-shadow 0.2s, transform 0.2s;
        }
        .card-wrap:hover {
            box-shadow: 0 30px 60px -15px rgba(0, 0, 0, 0.3);
            transform: translateY(-4px);
        }

        .logout-link {
            color: #7e22ce;
            font-weight: 600;
            transition: color 0.15s;
        }
        .logout-link:hover { color: #6b21a8; text-decoration: underline; }

        .divider-line {
            border-color: #e5e7eb;
        }

        @keyframes fadeOut {
            from { opacity: 1; transform: translateY(0); }
            to { opacity: 0; transform: translateY(-10px); }
        }
        .fade-out {
            animation: fadeOut 0.5s ease-out forwards;
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
                    Verify your email address
                </p>
            </div>

            <!-- Info Box -->
            <div class="info-box mb-6 p-4 rounded-xl">
                <div class="flex gap-3">
                    <svg class="h-5 w-5 text-purple-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-sm text-gray-700 leading-relaxed">
                        {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
                    </span>
                </div>
            </div>

            <!-- Success Message -->
            @if (session('status') == 'verification-link-sent')
                <div id="successMessage" class="mb-4 text-sm text-emerald-700 bg-emerald-50 border border-emerald-200 p-3 rounded-xl">
                    <div class="flex items-center gap-2">
                        <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>{{ __('A new verification link has been sent to the email address you provided during registration.') }}</span>
                    </div>
                </div>
            @endif

            <!-- Action Buttons -->
            <div class="space-y-4">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" class="verify-btn w-full py-2.5 text-white text-sm font-semibold rounded-xl tracking-wide uppercase">
                        Resend Verification Email
                    </button>
                </form>

                <div class="text-center">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="logout-link text-sm font-semibold">
                            ← Log Out
                        </button>
                    </form>
                </div>
            </div>

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
