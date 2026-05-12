<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ProductAddon extends Model {
 protected $fillable=['product_id','name','price','is_active'];
 protected $casts=['price'=>'decimal:2','is_active'=>'boolean'];
 public function product(){return $this->belongsTo(Product::class);}
 public function scopeActive($q){return $q->where('is_active',true);}
}
