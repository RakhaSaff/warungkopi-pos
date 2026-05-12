<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\{User, ProductCategory, Product, ProductAddon, ConsignmentSupplier, Ingredient, Shift, Expense};

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::updateOrCreate(['email'=>'owner@warungkopi.com'], [
            'name'=>'Budi Santosa (Owner)','password'=>Hash::make('password'),'role'=>'owner','is_active'=>true
        ]);
        $kasir = User::updateOrCreate(['email'=>'kasir@warungkopi.com'], [
            'name'=>'Siti Rahayu (Kasir)','password'=>Hash::make('password'),'role'=>'kasir','is_active'=>true
        ]);

        $cats = [
            ['name'=>'Kopi Panas','icon'=>'☕','sort_order'=>1],
            ['name'=>'Kopi Dingin','icon'=>'🧊','sort_order'=>2],
            ['name'=>'Non-Kopi','icon'=>'🍵','sort_order'=>3],
            ['name'=>'Makanan','icon'=>'🥐','sort_order'=>4],
            ['name'=>'Titipan','icon'=>'🛍️','sort_order'=>5],
        ];
        foreach ($cats as $c) ProductCategory::updateOrCreate(['name'=>$c['name']], $c + ['is_active'=>true]);

        $supplier = ConsignmentSupplier::updateOrCreate(['name'=>'Ibu Sri Snack'], ['phone'=>'081234567890','address'=>'Surabaya','balance_owed'=>0]);

        $products = [
            ['Americano','Kopi Panas',18000,7000,70,false,null],
            ['Latte','Kopi Panas',22000,9000,60,false,null],
            ['Cappuccino','Kopi Panas',22000,9000,60,false,null],
            ['Flat White','Kopi Panas',25000,10000,40,false,null],
            ['V60 Filter','Kopi Panas',28000,12000,30,false,null],
            ['Es Kopi Susu','Kopi Dingin',20000,8000,60,false,null],
            ['Es Latte','Kopi Dingin',23000,9000,50,false,null],
            ['Cold Brew','Kopi Dingin',27000,11000,35,false,null],
            ['Frappu Kopi','Kopi Dingin',25000,10000,35,false,null],
            ['Matcha Latte','Non-Kopi',22000,9000,40,false,null],
            ['Coklat Panas','Non-Kopi',18000,7000,40,false,null],
            ['Teh Tarik','Non-Kopi',12000,4000,80,false,null],
            ['Croissant','Makanan',18000,9000,30,false,null],
            ['Roti Bakar','Makanan',15000,7000,30,false,null],
            ['Kue Cubit','Titipan',5000,3500,50,true,$supplier->id],
            ['Risoles','Titipan',8000,5500,45,true,$supplier->id],
            ['Lemper','Titipan',6000,4000,40,true,$supplier->id],
            ['Getuk Goreng','Titipan',4000,2500,50,true,$supplier->id],
        ];

        foreach ($products as [$name,$cat,$price,$cost,$stock,$consign,$supplierId]) {
            $category = ProductCategory::where('name',$cat)->first();
            $product = Product::updateOrCreate(['name'=>$name], [
                'category_id'=>$category->id,'supplier_id'=>$supplierId,'price'=>$price,'cost_price'=>$cost,
                'stock'=>$stock,'stock_alert'=>5,'is_consignment'=>$consign,'is_active'=>true,
            ]);
            if (in_array($name, ['Americano','Latte','Cappuccino','Es Kopi Susu','Matcha Latte'])) {
                ProductAddon::updateOrCreate(['product_id'=>$product->id,'name'=>'Extra Shot'], ['price'=>3000,'is_active'=>true]);
            }
        }

        foreach ([['Susu Segar','liter',0.8,2,18000],['Biji Kopi Robusta','gram',320,500,70],['Oat Milk','carton',2,3,35000],['Simple Syrup','liter',1.2,0.5,12000]] as $i) {
            Ingredient::updateOrCreate(['name'=>$i[0]], ['unit'=>$i[1],'stock'=>$i[2],'stock_alert'=>$i[3],'cost_per_unit'=>$i[4]]);
        }

        Shift::firstOrCreate(['user_id'=>$kasir->id,'status'=>'active'], [
            'shift_name'=>'Shift Pagi','started_at'=>now()->setTime(8,0),'opening_balance'=>200000
        ]);

        Expense::firstOrCreate(['title'=>'Biji Kopi Arabika 1kg','expense_date'=>today()], [
            'user_id'=>$owner->id,'category'=>'cogs','amount'=>150000,'payment_method'=>'tunai'
        ]);
    }
}
