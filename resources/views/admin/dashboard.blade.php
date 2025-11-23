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
        @php
            // Provide safe fallbacks if controller didn't pass these
            if (!isset($totalUsers)) {
                try {
                    $totalUsers = \App\Models\User::count();
                } catch (\Throwable $e) {
                    $totalUsers = 0;
                }
            }

            if (!isset($totalInstructors)) {
                try {
                    $totalInstructors = \App\Models\User::where('role', 'instructor')->count();
                } catch (\Throwable $e) {
                    $totalInstructors = 0;
                }
            }

            if (!isset($totalMembers)) {
                try {
                    $totalMembers = \App\Models\User::where('role', 'member')->count();
                } catch (\Throwable $e) {
                    $totalMembers = 0;
                }
            }

            if (!isset($totalEarnings)) {
                try {
                    // Sum receipts amount (payments). Adjust to Booking::sum('amount') if you track payments elsewhere.
                    $totalEarnings = \App\Models\Receipt::sum('amount') ?? 0;
                } catch (\Throwable $e) {
                    $totalEarnings = 0;
                }
            }

            // Provide a list of all instructors for the "View All" modal if controller didn't pass it
            if (!isset($allInstructors)) {
                try {
                    $allInstructors = \App\Models\User::where('role', 'instructor')->orderBy('created_at','desc')->get();
                } catch (\Throwable $e) {
                    $allInstructors = collect();
                }
            }

            // Provide a list of all members for the "View All Members" modal if controller didn't pass it
            if (!isset($allMembers)) {
                try {
                    $allMembers = \App\Models\User::where('role', 'member')->orderBy('created_at','desc')->get();
                } catch (\Throwable $e) {
                    $allMembers = collect();
                }
            }
        @endphp

        <div class="flex h-full min-h-[70vh] light:bg-gray-100"
        style="background-image: url('{{ asset('images/background2.jpg') }}');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;">
            <!-- Sidebar -->
            <aside class="w-64 bg-white dark:bg-gray-800 border-r hidden md:block">

                <nav class="w-64 bg-white dark:bg-gray-800 border-r hidden md:block p-4">
                    <a href="{{ route('admin.instructors.index') }}" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-100 light:hover:bg-gray-700">
                        Instructors
                    </a>
                    <a href="{{ route('admin.members.index') }}" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-100 light:hover:bg-gray-700">
                        Members
                    </a>
                    <a href="{{ route('admin.earnings') }}" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-100 light:hover:bg-gray-700">
                        Earnings
                    </a>

                    <a href="{{ route('admin.reports.index') }}" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-100 light:hover:bg-gray-700">
                        Reports
                    </a>
                    <a href="{{ route('admin.settings.index') }}" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-100 light:hover:bg-gray-700">
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


                <!-- Action Row -->
                <div class="flex items-center space-x-3 mb-6">
                    <a href="{{ route('admin.instructors.create') }}"
                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-900 text-white rounded inline-block">
                        + Register Instructor
                    </a>

                    <a href="{{ route('admin.instructors.index') }}"
                    class="text-sm text-gray-600 hover:underline px-4 py-2 bg-green-600 text-white rounded inline-block">
                        View All Instructors
                    </a>
                </div>


                <!-- Success Message -->
@if(session('success'))
    <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
        {{ session('success') }}
    </div>
@endif

