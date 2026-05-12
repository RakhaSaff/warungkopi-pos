<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>{{ $title }}</title>
<style>
body{font-family:Arial,sans-serif;font-size:10px;color:#222}h2{margin:0 0 4px}.small{color:#666;margin-bottom:12px}table{width:100%;border-collapse:collapse}th,td{border:1px solid #ddd;padding:6px;vertical-align:top}th{background:#fce7f3;text-align:left}.money{text-align:right;white-space:nowrap}.total{margin-top:12px;text-align:right;font-size:14px;font-weight:bold;color:#f43f5e}
</style>
</head>
<body>
<h2>{{ $title }}</h2>
<div class="small">Warung Kopi Nusantara · Dicetak {{ now()->format('d/m/Y H:i:s') }}</div>
<table>
<thead><tr><th>Tanggal</th><th>Jam Input</th><th>No Bukti</th><th>Kategori</th><th>Judul</th><th>Deskripsi</th><th>Metode</th><th>Dicatat Oleh</th><th>Supplier</th><th>Nominal</th></tr></thead>
<tbody>
@forelse($expenses as $e)
<tr>
    <td>{{ optional($e->expense_date)->format('d/m/Y') }}</td><td>{{ optional($e->created_at)->format('H:i:s') }}</td><td>{{ $e->receipt_number ?? '-' }}</td><td>{{ $e->category_label ?? $e->category ?? '-' }}</td><td>{{ $e->title ?? '-' }}</td><td>{{ $e->description ?? '-' }}</td><td>{{ strtoupper($e->payment_method ?? '-') }}</td><td>{{ optional($e->user)->name ?? '-' }}</td><td>{{ optional($e->supplier)->name ?? '-' }}</td><td class="money">Rp {{ number_format($e->amount,0,',','.') }}</td>
</tr>
@empty
<tr><td colspan="10" style="text-align:center">Belum ada transaksi keluar.</td></tr>
@endforelse
</tbody>
</table>
<div class="total">Total Pengeluaran: Rp {{ number_format($total,0,',','.') }}</div>
</body>
</html>
