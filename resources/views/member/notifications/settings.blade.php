{{-- resources/views/member/notifications/settings.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Notification Settings
                </h2>
                <p class="text-sm text-gray-500 mt-1">Manage how you receive notifications</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('notifications.update-settings') }}">
                        @csrf
                        @method('PUT')

                        <!-- Delivery Channels -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold mb-4">Delivery Channels</h3>
                            <div class="space-y-3">
                                <label class="flex items-center">
                                    <input type="checkbox" name="in_app_enabled" value="1" {{ $settings->in_app_enabled ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                                    <span class="ml-3">In-App Notifications</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="push_enabled" value="1" {{ $settings->push_enabled ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                                    <span class="ml-3">Push Notifications</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="email_enabled" value="1" {{ $settings->email_enabled ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                                    <span class="ml-3">Email Notifications</span>
                                </label>
                            </div>
                        </div>

                        <!-- Notification Preferences -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold mb-4">What to Notify Me About</h3>
                            <div class="space-y-3">
                                <label class="flex items-center">
                                    <input type="checkbox" name="preferences[workout_reminders]" value="1" {{ ($settings->preferences['workout_reminders'] ?? true) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                                    <span class="ml-3">Workout Reminders</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="preferences[booking_updates]" value="1" {{ ($settings->preferences['booking_updates'] ?? true) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                                    <span class="ml-3">Booking Updates</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="preferences[payment_alerts]" value="1" {{ ($settings->preferences['payment_alerts'] ?? true) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                                    <span class="ml-3">Payment Alerts</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="preferences[achievements]" value="1" {{ ($settings->preferences['achievements'] ?? true) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                                    <span class="ml-3">Achievements & Goals</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="preferences[streak_alerts]" value="1" {{ ($settings->preferences['streak_alerts'] ?? true) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                                    <span class="ml-3">Streak Alerts</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="preferences[weekly_reports]" value="1" {{ ($settings->preferences['weekly_reports'] ?? true) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                                    <span class="ml-3">Weekly Reports</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="preferences[daily_motivation]" value="1" {{ ($settings->preferences['daily_motivation'] ?? true) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                                    <span class="ml-3">Daily Motivation</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="preferences[promotions]" value="1" {{ ($settings->preferences['promotions'] ?? false) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                                    <span class="ml-3">Promotions & Offers</span>
                                </label>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                                Save Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
