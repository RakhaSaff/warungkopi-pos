<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>POS Kasir</title>

@php
    $jsCategories = $categories->map(fn($c) => [
        'id' => $c->id,
        'name' => $c->name,
        'icon' => $c->icon ?? '☕',
    ])->values();

    $jsProducts = $products->map(fn($p) => [
        'id' => $p->id,
        'name' => $p->name,
        'icon' => optional($p->category)->icon ?? '☕',
        'price' => (float) $p->price,
        'cat' => optional($p->category)->name ?? 'Lainnya',
        'stock' => (int) $p->stock,
        'consign' => (bool) $p->is_consignment,
        'note' => $p->shift_note ?? null,
    ])->values();

    $expectedCash = (float) $activeShift->opening_balance + (float) $activeShift->totalCashRevenue();
@endphp

<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap');

*{box-sizing:border-box;margin:0;padding:0}
:root{
    --primary:#16A34A;
    --primary-dark:#0F7A38;
    --primary-soft:#EAFBF0;
    --primary-soft-2:#F4FFF7;
    --accent:#DB4D93;
    --accent-dark:#B83276;
    --accent-soft:#FCE7F3;
    --accent-soft-2:#FFF5FA;
    --bg:#F6FAF7;
    --white:#FFFFFF;
    --text:#18221C;
    --muted:#7A877F;
    --line:#D8EBDD;
    --line-strong:#A9E8BC;
    --green:#16A34A;
    --red:#DC3F5D;
    --blue:#2F80ED;
    --shadow:0 18px 45px rgba(15,122,56,.10);
    --shadow2:0 8px 22px rgba(15,122,56,.10);
    --radius:22px;
}

