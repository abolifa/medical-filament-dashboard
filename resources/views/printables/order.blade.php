<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>طباعة الطلب #{{ $order->id }}</title>
    <style>
        body {
            font-family: sans-serif;
            padding: 20px;
        }

        h1 {
            text-align: center;
            margin-bottom: 40px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: center;
        }

        @media print {
            body {
                background: white;
                color: black;
            }
        }
    </style>
</head>
<body>
<h1>تفاصيل الطلب #{{ $order->id }}</h1>

<p><strong>النوع:</strong> {{ $order->flow }}</p>
<p><strong>المركز:</strong> {{ $order->center->name }}</p>
@if($order->patient)
    <p><strong>المريض:</strong> {{ $order->patient->name }}</p>
@endif
@if($order->supplier)
    <p><strong>المورد:</strong> {{ $order->supplier->name }}</p>
@endif
@if($order->toCenter)
    <p><strong>إلى المركز:</strong> {{ $order->toCenter->name }}</p>
@endif

<table>
    <thead>
    <tr>
        <th>#</th>
        <th>المنتج</th>
        <th>الكمية</th>
    </tr>
    </thead>
    <tbody>
    @foreach($order->items as $i => $item)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $item->product->name }}</td>
            <td>{{ $item->quantity }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<script>
    window.print();
</script>
</body>
</html>
