<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Class Calendar
                </h2>
                <p class="text-sm text-gray-500 mt-1">View all your scheduled classes in calendar view</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('instructor.upcoming') }}"
                   class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-semibold transition-all duration-200">
                    List View
                </a>
                <a href="{{ route('instructor.create') }}"
                   class="px-4 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white rounded-xl text-sm font-semibold transition-all duration-200 shadow-md hover:shadow-lg">
                    + Schedule New Class
                </a>
            </div>
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

            <div class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl shadow-2xl ring-1 ring-white/30 overflow-hidden">
                <div class="p-6 md:p-8">

                    {{-- Calendar Header --}}
                    <div class="flex justify-between items-center mb-6">
                        <div class="flex items-center gap-4">
                            <button id="prevMonth" class="p-2 rounded-lg hover:bg-gray-100 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                            </button>
                            <h3 id="currentMonth" class="text-xl font-bold text-gray-800"></h3>
                            <button id="nextMonth" class="p-2 rounded-lg hover:bg-gray-100 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                        </div>
                        <button id="todayBtn" class="px-4 py-2 bg-purple-100 hover:bg-purple-200 text-purple-700 rounded-lg text-sm font-semibold transition">
                            Today
                        </button>
                    </div>

                    {{-- Calendar Grid --}}
                    <div id="calendar" class="grid grid-cols-7 gap-2">
                        {{-- Weekday headers will be added by JavaScript --}}
                    </div>

                    {{-- Legend --}}
                    <div class="mt-6 pt-4 border-t border-gray-100 flex flex-wrap gap-4 justify-center">
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 bg-purple-500 rounded"></div>
                            <span class="text-sm text-gray-600">Class Scheduled</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 bg-green-500 rounded"></div>
                            <span class="text-sm text-gray-600">Multiple Classes</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 bg-orange-500 rounded"></div>
                            <span class="text-sm text-gray-600">Today</span>
                        </div>
                    </div>

                    {{-- Classes Count Summary --}}
                    <div class="mt-6 pt-4 border-t border-gray-100">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="text-center p-3 bg-purple-50 rounded-xl">
                                <p class="text-2xl font-bold text-purple-600">{{ $classes->count() }}</p>
                                <p class="text-xs text-gray-500">Total Classes</p>
                            </div>
                            <div class="text-center p-3 bg-green-50 rounded-xl">
                                <p class="text-2xl font-bold text-green-600">{{ $classes->where('date_time', '>', now())->count() }}</p>
                                <p class="text-xs text-gray-500">Upcoming</p>
                            </div>
                            <div class="text-center p-3 bg-gray-50 rounded-xl">
                                <p class="text-2xl font-bold text-gray-600">{{ $classes->where('date_time', '<', now())->count() }}</p>
                                <p class="text-xs text-gray-500">Completed</p>
                            </div>
                            <div class="text-center p-3 bg-emerald-50 rounded-xl">
                                <p class="text-2xl font-bold text-emerald-600">{{ $classes->sum('members_count') }}</p>
                                <p class="text-xs text-gray-500">Total Bookings</p>
                            </div>
                        </div>
                    </div>
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
        .calendar-day {
            min-height: 100px;
            transition: all 0.2s ease;
        }
        .calendar-day:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
    </style>

    <script>
        // Calendar data from PHP
        const classes = @json($classes);

        // Group classes by date
        const classesByDate = {};
        classes.forEach(classItem => {
            const date = new Date(classItem.date_time).toDateString();
            if (!classesByDate[date]) {
                classesByDate[date] = [];
            }
            classesByDate[date].push(classItem);
        });

        let currentDate = new Date();

        function renderCalendar() {
            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();

            const firstDay = new Date(year, month, 1);
            const lastDay = new Date(year, month + 1, 0);
            const startDate = new Date(firstDay);
            startDate.setDate(startDate.getDate() - firstDay.getDay());

            const calendarDiv = document.getElementById('calendar');
            const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            document.getElementById('currentMonth').textContent = `${monthNames[month]} ${year}`;

            // Weekday headers
            const weekdays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
            let html = weekdays.map(day =>
                `<div class="text-center py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">${day}</div>`
            ).join('');

            // Calendar days
            let currentDay = new Date(startDate);
            while (currentDay <= lastDay || currentDay.getDay() !== 0) {
                const dateStr = currentDay.toDateString();
                const isCurrentMonth = currentDay.getMonth() === month;
                const isToday = dateStr === new Date().toDateString();
                const dayClasses = currentDay.getDate();
                const dayClassesList = classesByDate[dateStr] || [];
                const hasClasses = dayClassesList.length > 0;

                html += `
                    <div class="calendar-day p-2 rounded-xl border ${isCurrentMonth ? 'border-gray-200 bg-white/50' : 'border-gray-100 bg-gray-50/50'} ${isToday ? 'ring-2 ring-purple-500' : ''} hover:shadow-md transition-all">
                        <div class="flex justify-between items-start">
                            <span class="text-sm font-medium ${isCurrentMonth ? 'text-gray-800' : 'text-gray-400'}">${currentDay.getDate()}</span>
                            ${hasClasses ? `<span class="text-xs bg-purple-100 text-purple-600 px-1.5 py-0.5 rounded-full">${dayClassesList.length}</span>` : ''}
                        </div>
                        <div class="mt-1 space-y-1">
                            ${dayClassesList.slice(0, 2).map(classItem => `
                                <div class="text-xs p-1 bg-purple-50 rounded cursor-pointer hover:bg-purple-100 transition" onclick="viewClass(${classItem.id})">
                                    <div class="font-medium text-purple-700 truncate">${classItem.class_type.name}</div>
                                    <div class="text-gray-500">${new Date(classItem.date_time).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</div>
                                    <div class="text-gray-400">${classItem.members_count || 0} booked</div>
                                </div>
                            `).join('')}
                            ${dayClassesList.length > 2 ? `<div class="text-xs text-center text-gray-400 mt-1">+${dayClassesList.length - 2} more</div>` : ''}
                        </div>
                    </div>
                `;
                currentDay.setDate(currentDay.getDate() + 1);
            }

            calendarDiv.innerHTML = html;
        }

        function viewClass(classId) {
            window.location.href = `/instructor/schedule/${classId}`;
        }

        document.getElementById('prevMonth').addEventListener('click', () => {
            currentDate.setMonth(currentDate.getMonth() - 1);
            renderCalendar();
        });

        document.getElementById('nextMonth').addEventListener('click', () => {
            currentDate.setMonth(currentDate.getMonth() + 1);
            renderCalendar();
        });

        document.getElementById('todayBtn').addEventListener('click', () => {
            currentDate = new Date();
            renderCalendar();
        });

        renderCalendar();

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
