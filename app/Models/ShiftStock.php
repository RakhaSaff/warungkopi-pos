<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShiftStock extends Model
{
    protected $fillable = [
        'shift_id',
        'product_id',
        'opening_stock',
        'current_stock',
        'sold_stock',
        'is_consignment',
        'note',
    ];

    protected $casts = [
        'shift_id' => 'integer',
        'product_id' => 'integer',
        'opening_stock' => 'integer',
        'current_stock' => 'integer',
        'sold_stock' => 'integer',
        'is_consignment' => 'boolean',
    ];

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}