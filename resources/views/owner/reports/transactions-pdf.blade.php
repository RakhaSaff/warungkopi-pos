<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>{{ $title }}</title>
<style>
body{font-family:Arial,sans-serif;font-size:10px;color:#222}h2{margin:0 0 4px}.small{color:#666;margin-bottom:12px}table{width:100%;border-collapse:collapse}th,td{border:1px solid #ddd;padding:5px;vertical-align:top}th{background:#ecfdf5;text-align:left}.money{text-align:right;white-space:nowrap}.total{margin-top:12px;text-align:right;font-size:14px;font-weight:bold;color:#16a34a}
</style>
</head>
<body>
<h2>{{ $title }}</h2>
<div class="small">Warung Kopi Nusantara · Dicetak {{ now()->format('d/m/Y H:i:s') }}</div>
<table>
<thead><tr><th>Tanggal</th><th>Jam</th><th>Invoice</th><th>Kasir</th><th>Shift</th><th>Customer</th><th>Menu</th><th>Qty</th><th>Harga</th><th>Subtotal Item</th><th>Metode</th><th>Total Transaksi</th><th>Status</th></tr></thead>
<tbody>
@forelse($transactions as $trx)
    @foreach($trx->items as $item)
    <tr>
        <td>{{ optional($trx->created_at)->format('d/m/Y') }}</td><td>{{ optional($trx->created_at)->format('H:i:s') }}</td><td>{{ $trx->invoice_number }}</td><td>{{ optional($trx->kasir)->name ?? '-' }}</td><td>{{ optional($trx->shift)->shift_name ?? '-' }}</td><td>{{ $trx->customer_name ?? 'Umum' }}</td><td>{{ $item->product_name ?? '-' }}</td><td>{{ $item->quantity }}</td><td class="money">Rp {{ number_format($item->product_price,0,',','.') }}</td><td class="money">Rp {{ number_format($item->subtotal,0,',','.') }}</td><td>{{ strtoupper($trx->payment_method ?? '-') }}</td><td class="money">Rp {{ number_format($trx->total,0,',','.') }}</td><td>{{ $trx->status }}</td>
    </tr>
    @endforeach
@empty
<tr><td colspan="13" style="text-align:center">Belum ada transaksi masuk.</td></tr>
@endforelse
</tbody>
</table>
<div class="total">Total Pendapatan: Rp {{ number_format($total,0,',','.') }}</div>
</body>
</html>