<!-- Error Messages -->
@if($errors->any())
    <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
        <ul class="list-disc pl-5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif


                <!-- Recent Instructors table -->
                <div class="bg-white/75 backdrop-blur-sm border border-white/20 rounded-2xl p-4 shadow-2xl ring-1 ring-white/30 overflow-hidden  light:bg-gray-800 rounded-lg shadow mb-6 overflow-auto">
                    <table class="min-w-full divide-y">
                        <thead class="bg-indigo-600 light:bg-indigo-700 rounded-2xl">
                            <tr>
                                <th class="px-4 py-3 text-left text-sm text-gray-900">Name</th>
                                <th class="px-4 py-3 text-left text-sm text-gray-900">Email</th>
                                <th class="px-4 py-3 text-left text-sm text-gray-900">Joined</th>
                                <th class="px-5 py-3 text-left text-sm text-gray-900">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse($recentInstructors ?? [] as $instructor)
                                <tr class="hover:bg-gray-50 light:hover:bg-gray-700">
                                    <td class="px-4 py-3">{{ $instructor->name }}</td>
                                    <td class="px-4 py-3">{{ $instructor->email }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-500">{{ $instructor->created_at->format('M d, Y') }}</td>
                                    <td class="px-4 py-3 text-left">
                                        <a href="{{ route('admin.instructors.edit', $instructor->id) }}" class="px-2 py-1 text-indigo-600 hover:underline">
                                            Edit
                                        </a>

                                        <!-- Delete -->
                                        <form action="{{ route('admin.instructors.destroy', $instructor->id) }}"
                                            method="POST"
                                            class="inline-block delete-form"
                                            onsubmit="return confirm('Are you sure you want to delete this instructor?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2 py-1 text-red-600">
                                                Delete
                                            </button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="p-4 text-gray-500 text-center">No instructors found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Recent Members and Income summary -->
                <div class="bg-white/55 light:bg-gray-800 rounded-lg shadow mb-6 overflow-auto">
                    <div class="bg-white/55 light:bg-gray-800 rounded-lg p-4 shadow">
                        <div class="flex items-center justify-between mb-3">
                            <h2 class="font-semibold">Recent Members</h2>
                            <a href="{{ route('admin.members.index') }}" class="text-sm text-green-700 hover:underline">View All</a>
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
                                <li class="py-4 text-gray-500 text-center">No members found.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>        <!-- View All Members Modal -->
        <div id="view-members-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 modal">
            <div class="bg-white light:bg-gray-800 rounded-lg w-full max-w-3xl shadow-lg overflow-auto">
                <div class="p-4 border-b light:border-gray-700 flex justify-between items-center">
                    <h4 class="font-semibold">All Members</h4>
                    <button data-modal-close class="text-gray-500">✕</button>
                </div>
                <div class="p-4">
                    <div class="grid gap-3">
                        @forelse($allMembers as $member)
                            <div class="flex items-center justify-between p-3 border rounded-lg">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $member->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($member->name).'&background=06b6d4&color=fff' }}" alt="" class="w-12 h-12 rounded-full object-cover">
                                    <div>
                                        <div class="font-medium">{{ $member->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $member->plan->name ?? 'Plan' }} • {{ $member->status ?? 'active' }}</div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm text-gray-500">{{ $member->email }}</div>
                                    <div class="text-xs text-gray-400 mt-1">{{ $member->created_at->format('M d, Y') }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="p-4 text-gray-500">No members found.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Scripts -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <!-- Export libraries -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js" integrity="sha512-BNa5Rd3sC9zJ2mZ8k5s2R6h5G1X9v6y+Vx0GQz3f7q6a6r9y1k0PfXzVvBW2r7h2g2y5Rk9q1+W6Q2oYf3xQ2g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js" integrity="sha512-9Y5mQ8bq4q3yB+v3uJ7F9kTQq8s5kq3x2p8Gq0s3u7w2b9h1Y3Q9U1lM2g9E1nV1D5R1Y2z3Q4Y5Q6R7S8T9A==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

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

            // Export helpers
            function downloadCSV(filename, rows) {
                const csv = rows.map(r => r.map(v => `"${String(v).replace(/"/g,'""')}"`).join(',')).join('\n');
                const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = filename;
                document.body.appendChild(a);
                a.click();
                a.remove();
                URL.revokeObjectURL(url);
            }

            function exportArraysAsCSV(filename, headers, labels, data) {
                const rows = [headers];
                for (let i = 0; i < labels.length; i++) {
                    rows.push([labels[i], data[i]]);
                }
                downloadCSV(filename, rows);
            }

            async function exportElementToPDF(el, filename) {
                if (!el) return alert('Export area not found.');
                // use html2canvas then jsPDF
                const canvas = await html2canvas(el, { scale: 2 });
                const imgData = canvas.toDataURL('image/png');
                const { jsPDF } = window.jspdf || window.jspdf || {};
                try {
                    const pdf = new jsPDF('landscape', 'pt', 'a4');
                    const pdfWidth = pdf.internal.pageSize.getWidth();
                    const pdfHeight = (canvas.height * pdfWidth) / canvas.width;
                    pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
                    pdf.save(filename);
                } catch (err) {
                    // fallback if jspdf global not present
                    const a = document.createElement('a');
                    a.href = imgData;
                    a.download = filename.replace('.pdf', '.png');
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                }
            }

            // Wire export buttons
            document.getElementById('exportEarningsPdf')?.addEventListener('click', () => {
                const el = document.getElementById('earningsCard');
                exportElementToPDF(el, 'monthly-earnings.pdf');
            });

            document.getElementById('exportEarningsCsv')?.addEventListener('click', () => {
                exportArraysAsCSV('monthly-earnings.csv', ['Month', 'Earnings'], monthlyLabels, monthlyEarnings);
            });

            document.getElementById('exportInstructorCsv')?.addEventListener('click', () => {
                const headers = ['Instructor', 'Earnings'];
                const rows = instructorRaw.map(i => [i.name || i['name'] || '', i.amount || i['amount'] || 0]);
                downloadCSV('instructors-earnings.csv', [headers, ...rows]);
            });

            // Modal open/close
            // Modal open: show modal, prevent background scroll, focus first input
            document.querySelectorAll('[data-modal-open]').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    if (e) e.preventDefault();
                    const sel = btn.getAttribute('data-modal-open') || btn.getAttribute('href');
                    if (!sel) return;
                    const target = document.querySelector(sel);
                    if (!target) return;
                    target.classList.remove('hidden');
                    target.setAttribute('aria-hidden', 'false');
                    // prevent background scroll
                    document.body.style.overflow = 'hidden';
                    // focus first focusable element inside modal
                    const focusable = target.querySelector('input, button, [tabindex]:not([tabindex="-1"])');
                    if (focusable) focusable.focus();
                });
            });

            // Generalized close: close the nearest parent modal (element with class "modal")
            function closeModal(modal) {
                if (!modal) return;
                modal.classList.add('hidden');
                modal.setAttribute('aria-hidden', 'true');
                // restore background scroll (only if no other modal open)
                const anyOpen = Array.from(document.querySelectorAll('.modal')).some(m => !m.classList.contains('hidden'));
                if (!anyOpen) document.body.style.overflow = '';
            }

            document.querySelectorAll('[data-modal-close]').forEach(btn => {
                btn.addEventListener('click', () => {
                    const modal = btn.closest('.modal');
                    closeModal(modal);
                });
            });

            // Close when clicking the backdrop (i.e., the modal element itself, not the inner dialog)
            document.querySelectorAll('.modal').forEach(modal => {
                modal.addEventListener('click', (e) => {
                    if (e.target === modal) {
                        closeModal(modal);
                    }
                });
            });

            // Close on Escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    const openModal = document.querySelector('.modal:not(.hidden)');
                    if (openModal) closeModal(openModal);
                }
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

            // Helper to insert a new instructor row into the Recent Instructors table (top)
            function insertRecentInstructorRow(instr) {
                const tbody = document.querySelector('table tbody');
                if(!tbody) return;
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-gray-50 light:hover:bg-gray-700';
                tr.innerHTML = `
                    <td class="px-4 py-3">${instr.name}</td>
                    <td class="px-4 py-3">${instr.email}</td>
                    <td class="px-4 py-3 text-sm text-gray-500">${instr.joined || new Date().toLocaleDateString()}</td>
                    <td class="px-4 py-3 text-right">
                        <a href="#" class="px-2 py-1 text-indigo-600">Edit</a>
                        <form action="#" method="POST" class="inline-block delete-form">
                            <button type="button" class="px-2 py-1 text-red-600">Delete</button>
                        </form>
                    </td>
                `;
                // insert at top
                if(tbody.firstChild) tbody.insertBefore(tr, tbody.firstChild);
            }
