<?php

namespace App\Http\Controllers;

use App\Exports\ExpensesExport;
use App\Exports\TransactionsExport;
use App\Models\Expense;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class OwnerReportController extends Controller
{
    private function queryTransactions(Request $request)
    {
        $period = $request->get('period', 'daily');

        return Transaction::with(['kasir', 'shift', 'items'])
            ->completed()
            ->when($period === 'daily', function ($q) use ($request) {
                $date = $request->get('daily_date', today()->toDateString());
                $q->whereDate('created_at', $date);
            })
            ->when($period === 'monthly', function ($q) use ($request) {
                $month = $request->get('monthly_month', now()->format('Y-m'));
                $q->whereYear('created_at', substr($month, 0, 4))
                  ->whereMonth('created_at', substr($month, 5, 2));
            })
            ->latest('created_at')
            ->latest('id');
    }

    private function queryExpenses(Request $request)
    {
        $period = $request->get('period', 'daily');

        return Expense::with(['user', 'supplier'])
            ->when($period === 'daily', function ($q) use ($request) {
                $date = $request->get('daily_date', today()->toDateString());
                $q->whereDate('expense_date', $date);
            })
            ->when($period === 'monthly', function ($q) use ($request) {
                $month = $request->get('monthly_month', now()->format('Y-m'));
                $q->whereYear('expense_date', substr($month, 0, 4))
                  ->whereMonth('expense_date', substr($month, 5, 2));
            })
            ->latest('expense_date')
            ->latest('id');
    }

    private function title(Request $request, string $type): string
    {
        $period = $request->get('period', 'daily');
        $jenis  = $type === 'income' ? 'Transaksi Masuk' : 'Transaksi Keluar';

        if ($period === 'monthly') {
            return 'Laporan ' . $jenis . ' Bulanan - ' . $request->get('monthly_month', now()->format('Y-m'));
        }

        return 'Laporan ' . $jenis . ' Harian - ' . $request->get('daily_date', today()->toDateString());
    }

    public function transactionsPdf(Request $request)
    {
        $transactions = $this->queryTransactions($request)->get();

        $pdf = Pdf::loadView('owner.reports.transactions-pdf', [
            'title'        => $this->title($request, 'income'),
            'transactions' => $transactions,
            'total'        => $transactions->sum('total'),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('laporan-transaksi-masuk.pdf');
    }

    public function transactionsExcel(Request $request)
    {
        $transactions = $this->queryTransactions($request)->get();

        return Excel::download(
            new TransactionsExport($transactions, $this->title($request, 'income')),
            'laporan-transaksi-masuk.xlsx'
        );
    }

    public function expensesPdf(Request $request)
    {
        $expenses = $this->queryExpenses($request)->get();

        $pdf = Pdf::loadView('owner.reports.expenses-pdf', [
            'title'    => $this->title($request, 'expense'),
            'expenses' => $expenses,
            'total'    => $expenses->sum('amount'),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('laporan-transaksi-keluar.pdf');
    }

    public function expensesExcel(Request $request)
    {
        $expenses = $this->queryExpenses($request)->get();

        return Excel::download(
            new ExpensesExport($expenses, $this->title($request, 'expense')),
            'laporan-transaksi-keluar.xlsx'
        );
    }
}
