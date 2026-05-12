<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Buka Shift Kasir</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

        :root {
            --green-900: #064e3b;
            --green-800: #065f46;
            --green-700: #047857;
            --green-600: #16a34a;
            --green-100: #dcfce7;
            --green-50: #f0fdf4;
            --pink-600: #db2777;
            --pink-500: #ec4899;
            --pink-100: #fce7f3;
            --pink-50: #fdf2f8;
            --slate-900: #0f172a;
            --slate-700: #334155;
            --slate-500: #64748b;
            --slate-300: #cbd5e1;
            --slate-100: #f1f5f9;
            --white: #ffffff;
            --radius-xl: 22px;
            --radius-lg: 16px;
            --radius-md: 12px;
            --shadow-soft: 0 24px 70px rgba(15, 23, 42, .10);
            --shadow-card: 0 10px 30px rgba(15, 23, 42, .07);
        }

        * { box-sizing: border-box; }

        html { scroll-behavior: smooth; }

        body {
            font-family: Inter, Arial, sans-serif;
            min-height: 100vh;
            margin: 0;
            padding: 24px;
            color: var(--slate-900);
            background:
                radial-gradient(circle at top left, rgba(236,72,153,.12), transparent 34%),
                radial-gradient(circle at top right, rgba(22,163,74,.15), transparent 30%),
                linear-gradient(180deg, #fbfefc 0%, #f8fafc 100%);
            display: grid;
            place-items: center;
        }

        .card {
            width: min(100%, 620px);
            max-height: 92vh;
            overflow-y: auto;
            background: rgba(255,255,255,.92);
            border: 1px solid rgba(203, 213, 225, .75);
            border-radius: 28px;
            box-shadow: var(--shadow-soft);
            padding: 28px;
            scrollbar-width: thin;
            scrollbar-color: rgba(4,120,87,.35) transparent;
        }

        .card::-webkit-scrollbar,
        .stock-grid::-webkit-scrollbar,
        .consign-grid::-webkit-scrollbar,
        .kasir-options::-webkit-scrollbar { width: 8px; }

        .card::-webkit-scrollbar-thumb,
        .stock-grid::-webkit-scrollbar-thumb,
        .consign-grid::-webkit-scrollbar-thumb,
        .kasir-options::-webkit-scrollbar-thumb {
            background: rgba(4,120,87,.28);
            border-radius: 999px;
        }

        .logo {
            width: 48px;
            height: 48px;
            border-radius: 16px;
            background: linear-gradient(135deg, var(--green-600), var(--pink-500));
            display: grid;
            place-items: center;
            color: var(--white);
            font-size: 22px;
            margin-bottom: 14px;
            box-shadow: 0 14px 30px rgba(22,163,74,.18);
        }

        h2 {
            margin: 0;
            font-size: 24px;
            letter-spacing: -.03em;
            line-height: 1.2;
            color: var(--slate-900);
        }

        p {
            margin: 7px 0 20px;
            color: var(--slate-500);
            font-size: 13px;
            line-height: 1.55;
        }

        label {
            display: block;
            font-size: 12px;
            font-weight: 800;
            color: var(--slate-700);
            margin-top: 14px;
            margin-bottom: 7px;
        }

        input, select {
            width: 100%;
            min-height: 46px;
            padding: 12px 14px;
            border-radius: 14px;
            border: 1px solid var(--slate-300);
            outline: none;
            font-family: Inter, Arial, sans-serif;
            font-size: 13px;
            color: var(--slate-900);
            background: var(--white);
            transition: border-color .18s ease, box-shadow .18s ease, background .18s ease;
        }

        input::placeholder { color: #94a3b8; }

        input:focus, select:focus {
            border-color: var(--green-600);
            box-shadow: 0 0 0 4px rgba(22,163,74,.10);
        }

        .kasir-combobox { position: relative; }
        .kasir-input-wrap { position: relative; }
        .kasir-input-wrap input { padding-right: 46px; }

        .kasir-toggle {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            width: 32px;
            height: 32px;
            border: 0;
            border-radius: 10px;
            background: linear-gradient(135deg, rgba(22,163,74,.12), rgba(236,72,153,.12));
            color: var(--green-700);
            cursor: pointer;
            font-weight: 900;
        }

        .kasir-options {
            position: absolute;
            z-index: 999;
            left: 0;
            right: 0;
            top: calc(100% + 8px);
            background: var(--white);
            border: 1px solid rgba(203,213,225,.95);
            border-radius: 16px;
            box-shadow: var(--shadow-card);
            max-height: 210px;
            overflow-y: auto;
            padding: 6px;
            display: none;
        }

        .kasir-options.show { display: block; }

        .kasir-option {
            padding: 11px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 700;
            color: var(--slate-700);
            cursor: pointer;
        }

        .kasir-option:hover {
            background: var(--green-50);
            color: var(--green-700);
        }

        .kasir-empty {
            padding: 11px 12px;
            font-size: 12px;
            color: var(--slate-500);
        }

        .btn-main, .btn-add {
            width: 100%;
            border: 0;
            border-radius: 15px;
            color: var(--white);
            font-family: Inter, Arial, sans-serif;
            font-weight: 800;
            cursor: pointer;
            transition: transform .15s ease, box-shadow .15s ease, filter .15s ease;
        }

        .btn-main {
            padding: 14px;
            background: linear-gradient(135deg, var(--green-700), var(--green-600));
            margin-top: 20px;
            font-size: 14px;
            box-shadow: 0 14px 24px rgba(22,163,74,.20);
        }

        .btn-add {
            background: linear-gradient(135deg, var(--pink-500), var(--green-600));
            padding: 12px;
            margin-top: 12px;
            font-size: 13px;
        }

        .btn-main:hover, .btn-add:hover {
            transform: translateY(-1px);
            filter: brightness(.98);
        }

        .alert {
            padding: 12px 14px;
            border-radius: 14px;
            font-size: 12px;
            margin-bottom: 12px;
            line-height: 1.5;
            border: 1px solid transparent;
        }

        .success { background: var(--green-50); color: var(--green-800); border-color: var(--green-100); }
        .error { background: #fff1f2; color: #be123c; border-color: #fecdd3; }
        .info { background: var(--pink-50); color: #be185d; border-color: var(--pink-100); }

        .divider {
            border: 0;
            border-top: 1px solid var(--slate-100);
            margin: 24px 0 18px;
        }

        .section-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 8px;
        }

        .section-title {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 900;
            color: var(--green-800);
            letter-spacing: -.01em;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .stock-grid, .consign-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            max-height: 300px;
            overflow-y: auto;
            padding-right: 4px;
        }

        .stock-item, .consign-item, .add-box {
            border-radius: 18px;
            position: relative;
            border: 1px solid rgba(203,213,225,.75);
            box-shadow: 0 8px 20px rgba(15,23,42,.04);
        }

        .stock-item {
            background: linear-gradient(180deg, #ffffff, var(--green-50));
            padding: 12px;
        }

        .consign-item {
            background: linear-gradient(180deg, #ffffff, var(--pink-50));
            padding: 12px;
        }

        .stock-item-name, .consign-item-name {
            font-size: 12px;
            font-weight: 900;
            margin-bottom: 8px;
            padding-right: 58px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .stock-item-name { color: var(--green-900); }
        .consign-item-name { color: #9d174d; margin-bottom: 3px; }

        .stock-item input, .consign-item input {
            min-height: 40px;
            padding: 9px 11px;
            font-size: 13px;
            border-radius: 12px;
            margin: 0;
            background: var(--white);
        }

        .consign-supplier {
            font-size: 10.5px;
            color: var(--slate-500);
            margin-bottom: 8px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .delete-small {
            position: absolute;
            top: 9px;
            right: 9px;
            border: 0;
            background: var(--pink-100);
            color: #be123c;
            font-size: 10px;
            font-weight: 900;
            border-radius: 999px;
            padding: 5px 9px;
            cursor: pointer;
            transition: transform .15s ease, background .15s ease;
        }

        .delete-small:hover {
            transform: translateY(-1px);
            background: #fbcfe8;
        }

        .add-box {
            background: rgba(255,255,255,.82);
            padding: 16px;
            margin-bottom: 14px;
        }

        .add-title {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 900;
            color: var(--green-900);
            margin-bottom: 10px;
        }

        .helper {
            margin: 5px 0 12px;
            font-size: 12px;
            color: var(--slate-500);
            line-height: 1.55;
        }

        .select-helper {
            margin: 7px 0 0;
            font-size: 10.8px;
            color: var(--slate-500);
            line-height: 1.45;
        }

        @media(max-width: 650px) {
            body { padding: 14px; }
            .card { width: 100%; padding: 20px; border-radius: 22px; }
            .grid-2, .stock-grid, .consign-grid { grid-template-columns: 1fr; }
            h2 { font-size: 21px; }
        }
    </style>
</head>

<body>

<div class="card">
    <div class="logo">☕</div>
    <h2>Buka Shift Kasir</h2>
    <p>Pilih shift, pilih/ketik nama kasir, isi saldo awal, stok produk, dan barang titipan.</p>

    @if(session('success'))
        <div class="alert success">{{ session('success') }}</div>
    @endif

    @if(session('info'))
        <div class="alert info">{{ session('info') }}</div>
    @endif

    @if(session('error'))
        <div class="alert error">{{ session('error') }}</div>
    @endif

    @if($errors->any())
        <div class="alert error">{{ $errors->first() }}</div>
    @endif

    <form id="startShiftForm" method="POST" action="{{ route('kasir.start-shift') }}">
        @csrf

        <div class="grid-2">
            <div>
                <label>Nama Shift</label>
                <select name="shift_name" required>
                    <option value="">-- Pilih Shift --</option>
                    <option value="Shift Pagi" {{ old('shift_name', 'Shift Pagi') === 'Shift Pagi' ? 'selected' : '' }}>
                        Shift Pagi
                    </option>
                    <option value="Shift Siang/Sore" {{ old('shift_name') === 'Shift Siang/Sore' ? 'selected' : '' }}>
                        Shift Siang/Sore
                    </option>
                </select>
                <div class="select-helper"></div>
            </div>

            <div>
                <label>Nama Kasir</label>

                <div class="kasir-combobox" id="kasirCombobox">
                    <div class="kasir-input-wrap">
                        <input
                            id="kasirInput"
                            type="text"
                            name="kasir_name"
                            value="{{ old('kasir_name', auth()->user()->name) }}"
                            placeholder="Ketik / pilih nama kasir"
                            autocomplete="off"
                            required
                        >
                        <button type="button" class="kasir-toggle" id="kasirToggle">▼</button>
                    </div>

                    <div class="kasir-options" id="kasirOptions">
                        @forelse($kasirNames as $namaKasir)
                            <div class="kasir-option" data-value="{{ $namaKasir }}">
                                {{ $namaKasir }}
                            </div>
                        @empty
                            <div class="kasir-empty">Belum ada riwayat nama kasir.</div>
                        @endforelse
                    </div>
                </div>

                <div class="select-helper">Klik tombol ▼ untuk melihat semua nama, atau ketik nama baru.</div>
            </div>
        </div>

        <label>Saldo Awal / Modal Kasir</label>
        <input
            id="saldo_display"
            type="text"
            inputmode="numeric"
            placeholder="Contoh: 200.000"
            value="{{ number_format(old('opening_balance', 200000), 0, ',', '.') }}"
            autocomplete="off"
        >
        <input id="saldo_raw" name="opening_balance" type="hidden" value="{{ old('opening_balance', 200000) }}">

        <hr class="divider">

        <div class="section-title">📦 Stok Produk Utama</div>
        <p class="helper">Stok fisik produk utama warung untuk shift ini.</p>

        <div class="stock-grid">
            @foreach($products as $product)
                <div class="stock-item">
                    <button
                        type="button"
                        class="delete-small"
                        onclick="deleteProduk('{{ route('kasir.products.delete', $product->id) }}')"
                    >
                        Hapus
                    </button>

                    <div class="stock-item-name" title="{{ $product->name }}">
                        {{ optional($product->category)->icon ?? '☕' }} {{ $product->name }}
                    </div>

                    <input
                        type="number"
                        name="stocks[{{ $product->id }}]"
                        value="{{ old('stocks.' . $product->id, $product->stock) }}"
                        min="0"
                        placeholder="0"
                        required
                    >
                </div>
            @endforeach
        </div>

        <hr class="divider">

        <div class="section-head">
            <div class="section-title">🤝 Stok Titipan Awal</div>
        </div>

        <p class="helper">
            Opsional. Isi stok/keterangan titipan saat shift dibuka.
            Contoh: 1 bendel, 3 bungkus, 2 box.
        </p>

        @if($consignProducts->count() > 0)
            <div class="consign-grid">
                @foreach($consignProducts as $product)
                    <div class="consign-item">
                        <button
                            type="button"
                            class="delete-small"
                            onclick="deleteTitipan('{{ route('kasir.consignment-products.delete', $product->id) }}')"
                        >
                            Hapus
                        </button>

                        <div class="consign-item-name" title="{{ $product->name }}">
                            {{ optional($product->category)->icon ?? '🤝' }} {{ $product->name }}
                        </div>

                        <div class="consign-supplier">
                            Penitip: {{ optional($product->supplier)->name ?? '-' }}
                        </div>

                        <input
                            type="text"
                            name="consign_stocks[{{ $product->id }}]"
                            value="{{ old('consign_stocks.' . $product->id, '') }}"
                            placeholder="Contoh: 1 bendel"
                        >
                    </div>
                @endforeach
            </div>
        @else
            <p class="helper">Belum ada barang titipan.</p>
        @endif

        <button type="submit" class="btn-main">Mulai Shift</button>
    </form>

    <hr class="divider">

    <div class="add-box">
        <div class="add-title">➕ Tambah Produk Utama Baru</div>

        <form method="POST" action="{{ route('kasir.products.store') }}">
            @csrf

            <label>Nama Produk Utama</label>
            <input name="product_name" placeholder="Contoh: Es Kopi Susu, Roti Coklat" required>

            <label>Kategori</label>
            <input name="category_name" placeholder="Contoh: Minuman, Makanan" required>

            <label>Harga Jual</label>
            <input name="price" type="text" class="rupiah-input" inputmode="numeric" placeholder="Contoh: Rp 15.000" autocomplete="off" required>

            <label>Stok Awal</label>
            <input name="stock" type="number" min="0" placeholder="Contoh: 20" required>

            <button type="submit" class="btn-add">Tambah Produk Utama</button>
        </form>
    </div>

    <hr class="divider">

    <div class="add-box">
        <div class="add-title">➕ Tambah Barang Titipan Baru</div>

        <form method="POST" action="{{ route('kasir.consignment-products.store') }}">
            @csrf

            <label>Nama Barang Titipan</label>
            <input name="product_name" placeholder="Contoh: Kacang, Lemper, Risoles" required>

            <label>Nama Penitip</label>
            <input name="supplier_name" placeholder="Contoh: Ibu Sri Snack" required>

            <label>Harga Jual</label>
            <input name="price" type="text" class="rupiah-input" inputmode="numeric" placeholder="Contoh: Rp 3.000" autocomplete="off" required>

            <label>Stok Awal / Keterangan</label>
            <input name="initial_stock" type="text" placeholder="Contoh: 1 bendel / 3 bungkus">

            <button type="submit" class="btn-add">Tambah Barang Titipan</button>
        </form>
    </div>

</div>

<form id="deleteProdukForm" method="POST" style="display:none">
    @csrf
    @method('DELETE')
</form>

<form id="deleteTitipanForm" method="POST" style="display:none">
    @csrf
    @method('DELETE')
</form>

<script>
    function onlyNumber(value) {
        return String(value).replace(/\D/g, '');
    }

    function formatRupiah(value) {
        const num = onlyNumber(value);
        if (!num) return '';
        return Number(num).toLocaleString('id-ID');
    }

    const display = document.getElementById('saldo_display');
    const raw = document.getElementById('saldo_raw');

    if (display && raw) {
        display.addEventListener('input', function () {
            this.value = formatRupiah(this.value);
            raw.value = onlyNumber(this.value) || '0';
        });
    }

    const mainForm = document.getElementById('startShiftForm');

    if (mainForm) {
        mainForm.addEventListener('submit', function () {
            if (display && raw) {
                raw.value = onlyNumber(display.value) || '0';
            }
        });
    }

    document.querySelectorAll('.rupiah-input').forEach(function (input) {
        input.addEventListener('input', function () {
            const formatted = formatRupiah(this.value);
            this.value = formatted ? 'Rp ' + formatted : '';
        });

        if (input.form) {
            input.form.addEventListener('submit', function () {
                input.value = onlyNumber(input.value) || '0';
            });
        }
    });

    function deleteProduk(url) {
        if (!confirm('Yakin produk utama ini dihapus/nonaktifkan?')) {
            return;
        }

        const form = document.getElementById('deleteProdukForm');
        form.action = url;
        form.submit();
    }

    function deleteTitipan(url) {
        if (!confirm('Yakin barang titipan ini dihapus/nonaktifkan?')) {
            return;
        }

        const form = document.getElementById('deleteTitipanForm');
        form.action = url;
        form.submit();
    }

    const kasirInput = document.getElementById('kasirInput');
    const kasirToggle = document.getElementById('kasirToggle');
    const kasirOptions = document.getElementById('kasirOptions');
    const kasirCombobox = document.getElementById('kasirCombobox');

    function showKasirOptions() {
        if (kasirOptions) {
            kasirOptions.classList.add('show');
        }
    }

    function hideKasirOptions() {
        if (kasirOptions) {
            kasirOptions.classList.remove('show');
        }
    }

    if (kasirInput && kasirOptions) {
        kasirInput.addEventListener('focus', showKasirOptions);

        kasirInput.addEventListener('input', function () {
            const keyword = this.value.toLowerCase().trim();
            const options = kasirOptions.querySelectorAll('.kasir-option');

            options.forEach(function (option) {
                const text = option.dataset.value.toLowerCase();
                option.style.display = text.includes(keyword) ? 'block' : 'none';
            });

            showKasirOptions();
        });

        kasirOptions.querySelectorAll('.kasir-option').forEach(function (option) {
            option.addEventListener('click', function () {
                kasirInput.value = this.dataset.value;
                hideKasirOptions();
            });
        });
    }

    if (kasirToggle) {
        kasirToggle.addEventListener('click', function () {
            if (kasirOptions.classList.contains('show')) {
                hideKasirOptions();
            } else {
                kasirOptions.querySelectorAll('.kasir-option').forEach(function (option) {
                    option.style.display = 'block';
                });
                showKasirOptions();
                kasirInput.focus();
            }
        });
    }

    document.addEventListener('click', function (event) {
        if (kasirCombobox && !kasirCombobox.contains(event.target)) {
            hideKasirOptions();
        }
    });
</script>

</body>
</html>
