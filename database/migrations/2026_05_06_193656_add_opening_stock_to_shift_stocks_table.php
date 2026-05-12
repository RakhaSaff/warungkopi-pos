<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shift_stocks', function (Blueprint $table) {
            if (!Schema::hasColumn('shift_stocks', 'opening_stock')) {
                $table->integer('opening_stock')->default(0)->after('product_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('shift_stocks', function (Blueprint $table) {
            if (Schema::hasColumn('shift_stocks', 'opening_stock')) {
                $table->dropColumn('opening_stock');
            }
        });
    }
};