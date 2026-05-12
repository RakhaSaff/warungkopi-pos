<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'customer_name'          => 'nullable|string|max:100',
            'payment_method'         => 'required|in:tunai,qris,transfer',
            'amount_paid'            => 'required|numeric|min:0',
            'items'                  => 'required|array|min:1',
            'items.*.product_id'     => 'required|exists:products,id',
            'items.*.quantity'       => 'required|integer|min:1',
        ]);

        $user  = auth()->user();
        $shift = $user->activeShift();

        if (!$shift) {
            return response()->json(['message' => 'Tidak ada shift aktif.'], 422);
        }

        return DB::transaction(function () use ($request, $user, $shift) {
            $subtotal          = 0;
            $consignmentAmount = 0;
            $itemsData         = [];

            foreach ($request->items as $item) {
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);
                $qty     = (int) $item['quantity'];

                if ($product->stock < $qty) {
                    return response()->json([
                        'message' => "Stok {$product->name} tidak cukup."
                    ], 422);
                }

                $itemSubtotal = (float) $product->price * $qty;
                $subtotal    += $itemSubtotal;

                if ($product->is_consignment) {
                    $consignmentAmount += (float) $product->cost_price * $qty;
                }

                $itemsData[] = [
                    'product'  => $product,
                    'qty'      => $qty,
                    'subtotal' => $itemSubtotal,
                ];
            }

            $discount     = 0;
            $total        = $subtotal - $discount;
            $amountPaid   = (float) $request->amount_paid;
            $changeAmount = max(0, $amountPaid - $total);

            if ($request->payment_method === 'tunai' && $amountPaid < $total) {
                return response()->json([
                    'message' => 'Uang diterima kurang dari total belanja.'
                ], 422);
            }

            $transaction = Transaction::create([
                'invoice_number'     => Transaction::generateInvoiceNumber(),
                'shift_id'           => $shift->id,
                'user_id'            => $user->id,
                'customer_name'      => $request->customer_name ?: 'Umum',
                'payment_method'     => $request->payment_method,
                'payment_reference'  => null,
                'subtotal'           => $subtotal,
                'discount'           => $discount,
                'total'              => $total,
                'amount_paid'        => $amountPaid,
                'change_amount'      => $changeAmount,
                'consignment_amount' => $consignmentAmount,
                'status'             => 'completed',
            ]);

            foreach ($itemsData as $row) {
                $product = $row['product'];

                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id'     => $product->id,
                    'product_name'   => $product->name,
                    'product_price'  => $product->price,
                    'is_consignment' => $product->is_consignment,
                    'quantity'       => $row['qty'],
                    'addon_price'    => 0,
                    'addons'         => [],
                    'notes'          => null,
                    'subtotal'       => $row['subtotal'],
                ]);

                \App\Models\ShiftStock::where('shift_id', $shift->id)
                    ->where('product_id', $product->id)
                    ->decrement('current_stock', $row['qty']);

                $product->decrement('stock', $row['qty']);
            }

            // ── Tandai ada transaksi baru agar SSE / polling tahu ────────────
            Cache::put('dashboard_last_update', now()->timestamp, 3600);

            return response()->json([
                'success'        => true,
                'invoice_number' => $transaction->invoice_number,
                'total'          => (float) $transaction->total,
                'change'         => (float) $transaction->change_amount,
                'message'        => 'Transaksi berhasil.',
            ]);
        });
    }
}