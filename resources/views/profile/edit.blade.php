<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Profile Settings
                </h2>
                <p class="text-sm text-gray-500 mt-1">Manage your account information and preferences</p>
            </div>
            @if(Auth::user()->role === 'member')
            <a href="{{ route('member.classes') }}"
               class="px-4 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white rounded-xl text-sm font-semibold transition-all duration-200 shadow-md hover:shadow-lg">
                Browse Classes
            </a>
            @elseif(Auth::user()->role === 'instructor')
            <a href="{{ route('instructor.dashboard') }}"
               class="px-4 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white rounded-xl text-sm font-semibold transition-all duration-200 shadow-md hover:shadow-lg">
                Instructor Dashboard
            </a>
            @elseif(Auth::user()->role === 'admin')
            <a href="{{ route('admin.dashboard') }}"
               class="px-4 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white rounded-xl text-sm font-semibold transition-all duration-200 shadow-md hover:shadow-lg">
                Admin Dashboard
            </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12 min-h-screen"
        style="background-image: url('{{ asset('images/background2.jpg') }}');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

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

            {{-- Profile Sections --}}
            <div class="space-y-6">
                {{-- Update Profile Information --}}
                <div class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-md">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-gray-900">Profile Information</h2>
                                <p class="text-sm text-gray-500 mt-0.5">Update your account's profile information and email address</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <form method="POST" action="{{ route('profile.update') }}" class="space-y-6">
                            @csrf
                            @method('PATCH')

                            <div>
                                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Name</label>
                                <input id="name" name="name" type="text" value="{{ old('name', Auth::user()->name) }}"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all bg-white/80"
                                    required autofocus autocomplete="name">
                                @error('name')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                                <input id="email" name="email" type="email" value="{{ old('email', Auth::user()->email) }}"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all bg-white/80"
                                    required autocomplete="username">
                                @error('email')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex items-center gap-4">
                                <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white rounded-xl font-semibold transition-all duration-200 shadow-md hover:shadow-lg">
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Update Password --}}
                <div class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl flex items-center justify-center shadow-md">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-gray-900">Update Password</h2>
                                <p class="text-sm text-gray-500 mt-0.5">Ensure your account is using a long, random password to stay secure</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <form method="POST" action="{{ route('profile.password.update') }}" class="space-y-6">
                            @csrf
                            @method('PUT')

                            <div>
                                <label for="current_password" class="block text-sm font-semibold text-gray-700 mb-2">Current Password</label>
                                <input id="current_password" name="current_password" type="password"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all bg-white/80"
                                    autocomplete="current-password" required>
                                @error('current_password')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">New Password</label>
                                <input id="password" name="password" type="password"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all bg-white/80"
                                    autocomplete="new-password" required>
                                @error('password')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">Confirm New Password</label>
                                <input id="password_confirmation" name="password_confirmation" type="password"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all bg-white/80"
                                    autocomplete="new-password" required>
                            </div>

                            <div class="flex items-center gap-4">
                                <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white rounded-xl font-semibold transition-all duration-200 shadow-md hover:shadow-lg">
                                    Update Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Delete Account --}}
                <div class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-pink-600 rounded-xl flex items-center justify-center shadow-md">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-gray-900">Delete Account</h2>
                                <p class="text-sm text-gray-500 mt-0.5">Permanently delete your account and all associated data</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="mb-4 p-4 bg-red-50 rounded-xl border border-red-200">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-red-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                <div>
                                    <p class="text-sm font-semibold text-red-800">Warning: This action is irreversible!</p>
                                    <p class="text-xs text-red-700 mt-1">Once your account is deleted, all of your resources and data will be permanently deleted. Please download any data you wish to keep before proceeding.</p>
                                </div>
                            </div>
                        </div>

                        <button type="button" onclick="document.getElementById('deleteAccountModal').classList.remove('hidden')"
                            class="px-6 py-2.5 bg-gradient-to-r from-red-500 to-pink-500 hover:from-red-600 hover:to-pink-600 text-white rounded-xl font-semibold transition-all duration-200 shadow-md hover:shadow-lg">
                            Delete Account
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Account Confirmation Modal --}}
    <div id="deleteAccountModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl transform transition-all">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-900">Delete Account</h3>
                <button type="button" onclick="document.getElementById('deleteAccountModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <p class="text-gray-600 mb-4">Are you sure you want to delete your account? This action cannot be undone.</p>

            <form method="POST" action="{{ route('profile.destroy') }}">
                @csrf
                @method('DELETE')

                <div class="mb-4">
                    <label for="delete_password" class="block text-sm font-medium text-gray-700 mb-2">Enter your password to confirm</label>
                    <input id="delete_password" name="password" type="password"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500"
                        placeholder="Your password" required>
                    @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="document.getElementById('deleteAccountModal').classList.add('hidden')"
                        class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold transition">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white font-semibold transition shadow-md">
                        Delete Account
                    </button>
                </div>
            </form>
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

        /* Form input focus styles */
        input:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
        }

        /* Smooth transitions */
        button, a {
            transition: all 0.2s ease;
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
