<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('shift_stocks', function (Blueprint $table) {
            $table->boolean('is_consignment')->default(false)->after('current_stock');
        });
    }

    public function down(): void {
        Schema::table('shift_stocks', function (Blueprint $table) {
            $table->dropColumn('is_consignment');
        });
    }
};