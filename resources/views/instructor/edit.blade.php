<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Edit Class
                </h2>
                <p class="text-sm text-gray-500 mt-1">Update your class details and schedule</p>
            </div>
            <a href="{{ route('instructor.upcoming') }}"
               class="px-4 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white rounded-xl text-sm font-semibold transition-all duration-200 shadow-md hover:shadow-lg">
                ← Back to Upcoming Classes
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
