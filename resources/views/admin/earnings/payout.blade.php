<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Instructor Payout Report
        </h2>
    </x-slot>

    <div class="container mt-4">
        <table class="table table-striped table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Instructor</th>
                    <th>Total Earnings (UGX)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($report as $r)
                <tr>
                    <td>{{ $r->user->name }}</td>
                    <td>UGX {{ number_format($r->total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
