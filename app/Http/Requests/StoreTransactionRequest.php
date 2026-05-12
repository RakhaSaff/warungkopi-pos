<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class StoreTransactionRequest extends FormRequest {
 public function authorize(): bool {return auth()->check();}
 public function rules(): array {
  return [
   'customer_name'=>'nullable|string|max:100',
   'payment_method'=>'required|in:tunai,qris,transfer',
   'payment_reference'=>'nullable|string|max:100',
   'amount_paid'=>'nullable|numeric|min:0',
   'discount'=>'nullable|numeric|min:0',
   'items'=>'required|array|min:1',
   'items.*.product_id'=>'required|exists:products,id',
   'items.*.quantity'=>'required|integer|min:1',
   'items.*.addon_price'=>'nullable|numeric|min:0',
   'items.*.addons'=>'nullable|array',
   'items.*.notes'=>'nullable|string|max:255',
  ];
 }
}
