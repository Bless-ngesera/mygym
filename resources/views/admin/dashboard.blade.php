<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                MyGym Admin
                <p class="text-sm text-gray-500 mt-1">Control Panel</p>
            </h2>

            <div class="flex items-center space-x-4">
                <div class="hidden sm:block">
                    <input id="admin-search" type="search" placeholder="Search instructors, members..." class="px-3 py-2 border rounded-lg text-sm" />
                </div>

                <div class="flex items-center space-x-3">
                    <span class="text-gray-700 font-semibold">{{ Auth::user()->name ?? 'Admin' }}</span>
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'Admin') }}&background=4F46E5&color=fff" alt="Avatar" class="w-9 h-9 rounded-full">
                </div>
            </div>
        </div>
    </x-slot>

    @if(!Auth::check() || (Auth::user()->role ?? '') !== 'admin')
        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow-sm sm:rounded-lg p-6 text-center">
                    <h3 class="text-xl font-semibold">Unauthorized</h3>
                    <p class="text-gray-600 mt-2">You do not have permission to access the admin panel.</p>
                </div>
            </div>
        </div>
    @else
        <div class="flex h-full min-h-[70vh] light:bg-gray-100"
        style="background-image: url('{{ asset('images/background2.jpg') }}'); 
        background-size: cover; 
        background-position: center; 
        background-attachment: fixed;">
            <!-- Sidebar -->
            <aside class="w-64 bg-white dark:bg-gray-800 border-r hidden md:block">

                <nav class="w-64 bg-white dark:bg-gray-800 border-r hidden md:block p-4">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-100 light:hover:bg-gray-700">
                        Instructors
                    </a>
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-100 light:hover:bg-gray-700">
                        Members
                    </a>
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-100 light:hover:bg-gray-700">
                        Earnings
                    </a>
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-100 light:hover:bg-gray-700">
                        Reports
                    </a>
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-100 light:hover:bg-gray-700">
                        Settings
                    </a>
                </nav>

                <div class="mt-auto p-4 border-t light:border-gray-700 text-xs text-gray-500">
                    &copy; {{ date('Y') }} MyGym Uganda
                </div>
            </aside>

            <!-- Main -->
            <div class="flex-1 p-6">
                <!-- Summary cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6 ">
                    <div class="bg-white/75 backdrop-blur-sm border border-white/20 rounded-2xl p-4 shadow-2xl ring-1 ring-white/30 overflow-hidden">
                        <div class="text-sm text-gray-900">Total Users</div>
                        <div class="text-2xl font-bold">{{ $totalUsers ?? 0 }}</div>
                    </div>

                    <div class="bg-white/75 backdrop-blur-sm border border-white/20 rounded-2xl p-4 shadow-2xl ring-1 ring-white/30 overflow-hidden">
                        <div class="text-sm text-gray-900">Instructors</div>
                        <div class="text-2xl font-bold">{{ $totalInstructors ?? 0 }}</div>
                    </div>

                    <div class="bg-white/75 backdrop-blur-sm border border-white/20 rounded-2xl p-4 shadow-2xl ring-1 ring-white/30 overflow-hidden">
                        <div class="text-sm text-gray-900">Members</div>
                        <div class="text-2xl font-bold">{{ $totalMembers ?? 0 }}</div>
                    </div>

                    <div class="bg-white/75 backdrop-blur-sm border border-white/20 rounded-2xl p-4 shadow-2xl ring-1 ring-white/30 overflow-hidden">
                        <div class="text-sm text-gray-900">Total Earnings</div>
                        <div class="text-2xl font-bold">UGX {{ number_format($totalEarnings ?? 0, 2) }}</div>
                    </div>
                </div>

                <!-- Charts + Controls -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                    <div class="col-span-2 rounded-lg p-4 bg-white/75 backdrop-blur-sm border border-white/20 rounded-2xl shadow-2xl ring-1 ring-white/30 overflow-hidden">
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="font-semibold">Monthly Earnings</h3>
                            <div class="flex items-center space-x-2">
                                <select id="month-filter" class="px-2 py-1 border rounded">
                                    <option value="12">Last 12 months</option>
                                    <option value="6">Last 6 months</option>
                                    <option value="3">Last 3 months</option>
                                </select>
                                <a href="{{ route('admin.dashboard', ['type'=>'pdf','report'=>'earnings']) }}" class="px-3 py-1 bg-indigo-600 text-white rounded">Export PDF</a>
                                <a href="{{ route('admin.dashboard', ['type'=>'excel','report'=>'earnings']) }}" class="px-3 py-1 bg-green-600 text-white rounded">Export Excel</a>
                            </div>
                        </div>
                        <canvas id="earningsChart" height="120"></canvas>
                    </div>

                    <div class="p-4 bg-white/75 backdrop-blur-sm border border-white/20 rounded-2xl shadow-2xl ring-1 ring-white/30 overflow-hidden">
                        <h3 class="font-semibold mb-3">Earnings By Instructor</h3>
                        <canvas id="instructorChart" height="200"></canvas>
                    </div>
                </div>

                <!-- Action Row -->
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold bg-white/75 backdrop-blur-sm border border-white/20 rounded-2xl shadow-2xl ring-1 ring-white/30 overflow-hidden p-2">Recent Instructors</h3>
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-indigo-600 text-white rounded" data-modal-open="#instructor-modal">+ Register Instructor</a>
                        <a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-600">View All</a>
                    </div>
                </div>

                <!-- Recent Instructors table -->
                <div class="bg-white light:bg-gray-800 rounded-lg shadow mb-6 overflow-auto">
                    <table class="min-w-full divide-y">
                        <thead class="bg-gray-50 light:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left text-sm text-gray-600">Name</th>
                                <th class="px-4 py-3 text-left text-sm text-gray-600">Email</th>
                                <th class="px-4 py-3 text-left text-sm text-gray-600">Specialty</th>
                                <th class="px-4 py-3 text-left text-sm text-gray-600">Joined</th>
                                <th class="px-4 py-3 text-right text-sm text-gray-600">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse($recentInstructors ?? [] as $instructor)
                                <tr class="hover:bg-gray-50 light:hover:bg-gray-700">
                                    <td class="px-4 py-3">{{ $instructor->name }}</td>
                                    <td class="px-4 py-3">{{ $instructor->email }}</td>
                                    <td class="px-4 py-3">{{ $instructor->specialty ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-500">{{ $instructor->created_at->format('M d, Y') }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('admin.dashboard', $instructor->id) }}" class="px-2 py-1 text-indigo-600">Edit</a>
                                        <form action="{{ route('admin.dashboard', $instructor->id) }}" method="POST" class="inline-block delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2 py-1 text-red-600">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="p-4 text-gray-500">No instructors found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Recent Members and Income summary -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-white light:bg-gray-800 rounded-lg p-4 shadow">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="font-semibold">Recent Members</h3>
                            <a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-600">View All</a>
                        </div>
                        <ul class="divide-y">
                            @forelse($recentMembers ?? [] as $member)
                                <li class="py-3 flex justify-between items-center">
                                    <div>
                                        <div class="font-medium">{{ $member->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $member->plan->name ?? 'Plan' }} • {{ $member->status ?? 'active' }}</div>
                                    </div>
                                    <div class="text-sm text-gray-500">{{ $member->created_at->format('M d, Y') }}</div>
                                </li>
                            @empty
                                <li class="py-4 text-gray-500">No members found.</li>
                            @endforelse
                        </ul>
                    </div>

                    <div class="bg-white light:bg-gray-800 rounded-lg p-4 shadow">
                        <h3 class="font-semibold mb-3">Income Summary by Plan</h3>
                        <canvas id="planChart" height="200"></canvas>
                        <div class="mt-3 text-sm text-gray-500">
                            Generate detailed reports:
                            <a href="{{ route('admin.dashboard', ['type'=>'pdf','report'=>'plans']) }}" class="text-indigo-600">PDF</a> |
                            <a href="{{ route('admin.dashboard', ['type'=>'excel','report'=>'plans']) }}" class="text-green-600">Excel</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Instructor Modal (Register) -->
        <div id="instructor-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="bg-white light:bg-gray-800 rounded-lg w-full max-w-2xl shadow-lg overflow-auto">
                <div class="p-4 border-b light:border-gray-700 flex justify-between items-center">
                    <h4 class="font-semibold">Register Instructor</h4>
                    <button data-modal-close class="text-gray-500">✕</button>
                </div>
                <form action="{{ route('admin.dashboard') }}" method="POST" enctype="multipart/form-data" class="p-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <input name="name" placeholder="Full name" required class="px-3 py-2 border rounded" />
                        <input name="email" type="email" placeholder="Email" required class="px-3 py-2 border rounded" />
                        <input name="phone" placeholder="Phone" class="px-3 py-2 border rounded" />
                        <input name="specialty" placeholder="Specialty" class="px-3 py-2 border rounded" />
                        <input name="experience" placeholder="Years of experience" type="number" min="0" class="px-3 py-2 border rounded" />
                        <div>
                            <label class="block text-sm text-gray-600">Photo</label>
                            <input name="photo" type="file" accept="image/*" id="photo-input" class="mt-1" />
                            <img id="photo-preview" src="" alt="" class="mt-2 w-24 h-24 object-cover rounded hidden" />
                        </div>
                    </div>

                    <div class="mt-4 flex justify-end space-x-2">
                        <button type="button" data-modal-close class="px-4 py-2 border rounded">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">Save</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Scripts -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        @verbatim
        <script>
            // Data from controller (fallbacks)
                const monthlyLabels = @json($monthlyLabels ?? ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']);
                const monthlyEarnings = @json($monthlyEarnings ?? [0,0,0,0,0,0,0,0,0,0,0,0]);

                const instructorRaw = @json($instructorEarnings ?? []);
                const instructorLabels = (instructorRaw || []).map(i => i.name ?? i['name'] ?? '');
                const instructorData = (instructorRaw || []).map(i => i.amount ?? i['amount'] ?? 0);

                const planRaw = @json($planEarnings ?? []);
                const planLabels = (planRaw || []).map(p => p.plan ?? p['plan'] ?? '');
                const planData = (planRaw || []).map(p => p.amount ?? p['amount'] ?? 0);

            // Earnings chart
            const ctx = document.getElementById('earningsChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: monthlyLabels,
                    datasets: [{
                        label: 'Earnings',
                        data: monthlyEarnings,
                        backgroundColor: 'rgba(79,70,229,0.08)',
                        borderColor: 'rgba(79,70,229,1)',
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: { responsive: true, plugins: { legend: { display: false } } }
            });

            // Instructor donut
            const ctx2 = document.getElementById('instructorChart').getContext('2d');
            new Chart(ctx2, {
                type: 'doughnut',
                data: {
                    labels: instructorLabels,
                    datasets: [{ data: instructorData, backgroundColor: ['#6366f1','#8b5cf6','#ec4899','#f97316','#10b981'] }]
                },
                options: { responsive: true }
            });

            // Plan bar/pie
            const ctx3 = document.getElementById('planChart').getContext('2d');
            new Chart(ctx3, {
                type: 'bar',
                data: {
                    labels: planLabels,
                    datasets: [{ label: 'Income', data: planData, backgroundColor: 'rgba(16,185,129,0.7)' }]
                },
                options: { responsive: true, plugins: { legend: { display: false } } }
            });

            // light mode toggle
            const lightToggle = document.getElementById('light-toggle');
            lightToggle.addEventListener('click', () => {
                document.documentElement.classList.toggle('light');
            });

            // Modal open/close
            document.querySelectorAll('[data-modal-open]').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    const sel = btn.getAttribute('data-modal-open') || btn.getAttribute('href');
                    if (!sel) return;
                    document.querySelector(sel).classList.remove('hidden');
                });
            });
            document.querySelectorAll('[data-modal-close]').forEach(btn => {
                btn.addEventListener('click', () => btn.closest('#instructor-modal').classList.add('hidden'));
            });

            // Photo preview
            const photoInput = document.getElementById('photo-input');
            const photoPreview = document.getElementById('photo-preview');
            if(photoInput){
                photoInput.addEventListener('change', e => {
                    const file = e.target.files[0];
                    if(!file) return photoPreview.classList.add('hidden');
                    const url = URL.createObjectURL(file);
                    photoPreview.src = url;
                    photoPreview.classList.remove('hidden');
                });
            }

            // Delete confirmation
            document.querySelectorAll('.delete-form').forEach(form => {
                form.addEventListener('submit', function(e){
                    if(!confirm('Are you sure you want to delete this item?')) e.preventDefault();
                });
            });

            // Simple search handler (client-side filter)
            const searchInput = document.getElementById('admin-search');
            if(searchInput){
                searchInput.addEventListener('input', function(){
                    const q = this.value.toLowerCase();
                    document.querySelectorAll('table tbody tr').forEach(row => {
                        const txt = row.innerText.toLowerCase();
                        row.style.display = txt.includes(q) ? '' : 'none';
                    });
                });
            }
        </script>
        @endverbatim
    @endif
</x-app-layout>
