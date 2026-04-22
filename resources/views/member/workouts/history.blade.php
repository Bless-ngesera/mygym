<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Workout History
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Track your completed workouts and progress
                </p>
            </div>
            <a href="{{ route('member.dashboard') }}"
               class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white text-sm font-semibold rounded-xl transition-all duration-200 shadow-md hover:shadow-lg inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Dashboard
            </a>
        </div>
    </x-slot>

    <main class="min-h-screen"
          style="background-image: url('{{ asset('images/background2.jpg') }}');
                 background-size: cover;
                 background-position: center;
                 background-attachment: fixed;">
        <div class="mx-auto max-w-7xl space-y-6 px-4 py-6 sm:px-6 lg:px-8 md:py-8">

            {{-- Flash Messages --}}
            @if(session('success'))
                <div id="successMessage" class="flex items-center justify-between p-4 bg-emerald-50 border border-emerald-200 rounded-2xl" style="transition: opacity 0.5s ease;">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-emerald-100 rounded-xl flex items-center justify-center">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <span class="text-sm font-semibold text-emerald-800">{{ session('success') }}</span>
                    </div>
                    <button onclick="this.closest('div').style.opacity=0;setTimeout(()=>this.closest('div').remove(),500)" class="ml-4 p-1.5 rounded-lg text-emerald-500 hover:bg-emerald-100 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div id="errorMessage" class="flex items-center justify-between p-4 bg-red-50 border border-red-200 rounded-2xl" style="transition: opacity 0.5s ease;">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-red-100 rounded-xl flex items-center justify-center">
                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <span class="text-sm font-semibold text-red-800">{{ session('error') }}</span>
                    </div>
                    <button onclick="this.closest('div').style.opacity=0;setTimeout(()=>this.closest('div').remove(),500)" class="ml-4 p-1.5 rounded-lg text-red-500 hover:bg-red-100 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            @endif

            {{-- Stats Cards Row --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                {{-- Total Workouts Card --}}
                <div class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl p-5 shadow-lg">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="text-3xl font-bold text-gray-900 tracking-tight">{{ $workouts->total() }}</div>
                    <div class="text-xs font-medium text-gray-500 mt-0.5">Total Workouts</div>
                </div>

                {{-- Total Minutes Card --}}
                <div class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl p-5 shadow-lg">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2"/>
                            </svg>
                        </div>
                    </div>
                    <div class="text-3xl font-bold text-gray-900 tracking-tight">{{ number_format($totalMinutes ?? 0) }}</div>
                    <div class="text-xs font-medium text-gray-500 mt-0.5">Total Minutes</div>
                </div>

                {{-- Calories Burned Card --}}
                <div class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl p-5 shadow-lg">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 8.287 8.287 0 009 9.6a8.983 8.983 0 013.361-6.867 8.21 8.21 0 003 2.48z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="text-3xl font-bold text-gray-900 tracking-tight">{{ number_format($totalCalories ?? 0) }}</div>
                    <div class="text-xs font-medium text-gray-500 mt-0.5">Calories Burned</div>
                </div>
            </div>

            {{-- Workout History Content --}}
            <div class="bg-white/85 backdrop-blur-md border border-white/40 rounded-2xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 bg-indigo-100 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 text-sm tracking-tight">Your Workout Journey</h3>
                                <p class="text-xs text-gray-400 mt-0.5">{{ $workouts->total() }} total completed workouts</p>
                            </div>
                        </div>
                        <div class="text-xs text-gray-400">
                            Showing {{ $workouts->firstItem() ?? 0 }} - {{ $workouts->lastItem() ?? 0 }} of {{ $workouts->total() }}
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    @if($workouts->count() > 0)
                        <div class="space-y-3">
                            @foreach($workouts as $workout)
                                <div class="group p-4 rounded-xl transition-all duration-200 hover:shadow-md bg-gray-50/80 border border-gray-100 hover:border-indigo-200 hover:bg-white">
                                    <div class="flex justify-between items-start gap-4">
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 flex-wrap mb-2">
                                                <h3 class="font-semibold text-gray-900">{{ $workout->title }}</h3>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                                                    Completed
                                                </span>
                                            </div>
                                            <div class="flex items-center gap-4 flex-wrap">
                                                <p class="text-sm text-gray-500 flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                    </svg>
                                                    {{ $workout->date->format('F j, Y') }}
                                                </p>
                                                @if($workout->duration)
                                                    <p class="text-sm text-gray-500 flex items-center gap-1">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <circle cx="12" cy="12" r="10" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2"/>
                                                        </svg>
                                                        {{ $workout->duration }} min
                                                    </p>
                                                @endif
                                                @if($workout->calories_burn)
                                                    <p class="text-sm text-gray-500 flex items-center gap-1">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 8.287 8.287 0 009 9.6a8.983 8.983 0 013.361-6.867 8.21 8.21 0 003 2.48z"/>
                                                        </svg>
                                                        {{ number_format($workout->calories_burn) }} kcal
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                        <button onclick="viewWorkoutDetails({{ $workout->id }})"
                                                class="opacity-0 group-hover:opacity-100 transition-opacity duration-200 p-2 rounded-lg text-indigo-600 hover:bg-indigo-100">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6 pt-4 border-t border-gray-100">
                            {{ $workouts->links() }}
                        </div>
                    @else
                        <div class="text-center py-16">
                            <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">No workouts yet</h3>
                            <p class="text-sm text-gray-500 max-w-sm mx-auto">
                                Complete your first workout to see your history here. Every workout brings you closer to your goals!
                            </p>
                            <a href="{{ route('member.dashboard') }}"
                               class="inline-flex items-center gap-2 mt-6 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-lg shadow-indigo-200 transition-all duration-200 hover:-translate-y-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                                </svg>
                                Schedule a Workout
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </main>

    {{-- Workout Details Modal --}}
    <div id="workoutDetailsModal" class="fixed inset-0 z-[1000] hidden items-center justify-center bg-black/50 backdrop-blur-sm p-4" onclick="if(event.target===this) closeWorkoutDetailsModal()">
        <div class="w-full max-w-2xl bg-white rounded-2xl shadow-2xl overflow-hidden transform transition-all duration-300 scale-95 opacity-0" id="modalContent">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-indigo-100 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900 tracking-tight">Workout Details</h3>
                </div>
                <button onclick="closeWorkoutDetailsModal()" class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div id="workoutDetailsContent" class="max-h-[28rem] overflow-y-auto p-6 text-sm text-gray-700 custom-scrollbar"></div>
        </div>
    </div>

    <style>
        /* Pagination styling to match dashboard */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 6px;
            flex-wrap: wrap;
        }
        .pagination .page-item {
            display: inline-block;
        }
        .pagination .page-link {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            height: 36px;
            padding: 0 12px;
            border-radius: 12px;
            background: #f3f4f6;
            color: #374151;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.2s ease;
            text-decoration: none;
        }
        .pagination .page-link:hover {
            background: #e5e7eb;
            transform: translateY(-1px);
        }
        .pagination .active .page-link {
            background: #4f46e5;
            color: white;
            box-shadow: 0 4px 8px rgba(79, 70, 229, 0.2);
        }
        .pagination .disabled .page-link {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        /* Custom scrollbar */
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(79, 70, 229, 0.2); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(79, 70, 229, 0.45); }

        /* Modal animation */
        #workoutDetailsModal {
            transition: backdrop-filter 0.2s ease;
        }
    </style>

    <script>
        async function viewWorkoutDetails(workoutId) {
            try {
                const response = await fetch(`/member/workouts/${workoutId}`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }

                const data = await response.json();

                if (data.success && data.workout) {
                    const escapeHtml = (text) => {
                        if (!text) return '';
                        const div = document.createElement('div');
                        div.textContent = text;
                        return div.innerHTML;
                    };

                    const content = `
                        <div class="space-y-5">
                            <div>
                                <h4 class="text-lg font-bold text-gray-900 tracking-tight">${escapeHtml(data.workout.title)}</h4>
                                ${data.workout.description ? `<p class="mt-1 text-sm text-gray-500">${escapeHtml(data.workout.description)}</p>` : ''}
                            </div>
                            <div class="flex gap-2 flex-wrap">
                                ${data.workout.date ? `<span class="text-xs font-semibold text-indigo-700 bg-indigo-50 px-2.5 py-1 rounded-full">📅 ${escapeHtml(data.workout.date)}</span>` : ''}
                                ${data.workout.duration ? `<span class="text-xs font-semibold text-indigo-700 bg-indigo-50 px-2.5 py-1 rounded-full">⏱️ ${escapeHtml(String(data.workout.duration))} min</span>` : ''}
                                ${data.workout.calories_burn ? `<span class="text-xs font-semibold text-amber-700 bg-amber-50 px-2.5 py-1 rounded-full">🔥 ${escapeHtml(String(data.workout.calories_burn))} kcal</span>` : ''}
                            </div>
                            <div class="space-y-2">
                                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Exercises</p>
                                ${data.workout.exercises && data.workout.exercises.length > 0 ?
                                    data.workout.exercises.map((ex, i) => `
                                        <div class="flex items-center gap-3 p-3 bg-gray-50/80 border border-gray-100 rounded-xl">
                                            <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-white text-xs font-bold text-gray-500 shadow-sm">${i+1}</span>
                                            <div>
                                                <p class="text-sm font-semibold text-gray-800">${escapeHtml(ex.name)}</p>
                                                <p class="text-xs text-gray-400">${ex.pivot?.sets || 3} sets × ${ex.pivot?.reps || 12} reps${ex.pivot?.weight_kg ? ` • ${ex.pivot.weight_kg} kg` : ''}</p>
                                            </div>
                                        </div>
                                    `).join('') :
                                    '<p class="text-sm text-gray-500">No exercises recorded for this workout.</p>'
                                }
                            </div>
                        </div>
                    `;

                    const modal = document.getElementById('workoutDetailsModal');
                    const modalContent = document.getElementById('modalContent');

                    document.getElementById('workoutDetailsContent').innerHTML = content;
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');

                    setTimeout(() => {
                        modalContent.classList.remove('scale-95', 'opacity-0');
                        modalContent.classList.add('scale-100', 'opacity-100');
                    }, 10);

                    document.body.style.overflow = 'hidden';
                } else {
                    alert('Could not load workout details. Please try again.');
                }
            } catch (error) {
                console.error('Error fetching workout details:', error);
                alert('Failed to load workout details. Please check your connection and try again.');
            }
        }

        function closeWorkoutDetailsModal() {
            const modal = document.getElementById('workoutDetailsModal');
            const modalContent = document.getElementById('modalContent');

            modalContent.classList.remove('scale-100', 'opacity-100');
            modalContent.classList.add('scale-95', 'opacity-0');

            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.style.overflow = '';
            }, 200);
        }

        // Close modal on Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeWorkoutDetailsModal();
            }
        });

        // Auto-dismiss flash messages
        setTimeout(() => {
            const successMsg = document.getElementById('successMessage');
            const errorMsg = document.getElementById('errorMessage');
            if (successMsg) {
                successMsg.style.opacity = '0';
                setTimeout(() => successMsg.remove(), 500);
            }
            if (errorMsg) {
                errorMsg.style.opacity = '0';
                setTimeout(() => errorMsg.remove(), 500);
            }
        }, 5000);
    </script>
</x-app-layout>
