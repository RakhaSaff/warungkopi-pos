<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Product extends Model {
 use SoftDeletes;
 protected $fillable=['category_id','supplier_id','name','sku','description','price','cost_price','stock','stock_alert','is_consignment','is_active','has_variants','image'];
 protected $casts=['price'=>'decimal:2','cost_price'=>'decimal:2','is_consignment'=>'boolean','is_active'=>'boolean','has_variants'=>'boolean'];
 public function category(){return $this->belongsTo(ProductCategory::class);}
 public function supplier(){return $this->belongsTo(ConsignmentSupplier::class,'supplier_id');}
 public function addons(){return $this->hasMany(ProductAddon::class);}
 public function ingredients(){return $this->hasMany(ProductIngredient::class);}
 public function movements(){return $this->hasMany(StockMovement::class);}
 public function scopeActive($q){return $q->where('is_active',true);}
 public function scopeAvailable($q){return $q->active()->where('stock','>',0);}
 public function isLowStock(): bool {return $this->stock <= $this->stock_alert;}
}
