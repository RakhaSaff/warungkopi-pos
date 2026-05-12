<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role')) $table->string('role', 20)->default('kasir')->after('email');
            if (!Schema::hasColumn('users', 'is_active')) $table->boolean('is_active')->default(true)->after('role');
            if (!Schema::hasColumn('users', 'phone')) $table->string('phone', 20)->nullable()->after('is_active');
        });

        Schema::create('product_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('icon', 10)->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('consignment_suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone', 20)->nullable();
            $table->text('address')->nullable();
            $table->decimal('balance_owed', 15, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('product_categories');
            $table->foreignId('supplier_id')->nullable()->constrained('consignment_suppliers');
            $table->string('name');
            $table->string('sku', 50)->unique()->nullable();
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2);
            $table->decimal('cost_price', 12, 2)->default(0);
            $table->integer('stock')->default(0);
            $table->integer('stock_alert')->default(5);
            $table->boolean('is_consignment')->default(false);
            $table->boolean('is_active')->default(true);
            $table->boolean('has_variants')->default(false);
            $table->string('image')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index('category_id');
        });

        Schema::create('product_addons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->decimal('price', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('unit', 20);
            $table->decimal('stock', 12, 3)->default(0);
            $table->decimal('stock_alert', 12, 3)->default(0);
            $table->decimal('cost_per_unit', 12, 4)->default(0);
            $table->timestamps();
        });

        Schema::create('product_ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ingredient_id')->constrained()->cascadeOnDelete();
            $table->decimal('quantity', 10, 3);
            $table->timestamps();
            $table->unique(['product_id', 'ingredient_id']);
        });

        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('shift_name', 50);
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            $table->decimal('opening_balance', 12, 2)->default(0);
            $table->decimal('closing_balance_expected', 12, 2)->nullable();
            $table->decimal('closing_balance_actual', 12, 2)->nullable();
            $table->decimal('closing_balance_difference', 12, 2)->nullable();
            $table->text('notes')->nullable();
            $table->string('status', 10)->default('active');
            $table->timestamps();
            $table->index(['user_id', 'status']);
        });

        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 50)->unique();
            $table->foreignId('shift_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->string('customer_name', 100)->nullable();
            $table->string('payment_method', 20);
            $table->string('payment_reference', 100)->nullable();
            $table->decimal('subtotal', 12, 2);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('total', 12, 2);
            $table->decimal('amount_paid', 12, 2)->default(0);
            $table->decimal('change_amount', 12, 2)->default(0);
            $table->decimal('consignment_amount', 12, 2)->default(0);
            $table->string('status', 20)->default('completed');
            $table->text('void_reason')->nullable();
            $table->timestamp('voided_at')->nullable();
            $table->foreignId('voided_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->index(['created_at', 'status']);
            $table->index(['shift_id', 'status']);
            $table->index('payment_method');
        });

        Schema::create('transaction_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained();
            $table->string('product_name');
            $table->decimal('product_price', 12, 2);
            $table->boolean('is_consignment')->default(false);
            $table->integer('quantity');
            $table->decimal('addon_price', 10, 2)->default(0);
            $table->jsonb('addons')->nullable();
            $table->text('notes')->nullable();
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();
            $table->index('transaction_id');
            $table->index('product_id');
        });

        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('category', 30);
            $table->decimal('amount', 12, 2);
            $table->string('payment_method', 20)->default('tunai');
            $table->string('receipt_number', 100)->nullable();
            $table->date('expense_date');
            $table->foreignId('supplier_id')->nullable()->constrained('consignment_suppliers');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['expense_date', 'category']);
        });

        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->nullable()->constrained();
            $table->foreignId('ingredient_id')->nullable()->constrained();
            $table->string('type', 15);
            $table->decimal('quantity', 12, 3);
            $table->decimal('quantity_before', 12, 3);
            $table->decimal('quantity_after', 12, 3);
            $table->string('reference_type', 100)->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->constrained();
            $table->timestamps();
            $table->index(['product_id', 'created_at']);
            $table->index(['reference_type', 'reference_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('transaction_items');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('shifts');
        Schema::dropIfExists('product_ingredients');
        Schema::dropIfExists('ingredients');
        Schema::dropIfExists('product_addons');
        Schema::dropIfExists('products');
        Schema::dropIfExists('consignment_suppliers');
        Schema::dropIfExists('product_categories');
        Schema::table('users', function (Blueprint $table) {
            foreach (['role','is_active','phone'] as $col) if (Schema::hasColumn('users', $col)) $table->dropColumn($col);
        });
    }
};
