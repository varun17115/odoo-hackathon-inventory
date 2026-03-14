<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockMovement extends Model
{
    protected $fillable = ['movement_type', 'reference_type', 'reference_id', 'created_by', 'notes'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(StockMovementItem::class, 'movement_id');
    }

    /**
     * Create a receipt movement
     */
    public static function createReceipt($receiptId, $userId, $notes = null)
    {
        return self::create([
            'movement_type' => 'receipt',
            'reference_type' => 'Receipt',
            'reference_id' => $receiptId,
            'created_by' => $userId,
            'notes' => $notes,
        ]);
    }

    /**
     * Create a transfer movement
     */
    public static function createTransfer($transferId, $userId, $notes = null)
    {
        return self::create([
            'movement_type' => 'transfer',
            'reference_type' => 'Transfer',
            'reference_id' => $transferId,
            'created_by' => $userId,
            'notes' => $notes,
        ]);
    }

    /**
     * Create a delivery movement
     */
    public static function createDelivery($deliveryId, $userId, $notes = null)
    {
        return self::create([
            'movement_type' => 'delivery',
            'reference_type' => 'Delivery',
            'reference_id' => $deliveryId,
            'created_by' => $userId,
            'notes' => $notes,
        ]);
    }

    /**
     * Create an adjustment movement
     */
    public static function createAdjustment($userId, $notes = null)
    {
        return self::create([
            'movement_type' => 'adjustment',
            'reference_type' => 'Manual',
            'reference_id' => null,
            'created_by' => $userId,
            'notes' => $notes,
        ]);
    }

    /**
     * Get movement type badge color
     */
    public function getMovementTypeBadgeColor(): string
    {
        return match($this->movement_type) {
            'receipt' => 'success',
            'delivery' => 'danger',
            'transfer' => 'info',
            'adjustment' => 'warning',
            default => 'secondary',
        };
    }

    /**
     * Get movement type label
     */
    public function getMovementTypeLabel(): string
    {
        return ucfirst($this->movement_type);
    }
}
