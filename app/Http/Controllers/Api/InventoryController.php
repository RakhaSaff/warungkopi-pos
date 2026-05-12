<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Product;

class InventoryController extends Controller
{
    public function lowStock()
    {
        return Product::active()->whereColumn('stock','<=','stock_alert')->orderBy('stock')->get();
    }
}
