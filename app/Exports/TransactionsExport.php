<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class TransactionsExport implements FromArray, ShouldAutoSize
{
    public function __construct(
        private Collection $transactions,
        private string $title
    ) {}

    public function array(): array
    {
        $rows = [];
        $rows[] = [$this->title];
        $rows[] = [];
        $rows[] = [
            'Tanggal', 'Jam', 'Invoice', 'Kasir', 'Shift', 'Customer',
            'Menu', 'Qty', 'Harga Satuan', 'Subtotal Item', 'Jenis Produk',
            'Metode Bayar', 'Subtotal Transaksi', 'Diskon', 'Total',
            'Dibayar', 'Kembalian', 'Status'
        ];

        foreach ($this->transactions as $trx) {
            foreach ($trx->items as $item) {
                $rows[] = [
                    optional($trx->created_at)->format('d/m/Y'),
                    optional($trx->created_at)->format('H:i:s'),
                    $trx->invoice_number,
                    optional($trx->kasir)->name ?? '-',
                    optional($trx->shift)->shift_name ?? '-',
                    $trx->customer_name ?? 'Umum',
                    $item->product_name ?? '-',
                    (int) $item->quantity,
                    (float) $item->product_price,
                    (float) $item->subtotal,
                    $item->is_consignment ? 'Titipan/Konsinyasi' : 'Produk Utama',
                    strtoupper($trx->payment_method ?? '-'),
                    (float) $trx->subtotal,
                    (float) $trx->discount,
                    (float) $trx->total,
                    (float) $trx->amount_paid,
                    (float) $trx->change_amount,
                    $trx->status,
                ];
            }
        }

        $rows[] = [];
        $rows[] = ['TOTAL PENDAPATAN', '', '', '', '', '', '', '', '', '', '', '', '', '', (float) $this->transactions->sum('total')];

        return $rows;
    }
}
