<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reorder_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('warehouse_id')->nullable()->constrained()->onDelete('cascade');
            $table->integer('min_quantity')->default(0)->comment('Trigger reorder when stock falls below this');
            $table->integer('reorder_quantity')->default(0)->comment('Quantity to reorder');
            $table->foreignId('preferred_supplier_id')->nullable()->constrained('suppliers')->onDelete('set null');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['product_id', 'warehouse_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reorder_rules');
    }
};