+
+            // Also add to All Instructors modal list if present
+            function insertAllInstructorsListItem(instr) {
+                const modal = document.getElementById('view-instructors-modal');
+                if(!modal) return;
+                const container = modal.querySelector('.grid');
+                if(!container) return;
+                const div = document.createElement('div');
+                div.className = 'flex items-center justify-between p-3 border rounded-lg';
+                div.innerHTML = `
+                    <div class="flex items-center gap-3">
+                        <img src="${instr.photo || 'https://ui-avatars.com/api/?name='+encodeURIComponent(instr.name)+'&background=4F46E5&color=fff'}" class="w-12 h-12 rounded-full object-cover">
+                        <div>
+                            <div class="font-medium">${instr.name}</div>
+                            <div class="text-sm text-gray-500">${instr.specialty || 'General'}</div>
+                        </div>
+                    </div>
+                    <div class="text-sm text-gray-500">${instr.email}</div>
+                `;
+                container.insertBefore(div, container.firstChild);
+            }
+
+            // Intercept instructor register form to attempt AJAX submit and fallback to optimistic UI update
+            const instructorForm = document.getElementById('instructor-form');
+            if (instructorForm) {
+                instructorForm.addEventListener('submit', async (e) => {
+                    e.preventDefault();
+                    const submitBtn = instructorForm.querySelector('button[type="submit"]');
+                    const fd = new FormData(instructorForm);
+                    const instr = {
+                        name: fd.get('name') || 'Unnamed',
+                        email: fd.get('email') || '',
+                        specialty: fd.get('specialty') || '',
+                        photo: ''
+                    };
+                    // Try to set preview photo if user selected a file
+                    const file = fd.get('photo');
+                    if (file && file instanceof File) {
+                        instr.photo = URL.createObjectURL(file);
+                    }
+                    // disable button while processing
+                    if(submitBtn) submitBtn.disabled = true;
+                    try {
+                        // include same-origin credentials and request JSON response
+                        const action = instructorForm.getAttribute('action') || window.location.href;
+                        const response = await fetch(action, {
+                            method: 'POST',
+                            body: fd,
+                            credentials: 'same-origin',
+                            headers: {
+                                'X-Requested-With': 'XMLHttpRequest',
+                                'Accept': 'application/json'
+                            }
+                        });
+
+                        if (response.ok) {
+                            // parse JSON if available
+                            let data = null;
+                            try { data = await response.json(); } catch (err) { data = null; }
+
+                            const serverInstr = data ? {
+                                name: data.name || instr.name,
+                                email: data.email || instr.email,
+                                specialty: data.specialty || instr.specialty,
+                                photo: data.photo_url || instr.photo,
+                                joined: data.joined || new Date().toLocaleDateString()
+                            } : {
+                                name: instr.name,
+                                email: instr.email,
+                                specialty: instr.specialty,
+                                photo: instr.photo,
+                                joined: new Date().toLocaleDateString()
+                            };
+
+                            insertRecentInstructorRow(serverInstr);
+                            insertAllInstructorsListItem(serverInstr);
+                        } else if (response.status === 422) {
+                            // validation error: try to show first message
+                            let errJson = {};
+                            try { errJson = await response.json(); } catch (_) { errJson = {}; }
+                            const first = (errJson.errors && Object.values(errJson.errors)[0]) ? Object.values(errJson.errors)[0][0] : (errJson.message || 'Validation error');
+                            alert(first);
+                        } else {
+                            // fallback: optimistic UI update even if server returned other error
+                            insertRecentInstructorRow(instr);
+                            insertAllInstructorsListItem(instr);
+                        }
+                    } catch (err) {
+                        // network error - optimistic update
+                        insertRecentInstructorRow(instr);
+                        insertAllInstructorsListItem(instr);
+                    } finally {
+                        if(submitBtn) submitBtn.disabled = false;
+                        // close modal
+                        const modal = instructorForm.closest('.modal');
+                        if (modal) modal.classList.add('hidden');
+                        // reset form
+                        instructorForm.reset();
+                        if(photoPreview) { photoPreview.src = ''; photoPreview.classList.add('hidden'); }
+                    }
+                });
+            }
+
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

document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('instructor-modal');
    const openLinks = document.querySelectorAll('.open-modal');
    const closeLinks = document.querySelectorAll('.close-modal');

    // Open modal
    openLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            modal.classList.remove('hidden');
        });
    });

    // Close modal
    closeLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            modal.classList.add('hidden');
        });
    });

    // Close when clicking outside modal content
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.classList.add('hidden');
        }
    });
});

        </script>
        @endverbatim
    @endif


</x-app-layout>
