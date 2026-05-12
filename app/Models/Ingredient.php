<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Ingredient extends Model {
 protected $fillable=['name','unit','stock','stock_alert','cost_per_unit'];
 protected $casts=['stock'=>'decimal:3','stock_alert'=>'decimal:3','cost_per_unit'=>'decimal:4'];
 public function products(){return $this->hasMany(ProductIngredient::class);}
 public function isLowStock(): bool {return $this->stock <= $this->stock_alert;}
}
