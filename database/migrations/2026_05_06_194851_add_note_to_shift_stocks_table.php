<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shift_stocks', function (Blueprint $table) {
            if (!Schema::hasColumn('shift_stocks', 'note')) {
                $table->string('note')->nullable()->after('current_stock');
            }
        });
    }

    public function down(): void
    {
        Schema::table('shift_stocks', function (Blueprint $table) {
            if (Schema::hasColumn('shift_stocks', 'note')) {
                $table->dropColumn('note');
            }
        });
    }
};