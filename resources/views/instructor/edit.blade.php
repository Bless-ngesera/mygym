<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Edit Class
                </h2>
                <p class="text-sm text-gray-500 mt-1">Update your class details and schedule</p>
            </div>
            <a href="{{ route('instructor.upcoming') }}"
               class="px-3 sm:px-4 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white rounded-xl text-sm font-semibold transition-all duration-200 shadow-md hover:shadow-lg">
                ← Upcoming Classes
            </a>
        </div>
    </x-slot>

    <div class="py-12 min-h-screen"
        style="background-image: url('{{ asset('images/background2.jpg') }}');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;">

        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Success Message --}}
            @if(session('success'))
                <div id="successMessage" class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-xl shadow-md flex items-center justify-between animate-fade-in">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button onclick="this.parentElement.style.display='none'" class="text-green-700 hover:text-green-900">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            @endif

            {{-- Error Message --}}
            @if(session('error'))
                <div id="errorMessage" class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-xl shadow-md">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            {{-- Validation Errors --}}
            @if($errors->any())
                <div id="errorMessage" class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-xl shadow-md">
                    <div class="flex items-center mb-2">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="font-semibold">Please fix the following errors:</span>
                    </div>
                    <ul class="list-disc pl-8 space-y-1">
                        @foreach($errors->all() as $error)
                            <li class="text-sm">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl shadow-2xl ring-1 ring-white/30 overflow-hidden">
                <div class="p-6 md:p-8">
                    <form action="{{ route('instructor.schedule.update', $scheduledClass->id) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Class Type Selection -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                    Class Type
                                </span>
                            </label>
                            <select name="class_type_id"
                                    class="w-full px-4 py-2.5 bg-white/80 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all @error('class_type_id') border-red-500 @enderror"
                                    required>
                                <option value="">-- Select Class Type --</option>
                                @foreach($classTypes as $type)
                                    <option value="{{ $type->id }}" {{ old('class_type_id', $scheduledClass->class_type_id) == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }} ({{ $type->minutes }} mins)
                                    </option>
                                @endforeach
                            </select>
                            @error('class_type_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Date and Time Row -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <!-- Date -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        Date
                                    </span>
                                </label>
                                <input type="date"
                                       name="date"
                                       value="{{ old('date', $date) }}"
                                       class="w-full px-4 py-2.5 bg-white/80 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all @error('date') border-red-500 @enderror"
                                       min="{{ date('Y-m-d') }}"
                                       required>
                                @error('date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Time -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Time
                                    </span>
                                </label>
                                <select name="time"
                                        class="w-full px-4 py-2.5 bg-white/80 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all @error('time') border-red-500 @enderror"
                                        required>
                                    <option value="">Select Time</option>
                                    <option value="05:00" {{ old('time', $time) == '05:00' ? 'selected' : '' }}>5:00 AM</option>
                                    <option value="06:00" {{ old('time', $time) == '06:00' ? 'selected' : '' }}>6:00 AM</option>
                                    <option value="07:00" {{ old('time', $time) == '07:00' ? 'selected' : '' }}>7:00 AM</option>
                                    <option value="08:00" {{ old('time', $time) == '08:00' ? 'selected' : '' }}>8:00 AM</option>
                                    <option value="09:00" {{ old('time', $time) == '09:00' ? 'selected' : '' }}>9:00 AM</option>
                                    <option value="10:00" {{ old('time', $time) == '10:00' ? 'selected' : '' }}>10:00 AM</option>
                                    <option value="11:00" {{ old('time', $time) == '11:00' ? 'selected' : '' }}>11:00 AM</option>
                                    <option value="12:00" {{ old('time', $time) == '12:00' ? 'selected' : '' }}>12:00 PM</option>
                                    <option value="13:00" {{ old('time', $time) == '13:00' ? 'selected' : '' }}>1:00 PM</option>
                                    <option value="14:00" {{ old('time', $time) == '14:00' ? 'selected' : '' }}>2:00 PM</option>
                                    <option value="15:00" {{ old('time', $time) == '15:00' ? 'selected' : '' }}>3:00 PM</option>
                                    <option value="16:00" {{ old('time', $time) == '16:00' ? 'selected' : '' }}>4:00 PM</option>
                                    <option value="17:00" {{ old('time', $time) == '17:00' ? 'selected' : '' }}>5:00 PM</option>
                                    <option value="18:00" {{ old('time', $time) == '18:00' ? 'selected' : '' }}>6:00 PM</option>
                                    <option value="19:00" {{ old('time', $time) == '19:00' ? 'selected' : '' }}>7:00 PM</option>
                                    <option value="20:00" {{ old('time', $time) == '20:00' ? 'selected' : '' }}>8:00 PM</option>
                                </select>
                                @error('time')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Price Field (New) -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Price (UGX)
                                </span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 font-semibold">UGX</span>
                                <input type="number"
                                       name="price"
                                       value="{{ old('price', $scheduledClass->price) }}"
                                       class="w-full pl-12 pr-4 py-2.5 bg-white/80 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all @error('price') border-red-500 @enderror"
                                       placeholder="0"
                                       min="0"
                                       step="1000"
                                       required>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Enter the price for this class in Ugandan Shillings (UGX)</p>
                            @error('price')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Current Class Info -->
                        <div class="bg-purple-50 rounded-xl p-4 border border-purple-200">
                            <h4 class="text-sm font-semibold text-purple-800 mb-2 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Current Class Information
                            </h4>
                            <div class="grid grid-cols-2 gap-3 text-sm">
                                <div>
                                    <span class="text-gray-600">Current Price:</span>
                                    <span class="font-semibold text-purple-700 block">UGX {{ number_format($scheduledClass->price, 0) }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Current Date/Time:</span>
                                    <span class="font-semibold text-purple-700 block">{{ \Carbon\Carbon::parse($scheduledClass->date_time)->format('M d, Y h:i A') }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Class Type:</span>
                                    <span class="font-semibold text-purple-700 block">{{ $scheduledClass->classType->name ?? 'N/A' }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Booked Members:</span>
                                    <span class="font-semibold text-purple-700 block">{{ $scheduledClass->members()->count() }} members</span>
                                </div>
                            </div>
                        </div>

                        <!-- Update Button -->
                        <div class="pt-4">
                            <button type="submit"
                                    class="w-full py-3 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5">
                                <span class="flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    Update Class
                                </span>
                            </button>
                        </div>

                        <!-- Cancel Button -->
                        <div class="text-center">
                            <a href="{{ route('instructor.upcoming') }}"
                               class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Cancel and go back
                            </a>
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
                    <div class="w-14 rounded-xl flex items-center justify-center shadow-lg overflow-hidden">
                        <img src="{{ asset('images/logo.png') }}" alt="MyGym Logo" class="w-full h-full object-cover">
                    </div>
                    <h4 class="text-2xl font-bold text-white tracking-wider">My<span class="text-purple-400">Gym</span></h4>
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

            {{-- Column 2: Instructor Quick Links --}}
            <div>
                <h5 class="text-lg font-semibold text-white mb-4">Instructor Hub</h5>
                <ul class="space-y-3">
                    <li><a href="{{ route('instructor.dashboard') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">📊 Dashboard</a></li>
                    <li><a href="{{ route('instructor.create') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">🗓️ Schedule Class</a></li>
                    <li><a href="{{ route('instructor.classes') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">👥 My Classes</a></li>
                    <li><a href="{{ route('instructor.upcoming') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">📊 Upcoming Classes</a></li>
                    <li><a href="{{ route('instructor.calendar') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">📅 Calendar View</a></li>
                    <li><a href="{{ route('instructor.earnings.index') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">💵 My Earnings</a></li>
                </ul>
            </div>

            {{-- Column 3: Resources & Support --}}
            <div>
                <h5 class="text-lg font-semibold text-white mb-4">Resources</h5>
                <ul class="space-y-3">
                    <li><a href="{{ route('instructor.members.index') }}" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">👥 My Members</a></li>
                    <li><a href="#" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">📚 Training Guides</a></li>
                    <li><a href="#" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">💡 Tips & Tricks</a></li>
                    <li><a href="#" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">🎓 Certification</a></li>
                    <li><a href="#" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">❓ Help Center</a></li>
                    <li><a href="#" class="text-sm text-gray-400 hover:text-purple-400 transition duration-300">📧 Support</a></li>
                </ul>
            </div>

            {{-- Column 4: Contact Info --}}
            <div class="col-span-2 md:col-span-1">
                <h5 class="text-lg font-semibold text-white mb-4">Get In Touch</h5>
                <ul class="space-y-3 text-sm text-gray-400">
                    <li class="flex items-start">
                        <span class="mr-2 text-purple-400">📍</span>
                        <span>Ggaba road, Kampala, UGANDA</span>
                    </li>
                    <li class="flex items-start">
                        <span class="mr-2 text-purple-400">📞</span>
                        <span>+256 700 123 456</span>
                    </li>
                    <li class="flex items-start">
                        <span class="mr-2 text-purple-400">📧</span>
                        <span><a href="mailto:instructors@mygym.com" class="hover:text-purple-400">instructors@mygym.com</a></span>
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

        {{-- Copyright Section --}}
        <div class="mt-12 pt-8 border-t border-purple-500/30 text-center">
            <p class="text-sm text-gray-500">
                &copy; {{ date('Y') }} MyGym. All rights reserved. Powered by Passion.
            </p>
        </div>
    </div>
    </footer>

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

        /* Custom number input styling */
        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            opacity: 0.5;
        }

        input[type="number"]:hover::-webkit-inner-spin-button,
        input[type="number"]:hover::-webkit-outer-spin-button {
            opacity: 1;
        }
    </style>

    <script>
        // Auto-dismiss messages after 5 seconds
        setTimeout(function() {
            let successMessage = document.getElementById('successMessage');
            let errorMessage = document.getElementById('errorMessage');

            if (successMessage) {
                successMessage.style.transition = 'opacity 0.5s ease';
                successMessage.style.opacity = '0';
                setTimeout(function() {
                    successMessage.style.display = 'none';
                }, 500);
            }

            if (errorMessage) {
                errorMessage.style.transition = 'opacity 0.5s ease';
                errorMessage.style.opacity = '0';
                setTimeout(function() {
                    errorMessage.style.display = 'none';
                }, 500);
            }
        }, 5000);
    </script>
</x-app-layout>
