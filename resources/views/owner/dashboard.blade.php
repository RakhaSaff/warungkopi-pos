<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Dashboard Owner</title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap');
*{box-sizing:border-box;margin:0;padding:0}
:root{
    --orange:#22C55E;
    --orange-light:#ECFDF5;
    --orange-mid:#BBF7D0;
    --pink:#EC4899;
    --pink-light:#FCE7F3;
    --pink-mid:#F9A8D4;
    --bg:#F8FFFB;
    --white:#fff;
    --text:#162019;
    --muted:#748077;
    --green:#16A34A;
    --red:#F43F5E;
    --blue:#38BDF8;
    --line:#E6F4EA;
    --shadow:0 18px 45px rgba(16,185,129,.08);
    --shadow-soft:0 10px 28px rgba(236,72,153,.08);
    --radius:20px;
}
body{
    font-family:Poppins,sans-serif;
    background:
        radial-gradient(circle at top left, rgba(34,197,94,.13), transparent 34%),
        radial-gradient(circle at bottom right, rgba(236,72,153,.14), transparent 36%),
        linear-gradient(135deg,#F8FFFB,#FFF7FB);
    color:var(--text);
    font-size:13px;
}
.dashboard{min-height:100vh}
.topbar{
    background:rgba(255,255,255,.92);
    backdrop-filter:blur(16px);
    border-bottom:1px solid var(--line);
    padding:14px 22px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    position:sticky;
    top:0;
    z-index:10;
    box-shadow:0 8px 28px rgba(16,185,129,.04);
}
.brand{display:flex;gap:10px;align-items:center}
.logo{width:38px;height:38px;background:linear-gradient(135deg,#22C55E,#EC4899);border-radius:14px;display:grid;place-items:center;color:#fff;box-shadow:0 10px 24px rgba(236,72,153,.18)}
.brand-name{font-weight:800;letter-spacing:-.3px}.brand-name span{color:var(--pink)}
.live{background:var(--orange-light);border:1px solid var(--orange-mid);border-radius:999px;padding:7px 14px;font-size:11px;color:var(--green);font-weight:800;display:flex;align-items:center;gap:6px}
.live-dot{width:7px;height:7px;border-radius:50%;background:var(--green);animation:blink 1.2s infinite}
@keyframes blink{0%,100%{opacity:1}50%{opacity:.2}}
.logout{border:1px solid var(--pink-mid);background:#fff;border-radius:12px;padding:8px 13px;cursor:pointer;font-weight:800;color:#162019;box-shadow:0 6px 18px rgba(236,72,153,.06)}
.body{padding:18px 22px;display:flex;flex-direction:column;gap:16px}
.grid4{display:grid;grid-template-columns:repeat(4,1fr);gap:10px}
.card,.panel{background:rgba(255,255,255,.92);backdrop-filter:blur(14px);border-radius:var(--radius);border:1px solid var(--line);padding:16px;box-shadow:var(--shadow)}
.card{transition:.2s}.card:hover{box-shadow:var(--shadow-soft);transform:translateY(-2px);border-color:#BBF7D0}
.label{font-size:10px;color:var(--muted);text-transform:uppercase;font-weight:800;letter-spacing:.2px}
.value{font-size:20px;font-weight:800;margin-top:5px;color:#111827;letter-spacing:-.4px}
.two{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.title{font-size:13px;font-weight:800;margin-bottom:10px;display:flex;gap:6px;align-items:center}
.dot{width:8px;height:8px;background:var(--pink);border-radius:50%;box-shadow:0 0 0 4px rgba(236,72,153,.10)}
table{width:100%;border-collapse:collapse;font-size:10px}
th{text-align:left;color:#999;text-transform:uppercase;font-size:9px;border-bottom:1px solid #eee;padding:7px}
td{padding:8px;border-bottom:1px dashed #eee;vertical-align:top}
.money{font-weight:800;color:var(--pink)}
.green{color:var(--green)!important}.red{color:var(--red)!important}.blue{color:var(--blue)!important}
.datetime{font-size:10px;color:#555;line-height:1.4}.datetime b{color:#222}
.status-badge{padding:2px 8px;border-radius:20px;font-size:10px;font-weight:800;display:inline-block}
.status-active{background:#eafaf1;color:#1f8b4c}.status-closed{background:#f4f4f4;color:#999}
.product-row,.alert{display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:1px dashed #eee}
.rank{width:24px;height:24px;border-radius:50%;background:var(--pink-light);color:var(--pink);display:grid;place-items:center;font-size:10px;font-weight:800}
.bar-wrap{width:90px;height:6px;background:#eee;border-radius:5px;overflow:hidden}
.bar{height:100%;background:linear-gradient(90deg,#22C55E,#EC4899)}
.alert{border:1px solid #F9A8D4;border-radius:12px;padding:9px;margin-bottom:7px;background:#FFF7FB}
.pnl{background:linear-gradient(135deg,#F0FDF4,#FFF7FB);border:1px solid var(--orange-mid);border-radius:var(--radius);padding:16px;box-shadow:var(--shadow)}
.pnl-row{display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px dashed #D1FAE5}
canvas{height:180px!important}
.tabs{display:flex;gap:8px;margin-bottom:10px;flex-wrap:wrap}
.tab-btn{border:1px solid #F9A8D4;background:#fff;border-radius:999px;padding:8px 14px;font-weight:800;cursor:pointer;color:#BE185D;transition:.2s}
.tab-btn.active{background:linear-gradient(135deg,#22C55E,#EC4899);color:#fff;border-color:transparent;box-shadow:0 10px 22px rgba(236,72,153,.16)}
.tab-content{display:none}.tab-content.active{display:block}
.filter-row{display:flex;gap:8px;align-items:end;flex-wrap:wrap;margin-bottom:10px}
.filter-row label{font-size:10px;text-transform:uppercase;color:#888;font-weight:800}
.filter-row input,.filter-row select{width:100%;border:1px solid #D1FAE5;border-radius:12px;padding:10px 11px;font-family:Poppins,sans-serif;font-size:12px;outline:none;background:#fff}
.filter-row>div{flex:1}
.btn{border:0;background:linear-gradient(135deg,#22C55E,#EC4899);color:#fff;border-radius:12px;padding:10px 14px;font-weight:800;font-family:Poppins,sans-serif;cursor:pointer;box-shadow:0 10px 22px rgba(236,72,153,.14)}
.btn-blue{background:var(--blue)}.btn-red{background:var(--red)}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px}
.form-grid .full{grid-column:1/-1}
.form-grid label{font-size:11px;font-weight:800;color:#555;margin-bottom:5px;display:block}
.form-grid input,.form-grid select,.form-grid textarea{width:100%;border:1px solid #D1FAE5;border-radius:12px;padding:10px 11px;font-family:Poppins,sans-serif;font-size:12px;outline:none;background:#fff}
.success-msg{background:#eafaf1;color:#1f8b4c;padding:10px 12px;border-radius:10px;font-size:12px;font-weight:700}
.error-msg{background:#fdecea;color:#c0392b;padding:10px 12px;border-radius:10px;font-size:12px;font-weight:700}
.summary-box{display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:10px}
.mini{background:linear-gradient(135deg,#FFFFFF,#F8FFFB);border:1px solid var(--line);border-radius:14px;padding:12px}.mini b{font-size:17px}
/* flash animasi saat ada update */
.flash{animation:none!important}

.card-click{cursor:pointer;position:relative;overflow:hidden}
.card-click:after{content:'Klik untuk detail';position:absolute;right:14px;bottom:12px;font-size:9px;color:var(--pink);font-weight:800;opacity:.8}
.trx-modal{position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:999;display:none;align-items:center;justify-content:center;padding:18px}
.trx-modal.show{display:flex}
.trx-box{width:min(1050px,100%);max-height:88vh;overflow:hidden;background:#fff;border-radius:18px;box-shadow:0 20px 70px rgba(0,0,0,.25);display:flex;flex-direction:column}
.trx-head{padding:16px 18px;border-bottom:1px solid var(--line);display:flex;align-items:center;justify-content:space-between;gap:10px;background:linear-gradient(135deg,#F0FDF4,#FFF7FB)}
.trx-head h3{font-size:16px;margin:0}.trx-head small{color:#888;font-weight:600}
.trx-close{border:0;background:#f3f3f3;border-radius:10px;width:34px;height:34px;font-size:18px;cursor:pointer;font-weight:800}
.trx-body{padding:14px;overflow:auto}
.trx-toolbar{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:12px}
.trx-toolbar input,.trx-toolbar select{border:1px solid #D1FAE5;border-radius:12px;padding:10px 11px;font-family:Poppins,sans-serif;font-size:12px;min-width:170px;outline:none;background:#fff}
.trx-count{margin-left:auto;background:var(--orange-light);border:1px solid var(--orange-mid);color:var(--green);border-radius:999px;padding:9px 12px;font-weight:800;font-size:11px}
.trx-table-wrap{border:1px solid #eee;border-radius:14px;overflow:auto}
.trx-table{font-size:11px;min-width:930px}
.trx-row{cursor:pointer}.trx-row:hover{background:#F0FDF4}
.pay-badge{display:inline-block;padding:3px 8px;border-radius:999px;font-size:10px;font-weight:800;background:#f4f4f4;color:#555}
.pay-tunai{background:#eafaf1;color:#1f8b4c}.pay-qris{background:#eef4ff;color:#2F80ED}.pay-transfer{background:#FCE7F3;color:#BE185D}
.item-detail{display:none;background:#fcfcfc}.item-detail.show{display:table-row}
.item-card{padding:10px 12px;border-radius:12px;background:#fff;border:1px dashed #e5e5e5;margin:6px 0}
.item-line{display:flex;justify-content:space-between;gap:10px;border-bottom:1px dashed #eee;padding:6px 0}.item-line:last-child{border-bottom:0}
.empty-trx{text-align:center;color:#999;padding:22px;font-weight:700}


/* ===== Green Pink Professional Refinement ===== */
.grid4>.card>div:first-child{
    width:42px;height:42px;border-radius:16px;
    display:grid;place-items:center;
    background:linear-gradient(135deg,#DCFCE7,#FCE7F3);
    color:var(--green);
    margin-bottom:8px;
    font-size:18px;
}
.grid4>.card:nth-child(even)>div:first-child{color:var(--pink);}
th{border-bottom:1px solid var(--line);}td{border-bottom:1px dashed var(--line);}
.product-row{border-bottom:1px dashed var(--line)}
.summary-box .mini:first-child{background:linear-gradient(135deg,#FFF7FB,#FFFFFF)}
.card-click:hover:after{opacity:1;right:12px}
input:focus,select:focus,textarea:focus{border-color:#86EFAC!important;box-shadow:0 0 0 4px rgba(34,197,94,.10)}
::-webkit-scrollbar{width:10px;height:10px}::-webkit-scrollbar-thumb{background:linear-gradient(135deg,#BBF7D0,#F9A8D4);border-radius:999px}::-webkit-scrollbar-track{background:#F8FFFB}

@media(max-width:1000px){.grid4,.two,.form-grid{grid-template-columns:1fr}.topbar{align-items:flex-start;gap:10px}.body{padding:12px}.trx-toolbar input,.trx-toolbar select{width:100%;min-width:100%}.trx-count{margin-left:0}}
</style>
</head>
<body>
<div class="dashboard">
    <div class="topbar">
        <div class="brand">
            <div class="logo">☕</div>
            <div>
                <div class="brand-name">Warung <span>Kopi</span> Nusantara</div>
                <div style="font-size:10px;color:#999">Dashboard Owner · <span id="liveDateTime"></span></div>
            </div>
        </div>
        <div style="display:flex;gap:10px;align-items:center">
            <div class="live"><span class="live-dot"></span>LIVE</div>
            <form method="POST" action="{{ route('logout') }}">@csrf<button class="logout">Logout</button></form>
        </div>
    </div>

    <div class="body">
        @if(session('success'))<div class="success-msg">{{ session('success') }}</div>@endif
        @if($errors->any())<div class="error-msg">{{ $errors->first() }}</div>@endif

        {{-- SUMMARY CARDS --}}
        <div class="grid4">
            <div class="card card-click" id="card-revenue" onclick="openFinancialModal('revenue')"><div>💰</div><div class="label">Total Pendapatan</div><div class="value" id="val-revenue">Rp {{ number_format($summary['total_revenue'] ?? 0,0,',','.') }}</div></div>
            <div class="card card-click" id="card-expense" onclick="openFinancialModal('expense')"><div>💸</div><div class="label">Total Pengeluaran</div><div class="value" id="val-expense">Rp {{ number_format($summary['total_expense'] ?? 0,0,',','.') }}</div></div>
            <div class="card card-click" id="card-profit" onclick="openFinancialModal('profit')"><div>📈</div><div class="label">Laba Bersih</div><div class="value green" id="val-profit">Rp {{ number_format($summary['net_profit'] ?? 0,0,',','.') }}</div></div>
            <div class="card card-click" id="card-trx" onclick="openTransactionModal()"><div>🧾</div><div class="label">Transaksi</div><div class="value" id="val-trx">{{ $summary['transaction_count'] ?? 0 }} <span style="font-size:12px;color:#999">pesanan</span></div></div>
        </div>

        {{-- CHART --}}
        <div class="panel"><div class="title"><span class="dot"></span>Tren Omzet Hari Ini</div><canvas id="chart"></canvas></div>

        <div class="two">
            {{-- SHIFT --}}
            <div class="panel">
                <div class="title"><span class="dot"></span>Monitoring Shift Kasir</div>
                <table>
                    <thead><tr><th>Kasir</th><th>Shift</th><th>Buka Kasir</th><th>Tutup Kasir</th><th>Modal Awal</th><th>Status</th></tr></thead>
                    <tbody id="tbody-shift">
                    @forelse($shifts as $s)
                        <tr>
                            <td><b>{{ $s->kasir_name ?? optional($s->kasir)->name ?? '-' }}</b></td>
                            <td>{{ $s->shift_name ?? '-' }}</td>
                            <td><div class="datetime">@if($s->started_at)<b>{{ \Carbon\Carbon::parse($s->started_at)->translatedFormat('d F Y') }}</b><br>{{ \Carbon\Carbon::parse($s->started_at)->translatedFormat('H:i:s') }}@else-@endif</div></td>
                            <td><div class="datetime">@if($s->ended_at)<b>{{ \Carbon\Carbon::parse($s->ended_at)->translatedFormat('d F Y') }}</b><br>{{ \Carbon\Carbon::parse($s->ended_at)->translatedFormat('H:i:s') }}@else<span class="green" style="font-weight:800">Masih Aktif</span>@endif</div></td>
                            <td class="money">Rp {{ number_format($s->opening_balance ?? 0,0,',','.') }}</td>
                            <td><span class="status-badge {{ $s->status === 'active' ? 'status-active' : 'status-closed' }}">{{ $s->status === 'active' ? 'Aktif' : 'Selesai' }}</span></td>
                        </tr>
                    @empty<tr><td colspan="6" style="text-align:center;color:#999">Belum ada data shift.</td></tr>@endforelse
                    </tbody>
                </table>
                <div style="display:flex;gap:8px;margin-top:10px">
                    <div class="card" style="flex:1;text-align:center">Tunai<br><b id="val-cash">Rp {{ number_format($summary['cash'] ?? 0,0,',','.') }}</b></div>
                    <div class="card" style="flex:1;text-align:center">QRIS<br><b id="val-qris">Rp {{ number_format($summary['qris'] ?? 0,0,',','.') }}</b></div>
                    <div class="card" style="flex:1;text-align:center">Transfer<br><b id="val-transfer">Rp {{ number_format($summary['transfer'] ?? 0,0,',','.') }}</b></div>
                </div>
            </div>

            {{-- TOP PRODUCTS --}}
            <div class="panel">
                <div class="title"><span class="dot"></span>Produk Terlaris</div>
                <div id="top-products">
                @forelse($topProducts as $i=>$p)
                    <div class="product-row"><span class="rank">{{ $i+1 }}</span><span>☕</span><span style="flex:1"><b>{{ $p->name }}</b><br><small>{{ $p->sold }} terjual</small></span><div class="bar-wrap"><div class="bar" style="width:{{ min(100,$p->sold*10) }}%"></div></div><b>Rp {{ number_format($p->revenue,0,',','.') }}</b></div>
                @empty<p>Belum ada transaksi hari ini.</p>@endforelse
                </div>
            </div>
        </div>

        {{-- TABS --}}
        <div class="panel">
            <div class="title"><span class="dot"></span>Transaksi Masuk dan Keluar</div>
            <div class="tabs">
                <button class="tab-btn active" data-tab="masuk-harian">Masuk Harian</button>
                <button class="tab-btn" data-tab="masuk-bulanan">Masuk Bulanan</button>
                <button class="tab-btn" data-tab="keluar-harian">Keluar Harian</button>
                <button class="tab-btn" data-tab="keluar-bulanan">Keluar Bulanan</button>
                <button class="tab-btn" data-tab="input-keluar">+ Input Keluar</button>
            </div>

            <div id="masuk-harian" class="tab-content active">
                <form class="filter-row" method="GET">
                    <div><label>Tanggal Harian</label><input type="date" name="daily_date" value="{{ $dailyDate }}"></div>
                    <div><label>Bulan</label><input type="month" name="monthly_month" value="{{ $monthlyMonth }}"></div>
                    <button class="btn">Filter</button>
                </form>

                <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:12px">
                    <a class="btn" style="text-decoration:none" href="{{ route('owner.reports.transactions.pdf', ['period'=>'daily','daily_date'=>$dailyDate,'monthly_month'=>$monthlyMonth]) }}">Download PDF Masuk Harian</a>
                    <a class="btn" style="text-decoration:none" href="{{ route('owner.reports.transactions.excel', ['period'=>'daily','daily_date'=>$dailyDate,'monthly_month'=>$monthlyMonth]) }}">Download Excel Masuk Harian</a>
                </div>

                <div class="summary-box">
                    <div class="mini">Total Transaksi Masuk Harian<br><b class="green">Rp {{ number_format($dailyIncomeTotal,0,',','.') }}</b></div>
                    <div class="mini">Jumlah Transaksi<br><b>{{ $dailyIncome->count() }}</b></div>
                </div>

                <table>
                    <thead><tr><th>Tanggal</th><th>Jam</th><th>Invoice</th><th>Kasir</th><th>Shift</th><th>Menu</th><th>Metode</th><th>Total</th><th>Status</th></tr></thead>
                    <tbody>
                    @forelse($dailyIncome as $trx)
                        <tr>
                            <td>{{ $trx->created_at->translatedFormat('d F Y') }}</td>
                            <td>{{ $trx->created_at->format('H:i:s') }}</td>
                            <td><b>{{ $trx->invoice_number }}</b></td>
                            <td>{{ optional($trx->kasir)->name ?? '-' }}</td>
                            <td>{{ optional($trx->shift)->shift_name ?? '-' }}</td>
                            <td>
                                @foreach($trx->items as $item)
                                    {{ $item->product_name }} x{{ $item->quantity }}<br>
                                @endforeach
                            </td>
                            <td>{{ strtoupper($trx->payment_method ?? '-') }}</td>
                            <td class="green"><b>Rp {{ number_format($trx->total,0,',','.') }}</b></td>
                            <td>{{ $trx->status }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="9" style="text-align:center;color:#999">Belum ada transaksi masuk hari ini.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div id="masuk-bulanan" class="tab-content">
                <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:12px">
                    <a class="btn" style="text-decoration:none" href="{{ route('owner.reports.transactions.pdf', ['period'=>'monthly','daily_date'=>$dailyDate,'monthly_month'=>$monthlyMonth]) }}">Download PDF Masuk Bulanan</a>
                    <a class="btn" style="text-decoration:none" href="{{ route('owner.reports.transactions.excel', ['period'=>'monthly','daily_date'=>$dailyDate,'monthly_month'=>$monthlyMonth]) }}">Download Excel Masuk Bulanan</a>
                </div>

                <div class="summary-box">
                    <div class="mini">Total Transaksi Masuk Bulanan<br><b class="green">Rp {{ number_format($monthlyIncomeTotal,0,',','.') }}</b></div>
                    <div class="mini">Jumlah Transaksi<br><b>{{ $monthlyIncome->count() }}</b></div>
                </div>

                <table>
                    <thead><tr><th>Tanggal</th><th>Jam</th><th>Invoice</th><th>Kasir</th><th>Shift</th><th>Menu</th><th>Metode</th><th>Total</th><th>Status</th></tr></thead>
                    <tbody>
                    @forelse($monthlyIncome as $trx)
                        <tr>
                            <td>{{ $trx->created_at->translatedFormat('d F Y') }}</td>
                            <td>{{ $trx->created_at->format('H:i:s') }}</td>
                            <td><b>{{ $trx->invoice_number }}</b></td>
                            <td>{{ optional($trx->kasir)->name ?? '-' }}</td>
                            <td>{{ optional($trx->shift)->shift_name ?? '-' }}</td>
                            <td>
                                @foreach($trx->items as $item)
                                    {{ $item->product_name }} x{{ $item->quantity }}<br>
                                @endforeach
                            </td>
                            <td>{{ strtoupper($trx->payment_method ?? '-') }}</td>
                            <td class="green"><b>Rp {{ number_format($trx->total,0,',','.') }}</b></td>
                            <td>{{ $trx->status }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="9" style="text-align:center;color:#999">Belum ada transaksi masuk bulan ini.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div id="keluar-harian" class="tab-content">
                <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:12px">
                    <a class="btn" style="text-decoration:none" href="{{ route('owner.reports.expenses.pdf', ['period'=>'daily','daily_date'=>$dailyDate,'monthly_month'=>$monthlyMonth]) }}">Download PDF Keluar Harian</a>
                    <a class="btn" style="text-decoration:none" href="{{ route('owner.reports.expenses.excel', ['period'=>'daily','daily_date'=>$dailyDate,'monthly_month'=>$monthlyMonth]) }}">Download Excel Keluar Harian</a>
                </div>

                <div class="summary-box"><div class="mini">Total Keluar Harian<br><b class="red">Rp {{ number_format($dailyExpenseTotal,0,',','.') }}</b></div><div class="mini">Jumlah Data<br><b>{{ $dailyExpenses->count() }}</b></div></div>
                <table><thead><tr><th>Tanggal</th><th>Jam Input</th><th>No Bukti</th><th>Kategori</th><th>Judul</th><th>Metode</th><th>Nominal</th><th>Aksi</th></tr></thead><tbody>@forelse($dailyExpenses as $e)<tr><td>{{ $e->expense_date->translatedFormat('d F Y') }}</td><td>{{ optional($e->created_at)->format('H:i:s') }}</td><td>{{ $e->receipt_number ?? '-' }}</td><td>{{ $e->category_label ?? $e->category }}</td><td>{{ $e->title }}<br><small>{{ $e->description }}</small></td><td>{{ ucfirst($e->payment_method ?? '-') }}</td><td class="red"><b>Rp {{ number_format($e->amount,0,',','.') }}</b></td><td><form method="POST" action="{{ route('owner.expenses.delete',$e->id) }}" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="btn btn-red" style="padding:6px 9px;font-size:10px">Hapus</button></form></td></tr>@empty<tr><td colspan="8" style="text-align:center;color:#999">Belum ada transaksi keluar hari ini.</td></tr>@endforelse</tbody></table>
            </div>

            <div id="keluar-bulanan" class="tab-content">
                <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:12px">
                    <a class="btn" style="text-decoration:none" href="{{ route('owner.reports.expenses.pdf', ['period'=>'monthly','daily_date'=>$dailyDate,'monthly_month'=>$monthlyMonth]) }}">Download PDF Keluar Bulanan</a>
                    <a class="btn" style="text-decoration:none" href="{{ route('owner.reports.expenses.excel', ['period'=>'monthly','daily_date'=>$dailyDate,'monthly_month'=>$monthlyMonth]) }}">Download Excel Keluar Bulanan</a>
                </div>

                <div class="summary-box"><div class="mini">Total Keluar Bulanan<br><b class="red">Rp {{ number_format($monthlyExpenseTotal,0,',','.') }}</b></div><div class="mini">Jumlah Data<br><b>{{ $monthlyExpenses->count() }}</b></div></div>
                <table><thead><tr><th>Tanggal</th><th>Jam Input</th><th>No Bukti</th><th>Kategori</th><th>Judul</th><th>Metode</th><th>Nominal</th></tr></thead><tbody>@forelse($monthlyExpenses as $e)<tr><td>{{ $e->expense_date->translatedFormat('d F Y') }}</td><td>{{ optional($e->created_at)->format('H:i:s') }}</td><td>{{ $e->receipt_number ?? '-' }}</td><td>{{ $e->category_label ?? $e->category }}</td><td>{{ $e->title }}<br><small>{{ $e->description }}</small></td><td>{{ ucfirst($e->payment_method ?? '-') }}</td><td class="red"><b>Rp {{ number_format($e->amount,0,',','.') }}</b></td></tr>@empty<tr><td colspan="7" style="text-align:center;color:#999">Belum ada transaksi keluar bulan ini.</td></tr>@endforelse</tbody></table>
            </div>

            <div id="input-keluar" class="tab-content">
                <form method="POST" action="{{ route('owner.expenses.store') }}" class="form-grid">@csrf
                    <div><label>Jenis Pengeluaran</label><select name="category" required><option value="gaji_pegawai">Gaji Pegawai</option><option value="biaya_kulakan">Biaya Kulakan</option><option value="bayar_titipan">Bayar Titipan</option><option value="bayar_listrik">Bayar Listrik</option><option value="bayar_wifi">Bayar Wifi</option><option value="lainnya">Lainnya</option></select></div>
                    <div><label>Tanggal</label><input type="date" name="expense_date" value="{{ today()->toDateString() }}" required></div>
                    <div><label>Judul/Keperluan</label><input name="title" placeholder="Contoh: Gaji Dandi" required></div>
                    <div><label>Nominal</label><input name="amount" class="rupiah-input" inputmode="numeric" placeholder="Rp 100.000" required></div>
                    <div><label>Metode Bayar</label><select name="payment_method"><option value="tunai">Tunai</option><option value="transfer">Transfer</option><option value="qris">QRIS</option></select></div>
                    <div class="full"><label>Catatan</label><textarea name="description" rows="3" placeholder="Catatan tambahan" style="width:100%;border:1.5px solid #ead8c4;border-radius:10px;padding:10px 11px;font-family:Poppins,sans-serif;font-size:12px"></textarea></div>
                    <div class="full"><button class="btn btn-blue" type="submit">Simpan Transaksi Keluar</button></div>
                </form>
            </div>
        </div>

        <div class="two">
            <div class="panel">
                <div class="title"><span class="dot"></span>Pengeluaran Hari Ini</div>
                <table><thead><tr><th>Deskripsi</th><th>Kategori</th><th>Nominal</th></tr></thead><tbody>@forelse($expenses as $e)<tr><td>{{ $e->title }}</td><td>{{ $e->category_label ?? $e->category }}</td><td class="red"><b>Rp {{ number_format($e->amount,0,',','.') }}</b></td></tr>@empty<tr><td colspan="3" style="text-align:center;color:#999">Belum ada pengeluaran hari ini.</td></tr>@endforelse</tbody></table>
            </div>
            <div>
                <div class="pnl">
                    <div class="title">📊 Laporan Laba/Rugi Otomatis</div>
                    <div class="pnl-row"><span>Pendapatan</span><b class="green" id="pnl-revenue">Rp {{ number_format($summary['total_revenue'] ?? 0,0,',','.') }}</b></div>
                    <div class="pnl-row"><span>Konsinyasi</span><b id="pnl-consignment">- Rp {{ number_format($summary['consignment'] ?? 0,0,',','.') }}</b></div>
                    <div class="pnl-row"><span>Pengeluaran</span><b class="red" id="pnl-expense">- Rp {{ number_format($summary['total_expense'] ?? 0,0,',','.') }}</b></div>
                    <div class="pnl-row"><span>Laba Bersih</span><b class="green" id="pnl-profit">Rp {{ number_format($summary['net_profit'] ?? 0,0,',','.') }}</b></div>
                </div>
                <div class="panel" style="margin-top:10px">
                    <div class="title"><span class="dot"></span>Stok Kritis</div>
                    @forelse($lowStocks as $s)<div class="alert">⚠️ <span style="flex:1">{{ $s->name }}</span><b>{{ $s->stock }}</b></div>@empty<p>Stok aman.</p>@endforelse
                </div>
            </div>
        </div>
    </div>
</div>


<div class="trx-modal" id="transactionModal" onclick="modalBackdropClose(event)">
    <div class="trx-box">
        <div class="trx-head">
            <div>
                <h3>🧾 Detail Transaksi Hari Ini</h3>
                <small>Berisi invoice, kasir, shift, jam, metode bayar, item, dan total transaksi.</small>
            </div>
            <button type="button" class="trx-close" onclick="closeTransactionModal()">×</button>
        </div>
        <div class="trx-body">
            <div class="trx-toolbar">
                <input type="text" id="trxSearch" placeholder="Cari invoice / kasir / item..." oninput="renderTransactions()">
                <select id="trxPaymentFilter" onchange="renderTransactions()">
                    <option value="">Semua Pembayaran</option>
                    <option value="tunai">Tunai</option>
                    <option value="qris">QRIS</option>
                    <option value="transfer">Transfer</option>
                </select>
                <select id="trxShiftFilter" onchange="renderTransactions()">
                    <option value="">Semua Shift</option>
                </select>
                <div class="trx-count" id="trxCountInfo">0 transaksi</div>
            </div>
            <div class="trx-table-wrap">
                <table class="trx-table">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Jam</th>
                            <th>Kasir</th>
                            <th>Shift</th>
                            <th>Customer</th>
                            <th>Pembayaran</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-transactions">
                        <tr><td colspan="8" class="empty-trx">Memuat transaksi...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="trx-modal" id="financialModal" onclick="financialBackdropClose(event)">
    <div class="trx-box">
        <div class="trx-head">
            <div>
                <h3 id="financialTitle">Detail Keuangan Hari Ini</h3>
                <small id="financialSubtitle">Rincian data keuangan otomatis dari transaksi hari ini.</small>
            </div>
            <button type="button" class="trx-close" onclick="closeFinancialModal()">×</button>
        </div>
        <div class="trx-body" id="financialBody">
            <div class="empty-trx">Memuat detail...</div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// ── Jam real-time ─────────────────────────────────────────────
(function tickClock() {
    const el = document.getElementById('liveDateTime');
    if (el) {
        const now = new Date();
        el.textContent = now.toLocaleDateString('id-ID', {weekday:'long',day:'2-digit',month:'long',year:'numeric'})
            + ' ' + now.toLocaleTimeString('id-ID');
    }
    setTimeout(tickClock, 1000);
})();

// ── Chart ─────────────────────────────────────────────────────
const chartInst = new Chart(document.getElementById('chart'), {
    type: 'line',
    data: {
        labels: @json($chart['labels'] ?? []),
        datasets: [{
            label: 'Pendapatan',
            data: @json($chart['revenue'] ?? []),
            borderColor: '#22C55E',
            backgroundColor: 'rgba(34,197,94,.10)',
            fill: true, tension: .4
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { y: { ticks: { callback: v => 'Rp ' + (v/1000) + 'k' } } }
    }
});

// ── Helpers ───────────────────────────────────────────────────
const fmtRp = n => 'Rp ' + Number(n || 0).toLocaleString('id-ID');

let allTransactions = [];
let allExpenses = [];
let currentSummary = {};
let currentFinancialMode = 'revenue';

function esc(v) {
    return String(v ?? '').replace(/[&<>'"]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#039;','"':'&quot;'}[c]));
}

function payClass(method) {
    method = String(method || '').toLowerCase();
    if (method === 'tunai') return 'pay-tunai';
    if (method === 'qris') return 'pay-qris';
    if (method === 'transfer') return 'pay-transfer';
    return '';
}

function openTransactionModal() {
    document.getElementById('transactionModal').classList.add('show');
    renderTransactions();
}

function closeTransactionModal() {
    document.getElementById('transactionModal').classList.remove('show');
}

function modalBackdropClose(e) {
    if (e.target.id === 'transactionModal') closeTransactionModal();
}

function toggleItems(id) {
    const row = document.getElementById('items-' + id);
    if (row) row.classList.toggle('show');
}

function refreshShiftFilter() {
    const select = document.getElementById('trxShiftFilter');
    if (!select) return;
    const current = select.value;
    const shifts = [...new Set(allTransactions.map(t => t.shift_name || '-'))].filter(Boolean);
    select.innerHTML = '<option value="">Semua Shift</option>' + shifts.map(s => `<option value="${esc(s)}">${esc(s)}</option>`).join('');
    select.value = shifts.includes(current) ? current : '';
}

function transactionMatches(t, q, payment, shift) {
    const itemText = (t.items || []).map(i => i.product_name).join(' ');
    const haystack = `${t.invoice_number} ${t.customer_name} ${t.kasir_name} ${t.shift_name} ${t.payment_method} ${itemText}`.toLowerCase();
    if (q && !haystack.includes(q)) return false;
    if (payment && String(t.payment_method).toLowerCase() !== payment) return false;
    if (shift && String(t.shift_name) !== shift) return false;
    return true;
}

function renderTransactions() {
    const tbody = document.getElementById('tbody-transactions');
    if (!tbody) return;

    const q = (document.getElementById('trxSearch')?.value || '').toLowerCase().trim();
    const payment = (document.getElementById('trxPaymentFilter')?.value || '').toLowerCase();
    const shift = document.getElementById('trxShiftFilter')?.value || '';

    const rows = allTransactions.filter(t => transactionMatches(t, q, payment, shift));
    document.getElementById('trxCountInfo').textContent = rows.length + ' transaksi';

    if (!rows.length) {
        tbody.innerHTML = '<tr><td colspan="8" class="empty-trx">Belum ada transaksi sesuai filter.</td></tr>';
        return;
    }

    tbody.innerHTML = rows.map(t => {
        const items = (t.items || []).map(i => `
            <div class="item-line">
                <span>${esc(i.product_name)} ${i.is_consignment ? '<small class="money">Titipan</small>' : ''}<br><small>${esc(i.notes || '')}</small></span>
                <span><b>${i.quantity} x ${fmtRp(i.product_price)}</b><br><small>Subtotal: ${fmtRp(i.subtotal)}</small></span>
            </div>
        `).join('') || '<div class="item-line"><span>Item tidak tersedia</span></div>';

        return `
            <tr class="trx-row" onclick="toggleItems('${t.id}')">
                <td><b>${esc(t.invoice_number)}</b><br><small>${esc(t.date || '')}</small></td>
                <td><b>${esc(t.time || '-')}</b></td>
                <td>${esc(t.kasir_name || '-')}</td>
                <td>${esc(t.shift_name || '-')}</td>
                <td>${esc(t.customer_name || 'Umum')}</td>
                <td><span class="pay-badge ${payClass(t.payment_method)}">${esc(t.payment_label || t.payment_method || '-')}</span></td>
                <td><b class="green">${fmtRp(t.total || 0)}</b></td>
                <td>${esc(t.status || '-')}</td>
            </tr>
            <tr class="item-detail" id="items-${t.id}">
                <td colspan="8">
                    <div class="item-card">
                        <b>Detail Item</b>
                        ${items}
                        <div class="item-line"><span>Subtotal</span><b>${fmtRp(t.subtotal || 0)}</b></div>
                        <div class="item-line"><span>Diskon</span><b>${fmtRp(t.discount || 0)}</b></div>
                        <div class="item-line"><span>Total</span><b class="green">${fmtRp(t.total || 0)}</b></div>
                        <div class="item-line"><span>Dibayar</span><b>${fmtRp(t.amount_paid || 0)}</b></div>
                        <div class="item-line"><span>Kembalian</span><b>${fmtRp(t.change_amount || 0)}</b></div>
                    </div>
                </td>
            </tr>
        `;
    }).join('');
}


function openFinancialModal(type) {
    currentFinancialMode = type;
    document.getElementById('financialModal').classList.add('show');
    renderFinancialModal();
}

function closeFinancialModal() {
    document.getElementById('financialModal').classList.remove('show');
}

function financialBackdropClose(e) {
    if (e.target.id === 'financialModal') closeFinancialModal();
}

function renderFinancialModal() {
    const title = document.getElementById('financialTitle');
    const subtitle = document.getElementById('financialSubtitle');
    const body = document.getElementById('financialBody');
    const s = currentSummary || {};

    if (currentFinancialMode === 'revenue') {
        title.textContent = '💰 Detail Total Pendapatan Hari Ini';
        subtitle.textContent = 'Berisi semua transaksi masuk dari kasir, shift, jam, metode bayar, item, dan total.';
        body.innerHTML = `
            <div class="summary-box">
                <div class="mini">Tunai<br><b class="green">${fmtRp(s.cash || 0)}</b></div>
                <div class="mini">QRIS<br><b class="green">${fmtRp(s.qris || 0)}</b></div>
                <div class="mini">Transfer<br><b class="green">${fmtRp(s.transfer || 0)}</b></div>
                <div class="mini">Total Pendapatan<br><b class="green">${fmtRp(s.total_revenue || 0)}</b></div>
            </div>
            <div class="trx-toolbar">
                <input type="text" id="financeRevenueSearch" placeholder="Cari invoice / kasir / item..." oninput="renderFinancialModal()">
                <div class="trx-count">${allTransactions.length} transaksi</div>
            </div>
            <div class="trx-table-wrap">
                <table class="trx-table">
                    <thead><tr><th>Invoice</th><th>Jam</th><th>Kasir</th><th>Shift</th><th>Pembayaran</th><th>Item</th><th>Total</th></tr></thead>
                    <tbody>${renderRevenueRows()}</tbody>
                </table>
            </div>`;
        return;
    }

    if (currentFinancialMode === 'expense') {
        title.textContent = '💸 Detail Total Pengeluaran Hari Ini';
        subtitle.textContent = 'Berisi semua transaksi keluar/pengeluaran yang dicatat owner hari ini.';
        body.innerHTML = `
            <div class="summary-box">
                <div class="mini">Total Pengeluaran<br><b class="red">${fmtRp(s.total_expense || 0)}</b></div>
                <div class="mini">Jumlah Data<br><b>${allExpenses.length}</b></div>
            </div>
            <div class="trx-table-wrap">
                <table class="trx-table">
                    <thead><tr><th>Tanggal</th><th>Judul</th><th>Kategori</th><th>Metode</th><th>Dicatat Oleh</th><th>Catatan</th><th>Nominal</th></tr></thead>
                    <tbody>${renderExpenseRows()}</tbody>
                </table>
            </div>`;
        return;
    }

    title.textContent = '📈 Detail Laba Bersih Hari Ini';
    subtitle.textContent = 'Rumus: pendapatan dikurangi pengeluaran dan konsinyasi/titipan.';
    body.innerHTML = `
        <div class="summary-box">
            <div class="mini">Total Pendapatan<br><b class="green">${fmtRp(s.total_revenue || 0)}</b></div>
            <div class="mini">Konsinyasi/Titipan<br><b>${fmtRp(s.consignment || 0)}</b></div>
            <div class="mini">Total Pengeluaran<br><b class="red">${fmtRp(s.total_expense || 0)}</b></div>
            <div class="mini">Laba Bersih<br><b class="green">${fmtRp(s.net_profit || 0)}</b></div>
        </div>
        <div class="item-card">
            <div class="item-line"><span>Pendapatan dari transaksi kasir</span><b class="green">${fmtRp(s.total_revenue || 0)}</b></div>
            <div class="item-line"><span>Dikurangi konsinyasi/titipan</span><b>- ${fmtRp(s.consignment || 0)}</b></div>
            <div class="item-line"><span>Dikurangi pengeluaran operasional</span><b class="red">- ${fmtRp(s.total_expense || 0)}</b></div>
            <div class="item-line"><span><b>Laba bersih hari ini</b></span><b class="green">${fmtRp(s.net_profit || 0)}</b></div>
        </div>
        <div class="title" style="margin-top:14px"><span class="dot"></span>Transaksi Pendapatan</div>
        <div class="trx-table-wrap" style="margin-bottom:12px">
            <table class="trx-table">
                <thead><tr><th>Invoice</th><th>Jam</th><th>Kasir</th><th>Shift</th><th>Pembayaran</th><th>Total</th></tr></thead>
                <tbody>${renderProfitRevenueRows()}</tbody>
            </table>
        </div>
        <div class="title" style="margin-top:14px"><span class="dot"></span>Transaksi Pengeluaran</div>
        <div class="trx-table-wrap">
            <table class="trx-table">
                <thead><tr><th>Tanggal</th><th>Judul</th><th>Kategori</th><th>Metode</th><th>Nominal</th></tr></thead>
                <tbody>${renderProfitExpenseRows()}</tbody>
            </table>
        </div>`;
}

function renderRevenueRows() {
    const q = (document.getElementById('financeRevenueSearch')?.value || '').toLowerCase().trim();
    const rows = allTransactions.filter(t => {
        const itemText = (t.items || []).map(i => i.product_name).join(' ');
        const haystack = `${t.invoice_number} ${t.kasir_name} ${t.shift_name} ${t.payment_method} ${itemText}`.toLowerCase();
        return !q || haystack.includes(q);
    });

    if (!rows.length) return '<tr><td colspan="7" class="empty-trx">Belum ada pendapatan hari ini.</td></tr>';

    return rows.map(t => {
        const items = (t.items || []).map(i => `${esc(i.product_name)} x${i.quantity}`).join('<br>') || '-';
        return `<tr>
            <td><b>${esc(t.invoice_number)}</b><br><small>${esc(t.date || '')}</small></td>
            <td><b>${esc(t.time || '-')}</b></td>
            <td>${esc(t.kasir_name || '-')}</td>
            <td>${esc(t.shift_name || '-')}</td>
            <td><span class="pay-badge ${payClass(t.payment_method)}">${esc(t.payment_label || t.payment_method || '-')}</span></td>
            <td>${items}</td>
            <td><b class="green">${fmtRp(t.total || 0)}</b></td>
        </tr>`;
    }).join('');
}

function renderExpenseRows() {
    if (!allExpenses.length) return '<tr><td colspan="7" class="empty-trx">Belum ada pengeluaran hari ini.</td></tr>';
    return allExpenses.map(e => `<tr>
        <td><b>${esc(e.date || '-')}</b><br><small>${esc(e.time || '')}</small></td>
        <td><b>${esc(e.title || '-')}</b></td>
        <td>${esc(e.category_label || e.category || '-')}</td>
        <td><span class="pay-badge">${esc(e.payment_method || '-')}</span></td>
        <td>${esc(e.user_name || '-')}</td>
        <td>${esc(e.description || '-')}</td>
        <td><b class="red">${fmtRp(e.amount || 0)}</b></td>
    </tr>`).join('');
}

function renderProfitRevenueRows() {
    if (!allTransactions.length) return '<tr><td colspan="6" class="empty-trx">Belum ada transaksi pendapatan.</td></tr>';
    return allTransactions.map(t => `<tr>
        <td><b>${esc(t.invoice_number)}</b></td>
        <td>${esc(t.time || '-')}</td>
        <td>${esc(t.kasir_name || '-')}</td>
        <td>${esc(t.shift_name || '-')}</td>
        <td><span class="pay-badge ${payClass(t.payment_method)}">${esc(t.payment_label || t.payment_method || '-')}</span></td>
        <td><b class="green">${fmtRp(t.total || 0)}</b></td>
    </tr>`).join('');
}

function renderProfitExpenseRows() {
    if (!allExpenses.length) return '<tr><td colspan="5" class="empty-trx">Belum ada transaksi pengeluaran.</td></tr>';
    return allExpenses.map(e => `<tr>
        <td>${esc(e.date || '-')}</td>
        <td>${esc(e.title || '-')}</td>
        <td>${esc(e.category_label || e.category || '-')}</td>
        <td>${esc(e.payment_method || '-')}</td>
        <td><b class="red">${fmtRp(e.amount || 0)}</b></td>
    </tr>`).join('');
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') { closeTransactionModal(); closeFinancialModal(); }
});


function flash(id) {
    const el = document.getElementById(id);
    if (!el) return;
    el.classList.remove('flash');
    void el.offsetWidth;
    el.classList.add('flash');
}

function renderShifts(shifts) {
    const tbody = document.getElementById('tbody-shift');
    if (!shifts || !shifts.length) {
        tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;color:#999">Belum ada data shift.</td></tr>';
        return;
    }
    tbody.innerHTML = shifts.map(s => {
        const fmtDate = ts => ts ? new Date(ts).toLocaleDateString('id-ID',{day:'2-digit',month:'long',year:'numeric'}) : '';
        const fmtTime = ts => ts ? new Date(ts).toLocaleTimeString('id-ID') : '';
        const badge   = s.status === 'active'
            ? '<span class="status-badge status-active">Aktif</span>'
            : '<span class="status-badge status-closed">Selesai</span>';
        return `<tr>
            <td><b>${s.kasir_name}</b></td>
            <td>${s.shift_name}</td>
            <td><div class="datetime">${s.started_at ? `<b>${fmtDate(s.started_at)}</b><br>${fmtTime(s.started_at)}` : '-'}</div></td>
            <td><div class="datetime">${s.ended_at ? `<b>${fmtDate(s.ended_at)}</b><br>${fmtTime(s.ended_at)}` : '<span class="green" style="font-weight:800">Masih Aktif</span>'}</div></td>
            <td class="money">${fmtRp(s.opening_balance)}</td>
            <td>${badge}</td>
        </tr>`;
    }).join('');
}

function renderTopProducts(products) {
    const el = document.getElementById('top-products');
    if (!products || !products.length) { el.innerHTML = '<p>Belum ada transaksi hari ini.</p>'; return; }
    el.innerHTML = products.map((p, i) => `
        <div class="product-row">
            <span class="rank">${i+1}</span><span>☕</span>
            <span style="flex:1"><b>${p.name}</b><br><small>${p.sold} terjual</small></span>
            <div class="bar-wrap"><div class="bar" style="width:${Math.min(100,p.sold*10)}%"></div></div>
            <b>${fmtRp(p.revenue)}</b>
        </div>`).join('');
}

// ── Fungsi update semua data dari response JSON ───────────────
function applyData(d) {
    const s = d.summary;
    currentSummary = s || {};

    document.getElementById('val-revenue').textContent  = fmtRp(s.total_revenue ?? 0);
    document.getElementById('val-expense').textContent  = fmtRp(s.total_expense ?? 0);
    document.getElementById('val-profit').textContent   = fmtRp(s.net_profit ?? 0);
    document.getElementById('val-trx').innerHTML        = (s.transaction_count ?? 0) + ' <span style="font-size:12px;color:#999">pesanan</span>';

    document.getElementById('val-cash').textContent     = fmtRp(d.payment.cash ?? 0);
    document.getElementById('val-qris').textContent     = fmtRp(d.payment.qris ?? 0);
    document.getElementById('val-transfer').textContent = fmtRp(d.payment.transfer ?? 0);

    document.getElementById('pnl-revenue').textContent     = fmtRp(s.total_revenue ?? 0);
    document.getElementById('pnl-consignment').textContent = '- ' + fmtRp(s.consignment ?? 0);
    document.getElementById('pnl-expense').textContent     = '- ' + fmtRp(s.total_expense ?? 0);
    document.getElementById('pnl-profit').textContent      = fmtRp(s.net_profit ?? 0);

    if (d.chart && d.chart.labels) {
        chartInst.data.labels = d.chart.labels;
        chartInst.data.datasets[0].data = d.chart.revenue;
        chartInst.update('none');
    }

    renderShifts(d.shifts);
    renderTopProducts(d.topProducts);

    allTransactions = d.transactions || [];
    allExpenses = d.expenses || [];
    refreshShiftFilter();
    renderTransactions();
    if (document.getElementById('financialModal')?.classList.contains('show')) renderFinancialModal();
}

// ── Fetch data terbaru dari server ────────────────────────────
async function fetchData() {
    try {
        const res = await fetch('{{ route("owner.dashboard.live") }}', {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        if (!res.ok) return;
        applyData(await res.json());
    } catch (_) {}
}

// ── SSE: push dari server begitu ada transaksi baru ───────────
function connectSSE() {
    const es = new EventSource('{{ route("owner.dashboard.stream") }}');

    es.onmessage = e => {
        try {
            const msg = JSON.parse(e.data);
            if (msg.type === 'refresh') {
                // Server memberi tahu ada transaksi baru → langsung fetch
                fetchData();
            }
        } catch (_) {}
    };

    es.onerror = () => {
        es.close();
        // Reconnect setelah 5 detik jika SSE putus
        setTimeout(connectSSE, 5000);
    };
}

// ── Polling fallback setiap 10 detik (backup jika SSE putus) ─
setInterval(fetchData, 3000);

// ── Start ─────────────────────────────────────────────────────
// connectSSE(); // dimatikan untuk lokal php artisan serve agar POS tidak muter
fetchData();

// ── Tabs ──────────────────────────────────────────────────────
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        btn.classList.add('active');
        document.getElementById(btn.dataset.tab).classList.add('active');
    });
});

// ── Format rupiah input ───────────────────────────────────────
const angka = v => String(v).replace(/\D/g, '');
const fmtInput = v => { const n = angka(v); return n ? 'Rp ' + Number(n).toLocaleString('id-ID') : ''; };
document.querySelectorAll('.rupiah-input').forEach(inp => {
    inp.addEventListener('input', function () { this.value = fmtInput(this.value); });
    inp.form?.addEventListener('submit', () => { inp.value = angka(inp.value) || '0'; });
});
</script>
</body>
</html>