body{
    font-family:Poppins,sans-serif;
    background:
        radial-gradient(circle at top left, rgba(22,163,74,.13), transparent 34%),
        radial-gradient(circle at bottom right, rgba(219,77,147,.12), transparent 34%),
        linear-gradient(135deg,#FBFFFC,#F4FAF6);
    color:var(--text);
    font-size:13px;
    overflow:hidden;
}

.app{
    height:100vh;
    display:grid;
    grid-template-rows:auto 1fr;
}

.topbar{
    height:72px;
    background:rgba(255,255,255,.92);
    backdrop-filter:blur(16px);
    border-bottom:1px solid var(--line);
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding:0 22px;
    position:relative;
    z-index:5;
}

.brand-wrap{display:flex;align-items:center;gap:12px}
.logo{
    width:46px;height:46px;
    border-radius:16px;
    display:grid;place-items:center;
    color:#fff;
    background:linear-gradient(135deg,var(--primary-dark),var(--primary));
    box-shadow:var(--shadow2);
    font-size:22px;
}
.brand{font-weight:800;font-size:17px;letter-spacing:-.3px;color:var(--text)}
.brand span{color:var(--primary)}
.subtitle{font-size:10px;color:var(--muted);margin-top:1px}

.top-actions{display:flex;align-items:center;gap:9px}
.shift-pill{
    background:linear-gradient(135deg,var(--primary-soft),#FFFFFF);
    border:1px solid var(--line-strong);
    color:var(--primary-dark);
    border-radius:999px;
    padding:8px 13px;
    font-size:11px;
    font-weight:700;
    white-space:nowrap;
}
.action-btn,.logout{
    border:1px solid var(--line);
    background:#fff;
    color:var(--text);
    border-radius:13px;
    padding:9px 12px;
    cursor:pointer;
    font-family:Poppins,sans-serif;
    font-size:12px;
    font-weight:700;
    transition:.2s;
}
.action-btn:hover,.logout:hover{transform:translateY(-1px);box-shadow:var(--shadow2);border-color:var(--line-strong)}
.titipan-btn{background:var(--accent-soft-2);color:var(--accent-dark);border-color:#F7B7D7}
.close-shift{background:var(--primary-soft);color:var(--primary-dark);border-color:var(--line-strong)}

.main{
    height:calc(100vh - 72px);
    display:grid;
    grid-template-columns:280px 1fr 360px;
    gap:16px;
    padding:16px;
    overflow:hidden;
}

.sidebar,.menu-area,.cart-panel{
    background:rgba(255,255,255,.90);
    border:1px solid var(--line);
    box-shadow:var(--shadow);
    border-radius:var(--radius);
    overflow:hidden;
}

.sidebar{
    padding:18px;
    display:flex;
    flex-direction:column;
    gap:14px;
}

.cashier-card{
    background:linear-gradient(135deg,var(--primary-dark),var(--primary));
    color:#fff;
    border-radius:20px;
    padding:18px;
    box-shadow:var(--shadow2);
}
.cashier-card small{opacity:.78;font-size:10px}
.cashier-name{font-size:18px;font-weight:800;margin-top:6px}
.cashier-meta{font-size:11px;margin-top:8px;line-height:1.6;opacity:.9}

.info-box{
    background:var(--primary-soft-2);
    border:1px solid var(--line);
    border-radius:18px;
    padding:14px;
}
.info-title{font-size:11px;color:var(--muted);font-weight:700;text-transform:uppercase;margin-bottom:8px}
.info-row{display:flex;justify-content:space-between;gap:8px;padding:6px 0;border-bottom:1px dashed var(--line)}
.info-row:last-child{border-bottom:0}
.info-row span{color:var(--muted)}
.info-row b{font-size:12px}

.menu-area{
    display:grid;
    grid-template-rows:auto auto 1fr;
}

.menu-header{
    padding:18px 18px 10px;
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    gap:12px;
}
.menu-title h2{font-size:20px;letter-spacing:-.5px;color:var(--text)}
.menu-title p{font-size:11px;color:var(--muted);margin-top:3px}
.search-box{
    width:260px;
    background:#fff;
    border:1px solid var(--line);
    border-radius:14px;
    padding:10px 12px;
    transition:.2s;
}
.search-box:focus-within{border-color:var(--primary);box-shadow:0 0 0 4px rgba(22,163,74,.10)}
.search-box input{
    width:100%;
    border:0;
    outline:0;
    font-family:Poppins,sans-serif;
    font-size:12px;
}

.cats{
    padding:0 18px 14px;
    display:flex;
    gap:8px;
    overflow-x:auto;
}
.cat{
    padding:9px 15px;
    border-radius:999px;
    border:1px solid var(--line);
    background:#fff;
    cursor:pointer;
    font-family:Poppins,sans-serif;
    font-size:12px;
    font-weight:700;
    color:#526159;
    white-space:nowrap;
    transition:.2s;
}
.cat:hover{border-color:var(--line-strong);background:var(--primary-soft-2)}
.cat.active{
    background:linear-gradient(135deg,var(--primary),var(--accent));
    color:#fff;
    border-color:transparent;
    box-shadow:0 8px 18px rgba(22,163,74,.20);
}

.grid{
    overflow-y:auto;
    padding:0 18px 18px;
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(150px,1fr));
    gap:12px;
    align-content:start;
}
.product-card{
    background:#fff;
    border:1px solid var(--line);
    border-radius:20px;
    padding:14px;
    cursor:pointer;
    position:relative;
    transition:.2s;
    min-height:156px;
    display:flex;
    flex-direction:column;
    justify-content:space-between;
}
.product-card:hover{
    border-color:var(--primary);
    transform:translateY(-3px);
    box-shadow:var(--shadow2);
}
.product-card.disabled{opacity:.45;cursor:not-allowed;transform:none}
.prod-icon{
    width:48px;height:48px;
    border-radius:16px;
    background:linear-gradient(135deg,var(--primary-soft),#FFFFFF);
    display:grid;place-items:center;
    font-size:25px;
    margin-bottom:8px;
}
.prod-name{font-weight:800;font-size:13px;line-height:1.3;color:var(--text)}
.price{color:var(--primary);font-weight:800;font-size:12px;margin-top:5px}
.stock{font-size:10px;color:var(--muted);margin-top:2px}
.note-titipan{
    margin-top:6px;
    display:block;
    width:fit-content;
    max-width:100%;
    padding:5px 8px;
    border-radius:999px;
    background:var(--accent-soft-2);
    border:1px solid #F7B7D7;
    color:var(--accent-dark);
    font-size:10px;
    font-weight:800;
    line-height:1.25;
    white-space:normal;
}
.badge{
    position:absolute;
    top:10px;
    right:10px;
    background:var(--accent-soft);
    color:var(--accent-dark);
    font-size:9px;
    padding:3px 7px;
    border-radius:999px;
    font-weight:800;
}

.cart-panel{
    display:grid;
    grid-template-rows:auto auto 1fr auto auto;
    background:#fff;
}
.cart-head{
    padding:18px;
    border-bottom:1px solid var(--line);
    display:flex;
    justify-content:space-between;
    align-items:center;
}
.cart-head b{font-size:16px;color:var(--text)}
.count{
    background:linear-gradient(135deg,var(--primary),var(--accent));
    color:#fff;
    border-radius:999px;
    min-width:26px;height:26px;
    padding:0 8px;
    display:grid;place-items:center;
    font-size:12px;
    font-weight:800;
}
.customer{padding:12px 18px;background:var(--primary-soft-2)}
.customer input{
    width:100%;
    border:1px solid var(--line);
    background:#fff;
    outline:0;
    font-family:Poppins,sans-serif;
    border-radius:14px;
    padding:11px 12px;
    font-size:12px;
}
.customer input:focus{border-color:var(--primary);box-shadow:0 0 0 4px rgba(22,163,74,.10)}
.items{overflow-y:auto;padding:12px 18px}
.item{
    display:grid;
    grid-template-columns:1fr auto;
    gap:10px;
    padding:12px 0;
    border-bottom:1px dashed var(--line);
}
.item-name{font-size:12px;font-weight:800;line-height:1.4;color:var(--text)}
.item-price{font-size:11px;color:var(--muted);margin-top:2px}
.qty{display:flex;align-items:center;gap:7px;margin-top:8px}
.qty button{
    width:26px;height:26px;
    border-radius:50%;
    border:1px solid var(--line);
    background:#fff;
    cursor:pointer;
    font-weight:800;
    color:var(--primary-dark);
}
.qty button:hover{background:var(--primary-soft);border-color:var(--primary)}
.item-total{font-weight:800;color:var(--primary-dark);font-size:12px;white-space:nowrap}

.empty-cart{
    height:100%;
    display:grid;
    place-items:center;
    text-align:center;
    color:var(--muted);
    font-size:12px;
}
.empty-cart div{font-size:42px;margin-bottom:8px}

.summary{
    padding:12px 18px;
    border-top:1px solid var(--line);
    background:#FCFFFD;
}
.row{display:flex;justify-content:space-between;padding:5px 0;color:var(--muted)}
.row b{color:var(--text)}
.row.total{
    margin-top:6px;
    padding-top:10px;
    border-top:1px solid var(--line);
    font-size:17px;
    font-weight:800;
    color:var(--text);
}
.pay{padding:14px 18px 18px}
.pay-label{font-size:10px;font-weight:800;color:var(--muted);text-transform:uppercase;margin-bottom:8px}
.methods{display:grid;grid-template-columns:repeat(3,1fr);gap:7px;margin-bottom:12px}
.method{
    padding:10px 8px;
    border-radius:14px;
    border:1px solid var(--line);
    background:#fff;
    cursor:pointer;
    font-size:10px;
    font-weight:800;
    font-family:Poppins,sans-serif;
    color:var(--text);
}
.method.active{
    border-color:var(--primary);
    background:var(--primary-soft);
    color:var(--primary-dark);
}
.btn{
    width:100%;
    padding:13px;
    border:0;
    border-radius:16px;
    background:linear-gradient(135deg,var(--primary),var(--accent));
    color:#fff;
    font-weight:800;
    cursor:pointer;
    font-family:Poppins,sans-serif;
    box-shadow:0 10px 22px rgba(22,163,74,.20);
}
.btn:disabled{background:#ddd;box-shadow:none;cursor:not-allowed}
.btn-blue{background:linear-gradient(135deg,var(--primary),#36C16F)}
.btn-danger{background:linear-gradient(135deg,var(--accent),#E85E9F)}

.modal{
    position:fixed;
    inset:0;
    background:rgba(24,34,28,.50);
    backdrop-filter:blur(6px);
    display:none;
    place-items:center;
    z-index:999;
}
.modal.show{display:grid}
.box{
    width:410px;
    max-height:90vh;
    overflow-y:auto;
    background:#fff;
    border-radius:26px;
    padding:24px;
    box-shadow:0 30px 80px rgba(0,0,0,.22);
    border:1px solid var(--line);
}
.box h3{font-size:19px;margin-bottom:6px;color:var(--text)}
.box p{font-size:12px;color:var(--muted);margin-bottom:12px}
.box input,.box textarea,.box select{
    width:100%;
    padding:12px;
    border:1px solid var(--line);
    border-radius:14px;
    margin:8px 0 12px;
    font-family:Poppins,sans-serif;
    outline:none;
}
.box input:focus,.box textarea:focus,.box select:focus{border-color:var(--primary);box-shadow:0 0 0 4px rgba(22,163,74,.10)}
.cancel{
    width:100%;
    margin-top:9px;
    padding:12px;
    border:1px solid var(--line);
    background:#fff;
    border-radius:14px;
    cursor:pointer;
    font-weight:800;
    font-family:Poppins,sans-serif;
}
.cancel:hover{background:var(--primary-soft-2);border-color:var(--line-strong)}
.success{text-align:center}
.alert-err,.alert-ok{
    border-radius:14px;
    padding:11px 12px;
    font-size:12px;
    margin:10px 0;
}
.alert-err{background:#FDECF4;color:var(--accent-dark)}
.alert-ok{background:var(--primary-soft);color:var(--primary-dark)}
.cash-box{
    background:var(--primary-soft-2);
    border:1px solid var(--line);
    border-radius:18px;
    padding:14px;
    margin:12px 0;
}
.cash-box div{display:flex;justify-content:space-between;padding:5px 0}
.consign-list{display:flex;flex-direction:column;gap:8px;margin:12px 0}
.consign-row{
    display:flex;align-items:center;gap:10px;
    background:var(--accent-soft-2);
    border:1px solid #F7B7D7;
    border-radius:16px;
    padding:10px;
}
.consign-row-info{flex:1}
.consign-row-name{font-size:12px;font-weight:800;color:var(--accent-dark)}
.consign-row-supplier{font-size:10px;color:var(--muted)}
.consign-row input{
    width:150px;
    text-align:left;
    margin:0;
    padding:11px 12px;
    border:1px solid var(--line);
    border-radius:14px;
    background:#fff;
    font-family:Poppins,sans-serif;
    font-weight:700;
    font-size:12px;
}
.consign-row input::placeholder{color:#A8B0AB;font-weight:600}
.consign-row input:focus{
    border-color:var(--primary);
    box-shadow:0 0 0 4px rgba(22,163,74,.10);
}

.divider-title{
    display:flex;
    align-items:center;
    gap:10px;
    margin:18px 0 12px;
    color:var(--muted);
    font-size:11px;
    font-weight:800;
    text-transform:uppercase;
}
.divider-title::before,.divider-title::after{
    content:"";
    height:1px;
    background:var(--line);
    flex:1;
}
.new-consign-form{
    background:var(--primary-soft-2);
    border:1px solid var(--line);
    border-radius:18px;
    padding:14px;
    margin:12px 0;
}
.new-consign-form label{
    display:block;
    font-size:11px;
    font-weight:800;
    color:var(--text);
    margin:4px 0 6px;
}
.new-consign-form input{
    margin:0 0 10px;
}
.new-consign-hint{
    font-size:10px;
    color:var(--muted);
    margin:-4px 0 10px;
    line-height:1.5;
}

.spinner{
    display:inline-block;width:16px;height:16px;
    border:2px solid #fff;border-top-color:transparent;
    border-radius:50%;
    animation:spin .6s linear infinite;
    vertical-align:middle;margin-right:6px;
}
@keyframes spin{to{transform:rotate(360deg)}}

@media(max-width:1100px){
    .main{grid-template-columns:1fr 340px}
    .sidebar{display:none}
}
</style>
</head>

<body>
<div class="app">

    <div class="topbar">
        <div class="brand-wrap">
            <div class="logo">☕</div>
            <div>
                <div class="brand">Warung <span>Kopi</span> Nusantara</div>
                <div class="subtitle">Sistem POS Kasir · Profesional</div>
            </div>
        </div>

        <div class="top-actions">
            <div class="shift-pill">
                ● {{ $activeShift->shift_name }}
                · {{ $activeShift->kasir_name ?? auth()->user()->name }}
                · {{ \Carbon\Carbon::parse($activeShift->started_at)->translatedFormat('H:i') }}
            </div>

            <button type="button" class="action-btn titipan-btn" onclick="openConsignModal()">🤝 Titipan</button>
            <button type="button" id="openExpenseModalBtn" class="action-btn titipan-btn" onclick="openExpenseModal()">💸 Pengeluaran</button>
            <button type="button" class="action-btn close-shift" onclick="openCloseShiftModal()">🔒 Tutup Shift</button>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="logout">Logout</button>
            </form>
        </div>
    </div>

    <div class="main">

        <aside class="sidebar">
            <div class="cashier-card">
                <small>Kasir Aktif</small>
                <div class="cashier-name">{{ $activeShift->kasir_name ?? auth()->user()->name }}</div>
                <div class="cashier-meta">
                    Shift: {{ $activeShift->shift_name }}<br>
                    Buka: {{ \Carbon\Carbon::parse($activeShift->started_at)->translatedFormat('d F Y, H:i') }}
                </div>
            </div>

            <div class="info-box">
                <div class="info-title">Ringkasan Kas</div>
                <div class="info-row"><span>Saldo Awal</span><b>Rp {{ number_format($activeShift->opening_balance,0,',','.') }}</b></div>
                <div class="info-row"><span>Tunai</span><b>Rp {{ number_format($activeShift->totalCashRevenue(),0,',','.') }}</b></div>
                <div class="info-row"><span>Perkiraan Kas</span><b>Rp {{ number_format($expectedCash,0,',','.') }}</b></div>
            </div>

            <div class="info-box">
                <div class="info-title">Status Sistem</div>
                <div class="info-row"><span>Koneksi</span><b style="color:var(--green)">Online</b></div>
                <div class="info-row"><span>Mode</span><b>Kasir</b></div>
                <div class="info-row"><span>Waktu</span><b id="liveClock">--:--:--</b></div>
            </div>
        </aside>

        <section class="menu-area">
            <div class="menu-header">
                <div class="menu-title">
                    <h2>Daftar Menu</h2>
                    <p>Pilih produk untuk menambahkan ke pesanan.</p>
                </div>
                <div class="search-box">
                    <input id="search" placeholder="Cari menu...">
                </div>
            </div>

            <div class="cats" id="cats"></div>
            <div class="grid" id="grid"></div>
        </section>

        <aside class="cart-panel">
            <div class="cart-head">
                <b>🛒 Pesanan</b>
                <span class="count" id="count">0</span>
            </div>

            <div class="customer">
                <input id="customer" placeholder="👤 Nama pelanggan (opsional)">
            </div>

            <div class="items" id="items"></div>

            <div class="summary" id="summary" style="display:none">
                <div class="row"><span>Subtotal</span><b id="subtotal">Rp 0</b></div>
                <div class="row total"><span>Total</span><span id="total">Rp 0</span></div>
            </div>

            <div class="pay">
                <div class="pay-label">Metode Bayar</div>
                <div class="methods">
                    <button type="button" class="method active" data-m="tunai">💵 Tunai</button>
                    <button type="button" class="method" data-m="qris">📱 QRIS</button>
                    <button type="button" class="method" data-m="transfer">🏦 Transfer</button>
                </div>
                <button type="button" class="btn" id="payBtn" disabled>Bayar Sekarang</button>
            </div>
        </aside>

    </div>
</div>

<div class="modal" id="payModal">
    <div class="box">
        <h3 id="modalTitle">Pembayaran</h3>
        <p>Total pembayaran: <b id="modalTotal"></b></p>
        <input id="cash" type="text" inputmode="numeric" placeholder="Uang diterima" autocomplete="off">
        <button type="button" class="btn" id="submitPayBtn" onclick="submitPayment()">Konfirmasi Bayar</button>
        <button type="button" class="cancel" onclick="closeModal('payModal')">Batal</button>
    </div>
</div>

<div class="modal" id="okModal">
    <div class="box success">
        <div style="font-size:48px">✅</div>
        <h3>Transaksi Berhasil</h3>
        <p id="okText"></p>
        <button type="button" class="btn" onclick="newOrder()">+ Pesanan Baru</button>
    </div>
</div>

<div class="modal" id="consignModal">
    <div class="box">
        <h3>🤝 Titipan Masuk</h3>
        <p>Tambahkan stok titipan yang baru masuk ke shift aktif.</p>

        <div id="consignError" class="alert-err" style="display:none"></div>
        <div id="consignSuccess" class="alert-ok" style="display:none"></div>

        <div class="divider-title">Tambah Barang Baru</div>

        <form method="POST" action="{{ route('kasir.consignment-products.store') }}" class="new-consign-form js-clean-rupiah-form">
            @csrf

            <label>Nama Barang Titipan</label>
            <input
                type="text"
                name="product_name"
                placeholder="Contoh: Kacang Goreng"
                autocomplete="off"
                required
            >

            <label>Nama Penitip / Supplier</label>
            <input
                type="text"
                name="supplier_name"
                placeholder="Contoh: Bu Sari"
                autocomplete="off"
                required
            >

            <label>Harga Jual</label>
            <input
                type="text"
                name="price"
                class="js-rupiah"
                placeholder="Contoh: Rp 1.000"
                inputmode="numeric"
                autocomplete="off"
                required
            >
            <div class="new-consign-hint">Ketik angka saja, contoh 1000. Nanti otomatis menjadi Rp 1.000.</div>

            <label>Stok Awal / Keterangan Titipan</label>
            <input
                type="text"
                name="initial_stock"
                placeholder="Contoh: 1 bendel, 3 bungkus, 2 box"
                autocomplete="off"
            >

            <button type="submit" class="btn">Simpan Barang Titipan Baru</button>
        </form>

        <div class="divider-title">Tambah Stok Barang Lama</div>

        @if($consignProducts->count() > 0)
            <div class="consign-list">
                @foreach($consignProducts as $product)
                    <div class="consign-row">
                        <div class="consign-row-info">
                            <div class="consign-row-name">{{ optional($product->category)->icon ?? '🤝' }} {{ $product->name }}</div>
                            <div class="consign-row-supplier">Penitip: {{ optional($product->supplier)->name ?? '-' }}</div>
                        </div>
                        <input 
                            type="text" 
                            class="consign-input" 
                            data-id="{{ $product->id }}" 
                            placeholder="Contoh: 1 bendel"
                            autocomplete="off"
                        >
                    </div>
                @endforeach
            </div>
        @else
            <p style="text-align:center;color:#999;padding:20px">Belum ada produk titipan.</p>
        @endif

        <button type="button" class="btn btn-blue" id="consignBtn" onclick="submitConsignment()">Tambah Stok Titipan</button>
        <button type="button" class="cancel" onclick="closeModal('consignModal')">Tutup</button>
    </div>
</div>


<div class="modal" id="expenseModal">
    <div class="box">
        <h3>💸 Input Pengeluaran Kasir</h3>
        <p>Catat transaksi keluar pada shift aktif. Data ini otomatis muncul di Dashboard Owner dan laporan pengeluaran.</p>

        <div id="expenseError" class="alert-err" style="display:none"></div>
        <div id="expenseSuccess" class="alert-ok" style="display:none"></div>

        <label style="font-size:12px;font-weight:800">Jenis Pengeluaran</label>
        <input
            id="expenseCategory"
            type="text"
            placeholder="Contoh: Beli es batu, plastik, bensin, parkir, bayar titipan"
            autocomplete="off"
            required
        >

        <label style="font-size:12px;font-weight:800">Tanggal</label>
        <input id="expenseDate" type="date" value="{{ today()->toDateString() }}" required>

        <label style="font-size:12px;font-weight:800">Nominal</label>
        <input id="expenseAmount" type="text" inputmode="numeric" placeholder="Contoh: Rp 50.000" autocomplete="off" required>

        <label style="font-size:12px;font-weight:800">Metode Bayar</label>
        <select id="expensePaymentMethod">
            <option value="tunai">Tunai</option>
            <option value="qris">QRIS</option>
            <option value="transfer">Transfer</option>
        </select>

        <label style="font-size:12px;font-weight:800">Catatan</label>
        <textarea id="expenseDescription" rows="3" placeholder="Catatan tambahan, misalnya nama penerima atau detail barang"></textarea>

        <button type="button" class="btn btn-danger" id="expenseBtn" onclick="submitExpense()">Simpan Pengeluaran</button>
        <button type="button" class="cancel" onclick="closeModal('expenseModal')">Batal</button>
    </div>
</div>

<div class="modal" id="closeShiftModal">
    <div class="box">
        <h3>🔒 Tutup Shift</h3>
        <p>Cek uang tunai di laci kasir sebelum menutup shift.</p>

        <div id="closeShiftError" class="alert-err" style="display:none"></div>

        <div class="cash-box">
            <div><span>Saldo Awal</span><b>Rp {{ number_format($activeShift->opening_balance,0,',','.') }}</b></div>
            <div><span>Penjualan Tunai</span><b>Rp {{ number_format($activeShift->totalCashRevenue(),0,',','.') }}</b></div>
            <div style="border-top:1px dashed var(--line);padding-top:8px;margin-top:4px">
                <span>Perkiraan Kas</span><b>Rp {{ number_format($expectedCash,0,',','.') }}</b>
            </div>
        </div>

        <label style="font-size:12px;font-weight:800">Saldo Akhir Aktual</label>
        <input 
    id="closingBalance" 
    type="text" 
    value="{{ number_format($expectedCash,0,',','.') }}"
    inputmode="numeric"
    autocomplete="off"
    required
>

        <label style="font-size:12px;font-weight:800">Catatan</label>
        <textarea id="closingNotes" rows="3" placeholder="Contoh: kas sesuai, ada selisih, atau catatan lain"></textarea>

        <button type="button" class="btn btn-danger" id="closeShiftBtn" onclick="submitCloseShift()">Konfirmasi Tutup Shift</button>
        <button type="button" class="cancel" onclick="closeModal('closeShiftModal')">Batal</button>
    </div>
</div>

<script>
const categories = @json($jsCategories);
const products = @json($jsProducts);

let cart = [];
let cat = 'Semua';
let method = 'tunai';
let keyword = '';

function fmt(n){return 'Rp ' + Number(n).toLocaleString('id-ID');}
function getCsrf(){return document.querySelector('meta[name="csrf-token"]').content;}

function escapeHtml(v){
    return String(v).replaceAll('&','&amp;').replaceAll('<','&lt;')
        .replaceAll('>','&gt;').replaceAll('"','&quot;').replaceAll("'",'&#039;');
}

function updateClock(){
    const el = document.getElementById('liveClock');
    if(el) el.textContent = new Date().toLocaleTimeString('id-ID');
}
updateClock();
setInterval(updateClock, 1000);

async function refreshCsrf(){
    try{
        const res = await fetch('/kasir/csrf-token',{headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}});
        if(res.ok){
            const data = await res.json();
            document.querySelector('meta[name="csrf-token"]').setAttribute('content',data.token);
        }
    }catch(e){}
}

function closeModal(id){document.getElementById(id).classList.remove('show');}

function renderCats(){
    const allCats = ['Semua', ...categories.map(c => c.name)];
    document.getElementById('cats').innerHTML = allCats.map(c =>
        `<button type="button" class="cat ${c===cat?'active':''}" onclick="setCat('${escapeHtml(c)}')">${escapeHtml(c)}</button>`
    ).join('');
}

function setCat(name){
    cat = name;
    renderCats();
    renderMenu();
}

document.getElementById('search').addEventListener('input', e => {
    keyword = e.target.value.toLowerCase();
    renderMenu();
});

function renderMenu(){
    let data = cat === 'Semua' ? products : products.filter(p => p.cat === cat);

    if(keyword){
        data = data.filter(p => p.name.toLowerCase().includes(keyword));
    }

    document.getElementById('grid').innerHTML = data.length ? data.map(p => {
        const disabled = p.stock <= 0;
        return `
            <div class="product-card ${disabled?'disabled':''}" ${disabled?'':`onclick="add(${p.id})"`}>
                ${p.consign ? '<span class="badge">Titipan</span>' : ''}
                <div>
                    <div class="prod-icon">${p.icon}</div>
                    <div class="prod-name">${escapeHtml(p.name)}</div>
                    <div class="price">${fmt(p.price)}</div>
                    <div class="stock">Stok tersedia: ${p.stock}</div>
                    ${p.note ? `<div class="note-titipan">📝 ${escapeHtml(p.note)}</div>` : ''}
                </div>
                <small style="color:#8A7A6C;font-weight:700">${disabled ? 'Stok habis' : 'Klik untuk pilih'}</small>
            </div>
        `;
    }).join('') : `<div style="color:#999;padding:20px">Menu tidak ditemukan.</div>`;
}

function add(id){
    const product = products.find(x => x.id === id);
    if(!product || product.stock <= 0) return;

    const existing = cart.find(x => x.id === id);
    if(existing){
        if(existing.qty >= product.stock){alert('Stok tidak cukup.');return;}
        existing.qty++;
    }else{
        cart.push({...product, qty:1});
    }
    renderCart();
}

function upd(id,diff){
    const item = cart.find(x => x.id === id);
    const product = products.find(x => x.id === id);
    if(!item || !product) return;

    if(diff > 0 && item.qty >= product.stock){alert('Stok tidak cukup.');return;}

    item.qty += diff;
    if(item.qty <= 0) cart = cart.filter(x => x.id !== id);
    renderCart();
}

function total(){return cart.reduce((sum,i)=>sum+(i.price*i.qty),0);}

function renderCart(){
    const totalQty = cart.reduce((sum,i)=>sum+i.qty,0);

    document.getElementById('count').textContent = totalQty;
    document.getElementById('payBtn').disabled = cart.length === 0;
    document.getElementById('summary').style.display = cart.length ? 'block':'none';
    document.getElementById('subtotal').textContent = fmt(total());
    document.getElementById('total').textContent = fmt(total());

    document.getElementById('items').innerHTML = cart.length ? cart.map(i => `
        <div class="item">
            <div>
                <div class="item-name">${i.icon} ${escapeHtml(i.name)}</div>

                ${i.consign && i.note ? `
                    <div class="note-titipan" style="margin-top:5px">
                        📝 ${escapeHtml(i.note)}
                    </div>
                ` : ''}

                <div class="item-price">${fmt(i.price)} / item</div>

                <div class="qty">
                    <button type="button" onclick="upd(${i.id},-1)">−</button>
                    <b>${i.qty}</b>
                    <button type="button" onclick="upd(${i.id},1)">+</button>
                </div>
            </div>

            <div class="item-total">${fmt(i.price*i.qty)}</div>
        </div>
    `).join('') : `
        <div class="empty-cart">
            <section>
                <div>🍵</div>
                <b>Belum ada pesanan</b><br>
                Pilih menu untuk mulai transaksi.
            </section>
        </div>
    `;
}

document.querySelectorAll('.method').forEach(btn=>{
    btn.onclick=()=>{
        document.querySelectorAll('.method').forEach(x=>x.classList.remove('active'));
        btn.classList.add('active');
        method = btn.dataset.m;
    };
});

document.getElementById('payBtn').onclick=()=>{
    if(cart.length === 0) return;

    document.getElementById('modalTotal').textContent = fmt(total());

    const cashInput = document.getElementById('cash');
    document.getElementById('modalTitle').textContent = 'Pembayaran ' + method.toUpperCase();

    if(method === 'tunai'){
        cashInput.readOnly = false;
        cashInput.placeholder = 'Masukkan uang diterima';
        cashInput.value = Number(total()).toLocaleString('id-ID');
    }else{
        // QRIS dan Transfer tidak perlu input uang diterima.
        // Backend tetap butuh amount_paid, jadi nilainya otomatis = total belanja.
        cashInput.readOnly = true;
        cashInput.placeholder = 'Otomatis sesuai total pembayaran';
        cashInput.value = fmt(total());
    }

    document.getElementById('payModal').classList.add('show');
};

// Format rupiah saat ketik di input cash
document.getElementById('cash').addEventListener('input', function(){
    this.value = formatRupiahInput(this.value);
});

async function submitPayment(){
    if(cart.length === 0){
        alert('Pesanan masih kosong.');
        return;
    }

    const btn = document.getElementById('submitPayBtn');
    const grandTotal = Number(total());

    // FIX UTAMA:
    // Tunai mengambil input kasir.
    // QRIS dan Transfer otomatis dibayar sesuai total, jadi tombol Konfirmasi Bayar pasti bisa diproses.
    let amountPaid = grandTotal;

    if(method === 'tunai'){
        amountPaid = Number(onlyNumber(document.getElementById('cash').value) || 0);

        if(amountPaid < grandTotal){
            alert('Uang diterima kurang dari total belanja.');
            return;
        }
    }

    const payload = {
        customer_name: document.getElementById('customer').value || 'Umum',
        payment_method: method,
        amount_paid: amountPaid,
        items: cart.map(i => ({product_id:i.id, quantity:i.qty}))
    };

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner"></span>Memproses...';

    try{
        await refreshCsrf();

        const res = await fetch('/api/transactions',{
            method:'POST',
            headers:{
                'Content-Type':'application/json',
                'Accept':'application/json',
                'X-CSRF-TOKEN':getCsrf(),
                'X-Requested-With':'XMLHttpRequest'
            },
            body:JSON.stringify(payload)
        });

        let data = {};
        try{
            data = await res.json();
        }catch(e){
            data = {};
        }

        if(res.status === 419){
            alert('Sesi halaman kedaluwarsa. Halaman akan dimuat ulang, lalu coba transaksi lagi.');
            location.reload();
            return;
        }

        if(!res.ok){
            alert(data.message || 'Transaksi gagal.');
            btn.disabled = false;
            btn.innerHTML = 'Konfirmasi Bayar';
            return;
        }

        closeModal('payModal');
        document.getElementById('okText').innerHTML = `
            ${escapeHtml(data.invoice_number || '-')}<br>
            Metode ${method.toUpperCase()}<br>
            Total ${fmt(data.total || grandTotal)}<br>
            Kembalian ${fmt(data.change || 0)}
        `;
        document.getElementById('okModal').classList.add('show');

        btn.disabled = false;
        btn.innerHTML = 'Konfirmasi Bayar';
    }catch(e){
        alert('Gagal terhubung ke server transaksi.');
        btn.disabled = false;
        btn.innerHTML = 'Konfirmasi Bayar';
    }
}

function newOrder(){
    cart = [];
    document.getElementById('customer').value = '';
    closeModal('okModal');
    renderCart();
    location.reload();
}

async function openConsignModal(){
    await refreshCsrf();
    document.querySelectorAll('.consign-input').forEach(el=>el.value='');
    document.getElementById('consignError').style.display='none';
    document.getElementById('consignSuccess').style.display='none';
    document.getElementById('consignModal').classList.add('show');
}

async function submitConsignment(){
    const btn = document.getElementById('consignBtn');
    const errBox = document.getElementById('consignError');
    const okBox = document.getElementById('consignSuccess');
    const inputs = document.querySelectorAll('.consign-input');

    const consignStocks = {};
    let hasValue = false;

    inputs.forEach(el=>{
        const val = el.value.trim();

        if(val !== ''){
            consignStocks[el.dataset.id] = val;
            hasValue = true;
        }
    });

    if(!hasValue){
        errBox.textContent='Isi minimal satu keterangan titipan. Contoh: 1 bendel, 3 bungkus, atau 2 box.';
        errBox.style.display='block';
        okBox.style.display='none';
        return;
    }

    btn.disabled=true;
    btn.innerHTML='<span class="spinner"></span>Menyimpan...';

    try{
        await refreshCsrf();
        const res = await fetch('{{ route("kasir.add-consignment") }}',{
            method:'POST',
            headers:{
                'Content-Type':'application/json',
                'Accept':'application/json',
                'X-CSRF-TOKEN':getCsrf(),
                'X-Requested-With':'XMLHttpRequest'
            },
            body:JSON.stringify({consign_stocks:consignStocks})
        });

        const data = await res.json();

        if(res.ok && data.success){
            okBox.textContent=data.message;
            okBox.style.display='block';
            inputs.forEach(el=>el.value='');
            setTimeout(()=>location.reload(),1200);
        }else{
            errBox.textContent=data.message || 'Gagal menyimpan titipan.';
            errBox.style.display='block';
        }
    }catch(e){
        errBox.textContent='Koneksi gagal. Periksa jaringan.';
        errBox.style.display='block';
    }

    btn.disabled=false;
    btn.innerHTML='Tambah Stok Titipan';
}


async function openExpenseModal(){
    await refreshCsrf();

    const errBox = document.getElementById('expenseError');
    const okBox = document.getElementById('expenseSuccess');
    const category = document.getElementById('expenseCategory');
    const amount = document.getElementById('expenseAmount');
    const description = document.getElementById('expenseDescription');
    const date = document.getElementById('expenseDate');
    const modal = document.getElementById('expenseModal');

    if(errBox) errBox.style.display='none';
    if(okBox) okBox.style.display='none';
    if(category) category.value='';
    if(amount) amount.value='';
    if(description) description.value='';
    if(date) date.value='{{ today()->toDateString() }}';
    if(modal) modal.classList.add('show');
}

const expenseAmountInput = document.getElementById('expenseAmount');
if(expenseAmountInput){
    expenseAmountInput.addEventListener('input', function(){
        this.value = formatRupiahWithPrefix(this.value);
    });
}

async function submitExpense(){
    const btn = document.getElementById('expenseBtn');
    const errBox = document.getElementById('expenseError');
    const okBox = document.getElementById('expenseSuccess');

    const category = document.getElementById('expenseCategory').value.trim();
    const amount = onlyNumber(document.getElementById('expenseAmount').value);

    if(!category){
        errBox.textContent='Jenis pengeluaran wajib diisi.';
        errBox.style.display='block';
        okBox.style.display='none';
        return;
    }

    if(!amount || Number(amount) <= 0){
        errBox.textContent='Nominal pengeluaran wajib diisi dan lebih dari 0.';
        errBox.style.display='block';
        okBox.style.display='none';
        return;
    }

    btn.disabled=true;
    btn.innerHTML='<span class="spinner"></span>Menyimpan...';

    try{
        await refreshCsrf();

        const res = await fetch('{{ route("kasir.expenses.store") }}',{
            method:'POST',
            headers:{
                'Content-Type':'application/json',
                'Accept':'application/json',
                'X-CSRF-TOKEN':getCsrf(),
                'X-Requested-With':'XMLHttpRequest'
            },
            body:JSON.stringify({
                category: category,
                expense_date: document.getElementById('expenseDate').value,
                title: category,
                amount: amount,
                payment_method: document.getElementById('expensePaymentMethod').value,
                description: document.getElementById('expenseDescription').value || null
            })
        });

        let data = {};
        try{ data = await res.json(); }catch(e){ data = {}; }

        if(res.status === 419){
            alert('Sesi halaman kedaluwarsa. Halaman akan dimuat ulang, lalu coba lagi.');
            location.reload();
            return;
        }

        if(res.ok && data.success){
            okBox.textContent=data.message || 'Pengeluaran berhasil disimpan.';
            okBox.style.display='block';
            errBox.style.display='none';

            document.getElementById('expenseCategory').value='';
            document.getElementById('expenseAmount').value='';
            document.getElementById('expenseDescription').value='';

            setTimeout(()=>closeModal('expenseModal'),900);
        }else{
            errBox.textContent=data.message || 'Gagal menyimpan pengeluaran.';
            errBox.style.display='block';
            okBox.style.display='none';
        }
    }catch(e){
        errBox.textContent='Koneksi gagal. Periksa server atau jaringan.';
        errBox.style.display='block';
        okBox.style.display='none';
    }

    btn.disabled=false;
    btn.innerHTML='Simpan Pengeluaran';
}

async function openCloseShiftModal(){
    await refreshCsrf();
    document.getElementById('closeShiftError').style.display='none';
    document.getElementById('closeShiftModal').classList.add('show');
}

function onlyNumber(v){
    return String(v).replace(/\D/g, '');
}

function formatRupiahInput(v){
    const number = onlyNumber(v);

    if(!number) return '';

    return Number(number).toLocaleString('id-ID');
}


function formatRupiahWithPrefix(v){
    const number = onlyNumber(v);

    if(!number) return '';

    return 'Rp ' + Number(number).toLocaleString('id-ID');
}

document.querySelectorAll('.js-rupiah').forEach(input=>{
    input.addEventListener('input', function(){
        this.value = formatRupiahWithPrefix(this.value);
    });

    if(input.value){
        input.value = formatRupiahWithPrefix(input.value);
    }
});

document.querySelectorAll('.js-clean-rupiah-form').forEach(form=>{
    form.addEventListener('submit', function(){
        this.querySelectorAll('.js-rupiah').forEach(input=>{
            input.value = onlyNumber(input.value);
        });
    });
});


const closingBalanceInput = document.getElementById('closingBalance');

if(closingBalanceInput){
    closingBalanceInput.addEventListener('input', function(){
        this.value = formatRupiahInput(this.value);
    });
}





async function submitCloseShift(){
    const btn = document.getElementById('closeShiftBtn');
    const errBox = document.getElementById('closeShiftError');
    const balance = document.getElementById('closingBalance').value;
    const notes = document.getElementById('closingNotes').value;

    if(!balance){
        errBox.textContent='Saldo akhir wajib diisi.';
        errBox.style.display='block';
        return;
    }

    btn.disabled=true;
    btn.innerHTML='<span class="spinner"></span>Memproses...';

    try{
        await refreshCsrf();
        const res = await fetch('{{ route("kasir.close-shift") }}',{
            method:'POST',
            headers:{
                'Content-Type':'application/json',
                'Accept':'application/json',
                'X-CSRF-TOKEN':getCsrf(),
                'X-Requested-With':'XMLHttpRequest'
            },
            body:JSON.stringify({
                closing_balance_actual: balance,
                notes:notes || null
            })
        });

        const data = await res.json();

        if(res.ok && data.success){
            window.location.href = data.redirect || '{{ route("kasir.open-shift") }}';
        }else{
            errBox.textContent=data.message || 'Gagal menutup shift.';
            errBox.style.display='block';
            btn.disabled=false;
            btn.innerHTML='Konfirmasi Tutup Shift';
        }
    }catch(e){
        errBox.textContent='Koneksi gagal. Periksa jaringan.';
        errBox.style.display='block';
        btn.disabled=false;
        btn.innerHTML='Konfirmasi Tutup Shift';
    }
}


document.addEventListener('DOMContentLoaded', function(){
    const expenseTrigger = document.getElementById('openExpenseModalBtn');
    if(expenseTrigger){
        expenseTrigger.addEventListener('click', openExpenseModal);
    }
});

setInterval(refreshCsrf,25*60*1000);

renderCats();
renderMenu();
renderCart();
</script>
</body>
</html>