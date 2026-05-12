<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Shift;
use App\Models\ShiftStock;
use App\Models\ConsignmentSupplier;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class KasirController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $activeShift = $user->activeShift();

        if (!$activeShift) {
            return redirect()->route('kasir.open-shift');
        }

        $categories = ProductCategory::active()
            ->orderBy('sort_order')
            ->get();

        $products = Product::with(['category', 'addons'])
            ->active()
            ->orderBy('category_id')
            ->orderBy('name')
            ->get();

        $shiftStocks = ShiftStock::where('shift_id', $activeShift->id)
            ->get()
            ->keyBy('product_id');

        $products->each(function ($product) use ($shiftStocks) {
            if ($shiftStocks->has($product->id)) {
                $shiftStock = $shiftStocks[$product->id];
                $product->stock = (int) $shiftStock->current_stock;
                $product->shift_note = $shiftStock->note;
            } else {
                $product->shift_note = null;
            }
        });

        $consignProducts = Product::with(['category', 'supplier'])
            ->where('is_consignment', true)
            ->active()
            ->orderBy('name')
            ->get();

        $kasirName = $activeShift->kasir_name ?? $user->name;

        return view('kasir.pos', compact(
            'categories',
            'products',
            'activeShift',
            'consignProducts',
            'kasirName'
        ));
    }

    public function openShift()
    {
        $activeShift = auth()->user()->activeShift();

        if ($activeShift) {
            return redirect()->route('kasir.pos');
        }

        $products = Product::with('category')
            ->active()
            ->where('is_consignment', false)
            ->orderBy('category_id')
            ->orderBy('name')
            ->get();

        $consignProducts = Product::with(['category', 'supplier'])
            ->where('is_consignment', true)
            ->active()
            ->orderBy('name')
            ->get();

        $kasirDariUsers = User::where('role', 'kasir')
            ->where('is_active', true)
            ->pluck('name');

        $kasirDariRiwayatShift = Shift::query()
            ->whereNotNull('kasir_name')
            ->where('kasir_name', '!=', '')
            ->pluck('kasir_name');

        $kasirNames = $kasirDariUsers
            ->merge($kasirDariRiwayatShift)
            ->push(auth()->user()->name)
            ->map(fn ($name) => trim((string) $name))
            ->filter()
            ->unique()
            ->sort()
            ->values();

        return view('kasir.open-shift', compact(
            'products',
            'consignProducts',
            'kasirNames'
        ));
    }

    public function startShift(Request $request)
    {
        $request->merge([
            'opening_balance' => $this->bersihkanRupiah($request->opening_balance),
            'kasir_name' => trim((string) $request->kasir_name),
        ]);

        $data = $request->validate([
            'shift_name'       => 'required|string|in:Shift Pagi,Shift Siang/Sore',
            'kasir_name'       => 'required|string|max:100',
            'opening_balance'  => 'required|numeric|min:0',
            'stocks'           => 'required|array',
            'stocks.*'         => 'required|integer|min:0',
            'consign_stocks'   => 'nullable|array',
            'consign_stocks.*' => 'nullable|string|max:100',
            'consign_notes'    => 'nullable|array',
            'consign_notes.*'  => 'nullable|string|max:150',
        ]);

        $activeShift = auth()->user()->activeShift();

        if ($activeShift) {
            return redirect()->route('kasir.pos')
                ->with('info', 'Shift masih aktif.');
        }

        DB::beginTransaction();

        try {
            $shift = auth()->user()->shifts()->create([
                'shift_name'      => $data['shift_name'],
                'kasir_name'      => $data['kasir_name'],
                'started_at'      => now(),
                'opening_balance' => $data['opening_balance'],
                'status'          => 'active',
            ]);

            foreach ($data['stocks'] as $productId => $stock) {
                ShiftStock::create([
                    'shift_id'       => $shift->id,
                    'product_id'     => (int) $productId,
                    'opening_stock'  => (int) $stock,
                    'current_stock'  => (int) $stock,
                    'is_consignment' => false,
                    'note'           => null,
                ]);
            }

            if (!empty($data['consign_stocks'])) {
                foreach ($data['consign_stocks'] as $productId => $inputText) {
                    $inputText = trim((string) $inputText);

                    if ($inputText === '' || $inputText === '0') {
                        continue;
                    }

                    $stock = $this->ambilAngkaTitipan($inputText);
                    $noteTambahan = trim((string) ($data['consign_notes'][$productId] ?? ''));
                    $note = $this->gabungCatatanTitipan($inputText, $noteTambahan);

                    if ($stock > 0) {
                        ShiftStock::updateOrCreate(
                            [
                                'shift_id'   => $shift->id,
                                'product_id' => (int) $productId,
                            ],
                            [
                                'opening_stock'  => $stock,
                                'current_stock'  => $stock,
                                'is_consignment' => true,
                                'note'           => $note,
                            ]
                        );
                    }
                }
            }

            DB::commit();

            return redirect()->route('kasir.pos')
                ->with('success', 'Shift berhasil dibuka.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Gagal membuka shift: ' . $e->getMessage());
        }
    }

    public function addConsignment(Request $request)
    {
        $data = $request->validate([
            'consign_stocks'   => 'required|array|min:1',
            'consign_stocks.*' => 'required|string|max:100',
            'consign_notes'    => 'nullable|array',
            'consign_notes.*'  => 'nullable|string|max:150',
        ]);

        $shift = auth()->user()->activeShift();

        if (!$shift) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada shift aktif.',
            ], 422);
        }

        DB::beginTransaction();

        try {
            $updated = 0;

            foreach ($data['consign_stocks'] as $productId => $inputText) {
                $inputText = trim((string) $inputText);

                if ($inputText === '') {
                    continue;
                }

                $stock = $this->ambilAngkaTitipan($inputText);

                if ($stock <= 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Keterangan titipan harus diawali angka. Contoh: 1 bendel, 3 bungkus, 2 box.',
                    ], 422);
                }

                $noteTambahan = trim((string) ($data['consign_notes'][$productId] ?? ''));
                $noteBaru = $this->gabungCatatanTitipan($inputText, $noteTambahan);

                $existing = ShiftStock::where('shift_id', $shift->id)
                    ->where('product_id', (int) $productId)
                    ->first();

                if ($existing) {
                    $catatanLama = trim((string) $existing->note);

                    $catatanFinal = $catatanLama !== ''
                        ? $catatanLama . '; ' . $noteBaru
                        : $noteBaru;

                    $existing->update([
                        'opening_stock'  => (int) $existing->opening_stock + $stock,
                        'current_stock'  => (int) $existing->current_stock + $stock,
                        'is_consignment' => true,
                        'note'           => $catatanFinal,
                    ]);
                } else {
                    ShiftStock::create([
                        'shift_id'       => $shift->id,
                        'product_id'     => (int) $productId,
                        'opening_stock'  => $stock,
                        'current_stock'  => $stock,
                        'is_consignment' => true,
                        'note'           => $noteBaru,
                    ]);
                }

                $updated++;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "{$updated} produk titipan berhasil ditambahkan.",
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan titipan: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function storeMainProduct(Request $request)
    {
        $request->merge([
            'price' => $this->bersihkanRupiah($request->price),
        ]);

        $data = $request->validate([
            'product_name'  => 'required|string|max:100',
            'category_name' => 'required|string|max:100',
            'price'         => 'required|numeric|min:0',
            'stock'         => 'required|integer|min:0',
        ]);

        DB::beginTransaction();

        try {
            $category = ProductCategory::firstOrCreate(
                ['name' => trim($data['category_name'])],
                [
                    'icon' => '☕',
                    'sort_order' => 1,
                    'is_active' => true,
                ]
            );

            Product::create([
                'category_id'    => $category->id,
                'supplier_id'    => null,
                'name'           => trim($data['product_name']),
                'sku'            => 'PRD-' . strtoupper(uniqid()),
                'description'    => 'Produk utama kasir',
                'price'          => (float) $data['price'],
                'cost_price'     => 0,
                'stock'          => (int) $data['stock'],
                'stock_alert'    => 0,
                'is_consignment' => false,
                'is_active'      => true,
                'has_variants'   => false,
                'image'          => null,
            ]);

            DB::commit();

            return back()->with('success', 'Produk utama baru berhasil ditambahkan.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', 'Gagal menambahkan produk utama: ' . $e->getMessage());
        }
    }

    public function deleteMainProduct(Product $product)
    {
        if ($product->is_consignment) {
            return back()->with('error', 'Produk ini barang titipan, hapus dari bagian titipan.');
        }

        try {
            $product->update([
                'is_active' => false,
            ]);

            $product->delete();

            return back()->with('success', 'Produk utama berhasil dihapus/nonaktifkan.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal menghapus produk utama: ' . $e->getMessage());
        }
    }

    public function storeConsignmentProduct(Request $request)
    {
        $request->merge([
            'price' => $this->bersihkanRupiah($request->price),
        ]);

        $data = $request->validate([
            'product_name'   => 'required|string|max:100',
            'supplier_name'  => 'required|string|max:100',
            'price'          => 'required|numeric|min:0',
            'initial_stock'  => 'nullable|string|max:100',
        ]);

        DB::beginTransaction();

        try {
            $supplier = ConsignmentSupplier::firstOrCreate(
                ['name' => trim($data['supplier_name'])],
                [
                    'phone' => null,
                    'address' => null,
                    'balance_owed' => 0,
                ]
            );

            $category = ProductCategory::firstOrCreate(
                ['name' => 'Titipan'],
                [
                    'icon' => '🤝',
                    'sort_order' => 999,
                    'is_active' => true,
                ]
            );

            $product = Product::create([
                'category_id'    => $category->id,
                'supplier_id'    => $supplier->id,
                'name'           => trim($data['product_name']),
                'sku'            => 'TTP-' . strtoupper(uniqid()),
                'description'    => 'Produk titipan kasir',
                'price'          => (float) $data['price'],
                'cost_price'     => 0,
                'stock'          => 0,
                'stock_alert'    => 0,
                'is_consignment' => true,
                'is_active'      => true,
                'has_variants'   => false,
                'image'          => null,
            ]);

            $shift = auth()->user()->activeShift();
            $initialText = trim((string) ($data['initial_stock'] ?? ''));

            if ($shift && $initialText !== '') {
                $stock = $this->ambilAngkaTitipan($initialText);

                if ($stock > 0) {
                    ShiftStock::create([
                        'shift_id'       => $shift->id,
                        'product_id'     => $product->id,
                        'opening_stock'  => $stock,
                        'current_stock'  => $stock,
                        'is_consignment' => true,
                        'note'           => $initialText,
                    ]);
                }
            }

            DB::commit();

            return back()->with('success', 'Barang titipan baru berhasil ditambahkan.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', 'Gagal menambahkan barang titipan: ' . $e->getMessage());
        }
    }

    public function deleteConsignmentProduct(Product $product)
    {
        if (!$product->is_consignment) {
            return back()->with('error', 'Produk ini bukan barang titipan.');
        }

        try {
            $product->update([
                'is_active' => false,
            ]);

            $product->delete();

            return back()->with('success', 'Barang titipan berhasil dihapus/nonaktifkan.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal menghapus barang titipan: ' . $e->getMessage());
        }
    }


    public function storeExpense(Request $request)
    {
        $shift = auth()->user()->activeShift();

        if (!$shift) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada shift aktif.',
                ], 422);
            }

            return redirect()->route('kasir.open-shift')
                ->with('error', 'Tidak ada shift aktif.');
        }

        $request->merge([
            'amount' => $this->bersihkanRupiah($request->amount),
        ]);

        $data = $request->validate([
            'category'       => 'required|string|max:100',
            'title'          => 'nullable|string|max:150',
            'amount'         => 'required|numeric|min:1',
            'payment_method' => 'nullable|string|in:tunai,qris,transfer',
            'expense_date'   => 'required|date',
            'description'    => 'nullable|string|max:500',
        ]);

        $expense = Expense::create([
            'user_id'        => auth()->id(),
            'shift_id'       => $shift->id,
            'title'          => trim($data['category']),
            'description'    => $data['description'] ?? null,
            'category'       => trim($data['category']),
            'amount'         => $data['amount'],
            'payment_method' => $data['payment_method'] ?? 'tunai',
            'receipt_number' => 'OUT-' . now()->format('YmdHis'),
            'expense_date'   => $data['expense_date'],
            'supplier_id'    => null,
        ]);

        Cache::put('dashboard_last_update', now()->timestamp, 3600);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Pengeluaran berhasil disimpan dan masuk ke dashboard owner.',
                'expense' => [
                    'id' => $expense->id,
                    'receipt_number' => $expense->receipt_number,
                    'kasir_name' => $shift->kasir_name ?? auth()->user()->name,
                    'shift_name' => $shift->shift_name,
                    'amount' => (float) $expense->amount,
                ],
            ]);
        }

        return redirect()->route('kasir.pos')
            ->with('success', 'Pengeluaran berhasil disimpan dan masuk ke dashboard owner.');
    }

    public function closeShift(Request $request)
    {
        $request->merge([
            'closing_balance_actual' => $this->bersihkanRupiah($request->closing_balance_actual),
        ]);

        $data = $request->validate([
            'closing_balance_actual' => 'required|numeric|min:0',
            'notes'                  => 'nullable|string|max:500',
        ]);

        $shift = auth()->user()->activeShift();

        if (!$shift) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada shift aktif.',
                ], 422);
            }

            return redirect()->route('kasir.open-shift')
                ->with('error', 'Tidak ada shift aktif.');
        }

        $expected = (float) $shift->opening_balance + (float) $shift->totalCashRevenue();
        $actualNumber = (float) $data['closing_balance_actual'];

        $shift->update([
            'ended_at'                   => now(),
            'closing_balance_expected'   => $expected,
            'closing_balance_actual'     => $actualNumber,
            'closing_balance_difference' => $actualNumber - $expected,
            'notes'                      => $data['notes'] ?? null,
            'status'                     => 'closed',
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success'  => true,
                'message'  => 'Shift berhasil ditutup.',
                'redirect' => route('kasir.open-shift'),
            ]);
        }

        return redirect()->route('kasir.open-shift')
            ->with('success', 'Shift berhasil ditutup.');
    }

    public function csrfToken()
    {
        return response()->json([
            'token' => csrf_token(),
        ]);
    }

    private function bersihkanRupiah($value): string
    {
        return preg_replace('/[^0-9]/', '', (string) $value);
    }

    private function ambilAngkaTitipan(string $text): int
    {
        preg_match('/\d+/', $text, $matches);

        return isset($matches[0]) ? (int) $matches[0] : 0;
    }

    private function gabungCatatanTitipan(string $inputText, ?string $noteTambahan = null): string
    {
        $inputText = trim($inputText);
        $noteTambahan = trim((string) $noteTambahan);

        if ($noteTambahan !== '') {
            return $inputText . ' - ' . $noteTambahan;
        }

        return $inputText;
    }
}
