<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Settings') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12"
         style="background-image: url('{{ asset('images/background2.jpg') }}');
                background-size: cover;
                background-position: center;
                background-attachment: fixed;
                min-height: calc(100vh - 4rem);">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header with Actions -->
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Site Settings</h1>
                <div class="space-x-2">
                    <form action="{{ route('admin.settings.reset') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded-lg transition-colors shadow-md hover:shadow-lg" onclick="return confirm('Are you sure you want to reset all settings to default?')">
                            Reset to Default
                        </button>
                    </form>
                    <form action="{{ route('admin.settings.clear-cache') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg transition-colors shadow-md hover:shadow-lg">
                            Clear Cache
                        </button>
                    </form>
                </div>
            </div>

            <!-- Success Message -->
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded shadow-md">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            <!-- Error Messages -->
            @if($errors->any())
                <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded shadow-md">
                    <div class="font-medium mb-2">Please fix the following errors:</div>
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Settings Form -->
            <div class="bg-white/90 backdrop-blur-sm border border-white/20 rounded-2xl shadow-2xl ring-1 ring-white/30 overflow-hidden">
                <div class="p-6">
                    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- General Settings -->
                            <div class="col-span-2">
                                <h2 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">General Settings</h2>
                            </div>

                            <div class="mb-4">
                                <label for="site_name" class="block text-sm font-medium text-gray-700 mb-2">Site Name</label>
                                <input type="text" name="site_name" id="site_name" value="{{ old('site_name', $settings['site_name']) }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 transition-all">
                            </div>

                            <div class="mb-4">
                                <label for="site_email" class="block text-sm font-medium text-gray-700 mb-2">Site Email</label>
                                <input type="email" name="site_email" id="site_email" value="{{ old('site_email', $settings['site_email']) }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 transition-all">
                            </div>

                            <div class="mb-4">
                                <label for="site_phone" class="block text-sm font-medium text-gray-700 mb-2">Site Phone</label>
                                <input type="text" name="site_phone" id="site_phone" value="{{ old('site_phone', $settings['site_phone']) }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 transition-all">
                            </div>

                            <div class="mb-4">
                                <label for="site_address" class="block text-sm font-medium text-gray-700 mb-2">Site Address</label>
                                <input type="text" name="site_address" id="site_address" value="{{ old('site_address', $settings['site_address']) }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 transition-all">
                            </div>

                            <!-- Currency Settings -->
                            <div class="col-span-2">
                                <h2 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">Currency Settings</h2>
                            </div>

                            <div class="mb-4">
                                <label for="currency" class="block text-sm font-medium text-gray-700 mb-2">Currency</label>
                                <select name="currency" id="currency"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 transition-all">
                                    <option value="USD" {{ $settings['currency'] == 'USD' ? 'selected' : '' }}>USD ($)</option>
                                    <option value="EUR" {{ $settings['currency'] == 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                                    <option value="GBP" {{ $settings['currency'] == 'GBP' ? 'selected' : '' }}>GBP (£)</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label for="tax_rate" class="block text-sm font-medium text-gray-700 mb-2">Tax Rate (%)</label>
                                <input type="number" step="0.01" min="0" max="100" name="tax_rate" id="tax_rate" value="{{ old('tax_rate', $settings['tax_rate']) }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 transition-all">
                            </div>

                            <!-- Booking Settings -->
                            <div class="col-span-2">
                                <h2 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">Booking Settings</h2>
                            </div>

                            <div class="mb-4">
                                <label for="booking_lead_time" class="block text-sm font-medium text-gray-700 mb-2">Booking Lead Time (hours)</label>
                                <input type="number" name="booking_lead_time" id="booking_lead_time" value="{{ old('booking_lead_time', $settings['booking_lead_time']) }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 transition-all">
                            </div>

                            <div class="mb-4">
                                <label for="max_booking_per_user" class="block text-sm font-medium text-gray-700 mb-2">Max Bookings Per User</label>
                                <input type="number" name="max_booking_per_user" id="max_booking_per_user" value="{{ old('max_booking_per_user', $settings['max_booking_per_user']) }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 transition-all">
                            </div>

                            <!-- Social Media -->
                            <div class="col-span-2">
                                <h2 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">Social Media</h2>
                            </div>

                            <div class="mb-4">
                                <label for="facebook_url" class="block text-sm font-medium text-gray-700 mb-2">Facebook URL</label>
                                <input type="url" name="facebook_url" id="facebook_url" value="{{ old('facebook_url', $settings['facebook_url']) }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 transition-all">
                            </div>

                            <div class="mb-4">
                                <label for="twitter_url" class="block text-sm font-medium text-gray-700 mb-2">Twitter URL</label>
                                <input type="url" name="twitter_url" id="twitter_url" value="{{ old('twitter_url', $settings['twitter_url']) }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 transition-all">
                            </div>

                            <div class="mb-4">
                                <label for="instagram_url" class="block text-sm font-medium text-gray-700 mb-2">Instagram URL</label>
                                <input type="url" name="instagram_url" id="instagram_url" value="{{ old('instagram_url', $settings['instagram_url']) }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 transition-all">
                            </div>

                            <div class="mb-4">
                                <label for="youtube_url" class="block text-sm font-medium text-gray-700 mb-2">YouTube URL</label>
                                <input type="url" name="youtube_url" id="youtube_url" value="{{ old('youtube_url', $settings['youtube_url']) }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 transition-all">
                            </div>

                            <!-- Feature Toggles -->
                            <div class="col-span-2">
                                <h2 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">Feature Toggles</h2>
                            </div>

                            <div class="mb-4">
                                <label class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                                    <input type="checkbox" name="enable_registration" value="1" {{ $settings['enable_registration'] == '1' ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-200">
                                    <span class="text-sm font-medium text-gray-700">Enable User Registration</span>
                                </label>
                            </div>

                            <div class="mb-4">
                                <label class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                                    <input type="checkbox" name="maintenance_mode" value="1" {{ $settings['maintenance_mode'] == '1' ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-200">
                                    <span class="text-sm font-medium text-gray-700">Maintenance Mode</span>
                                </label>
                            </div>

                            <!-- Site Content -->
                            <div class="col-span-2">
                                <h2 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">Site Content</h2>
                            </div>

                            <div class="mb-4 col-span-2">
                                <label for="about_us" class="block text-sm font-medium text-gray-700 mb-2">About Us</label>
                                <textarea name="about_us" id="about_us" rows="4"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 transition-all">{{ old('about_us', $settings['about_us']) }}</textarea>
                            </div>

                            <div class="mb-4 col-span-2">
                                <label for="terms_conditions" class="block text-sm font-medium text-gray-700 mb-2">Terms & Conditions</label>
                                <textarea name="terms_conditions" id="terms_conditions" rows="4"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 transition-all">{{ old('terms_conditions', $settings['terms_conditions']) }}</textarea>
                            </div>

                            <div class="mb-4 col-span-2">
                                <label for="privacy_policy" class="block text-sm font-medium text-gray-700 mb-2">Privacy Policy</label>
                                <textarea name="privacy_policy" id="privacy_policy" rows="4"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 transition-all">{{ old('privacy_policy', $settings['privacy_policy']) }}</textarea>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end mt-8">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-6 rounded-lg transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                                <span class="flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                                    </svg>
                                    Save Settings
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

