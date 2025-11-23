<table>
    <thead>
        <tr>
            <th>User</th>
            <th>Payment Method</th>
            <th>Amount</th>
            <th>Reference</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($transactions as $t)
        <tr>
            <td>{{ $t->user->name }}</td>
            <td>{{ $t->payment_method }}</td>
            <td>{{ $t->amount }}</td>
            <td>{{ $t->reference_number }}</td>
            <td>{{ $t->created_at }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

