<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShiftStock extends Model
{
    protected $fillable = ['shift_id', 'product_id', 'opening_stock', 'current_stock'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
}