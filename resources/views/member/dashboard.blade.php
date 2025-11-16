<x-app-layout>
    <x-slot name="header">
        <div id="welcome-message" class="flex items-center justify-between">
            <h2 class="font-bold text-2xl text-gray-800">
                {{ __('Welcome Back, ') . Auth::user()->name . '!' }}
            </h2>
            <span class="text-sm text-gray-500">
                Last login: {{ now()->format('M d, Y') }}
            </span>
        </div>

        <script>
            // Fade out then remove the welcome message after 5s
            setTimeout(() => {
                const welcomeMessage = document.getElementById('welcome-message');
                if (welcomeMessage) {
                    welcomeMessage.style.transition = 'opacity 0.8s ease';
                    welcomeMessage.style.opacity = '0';
                    setTimeout(() => welcomeMessage.remove(), 900);
                }
            }, 5000);
        </script>
    </x-slot>

    <div class="py-10 bg-gray-50 min-h-screen"
        style="background-image: url('{{ asset('images/background2.jpg') }}'); 
        background-size: cover; 
        background-position: center; 
        background-attachment: fixed;">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <!-- Hero Section -->
            <div class="bg-white/75 backdrop-blur-sm border border-white/20 rounded-2xl shadow-2xl ring-1 ring-white/30 overflow-hidden">
                <div class="flex flex-col md:flex-row items-center justify-between p-8">
                    <div class="space-y-4">
                        <h1 class="text-3xl md:text-4xl font-bold">Welcome to <span class="text-purple-900">MyGym</span></h1>
                        <p class="text-gray-900 max-w-md">
                            Stay motivated and keep track of your workouts, schedules, and progress — every rep brings you closer to your goals. Train smart, stay consistent, and celebrate your growth!
                        </p>
                        <a href="{{ route('profile.edit') }}"
                           class="inline-block mt-3 bg-purple-400 hover:bg-purple-500 text-gray-900 font-semibold px-5 py-2 rounded-lg transition duration-300 ease-in-out shadow-md">
                           View Profile
                        </a>
                    </div>

                    <img src="https://images.pexels.com/photos/1552242/pexels-photo-1552242.jpeg?auto=compress&cs=tinysrgb&w=800"
                         alt="African fitness community"
                         loading="lazy"
                         class="rounded-xl mt-6 md:mt-0 w-full md:w-1/2 object-cover shadow-lg">
                </div>
            </div>

            <!-- Classes Section -->
            <div class="bg-white/75 backdrop-blur-sm border border-white/20 rounded-2xl shadow-2xl ring-1 ring-white/30 overflow-hidden p-8">
                <h3 class="text-xl font-semibold text-gray-900 mb-6">Our Classes</h3>

                @php
                    $classes = [
                        [
                            'title' => 'Pilates',
                            'description' => 'Strengthen your core and improve flexibility with our guided Pilates sessions, perfect for all fitness levels.',
                            'image' => 'https://images.pexels.com/photos/4804312/pexels-photo-4804312.jpeg?auto=compress&cs=tinysrgb&w=400',
                            'schedule' => 'Mon & Wed, 6 PM'
                        ],
                        [
                            'title' => 'Yoga',
                            'description' => 'Find balance and peace through our calming Yoga classes, suitable for all levels, young and old people.',
                            'image' => 'https://images.pexels.com/photos/8436605/pexels-photo-8436605.jpeg?auto=compress&cs=tinysrgb&w=400',
                            'schedule' => 'Tue & Thu, 7 AM'
                        ],
                        [
                            'title' => 'Dance Fitness',
                            'description' => 'Move to the rhythm with Afrobeat-inspired dance workouts that burn calories and uplift your spirits.',
                            'image' => 'https://images.pexels.com/photos/8957662/pexels-photo-8957662.jpeg?auto=compress&cs=tinysrgb&w=400',
                            'schedule' => 'Fri & Sat, 5 PM'
                        ],
                        [
                            'title' => 'Boxing',
                            'description' => 'Unleash your strength with high-energy boxing classes that build power and confidence and even stress relief.',
                            'image' => 'https://images.pexels.com/photos/4804040/pexels-photo-4804040.jpeg?auto=compress&cs=tinysrgb&w=400',
                            'schedule' => 'Mon & Fri, 7 PM'
                        ],
                    ];
                @endphp

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach ($classes as $class)
                        <div class="bg-gray-50 p-4 rounded-lg shadow hover:shadow-purple-900/90 transition duration-300">
                            <img src="{{ $class['image'] }}" alt="{{ $class['title'] }}" loading="lazy" class="w-full h-40 object-cover rounded-lg mb-3">
                            <h4 class="text-lg font-semibold text-gray-800">{{ $class['title'] }}</h4>
                            <p class="text-sm text-gray-600 mt-2">{{ $class['description'] }}</p>
                            <p class="text-sm text-indigo-600 font-semibold mt-2">{{ $class['schedule'] }}</p>
                            <a href="{{ route('booking.create') }}"
                               class="inline-block text-indigo-600 hover:text-indigo-800 font-semibold mt-3">Join Now</a>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Value Proposition Section -->
            <div class="bg-white/75 backdrop-blur-sm border border-white/20 rounded-2xl shadow-2xl ring-1 ring-white/30 overflow-hidden p-8 text-center">
                <h3 class="text-2xl font-bold mb-4">Why Choose MyGym?</h3>
                <p class="text-gray-900 max-w-2xl mx-auto leading-relaxed mb-6">
                    At MyGym, we’re more than a gym—we’re a community rooted in African strength and unity. Our modern facilities, expert trainers, and vibrant classes like Afrobeat Dance Fitness and Boxing empower you to achieve your goals. With affordable memberships and a welcoming environment, we make fitness accessible and fun for everyone.
                </p>
                <a href="{{ route('member.dashboard') }}"
                   class="inline-block bg-white hover:bg-gray-100 text-indigo-600 font-semibold px-6 py-3 rounded-lg transition duration-300 ease-in-out shadow-md">
                    Explore Memberships
                </a>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @php
                    $stats = [
                        ['icon' => 'https://cdn-icons-png.flaticon.com/512/1048/1048953.png', 'title' => 'Workouts Completed', 'value' => 24, 'subtitle' => 'This Month'],
                        ['icon' => 'https://cdn-icons-png.flaticon.com/512/706/706164.png', 'title' => 'Upcoming Classes', 'value' => 3, 'subtitle' => 'This Week'],
                        ['icon' => 'https://cdn-icons-png.flaticon.com/512/1680/1680324.png', 'title' => 'Personal Trainer', 'value' => 'Nzabanita Caleb', 'subtitle' => 'Next session: Tomorrow'],
                        ['icon' => 'https://cdn-icons-png.flaticon.com/512/1055/1055646.png', 'title' => 'Goal Progress', 'value' => '70%', 'subtitle' => 'Achieved'],
                    ];
                @endphp

                @foreach ($stats as $stat)
                    <div class="bg-white/75 backdrop-blur-sm border border-white/20 rounded-2xl shadow-2xl ring-1 ring-white/30 overflow-hidden p-8">
                        <img src="{{ $stat['icon'] }}" class="w-10 h-10 mb-3" alt="icon" loading="lazy">
                        <h3 class="text-lg font-semibold text-gray-800">{{ $stat['title'] }}</h3>

                        @if (is_numeric($stat['value']))
                            <p class="text-3xl font-bold text-purple-900 mt-2">{{ $stat['value'] }}</p>
                        @else
                            <p class="text-xl font-bold text-purple-900 mt-2">{{ $stat['value'] }}</p>
                        @endif

                        <p class="text-sm text-gray-500 mt-1">{{ $stat['subtitle'] }}</p>

                        @if ($stat['title'] === 'Goal Progress')
                            <div class="w-full bg-gray-200 rounded-full h-3 mt-3" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100">
                                <div class="bg-purple-900 h-3 rounded-full" style="width: 70%"></div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- Activity Feed -->
            <div class="bg-white/75 backdrop-blur-sm border border-white/20 rounded-2xl shadow-2xl ring-1 ring-white/30 overflow-hidden p-8">
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Recent Activity</h3>
                <ul class="divide-y divide-gray-200">
                    <li class="py-3 flex justify-between items-center">
                        <span class="text-gray-700">🏋️ Completed Boxing Session</span>
                        <span class="text-sm text-gray-500">2 hours ago</span>
                    </li>
                    <li class="py-3 flex justify-between items-center">
                        <span class="text-gray-700">🕺 Joined Dance Fitness Class</span>
                        <span class="text-sm text-gray-500">Yesterday</span>
                    </li>
                    <li class="py-3 flex justify-between items-center">
                        <span class="text-gray-700">🧘 Achieved Yoga Milestone</span>
                        <span class="text-sm text-gray-500">3 days ago</span>
                    </li>
                </ul>
            </div>
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
