<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Instructors</h2>
                <p class="text-sm text-gray-500 mt-1">All registered instructors</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.dashboard') }}"
                   class="px-3 py-2 text-sm bg-white rounded shadow hover:bg-gray-50 transition">
                   Back to Dashboard
                </a>
                <a href="{{ route('admin.instructors.create') }}"
                   class="px-4 py-2 bg-indigo-600 hover:bg-indigo-900 text-white rounded inline-block">
                    + Register Instructor
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4 flex justify-between items-center">
                <input id="instructor-search" type="search" placeholder="Search instructors..."
                       class="w-1/3 px-3 py-2 border rounded-lg" />
                <div class="text-sm text-gray-600">
                    Total: <span class="font-medium">{{ $instructors->count() }}</span>
                </div>
            </div>

            <div class="grid gap-3">
                @forelse($instructors as $ins)
                    <div class="flex items-center justify-between p-4 bg-white rounded-lg shadow">
                        <div class="flex items-center gap-4">
                            <!-- Avatar + Name block -->
                            <img src="{{ $ins->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($ins->name).'&background=06b6d4&color=fff' }}"
                                class="w-14 h-14 rounded-full object-cover" alt="Instructor photo">

                            <div>
                                <div class="font-semibold text-gray-900">{{ $ins->name }}</div>
                                <div class="text-sm text-gray-500">{{ $ins->specialty ?? 'General' }}</div>
                                <div class="text-xs text-gray-400 mt-1">Joined {{ $ins->created_at->format('M d, Y') }}</div>
                            </div>
                        </div>

                        <div class="flex flex-col items-end">
                            <div class="text-sm text-gray-600">{{ $ins->email }}</div>
                            <div class="mt-2 flex gap-2">
                                <a href="{{ route('admin.instructors.edit', $ins->id) }}"
                                class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded text-sm">Edit</a>

                                <form action="{{ route('admin.instructors.destroy', $ins->id) }}"
                                    method="POST"
                                    onsubmit="return confirm('Are you sure you want to delete this instructor?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="px-3 py-1 bg-red-100 text-red-700 rounded text-sm">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-gray-500 bg-white rounded-lg shadow">No instructors found.</div>
                @endforelse

            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.getElementById('instructor-search')?.addEventListener('input', function(){
            const q = this.value.toLowerCase();
            document.querySelectorAll('main .grid > div').forEach(card => {
                const txt = card.innerText.toLowerCase();
                card.style.display = txt.includes(q) ? '' : 'none';
            });
        });
    </script>
    @endpush
</x-app-layout>
