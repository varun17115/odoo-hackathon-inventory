<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stock extends Model
{
    protected $fillable = ['product_id', 'warehouse_id', 'location_id', 'quantity'];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Rack::class, 'location_id');
    }

    /**
     * Get or create a stock record
     */
    public static function getOrCreate($productId, $warehouseId, $locationId = null)
    {
        return self::firstOrCreate(
            [
                'product_id' => $productId,
                'warehouse_id' => $warehouseId,
                'location_id' => $locationId,
            ],
            ['quantity' => 0]
        );
    }

    /**
     * Increment stock quantity
     */
    public function addStock($quantity)
    {
        $this->increment('quantity', $quantity);
        return $this;
    }

    /**
     * Decrement stock quantity
     */
    public function removeStock($quantity)
    {
        if ($this->quantity < $quantity) {
            throw new \Exception("Insufficient stock. Available: {$this->quantity}, Requested: {$quantity}");
        }
        $this->decrement('quantity', $quantity);
        return $this;
    }

    /**
     * Get total stock for a product across all locations in a warehouse
     */
    public static function getWarehouseTotal($productId, $warehouseId)
    {
        return self::where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->sum('quantity');
    }

    /**
     * Get total stock for a product across all warehouses
     */
    public static function getProductTotal($productId)
    {
        return self::where('product_id', $productId)->sum('quantity');
    }
}
