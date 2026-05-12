<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'category',
        'amount',
        'payment_method',
        'receipt_number',
        'expense_date',
        'supplier_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function supplier()
    {
        return $this->belongsTo(ConsignmentSupplier::class, 'supplier_id');
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'gaji_pegawai' => 'Gaji Pegawai',
            'biaya_kulakan' => 'Biaya Kulakan',
            'bayar_titipan' => 'Bayar Titipan',
            'bayar_listrik' => 'Bayar Listrik',
            'bayar_wifi' => 'Bayar Wifi',
            default => 'Lainnya',
        };
    }
}
