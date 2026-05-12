<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class StockMovement extends Model {
 protected $fillable=['product_id','ingredient_id','type','quantity','quantity_before','quantity_after','reference_type','reference_id','notes','user_id'];
 protected $casts=['quantity'=>'decimal:3','quantity_before'=>'decimal:3','quantity_after'=>'decimal:3'];
 public function product(){return $this->belongsTo(Product::class);}
 public function ingredient(){return $this->belongsTo(Ingredient::class);}
 public function user(){return $this->belongsTo(User::class);}
}
