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
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-6">
                <div class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl shadow-lg overflow-hidden p-3">
                    <h1 class="text-2xl font-bold text-gray-800">Site Settings</h1>
                </div>

                <div class="flex flex-wrap gap-2">
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
            <footer class="bg-gradient-to-r from-gray-900 to-gray-800 border-t border-purple-500/30">
            <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 gap-8 md:grid-cols-4 lg:grid-cols-5">
                    {{-- Column 1: Logo/Brand Info --}}
                    <div class="col-span-2 md:col-span-1 lg:col-span-2">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="flex items-center">
                                <img src="{{ asset('images/Project_Logo.png') }}" alt="Gym Logo" class="h-7 w-auto object-contain ml-1">
                            </div>
                        </div>
                        <p class="text-sm text-gray-400 leading-relaxed">
                            Train smart, stay consistent, and celebrate your growth. We're a community rooted in African strength and unity.
                        </p>
                        <div class="flex space-x-4 mt-4">
                            <a href="#" class="text-gray-400 hover:text-purple-400 transition duration-300">
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.77l-.44 2.89h-2.33v6.987A10 10 0 0022 12z" clip-rule="evenodd" /></svg>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-purple-400 transition duration-300">
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M12.315 2c2.43 0 2.715.01 3.67.058 1.036.05 1.745.21 2.37.456.684.276 1.258.74 1.717 1.259.46.52.825 1.094 1.102 1.717.246.625.407 1.334.456 2.37.048.955.058 1.23.058 3.67s-.01 2.715-.058 3.67c-.05.97-.21 1.745-.456 2.37-.276.684-.74 1.258-1.259 1.717-.52.46-1.094.825-1.717 1.102-.625.246-1.334.407-2.37.456-.955.048-1.23.058-3.67.058s-2.715-.01-3.67-.058c-.97-.05-1.745-.21-2.37-.456-.684-.276-1.258-.74-1.717-1.259-.46-.52-.825-1.094-1.102-1.717-.246-.625-.407-1.334-.456-2.37-.048-.955-.058-1.23-.058-3.67s.01-2.715.058-3.67c.05-.97.21-1.745.456-2.37.276-.684.74-1.258 1.259-1.717.46-.52 1.094-.825 1.717-1.102.625-.246 1.334-.407 2.37-.456C9.59 2.01 9.875 2 12.315 2zm0 1.637c-2.35 0-2.6.01-3.535.056-.983.05-1.503.21-1.85.347-.417.164-.78.384-1.095.698-.315.315-.534.678-.698 1.095-.137.347-.297.867-.347 1.85-.046.935-.056 1.185-.056 3.535s.01 2.6.056 3.535c.05.983.21 1.503.347 1.85.164.417.384.78.698 1.095.315.315.678.534 1.095.698.347.137.867.297 1.85.347.935.046 1.185.056 3.535.056s2.6-.01 3.535-.056c.983-.05 1.503-.21 1.85-.347.417-.164.78-.384 1.095-.698.315-.315.534-.678.698-1.095.137-.347.297-.867.347-1.85.046-.935.056-1.185.056-3.535s-.01-2.6-.056-3.535c-.05-.983-.21-1.503-.347-1.85-.164-.417-.384-.78-.698-1.095-.315-.315-.678-.534-1.095-.698-.347-.137-.867-.297-1.85-.347-.935-.046-1.185-.056-3.535-.056zM12.315 5.564c-3.714 0-6.75 3.036-6.75 6.75s3.036 6.75 6.75 6.75 6.75-3.036 6.75-6.75-3.036-6.75-6.75-6.75zm0 11.235c-2.476 0-4.485-2.009-4.485-4.485S9.839 7.828 12.315 7.828s4.485 2.009 4.485 4.485-2.009 4.485-4.485 4.485zm4.991-9.982c-.52 0-.942-.423-.942-.942s.422-.942.942-.942.942.423.942.942-.422.942-.942.942z" clip-rule="evenodd" /></svg>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-purple-400 transition duration-300">
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                            </a>
                        </div>
                    </div>

                    {{-- Column 2: Admin Quick Links --}}
                    <div>
                        <h5 class="text-lg font-semibold text-white mb-4">Admin Panel</h5>
                        <ul class="space-y-3">
                            <li><a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">📊 Dashboard</a></li>
                            <li><a href="{{ route('admin.members.index') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">👥 Manage Members</a></li>
                            <li><a href="{{ route('admin.instructors.index') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">👨‍🏫 Manage Instructors</a></li>
                            <li><a href="{{ route('admin.earnings.index') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">💰 Earnings Overview</a></li>
                            <li><a href="{{ route('admin.reports.index') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">📈 Reports</a></li>
                            <li><a href="{{ route('admin.settings.index') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">⚙️ Settings</a></li>
                        </ul>
                    </div>

                    {{-- Column 3: System Management --}}
                    <div>
                        <h5 class="text-lg font-semibold text-white mb-4">System</h5>
                        <ul class="space-y-3">
                            <li><a href="{{ route('admin.system.health') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">🩺 System Health</a></li>
                            <li><a href="{{ route('admin.system.logs') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">📋 System Logs</a></li>
                            <li><a href="{{ route('admin.database.backup') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">💾 Database Backup</a></li>
                            <li>
                                <form method="POST" action="{{ route('admin.system.clear-cache') }}" class="inline" onsubmit="return confirm('Are you sure you want to clear the system cache?');">
                                    @csrf
                                    <button type="submit" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">🗑️ Clear Cache</button>
                                </form>
                            </li>
                            <li><a href="{{ route('admin.system.queue-status') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">⏳ Queue Status</a></li>
                        </ul>
                    </div>

                    {{-- Column 4: Contact & Support --}}
                    <div class="col-span-2 md:col-span-1">
                        <h5 class="text-lg font-semibold text-white mb-4">Get In Touch</h5>
                        <ul class="space-y-3 text-sm text-gray-400">
                            <li class="flex items-start">
                                <span class="mr-2 text-purple-400">📍</span>
                                <span>Ggaba Road, Kampala, UGANDA</span>
                            </li>
                            <li class="flex items-start">
                                <span class="mr-2 text-purple-400">📞</span>
                                <span>+256 700 123 456</span>
                            </li>
                            <li class="flex items-start">
                                <span class="mr-2 text-purple-400">📧</span>
                                <span><a href="mailto:admin@mygym.com" class="hover:text-purple-400">admin@mygym.com</a></span>
                            </li>
                        </ul>
                        <div class="mt-6">
                            <h5 class="text-sm font-semibold text-white mb-2">Support Hours</h5>
                            <p class="text-xs text-gray-400">Monday - Friday: 9AM - 6PM</p>
                            <p class="text-xs text-gray-400">Saturday: 10AM - 4PM</p>
                            <p class="text-xs text-gray-400">Sunday: Closed</p>
                        </div>
                    </div>
                </div>

                {{-- Copyright Section with Links --}}
                <div class="mt-12 pt-8 border-t border-purple-500/30">
                    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                        <div class="flex gap-6">
                            <a href="#" class="text-xs text-gray-500 hover:text-purple-400 transition-colors">About Us</a>
                            <a href="#" class="text-xs text-gray-500 hover:text-purple-400 transition-colors">Terms of Service</a>
                            <a href="#" class="text-xs text-gray-500 hover:text-purple-400 transition-colors">Privacy Policy</a>
                            <a href="#" class="text-xs text-gray-500 hover:text-purple-400 transition-colors">Cookie Policy</a>
                        </div>
                        <div class="flex items-center gap-2">
                            <p class="text-sm text-gray-500">
                                &copy; {{ date('Y') }} MyGym. All rights reserved. Powered by Passion.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
</x-app-layout>

