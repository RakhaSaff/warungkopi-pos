<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Transaction extends Model {
 protected $fillable=['invoice_number','shift_id','user_id','customer_name','payment_method','payment_reference','subtotal','discount','total','amount_paid','change_amount','consignment_amount','status','void_reason','voided_at','voided_by'];
 protected $casts=['subtotal'=>'decimal:2','discount'=>'decimal:2','total'=>'decimal:2','amount_paid'=>'decimal:2','change_amount'=>'decimal:2','consignment_amount'=>'decimal:2','voided_at'=>'datetime'];
 public function items(){return $this->hasMany(TransactionItem::class);}
 public function shift(){return $this->belongsTo(Shift::class);}
 public function kasir(){return $this->belongsTo(User::class,'user_id');}
 public function voidedBy(){return $this->belongsTo(User::class,'voided_by');}
 public static function generateInvoiceNumber(): string {
   $prefix='INV-'.now()->format('Ymd');
   $last=static::where('invoice_number','like',"$prefix%")->orderByDesc('id')->first();
   $seq=$last ? ((int)substr($last->invoice_number,-4))+1 : 1;
   return $prefix.'-'.str_pad($seq,4,'0',STR_PAD_LEFT);
 }
 public function scopeCompleted($q){return $q->where('status','completed');}
 public function scopeToday($q){return $q->whereDate('created_at',today());}
 public function scopeByMethod($q,$method){return $q->where('payment_method',$method);}
}
