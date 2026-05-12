<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ConsignmentSupplier extends Model {
 protected $fillable=['name','phone','address','balance_owed'];
 protected $casts=['balance_owed'=>'decimal:2'];
 public function products(){return $this->hasMany(Product::class,'supplier_id');}
}
