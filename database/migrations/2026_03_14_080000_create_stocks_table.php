<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('warehouse_id');
            $table->unsignedBigInteger('location_id')->nullable(); // rack_id
            $table->integer('quantity')->default(0);
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
            $table->foreign('location_id')->references('id')->on('racks')->onDelete('set null');
            
            // Unique constraint: one stock record per product-warehouse-location combination
            $table->unique(['product_id', 'warehouse_id', 'location_id']);
            
            // Indexes for common queries
            $table->index('product_id');
            $table->index('warehouse_id');
            $table->index('location_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
