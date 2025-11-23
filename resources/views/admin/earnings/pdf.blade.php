<table width="100%" border="1" cellspacing="0" cellpadding="5">
    <thead>
        <tr>
            <th>#</th>
            <th>User</th>
            <th>Payment</th>
            <th>Amount</th>
            <th>Reference</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($transactions as $t)
        <tr>
            <td>{{ $t->id }}</td>
            <td>{{ $t->user->name }}</td>
            <td>{{ $t->payment_method }}</td>
            <td>${{ number_format($t->amount,2) }}</td>
            <td>{{ $t->reference_number }}</td>
            <td>{{ $t->created_at->format('Y-m-d') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
