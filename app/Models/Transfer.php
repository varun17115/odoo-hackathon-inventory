<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transfer extends Model
{
    protected $fillable = ['transfer_number', 'from_warehouse_id', 'to_warehouse_id', 'user_id', 'transfer_date', 'status', 'notes'];

    protected $casts = [
        'transfer_date' => 'date',
    ];

    public function fromWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'from_warehouse_id');
    }

    public function toWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'to_warehouse_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(TransferItem::class);
    }

    public static function generateTransferNumber(): string
    {
        $lastTransfer = self::latest('id')->first();
        $number = ($lastTransfer?->id ?? 0) + 1;
        return 'TRF-' . date('Ymd') . '-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    public function getTotalQuantity(): int
    {
        return $this->items()->sum('quantity');
    }
}
