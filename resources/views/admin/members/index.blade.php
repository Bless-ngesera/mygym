<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Members</h2>
                <p class="text-sm text-gray-500 mt-1">All registered members</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.dashboard') }}" class="px-3 py-2 text-sm bg-white rounded shadow">Back to Dashboard</a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <!-- Search + Count -->
            <div class="mb-4 flex justify-between items-center">
                <input
                    id="member-search"
                    type="search"
                    placeholder="Search members..."
                    class="w-1/3 px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                />
                <div class="text-sm text-gray-600">
                    Total: <span id="member-count" class="font-medium">{{ $members->count() }}</span>
                </div>
            </div>

            <!-- No results message -->
            <div id="no-results" class="hidden p-6 text-center text-gray-500 bg-white rounded-lg shadow">
                No matching members found.
            </div>

            <!-- Member cards -->
            <div class="grid gap-3" id="member-grid">
                @forelse($members as $m)
                    <div class="member-card flex items-center justify-between p-4 bg-white rounded-lg shadow"
                         data-name="{{ strtolower($m->name) }}"
                         data-email="{{ strtolower($m->email) }}"
                         data-plan="{{ strtolower($m->plan->name ?? 'plan') }}"
                         data-status="{{ strtolower($m->status ?? 'active') }}">
                        <div class="flex items-center gap-4">
                            <img src="{{ $m->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($m->name).'&background=06b6d4&color=fff' }}"
                                 class="w-14 h-14 rounded-full object-cover" alt="">
                            <div>
                                <div class="font-semibold">{{ $m->name }}</div>
                                <div class="text-sm text-gray-500">{{ $m->plan->name ?? 'Plan' }} • {{ $m->status ?? 'active' }}</div>
                                <div class="text-xs text-gray-400 mt-1">Joined {{ $m->created_at->format('M d, Y') }}</div>
                            </div>
                        </div>
                        <div class="text-sm text-gray-600">{{ $m->email }}</div>
                    </div>
                @empty
                    <div class="p-6 text-center text-gray-500 bg-white rounded-lg shadow">No members found.</div>
                @endforelse
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('member-search');
            const cards = document.querySelectorAll('#member-grid .member-card');
            const countEl = document.getElementById('member-count');
            const noResults = document.getElementById('no-results');

            // Normalize text (handles accents, case)
            const normalize = str => str.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase();

            function filterMembers(query) {
                let visibleCount = 0;
                const q = normalize(query);

                cards.forEach(card => {
                    const text = normalize(card.textContent);
                    const match = text.includes(q);
                    card.style.display = match ? '' : 'none';
                    if (match) visibleCount++;
                });

                countEl.textContent = visibleCount;
                noResults.style.display = visibleCount === 0 ? '' : 'none';
            }

            // Debounce for smoother UX
            let debounceTimer;
            searchInput.addEventListener('input', function () {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    filterMembers(this.value);
                }, 200);
            });

            // Allow ESC to clear search
            searchInput.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    this.value = '';
                    filterMembers('');
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
