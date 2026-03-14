<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReorderRule extends Model
{
    protected $fillable = [
        'product_id', 'warehouse_id', 'min_quantity',
        'reorder_quantity', 'preferred_supplier_id', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function preferredSupplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'preferred_supplier_id');
    }

    /**
     * Check if this rule is triggered based on current stock
     */
    public function isTriggered(): bool
    {
        $stock = $this->warehouse_id
            ? Stock::getWarehouseTotal($this->product_id, $this->warehouse_id)
            : Stock::getProductTotal($this->product_id);

        return $stock <= $this->min_quantity;
    }
}
