<?php
namespace App\Services;

use App\Models\{Product, StockMovement};

class InventoryService
{
    public function decreaseStock(Product $product, int $quantity, $reference = null): void
    {
        if ($product->stock < $quantity) throw new \RuntimeException("Stok {$product->name} tidak cukup.");
        $before = $product->stock;
        $product->decrement('stock', $quantity);

        StockMovement::create([
            'product_id'=>$product->id,'type'=>'out','quantity'=>$quantity,
            'quantity_before'=>$before,'quantity_after'=>$before-$quantity,
            'reference_type'=>$reference ? get_class($reference) : null,
            'reference_id'=>$reference?->id,'user_id'=>auth()->id() ?? 1,
        ]);

        foreach ($product->ingredients as $pi) {
            $ingredient = $pi->ingredient;
            $ingredient->decrement('stock', $pi->quantity * $quantity);
        }
    }

    public function increaseStock(Product $product, int $quantity, $reference = null, string $notes = ''): void
    {
        $before = $product->stock;
        $product->increment('stock', $quantity);
        StockMovement::create([
            'product_id'=>$product->id,'type'=>'in','quantity'=>$quantity,
            'quantity_before'=>$before,'quantity_after'=>$before+$quantity,
            'reference_type'=>$reference ? get_class($reference) : null,
            'reference_id'=>$reference?->id,'notes'=>$notes,'user_id'=>auth()->id() ?? 1,
        ]);
    }
}
