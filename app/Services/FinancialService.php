<?php
namespace App\Services;
use App\Models\{Transaction, Expense, Product, Shift};
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class FinancialService
{
    public function getTodaySummary(): array
    {
        $base = Transaction::completed()->today();
        $count = (clone $base)->count();
        $revenue = (float)(clone $base)->sum('total');
        $expense = (float)Expense::whereDate('expense_date', today())->sum('amount');
        return [
            'total_revenue'=>$revenue,
            'total_expense'=>$expense,
            'net_profit'=>$revenue-$expense,
            'transaction_count'=>$count,
            'avg_order_value'=>$count ? $revenue/$count : 0,
            'cash'=>(float)(clone $base)->byMethod('tunai')->sum('total'),
            'qris'=>(float)(clone $base)->byMethod('qris')->sum('total'),
            'transfer'=>(float)(clone $base)->byMethod('transfer')->sum('total'),
            'consignment'=>(float)(clone $base)->sum('consignment_amount'),
        ];
    }

    public function topProducts(int $limit=5)
    {
        return DB::table('transaction_items as ti')
            ->join('transactions as t','t.id','=','ti.transaction_id')
            ->select('ti.product_name as name', DB::raw('SUM(ti.quantity) as sold'), DB::raw('SUM(ti.subtotal) as revenue'))
            ->where('t.status','completed')->whereDate('t.created_at',today())
            ->groupBy('ti.product_name')->orderByDesc('sold')->limit($limit)->get();
    }

    public function lowStocks()
    {
        return Product::active()->whereColumn('stock','<=','stock_alert')->orderBy('stock')->limit(8)->get();
    }

    public function hourlyChart(): array
    {
        $rows = Transaction::completed()->today()
            ->selectRaw("EXTRACT(HOUR FROM created_at) as hour, SUM(total) as total")
            ->groupByRaw('EXTRACT(HOUR FROM created_at)')->orderBy('hour')->get();
        return [
            'labels'=>$rows->map(fn($r)=>str_pad((int)$r->hour,2,'0',STR_PAD_LEFT).':00'),
            'revenue'=>$rows->pluck('total')->map(fn($v)=>(float)$v),
        ];
    }
}
