<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            Upcoming Classes
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen"
        style="background-image: url('{{ asset('images/background2.jpg') }}'); 
        background-size: cover; 
        background-position: center; 
        background-attachment: fixed;">
        
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @forelse ($bookings as $class)
                <div class="bg-white/75 backdrop-blur-sm border border-white/20 rounded-2xl shadow-2xl ring-1 ring-white/30 overflow-hidden p-8">

                    <div class="flex flex-col md:flex-row justify-between gap-6">
                        <!-- Class Info -->
                        <div>
                            <p class="text-2xl font-semibold text-purple-700">{{ $class->classType->name }}</p>
                            <p class="text-sm text-gray-600">{{ $class->instructor->name }}</p>
                        </div>

                        <!-- Date & Time -->
                        <div class="text-right flex-shrink-0">
                            <p class="text-lg font-bold text-gray-800">{{ $class->date_time->format('g:i a') }}</p>
                            <p class="text-sm text-gray-500">{{ $class->date_time->format('jS M') }}</p>
                        </div>
                    </div>

                    <!-- Cancel Button -->
                    <div class="mt-4 text-right">
                        <form method="post" action="{{ route('booking.destroy', $class->id) }}">
                            @csrf
                            @method('delete')
                            <x-danger-button 
                                class="px-4 py-2 bg-red-600 hover:bg-red-700 rounded-lg text-white font-semibold transition"
                                onclick="return confirm('Are you sure you want to cancel this class?')">
                                Cancel Booking
                            </x-danger-button>
                        </form>
                    </div>

                </div>
            @empty
                <!-- Empty State -->
                <div class="bg-white/75 backdrop-blur-sm border border-white/20 rounded-2xl shadow-2xl ring-1 ring-white/30 overflow-hidden p-5 text-center space-y-2">
                    <p class="text-gray-700 text-lg font-medium">
                        You have no upcoming bookings.
                    </p>
                    <p class="text-gray-500">
                        Browse available classes and book one to stay on track with your fitness goals!
                    </p>
                    <a href="{{ route('booking.create') }}" 
                       class="inline-block bg-purple-600 hover:bg-purple-700 text-white font-semibold px-5 py-2 rounded-lg transition">
                        Browse Classes
                    </a>
                </div>
            @endforelse

        </div>
    </div>

     <footer class="bg-gray-900 border-t border-indigo-500/30">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 gap-8 md:grid-cols-4 lg:grid-cols-5">
                {{-- Column 1: Logo/Brand Info --}}
                <div class="col-span-2 md:col-span-1 lg:col-span-2">
                    <h4 class="text-2xl font-bold text-white mb-4 tracking-wider">My<span class="text-yellow-400">Gym</span></h4>
                    <p class="text-sm text-gray-400 leading-relaxed">
                        Train smart, stay consistent, and celebrate your growth. We're a community rooted in African strength and unity.
                    </p>
                    <div class="flex space-x-4 mt-4">
                        <a href="#" class="text-gray-400 hover:text-indigo-400 transition duration-300" aria-label="Facebook">
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.77l-.44 2.89h-2.33v6.987A10 10 0 0022 12z" clip-rule="evenodd" /></svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-indigo-400 transition duration-300" aria-label="Instagram">
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M12.315 2c2.43 0 2.715.01 3.67.058 1.036.05 1.745.21 2.37.456.684.276 1.258.74 1.717 1.259.46.52.825 1.094 1.102 1.717.246.625.407 1.334.456 2.37.048.955.058 1.23.058 3.67s-.01 2.715-.058 3.67c-.05.97-.21 1.745-.456 2.37-.276.684-.74 1.258-1.259 1.717-.52.46-1.094.825-1.717 1.102-.625.246-1.334.407-2.37.456-.955.048-1.23.058-3.67.058s-2.715-.01-3.67-.058c-.97-.05-1.745-.21-2.37-.456-.684-.276-1.258-.74-1.717-1.259-.46-.52-.825-1.094-1.102-1.717-.246-.625-.407-1.334-.456-2.37-.048-.955-.058-1.23-.058-3.67s.01-2.715.058-3.67c.05-.97.21-1.745.456-2.37.276-.684.74-1.258 1.259-1.717.46-.52 1.094-.825 1.717-1.102.625-.246 1.334-.407 2.37-.456C9.59 2.01 9.875 2 12.315 2zm0 1.637c-2.35 0-2.6.01-3.535.056-.983.05-1.503.21-1.85.347-.417.164-.78.384-1.095.698-.315.315-.534.678-.698 1.095-.137.347-.297.867-.347 1.85-.046.935-.056 1.185-.056 3.535s.01 2.6.056 3.535c.05.983.21 1.503.347 1.85.164.417.384.78.698 1.095.315.315.678.534 1.095.698.347.137.867.297 1.85.347.935.046 1.185.056 3.535.056s2.6-.01 3.535-.056c.983-.05 1.503-.21 1.85-.347.417-.164.78-.384 1.095-.698.315-.315.534-.678.698-1.095.137-.347.297-.867.347-1.85.046-.935.056-1.185.056-3.535s-.01-2.6-.056-3.535c-.05-.983-.21-1.503-.347-1.85-.164-.417-.384-.78-.698-1.095-.315-.315-.678-.534-1.095-.698-.347-.137-.867-.297-1.85-.347-.935-.046-1.185-.056-3.535-.056zM12.315 5.564c-3.714 0-6.75 3.036-6.75 6.75s3.036 6.75 6.75 6.75 6.75-3.036 6.75-6.75-3.036-6.75-6.75-6.75zm0 11.235c-2.476 0-4.485-2.009-4.485-4.485S9.839 7.828 12.315 7.828s4.485 2.009 4.485 4.485-2.009 4.485-4.485 4.485zm4.991-9.982c-.52 0-.942-.423-.942-.942s.422-.942.942-.942.942.423.942.942-.422.942-.942.942z" clip-rule="evenodd" /></svg>
                        </a>
                    </div>
                </div>

                {{-- Column 2: Quick Links --}}
                <div>
                    <h5 class="text-lg font-semibold text-white mb-4">Quick Links</h5>
                    <ul class="space-y-3">
                        <li><a href="{{ route('member.dashboard') }}" class="text-sm text-gray-400 hover:text-indigo-400 transition duration-300">Dashboard</a></li>
                        <li><a href="{{ route('booking.create') }}" class="text-sm text-gray-400 hover:text-indigo-400 transition duration-300">Book a Class</a></li>
                        <li><a href="{{ route('profile.edit') }}" class="text-sm text-gray-400 hover:text-indigo-400 transition duration-300">Manage Profile</a></li>
                        <li><a href="#" class="text-sm text-gray-400 hover:text-indigo-400 transition duration-300">Contact Support</a></li>
                    </ul>
                </div>

                {{-- Column 3: Classes --}}
                <div>
                    <h5 class="text-lg font-semibold text-white mb-4">Popular Classes</h5>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-sm text-gray-400 hover:text-indigo-400 transition duration-300">Pilates</a></li>
                        <li><a href="#" class="text-sm text-gray-400 hover:text-indigo-400 transition duration-300">Yoga</a></li>
                        <li><a href="#" class="text-sm text-gray-400 hover:text-indigo-400 transition duration-300">Dance Fitness</a></li>
                        <li><a href="#" class="text-sm text-gray-400 hover:text-indigo-400 transition duration-300">Boxing</a></li>
                    </ul>
                </div>

                {{-- Column 4: Contact Info --}}
                <div class="col-span-2 md:col-span-1">
                    <h5 class="text-lg font-semibold text-white mb-4">Get In Touch</h5>
                    <ul class="space-y-3 text-sm text-gray-400">
                        <li class="flex items-start">
                            <span class="mr-2 text-indigo-400">📍</span>
                            <span>Ggaba road, Kampala, UGANDA</span>
                        </li>
                        <li class="flex items-start">
                            <span class="mr-2 text-indigo-400">📞</span>
                            <span>+256 700 123 456</span>
                        </li>
                        <li class="flex items-start">
                            <span class="mr-2 text-indigo-400">📧</span>
                            <span><a href="mailto:info@mygym.com" class="hover:text-indigo-400">info@mygym.com</a></span>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Copyright Section --}}
            <div class="mt-12 pt-8 border-t border-indigo-500/30 text-center">
                <p class="text-sm text-gray-500">
                    &copy; {{ date('Y') }} MyGym. All rights reserved. Powered by Passion.
                </p>
            </div>
        </div>
    </footer>
</x-app-layout>
