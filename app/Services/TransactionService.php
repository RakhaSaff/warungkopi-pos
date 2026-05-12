<?php
namespace App\Services;

use App\Models\{Transaction, TransactionItem, Product};
use Illuminate\Support\Facades\DB;

class TransactionService
{
    public function __construct(private InventoryService $inventory) {}

    public function process(array $data, int $userId, int $shiftId): Transaction
    {
        return DB::transaction(function () use ($data,$userId,$shiftId) {
            $subtotal = 0; $consignmentTotal = 0;
            foreach ($data['items'] as $item) {
                $product = Product::with('supplier')->lockForUpdate()->findOrFail($item['product_id']);
                if ($product->stock < $item['quantity']) throw new \RuntimeException("Stok {$product->name} tidak cukup.");
                $addonPrice = (float)($item['addon_price'] ?? 0);
                $line = ((float)$product->price + $addonPrice) * (int)$item['quantity'];
                $subtotal += $line;
                if ($product->is_consignment) $consignmentTotal += (float)$product->cost_price * (int)$item['quantity'];
            }

            $discount = (float)($data['discount'] ?? 0);
            $total = max(0, $subtotal - $discount);
            $paid = (float)($data['amount_paid'] ?? $total);

            $transaction = Transaction::create([
                'invoice_number'=>Transaction::generateInvoiceNumber(),
                'shift_id'=>$shiftId,'user_id'=>$userId,'customer_name'=>$data['customer_name'] ?? null,
                'payment_method'=>$data['payment_method'],'payment_reference'=>$data['payment_reference'] ?? null,
                'subtotal'=>$subtotal,'discount'=>$discount,'total'=>$total,'amount_paid'=>$paid,
                'change_amount'=>max(0,$paid-$total),'consignment_amount'=>$consignmentTotal,'status'=>'completed',
            ]);

            foreach ($data['items'] as $item) {
                $product = Product::with('supplier')->lockForUpdate()->findOrFail($item['product_id']);
                $addonPrice = (float)($item['addon_price'] ?? 0);
                TransactionItem::create([
                    'transaction_id'=>$transaction->id,'product_id'=>$product->id,
                    'product_name'=>$product->name,'product_price'=>$product->price,
                    'is_consignment'=>$product->is_consignment,'quantity'=>$item['quantity'],
                    'addon_price'=>$addonPrice,'addons'=>$item['addons'] ?? [],
                    'notes'=>$item['notes'] ?? null,
                    'subtotal'=>((float)$product->price + $addonPrice) * (int)$item['quantity'],
                ]);
                $this->inventory->decreaseStock($product, (int)$item['quantity'], $transaction);

                if ($product->is_consignment && $product->supplier) {
                    $product->supplier->increment('balance_owed', (float)$product->cost_price * (int)$item['quantity']);
                }
            }

            return $transaction->load('items');
        });
    }

    public function void(Transaction $transaction, string $reason, int $userId): void
    {
        DB::transaction(function () use ($transaction,$reason,$userId) {
            foreach ($transaction->items as $item) {
                $this->inventory->increaseStock($item->product, (int)$item->quantity, $transaction, "Void {$transaction->invoice_number}");
            }
            $transaction->update(['status'=>'voided','void_reason'=>$reason,'voided_at'=>now(),'voided_by'=>$userId]);
        });
    }
}
