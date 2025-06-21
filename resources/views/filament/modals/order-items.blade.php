<style>
    table.order-items {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
    }

    table.order-items th,
    table.order-items td {
        padding: 12px 16px;
        text-align: center;
    }

    table.order-items th {
        font-weight: bold;
        border-bottom: 1px solid currentColor;
    }

    table.order-items td {
        border-bottom: 1px solid rgba(255, 255, 255, 0.1); /* dark-mode friendly */
    }

    table.order-items tr:last-child td {
        border-bottom: none;
    }
</style>

<table class="order-items">
    <thead>
    <tr>
        <th>#</th>
        <th>المنتج</th>
        <th>الكمية</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($items as $idx => $item)
        <tr>
            <td>{{ $idx + 1 }}</td>
            <td>{{ $item->product->name }}</td>
            <td><strong>{{ $item->quantity }}</strong></td>
        </tr>
    @endforeach
    </tbody>
</table>
