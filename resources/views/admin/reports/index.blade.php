<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Reports</h2>
                <p class="text-sm text-gray-500 mt-1">Generate financial and membership reports</p>
            </div>
            <div>
                <a href="{{ route('admin.dashboard') }}" class="px-3 py-2 text-sm bg-white rounded shadow">Back to Dashboard</a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Filters -->
            <div class="bg-white rounded-lg shadow p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div class="flex items-center gap-3 flex-1">
                    <select id="report-type" class="px-3 py-2 border rounded w-48">
                        <option value="income_year">Income by year</option>
                        <option value="income_month">Income by month</option>
                        <option value="instructors">Instructors earnings</option>
                        <option value="members">Members growth</option>
                    </select>

                    <input id="report-from" type="date" class="px-3 py-2 border rounded" />
                    <span class="text-sm text-gray-400">to</span>
                    <input id="report-to" type="date" class="px-3 py-2 border rounded" />
                </div>

                <div class="flex items-center gap-2">
                    <button id="generate-report" class="px-3 py-2 bg-indigo-600 text-white rounded">Generate</button>
                    <button id="download-pdf" class="px-3 py-2 bg-gray-100 rounded">Download PDF</button>
                    <button id="download-excel" class="px-3 py-2 bg-green-600 text-white rounded">Download Excel</button>
                </div>
            </div>

            <!-- Results -->
            <div id="report-results" class="bg-white rounded-lg shadow p-4 min-h-[200px]">
                <p class="text-gray-500">Select report type and date range, then click "Generate".</p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    document.getElementById('generate-report')?.addEventListener('click', () => {
        const type = document.getElementById('report-type').value;
        const from = document.getElementById('report-from').value;
        const to   = document.getElementById('report-to').value;
        const el   = document.getElementById('report-results');

        el.innerHTML = `<div class="text-sm text-gray-600">Loading ${type} report...</div>`;

        fetch("{{ route('reports.generate') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ type, from, to })
        })
        .then(res => res.json())
        .then(data => {
            if (!data.length) {
                el.innerHTML = `<div class="text-gray-500">No data found for this selection.</div>`;
                return;
            }

            // Income reports → chart
            if (type === 'income_year' || type === 'income_month') {
                el.innerHTML = `<canvas id="reportChart"></canvas>`;
                const ctx = document.getElementById('reportChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.map(d => type === 'income_year' ? d.Year : `${d.Year}-${d.Month}`),
                        datasets: [{
                            label: 'Income (UGX)',
                            data: data.map(d => d.Total),
                            backgroundColor: '#6366f1'
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: value => 'UGX ' + Number(value).toLocaleString()
                                }
                            }
                        }
                    }
                });
            }
            // Instructors earnings → fixed table
            else if (type === 'instructors') {
                let html = `
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Instructor Name</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Instructor Email</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Scheduled Class</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Total (UGX)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                `;
                data.forEach(r => {
                    html += `
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 text-sm text-gray-800">${r['Instructor Name']}</td>
                            <td class="px-4 py-2 text-sm text-gray-600">${r['Instructor Email']}</td>
                            <td class="px-4 py-2 text-sm text-gray-800">${r['Scheduled Class']}</td>
                            <td class="px-4 py-2 text-sm font-medium text-green-700">${Number(r['Total (UGX)']).toLocaleString()}</td>
                        </tr>
                    `;
                });
                html += `</tbody></table>`;
                el.innerHTML = html;
            }
            // Plans → table
            else if (type === 'plans') {
                let html = `
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Plan</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Total (UGX)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                `;
                data.forEach(r => {
                    html += `
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 text-sm text-gray-800">${r.Plan}</td>
                            <td class="px-4 py-2 text-sm font-medium text-green-700">${Number(r.Total).toLocaleString()}</td>
                        </tr>
                    `;
                });
                html += `</tbody></table>`;
                el.innerHTML = html;
            }
            // Members growth → table
            else if (type === 'members') {
                let html = `
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Year</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Members</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                `;
                data.forEach(r => {
                    html += `
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 text-sm text-gray-800">${r.Year}</td>
                            <td class="px-4 py-2 text-sm font-medium text-indigo-700">${r.Members}</td>
                        </tr>
                    `;
                });
                html += `</tbody></table>`;
                el.innerHTML = html;
            }
        })
        .catch(() => {
            el.innerHTML = `<div class="text-red-600">Error generating report.</div>`;
        });
    });

    // Download buttons — always include type/from/to
    document.getElementById('download-pdf')?.addEventListener('click', () => {
        const type = document.getElementById('report-type').value;
        const from = document.getElementById('report-from').value;
        const to   = document.getElementById('report-to').value;
        const url  = new URL("{{ route('reports.pdf') }}", window.location.origin);
        url.searchParams.set('type', type);
        if (from) url.searchParams.set('from', from);
        if (to)   url.searchParams.set('to', to);
        window.location.href = url.toString();
    });

    document.getElementById('download-excel')?.addEventListener('click', () => {
        const type = document.getElementById('report-type').value;
        const from = document.getElementById('report-from').value;
        const to   = document.getElementById('report-to').value;
        const url  = new URL("{{ route('reports.excel') }}", window.location.origin);
        url.searchParams.set('type', type);
        if (from) url.searchParams.set('from', from);
        if (to)   url.searchParams.set('to', to);
        window.location.href = url.toString();
    });
    </script>
</x-app-layout>
