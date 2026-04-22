<div id="scheduleWorkoutModal" class="fixed inset-0 z-[1000] hidden items-center justify-center bg-black/50 backdrop-blur-sm p-4" onclick="if(event.target===this) closeScheduleWorkoutModal()">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-indigo-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25"/></svg>
                </div>
                <h3 class="font-bold text-gray-900 tracking-tight">Schedule Workout</h3>
            </div>
            <button onclick="closeScheduleWorkoutModal()" class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="scheduleWorkoutForm" method="POST" action="{{ route('member.workouts.schedule') }}" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block mb-1.5 text-xs font-bold text-gray-600 uppercase tracking-wider">Workout Type</label>
                <select name="workout_template_id" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-800 focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 focus:bg-white outline-none transition-all duration-200">
                    <option value="">Select a workout...</option>
                    @foreach($workoutTemplates ?? [] as $template)
                        <option value="{{ $template->id }}">{{ $template->title }} ({{ $template->duration_minutes ?? 45 }} min)</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block mb-1.5 text-xs font-bold text-gray-600 uppercase tracking-wider">Date</label>
                <input type="date" name="scheduled_date" required min="{{ now()->format('Y-m-d') }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-800 focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 focus:bg-white outline-none transition-all duration-200">
            </div>
            <div>
                <label class="block mb-1.5 text-xs font-bold text-gray-600 uppercase tracking-wider">Time <span class="text-gray-400 normal-case">(optional)</span></label>
                <input type="time" name="scheduled_time" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-800 focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 focus:bg-white outline-none transition-all duration-200">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeScheduleWorkoutModal()" class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition-colors">Cancel</button>
                <button type="submit" class="flex-1 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-lg shadow-indigo-200 hover:shadow-xl transition-all duration-200 active:scale-95">Schedule</button>
            </div>
        </form>
    </div>
</div>
