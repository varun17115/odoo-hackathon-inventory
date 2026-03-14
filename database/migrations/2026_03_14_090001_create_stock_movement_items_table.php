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
        Schema::create('stock_movement_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('movement_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('source_location_id')->nullable(); // warehouse_id or rack_id
            $table->unsignedBigInteger('destination_location_id')->nullable(); // warehouse_id or rack_id
            $table->integer('quantity');
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('movement_id')->references('id')->on('stock_movements')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            
            // Indexes
            $table->index('product_id');
            $table->index('movement_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movement_items');
    }
};
