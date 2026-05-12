<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class TransactionItem extends Model {
 protected $fillable=['transaction_id','product_id','product_name','product_price','is_consignment','quantity','addon_price','addons','notes','subtotal'];
 protected $casts=['product_price'=>'decimal:2','is_consignment'=>'boolean','addon_price'=>'decimal:2','subtotal'=>'decimal:2','addons'=>'array'];
 public function transaction(){return $this->belongsTo(Transaction::class);}
 public function product(){return $this->belongsTo(Product::class);}
}
