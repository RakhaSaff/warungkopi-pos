<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Shift;
use App\Models\Transaction;
use App\Services\FinancialService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OwnerController extends Controller
{
    public function __construct(private FinancialService $finance) {}

    public function dashboard(Request $request)
    {
        $dailyDate    = $request->get('daily_date', today()->toDateString());
        $monthlyMonth = $request->get('monthly_month', now()->format('Y-m'));

        // Pemasukan = transaksi POS yang benar-benar masuk, bukan saldo awal shift
        $dailyIncome = Transaction::with(['kasir', 'shift', 'items'])
            ->completed()
            ->whereDate('created_at', $dailyDate)
            ->latest('created_at')
            ->latest('id')
            ->get();

        $monthlyIncome = Transaction::with(['kasir', 'shift', 'items'])
            ->completed()
            ->whereYear('created_at', substr($monthlyMonth, 0, 4))
            ->whereMonth('created_at', substr($monthlyMonth, 5, 2))
            ->latest('created_at')
            ->latest('id')
            ->get();

        $dailyExpenses = Expense::with(['user', 'supplier'])
            ->whereDate('expense_date', $dailyDate)
            ->latest('expense_date')
            ->latest('id')
            ->get();

        $monthlyExpenses = Expense::with(['user', 'supplier'])
            ->whereYear('expense_date', substr($monthlyMonth, 0, 4))
            ->whereMonth('expense_date', substr($monthlyMonth, 5, 2))
            ->latest('expense_date')
            ->latest('id')
            ->get();

        return view('owner.dashboard', [
            'summary'             => $this->finance->getTodaySummary(),
            'topProducts'         => $this->finance->topProducts(),
            'lowStocks'           => $this->finance->lowStocks(),
            'expenses'            => Expense::whereDate('expense_date', today())->latest()->limit(5)->get(),
            'shifts'              => Shift::with('kasir')->latest()->limit(5)->get(),
            'chart'               => $this->finance->hourlyChart(),

            'dailyDate'           => $dailyDate,
            'monthlyMonth'        => $monthlyMonth,
            'dailyIncome'         => $dailyIncome,
            'monthlyIncome'       => $monthlyIncome,
            'dailyExpenses'       => $dailyExpenses,
            'monthlyExpenses'     => $monthlyExpenses,
            'dailyIncomeTotal'    => $dailyIncome->sum('total'),
            'monthlyIncomeTotal'  => $monthlyIncome->sum('total'),
            'dailyExpenseTotal'   => $dailyExpenses->sum('amount'),
            'monthlyExpenseTotal' => $monthlyExpenses->sum('amount'),
        ]);
    }

    public function dashboardLive()
    {
        $summary     = $this->finance->getTodaySummary();
        $topProducts = $this->finance->topProducts();
        $chart       = $this->finance->hourlyChart();
        $shifts      = Shift::with('kasir')->latest()->limit(5)->get();

        $transactions = Transaction::with(['kasir', 'shift', 'items'])
            ->whereDate('created_at', today())
            ->latest('created_at')
            ->latest('id')
            ->limit(50)
            ->get();

        $expenses = Expense::with(['user', 'supplier'])
            ->whereDate('expense_date', today())
            ->latest('expense_date')
            ->latest('id')
            ->limit(100)
            ->get();

        return response()->json([
            'summary'     => $summary,
            'chart'       => $chart,
            'topProducts' => $topProducts->map(fn ($p) => [
                'name'    => $p->name,
                'sold'    => $p->sold,
                'revenue' => $p->revenue,
            ]),
            'shifts' => $shifts->map(fn ($s) => [
                'kasir_name'      => $s->kasir_name ?? optional($s->kasir)->name ?? '-',
                'shift_name'      => $s->shift_name ?? '-',
                'started_at'      => optional($s->started_at)->toISOString(),
                'ended_at'        => optional($s->ended_at)->toISOString(),
                'opening_balance' => (float) ($s->opening_balance ?? 0),
                'status'          => $s->status,
            ]),
            'payment' => [
                'cash'     => $summary['cash']     ?? 0,
                'qris'     => $summary['qris']     ?? 0,
                'transfer' => $summary['transfer'] ?? 0,
            ],
            'transactions' => $transactions->map(fn ($t) => [
                'id'              => $t->id,
                'invoice_number'  => $t->invoice_number,
                'customer_name'   => $t->customer_name ?? 'Umum',
                'kasir_name'      => optional($t->kasir)->name ?? '-',
                'shift_name'      => optional($t->shift)->shift_name ?? '-',
                'payment_method'  => $t->payment_method ?? '-',
                'payment_label'   => strtoupper($t->payment_method ?? '-'),
                'subtotal'        => (float) ($t->subtotal ?? 0),
                'discount'        => (float) ($t->discount ?? 0),
                'total'           => (float) ($t->total ?? 0),
                'amount_paid'     => (float) ($t->amount_paid ?? 0),
                'change_amount'   => (float) ($t->change_amount ?? 0),
                'status'          => $t->status ?? '-',
                'date'            => optional($t->created_at)->translatedFormat('d F Y'),
                'time'            => optional($t->created_at)->format('H:i:s'),
                'created_at'      => optional($t->created_at)->toISOString(),
                'items'           => $t->items->map(fn ($i) => [
                    'product_name'   => $i->product_name ?? '-',
                    'quantity'       => (int) ($i->quantity ?? 0),
                    'product_price'  => (float) ($i->product_price ?? 0),
                    'addon_price'    => (float) ($i->addon_price ?? 0),
                    'subtotal'       => (float) ($i->subtotal ?? 0),
                    'is_consignment' => (bool) ($i->is_consignment ?? false),
                    'notes'          => $i->notes,
                ]),
            ]),
            'expenses' => $expenses->map(fn ($e) => [
                'id'             => $e->id,
                'title'          => $e->title,
                'description'    => $e->description,
                'category'       => $e->category,
                'category_label' => $e->category_label ?? $e->category,
                'payment_method' => ucfirst($e->payment_method ?? '-'),
                'amount'         => (float) ($e->amount ?? 0),
                'user_name'      => optional($e->user)->name ?? '-',
                'supplier_name'  => optional($e->supplier)->name ?? '-',
                'date'           => optional($e->expense_date)->translatedFormat('d F Y'),
                'time'           => optional($e->created_at)->format('H:i:s'),
            ]),
            'last_update' => Cache::get('dashboard_last_update', 0),
        ]);
    }

    public function dashboardStream(): StreamedResponse
    {
        return response()->stream(function () {
            $lastSeen = Cache::get('dashboard_last_update', 0);

            echo "data: {\"type\":\"connected\"}\n\n";
            @ob_flush();
            flush();

            $i = 0;
            while (true) {
                if (connection_aborted()) break;

                $current = Cache::get('dashboard_last_update', 0);

                if ($current !== $lastSeen) {
                    $lastSeen = $current;
                    echo "data: {\"type\":\"refresh\"}\n\n";
                    @ob_flush();
                    flush();
                }

                if ($i % 4 === 0) {
                    echo ": heartbeat\n\n";
                    @ob_flush();
                    flush();
                }

                $i++;
                sleep(5);
            }
        }, 200, [
            'Content-Type'      => 'text/event-stream',
            'Cache-Control'     => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Connection'        => 'keep-alive',
        ]);
    }

    public function storeExpense(Request $request)
    {
        $request->merge([
            'amount' => preg_replace('/[^0-9]/', '', (string) $request->amount),
        ]);

        $data = $request->validate([
            'title'          => 'required|string|max:150',
            'category'       => 'required|string|in:gaji_pegawai,biaya_kulakan,bayar_titipan,bayar_listrik,bayar_wifi,lainnya',
            'amount'         => 'required|numeric|min:1',
            'payment_method' => 'nullable|string|max:50',
            'expense_date'   => 'required|date',
            'description'    => 'nullable|string|max:500',
        ]);

        Expense::create([
            'user_id'        => auth()->id(),
            'title'          => $data['title'],
            'description'    => $data['description'] ?? null,
            'category'       => $data['category'],
            'amount'         => $data['amount'],
            'payment_method' => $data['payment_method'] ?? 'tunai',
            'receipt_number' => 'OUT-' . now()->format('YmdHis'),
            'expense_date'   => $data['expense_date'],
            'supplier_id'    => null,
        ]);

        Cache::put('dashboard_last_update', now()->timestamp, 3600);

        return redirect()->route('owner.dashboard')->with('success', 'Transaksi keluar berhasil disimpan.');
    }

    public function deleteExpense(Expense $expense)
    {
        $expense->delete();
        Cache::put('dashboard_last_update', now()->timestamp, 3600);

        return redirect()->route('owner.dashboard')->with('success', 'Transaksi keluar berhasil dihapus.');
    }
}
