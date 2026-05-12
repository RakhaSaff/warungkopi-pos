<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    protected $fillable = [
        'user_id',
        'shift_name',
        'kasir_name',
        'started_at',
        'ended_at',
        'opening_balance',
        'closing_balance_expected',
        'closing_balance_actual',
        'closing_balance_difference',
        'notes',
        'status',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'opening_balance' => 'decimal:2',
        'closing_balance_expected' => 'decimal:2',
        'closing_balance_actual' => 'string',
        'closing_balance_difference' => 'decimal:2',
    ];

    public function kasir()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function scopeActive($q)
    {
        return $q->where('status', 'active');
    }

    public function totalRevenue(): float
    {
        return (float) $this->transactions()->completed()->sum('total');
    }

    public function totalCashRevenue(): float
    {
        return (float) $this->transactions()->completed()->byMethod('tunai')->sum('total');
    }
}