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
        .register-btn:hover:not(:disabled) {
            background: linear-gradient(135deg, #1e293b 0%, #7e22ce 100%);
            box-shadow: 0 20px 25px -5px rgba(126, 34, 206, 0.4);
            transform: translateY(-2px);
        }
        .register-btn:active:not(:disabled) { transform: translateY(0); }
        .register-btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

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

        /* Toast notification styles */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        }
        .toast {
            background: white;
            border-radius: 12px;
            padding: 12px 16px;
            margin-bottom: 10px;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 280px;
            animation: slideIn 0.3s ease-out;
            border-left: 4px solid;
        }
        .toast-success { border-left-color: #10b981; }
        .toast-error { border-left-color: #ef4444; }
        .toast-warning { border-left-color: #f59e0b; }
        .toast-info { border-left-color: #3b82f6; }

        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }
    </style>

    <!-- Toast Container -->
    <div id="toastContainer" class="toast-container"></div>

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
                    Create your fitness account
                </p>
            </div>

            <!-- Validation Errors Display -->
            <div id="errorContainer" class="hidden mb-4 p-3 bg-red-50 border border-red-200 rounded-xl">
                <div class="flex items-center gap-2 mb-1">
                    <svg class="h-4 w-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-xs font-semibold text-red-700">Please fix the following errors:</span>
                </div>
                <ul id="errorList" class="list-disc list-inside text-xs text-red-600"></ul>
            </div>

            <form id="registerForm" method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Name -->
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
                    <div id="nameError" class="text-xs text-red-500 mt-1 hidden"></div>
                </div>

                <!-- Email Address -->
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
                    <div id="emailError" class="text-xs text-red-500 mt-1 hidden"></div>
                </div>

                <!-- Password -->
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
                    <div id="passwordError" class="text-xs text-red-500 mt-1 hidden"></div>
                </div>

                <!-- Confirm Password -->
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
                    <div id="confirmPasswordError" class="text-xs text-red-500 mt-1 hidden"></div>
                </div>

                <!-- Terms -->
                <div class="flex items-start mb-6">
                    <input type="checkbox" id="terms" name="terms" required
                           class="mt-1 w-4 h-4 rounded border-gray-300 text-purple-700 focus:ring-purple-300">
                    <label for="terms" class="ml-2 text-xs text-gray-600 leading-relaxed">
                        I agree to the <a href="#" class="text-purple-700 hover:text-purple-900 font-medium">Terms of Service</a> and
                        <a href="#" class="text-purple-700 hover:text-purple-900 font-medium">Privacy Policy</a>
                    </label>
                </div>

                <!-- Register Button -->
                <button type="submit" id="registerBtn"
                        class="register-btn w-full py-2.5 text-white text-sm font-semibold rounded-xl tracking-wide uppercase">
                    Create Account
                </button>

                <!-- Login Link -->
                <p class="mt-5 text-center text-sm text-gray-600">
                    Already have an account?
                    <a href="{{ route('login') }}" class="login-link ml-1 font-semibold">Sign in →</a>
                </p>
            </form>

            {{-- Footer --}}
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
        (function() {
            'use strict';

            const form = document.getElementById('registerForm');
            const registerBtn = document.getElementById('registerBtn');
            const errorContainer = document.getElementById('errorContainer');
            const errorList = document.getElementById('errorList');

            // Individual error elements
            const nameError = document.getElementById('nameError');
            const emailError = document.getElementById('emailError');
            const passwordError = document.getElementById('passwordError');
            const confirmPasswordError = document.getElementById('confirmPasswordError');

            // Helper: Show toast notification
            function showToast(message, type = 'error') {
                const container = document.getElementById('toastContainer');
                if (!container) return;

                const toast = document.createElement('div');
                toast.className = `toast toast-${type}`;

                const icon = type === 'success' ? '✓' : type === 'error' ? '✗' : '⚠';
                const iconColor = type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#f59e0b';

                toast.innerHTML = `
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="${iconColor}" stroke-width="2">
                        ${type === 'success' ? '<path d="M20 6L9 17l-5-5"/>' :
                          type === 'error' ? '<path d="M6 18L18 6M6 6l12 12"/>' :
                          '<path d="M12 9v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>'}
                    </svg>
                    <span style="color: #1f2937; font-size: 14px; font-weight: 500;">${escapeHtml(message)}</span>
                    <button onclick="this.parentElement.remove()" style="margin-left: auto; background: none; border: none; cursor: pointer; color: #9ca3af;">✕</button>
                `;

                container.appendChild(toast);

                setTimeout(() => {
                    toast.style.animation = 'fadeOut 0.3s ease-out forwards';
                    setTimeout(() => toast.remove(), 300);
                }, 5000);
            }

            // Helper: Escape HTML
            function escapeHtml(str) {
                if (!str) return '';
                return str.replace(/[&<>]/g, function(m) {
                    if (m === '&') return '&amp;';
                    if (m === '<') return '&lt;';
                    if (m === '>') return '&gt;';
                    return m;
                });
            }

            // Helper: Clear all errors
            function clearErrors() {
                errorContainer.classList.add('hidden');
                errorList.innerHTML = '';

                const errorDivs = [nameError, emailError, passwordError, confirmPasswordError];
                errorDivs.forEach(div => {
                    if (div) {
                        div.classList.add('hidden');
                        div.textContent = '';
                    }
                });
            }

            // Helper: Display validation errors
            function displayErrors(errors) {
                clearErrors();

                const errorMessages = [];

                for (const [field, messages] of Object.entries(errors)) {
                    const message = messages[0];
                    errorMessages.push(message);

                    // Display individual field errors
                    switch(field) {
                        case 'name':
                            if (nameError) {
                                nameError.textContent = message;
                                nameError.classList.remove('hidden');
                            }
                            break;
                        case 'email':
                            if (emailError) {
                                emailError.textContent = message;
                                emailError.classList.remove('hidden');
                            }
                            break;
                        case 'password':
                            if (passwordError) {
                                passwordError.textContent = message;
                                passwordError.classList.remove('hidden');
                            }
                            break;
                        default:
                            break;
                    }
                }

                // Display in error container
                if (errorMessages.length > 0) {
                    errorMessages.forEach(msg => {
                        const li = document.createElement('li');
                        li.textContent = msg;
                        errorList.appendChild(li);
                    });
                    errorContainer.classList.remove('hidden');
                }
            }

            // Real-time password match validation
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('password_confirmation');

            function validatePasswordMatch() {
                const password = passwordInput?.value || '';
                const confirm = confirmPasswordInput?.value || '';

                if (confirm && password !== confirm) {
                    if (confirmPasswordError) {
                        confirmPasswordError.textContent = 'Passwords do not match';
                        confirmPasswordError.classList.remove('hidden');
                    }
                    return false;
                } else {
                    if (confirmPasswordError) {
                        confirmPasswordError.classList.add('hidden');
                    }
                    return true;
                }
            }

            if (confirmPasswordInput) {
                confirmPasswordInput.addEventListener('input', validatePasswordMatch);
            }
            if (passwordInput) {
                passwordInput.addEventListener('input', validatePasswordMatch);
            }

            // Real-time email format validation
            const emailInput = document.getElementById('email');
            function validateEmailFormat() {
                const email = emailInput?.value || '';
                const emailRegex = /^[^\s@]+@([^\s@]+\.)+[^\s@]+$/;

                if (email && !emailRegex.test(email)) {
                    if (emailError) {
                        emailError.textContent = 'Please enter a valid email address';
                        emailError.classList.remove('hidden');
                    }
                    return false;
                } else {
                    if (emailError) {
                        emailError.classList.add('hidden');
                    }
                    return true;
                }
            }

            if (emailInput) {
                emailInput.addEventListener('input', validateEmailFormat);
            }

            // Real-time name validation
            const nameInput = document.getElementById('name');
            function validateName() {
                const name = nameInput?.value?.trim() || '';

                if (name && name.length < 2) {
                    if (nameError) {
                        nameError.textContent = 'Name must be at least 2 characters';
                        nameError.classList.remove('hidden');
                    }
                    return false;
                } else {
                    if (nameError) {
                        nameError.classList.add('hidden');
                    }
                    return true;
                }
            }

            if (nameInput) {
                nameInput.addEventListener('input', validateName);
            }

            // Real-time password strength validation
            function validatePasswordStrength() {
                const password = passwordInput?.value || '';

                if (password && password.length < 8) {
                    if (passwordError) {
                        passwordError.textContent = 'Password must be at least 8 characters';
                        passwordError.classList.remove('hidden');
                    }
                    return false;
                } else {
                    if (passwordError) {
                        passwordError.classList.add('hidden');
                    }
                    return true;
                }
            }

            if (passwordInput) {
                passwordInput.addEventListener('input', validatePasswordStrength);
            }

            // Form submission with AJAX for better UX
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                clearErrors();

                // Validate terms checkbox
                const termsCheckbox = document.getElementById('terms');
                if (!termsCheckbox.checked) {
                    showToast('Please agree to the Terms of Service and Privacy Policy', 'warning');
                    return;
                }

                // Validate all fields
                const isNameValid = validateName();
                const isEmailValid = validateEmailFormat();
                const isPasswordValid = validatePasswordStrength();
                const isPasswordMatchValid = validatePasswordMatch();

                if (!isNameValid || !isEmailValid || !isPasswordValid || !isPasswordMatchValid) {
                    showToast('Please fix the errors before submitting', 'error');
                    return;
                }

                // Disable button and show loading state
                const originalButtonText = registerBtn.innerHTML;
                registerBtn.disabled = true;
                registerBtn.innerHTML = `
                    <svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                `;

                try {
                    // Collect form data
                    const formData = new FormData(form);

                    // Submit via fetch
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]')?.value || '',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (response.ok && data.success !== false) {
                        // Registration successful
                        showToast('Account created successfully! Redirecting...', 'success');

                        // If there's a redirect URL in response, use it
                        if (data.redirect) {
                            setTimeout(() => {
                                window.location.href = data.redirect;
                            }, 1500);
                        } else {
                            // Default redirect to member dashboard
                            setTimeout(() => {
                                window.location.href = '{{ route("member.dashboard") }}';
                            }, 1500);
                        }
                    } else {
                        // Handle validation errors
                        if (data.errors) {
                            displayErrors(data.errors);
                            showToast('Please fix the errors and try again', 'error');
                        } else if (data.message) {
                            showToast(data.message, 'error');
                        } else {
                            showToast('Registration failed. Please try again.', 'error');
                        }
                        registerBtn.disabled = false;
                        registerBtn.innerHTML = originalButtonText;
                    }
                } catch (error) {
                    console.error('Registration error:', error);

                    // Fallback to traditional form submission if AJAX fails
                    // This handles cases where JavaScript fails or CSRF token issues
                    const tempForm = document.createElement('form');
                    tempForm.method = 'POST';
                    tempForm.action = form.action;

                    // Copy all form data
                    const formData = new FormData(form);
                    for (let [key, value] of formData.entries()) {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = key;
                        input.value = value;
                        tempForm.appendChild(input);
                    }

                    document.body.appendChild(tempForm);
                    tempForm.submit();
                }
            });

            // CSRF token refresh utility
            async function refreshCsrfToken() {
                try {
                    const response = await fetch('/csrf-token', {
                        method: 'GET',
                        headers: { 'Accept': 'application/json' }
                    });
                    const data = await response.json();
                    if (data.token) {
                        const tokenInput = document.querySelector('input[name="_token"]');
                        if (tokenInput) tokenInput.value = data.token;
                    }
                } catch (e) {
                    console.log('CSRF refresh not available, using existing token');
                }
            }

            // Optional: Refresh token periodically if page is idle for long
            // setInterval(refreshCsrfToken, 15 * 60 * 1000);
        })();
    </script>
</x-guest-layout>
