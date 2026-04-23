{{-- resources/views/notifications/settings.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Notification Settings
                </h2>
                <p class="text-sm text-gray-500 mt-1">Manage how you receive notifications</p>
            </div>
            <a href="{{ route('notifications.index') }}"
               class="px-4 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white rounded-xl text-sm font-semibold transition-all duration-200 shadow-md hover:shadow-lg flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Notifications
            </a>
        </div>
    </x-slot>

    <div class="py-12 min-h-screen"
         style="background-image: url('{{ asset('images/background2.jpg') }}');
                background-size: cover;
                background-position: center;
                background-attachment: fixed;">

        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            {{-- Flash Messages --}}
            @if(session('success'))
                <div id="successMessage" class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-xl shadow-md flex items-center justify-between animate-fade-in">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button onclick="this.closest('#successMessage').remove()" class="text-green-700 hover:text-green-900">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div id="errorMessage" class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-xl shadow-md flex items-center justify-between animate-fade-in">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button onclick="this.closest('#errorMessage').remove()" class="text-red-700 hover:text-red-900">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            @endif

            <div class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl shadow-2xl ring-1 ring-white/30 overflow-hidden">
                <div class="p-6 md:p-8">
                    <form method="POST" action="{{ route('notifications.update-settings') }}" id="settingsForm">
                        @csrf
                        @method('PUT')

                        <!-- Delivery Channels Section -->
                        <div class="mb-8">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Delivery Channels</h3>
                                    <p class="text-sm text-gray-500">Choose how you want to receive notifications</p>
                                </div>
                            </div>

                            <div class="bg-gray-50/80 rounded-xl p-4 space-y-3">
                                <label class="flex items-center justify-between p-3 bg-white rounded-lg cursor-pointer hover:bg-indigo-50/30 transition-colors">
                                    <div class="flex items-center">
                                        <input type="checkbox" name="in_app_enabled" value="1" {{ $settings->in_app_enabled ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        <span class="ml-3">
                                            <span class="font-medium text-gray-900">In-App Notifications</span>
                                            <p class="text-xs text-gray-500">Show notifications within the website</p>
                                        </span>
                                    </div>
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </label>

                                <label class="flex items-center justify-between p-3 bg-white rounded-lg cursor-pointer hover:bg-indigo-50/30 transition-colors">
                                    <div class="flex items-center">
                                        <input type="checkbox" name="push_enabled" value="1" {{ $settings->push_enabled ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        <span class="ml-3">
                                            <span class="font-medium text-gray-900">Push Notifications</span>
                                            <p class="text-xs text-gray-500">Receive notifications on your device</p>
                                        </span>
                                    </div>
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                    </svg>
                                </label>

                                <label class="flex items-center justify-between p-3 bg-white rounded-lg cursor-pointer hover:bg-indigo-50/30 transition-colors">
                                    <div class="flex items-center">
                                        <input type="checkbox" name="email_enabled" value="1" {{ $settings->email_enabled ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        <span class="ml-3">
                                            <span class="font-medium text-gray-900">Email Notifications</span>
                                            <p class="text-xs text-gray-500">Receive notifications via email</p>
                                        </span>
                                    </div>
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </label>
                            </div>
                        </div>

                        <!-- Notification Preferences Section -->
                        <div class="mb-8">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Notification Types</h3>
                                    <p class="text-sm text-gray-500">Select which notifications you want to receive</p>
                                </div>
                            </div>

                            <div class="bg-gray-50/80 rounded-xl p-4 space-y-2">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <label class="flex items-center p-3 bg-white rounded-lg cursor-pointer hover:bg-indigo-50/30 transition-colors">
                                        <input type="checkbox" name="preferences[workout_reminders]" value="1" {{ ($settings->preferences['workout_reminders'] ?? true) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        <span class="ml-3">
                                            <span class="font-medium text-gray-900">💪 Workout Reminders</span>
                                            <p class="text-xs text-gray-500">Get reminded about your scheduled workouts</p>
                                        </span>
                                    </label>

                                    <label class="flex items-center p-3 bg-white rounded-lg cursor-pointer hover:bg-indigo-50/30 transition-colors">
                                        <input type="checkbox" name="preferences[booking_updates]" value="1" {{ ($settings->preferences['booking_updates'] ?? true) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        <span class="ml-3">
                                            <span class="font-medium text-gray-900">📅 Booking Updates</span>
                                            <p class="text-xs text-gray-500">Class bookings, cancellations, and changes</p>
                                        </span>
                                    </label>

                                    <label class="flex items-center p-3 bg-white rounded-lg cursor-pointer hover:bg-indigo-50/30 transition-colors">
                                        <input type="checkbox" name="preferences[payment_alerts]" value="1" {{ ($settings->preferences['payment_alerts'] ?? true) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        <span class="ml-3">
                                            <span class="font-medium text-gray-900">💰 Payment Alerts</span>
                                            <p class="text-xs text-gray-500">Payment confirmations and subscription reminders</p>
                                        </span>
                                    </label>

                                    <label class="flex items-center p-3 bg-white rounded-lg cursor-pointer hover:bg-indigo-50/30 transition-colors">
                                        <input type="checkbox" name="preferences[achievements]" value="1" {{ ($settings->preferences['achievements'] ?? true) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        <span class="ml-3">
                                            <span class="font-medium text-gray-900">🏆 Achievements & Goals</span>
                                            <p class="text-xs text-gray-500">Goal progress and achievement unlocks</p>
                                        </span>
                                    </label>

                                    <label class="flex items-center p-3 bg-white rounded-lg cursor-pointer hover:bg-indigo-50/30 transition-colors">
                                        <input type="checkbox" name="preferences[streak_alerts]" value="1" {{ ($settings->preferences['streak_alerts'] ?? true) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        <span class="ml-3">
                                            <span class="font-medium text-gray-900">🔥 Streak Alerts</span>
                                            <p class="text-xs text-gray-500">Maintain your workout streaks</p>
                                        </span>
                                    </label>

                                    <label class="flex items-center p-3 bg-white rounded-lg cursor-pointer hover:bg-indigo-50/30 transition-colors">
                                        <input type="checkbox" name="preferences[weekly_reports]" value="1" {{ ($settings->preferences['weekly_reports'] ?? true) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        <span class="ml-3">
                                            <span class="font-medium text-gray-900">📊 Weekly Reports</span>
                                            <p class="text-xs text-gray-500">Weekly progress summary</p>
                                        </span>
                                    </label>

                                    <label class="flex items-center p-3 bg-white rounded-lg cursor-pointer hover:bg-indigo-50/30 transition-colors">
                                        <input type="checkbox" name="preferences[daily_motivation]" value="1" {{ ($settings->preferences['daily_motivation'] ?? true) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        <span class="ml-3">
                                            <span class="font-medium text-gray-900">💡 Daily Motivation</span>
                                            <p class="text-xs text-gray-500">Daily motivational quotes</p>
                                        </span>
                                    </label>

                                    <label class="flex items-center p-3 bg-white rounded-lg cursor-pointer hover:bg-indigo-50/30 transition-colors">
                                        <input type="checkbox" name="preferences[promotions]" value="1" {{ ($settings->preferences['promotions'] ?? false) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        <span class="ml-3">
                                            <span class="font-medium text-gray-900">🎉 Promotions & Offers</span>
                                            <p class="text-xs text-gray-500">Special offers and promotions</p>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t border-gray-200">
                            <button type="button" onclick="resetToDefaults()"
                                    class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-semibold transition-all duration-200">
                                Reset to Defaults
                            </button>
                            <button type="submit"
                                    class="px-6 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white rounded-xl text-sm font-semibold transition-all duration-200 shadow-md hover:shadow-lg flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Save Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Info Card -->
            <div class="mt-6 bg-blue-50/80 backdrop-blur-sm border border-blue-200 rounded-xl p-4">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold text-blue-800">About Notifications</h4>
                        <p class="text-xs text-blue-600 mt-1">
                            Critical notifications (payment failures, urgent updates) will always be sent regardless of your preferences.
                            You can change these settings at any time, and they will apply to all future notifications.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-dismiss flash messages after 5 seconds
        setTimeout(function() {
            const successMessage = document.getElementById('successMessage');
            const errorMessage = document.getElementById('errorMessage');
            if (successMessage) {
                successMessage.style.transition = 'opacity 0.5s ease';
                successMessage.style.opacity = '0';
                setTimeout(() => successMessage.remove(), 500);
            }
            if (errorMessage) {
                errorMessage.style.transition = 'opacity 0.5s ease';
                errorMessage.style.opacity = '0';
                setTimeout(() => errorMessage.remove(), 500);
            }
        }, 5000);

        // Reset to default settings
        function resetToDefaults() {
            if (confirm('Reset all notification settings to defaults?')) {
                // Reset delivery channels
                document.querySelector('input[name="in_app_enabled"]').checked = true;
                document.querySelector('input[name="push_enabled"]').checked = true;
                document.querySelector('input[name="email_enabled"]').checked = true;

                // Reset preferences
                const defaultPreferences = {
                    'workout_reminders': true,
                    'booking_updates': true,
                    'payment_alerts': true,
                    'achievements': true,
                    'streak_alerts': true,
                    'weekly_reports': true,
                    'daily_motivation': true,
                    'promotions': false
                };

                for (const [key, value] of Object.entries(defaultPreferences)) {
                    const checkbox = document.querySelector(`input[name="preferences[${key}]"]`);
                    if (checkbox) {
                        checkbox.checked = value;
                    }
                }

                // Show feedback
                const btn = event.target;
                const originalText = btn.innerHTML;
                btn.innerHTML = '✓ Reset!';
                btn.classList.add('bg-green-500', 'text-white');
                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.classList.remove('bg-green-500', 'text-white');
                }, 2000);
            }
        }

        // Select/Deselect all preferences
        function toggleAllPreferences() {
            const checkboxes = document.querySelectorAll('input[name^="preferences["]');
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            checkboxes.forEach(cb => cb.checked = !allChecked);
        }

        // Add keyboard shortcut (Ctrl+S to save)
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault();
                document.getElementById('settingsForm').submit();
            }
        });
    </script>

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

        /* Custom checkbox styling */
        input[type="checkbox"] {
            width: 1.2rem;
            height: 1.2rem;
            cursor: pointer;
        }

        /* Hover effects for setting cards */
        .bg-white.rounded-lg {
            transition: all 0.2s ease;
        }

        .bg-white.rounded-lg:hover {
            transform: translateX(4px);
        }
    </style>
</x-app-layout>
