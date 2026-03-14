<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Delivery extends Model
{
    protected $fillable = [
        'delivery_number',
        'customer_name',
        'customer_phone',
        'customer_email',
        'delivery_address',
        'warehouse_id',
        'user_id',
        'delivery_date',
        'status',
        'total_amount',
        'notes'
    ];

    protected $casts = [
        'delivery_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(DeliveryItem::class);
    }

    public static function generateDeliveryNumber(): string
    {
        $lastDelivery = self::latest('id')->first();
        $number = ($lastDelivery?->id ?? 0) + 1;
        return 'DEL-' . date('Ymd') . '-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    public function getTotalQuantity(): int
    {
        return $this->items()->sum('quantity');
    }

    public function getStatusBadgeColor(): string
    {
        return match($this->status) {
            'draft' => 'secondary',
            'picking' => 'info',
            'packed' => 'primary',
            'validated' => 'success',
            'shipped' => 'dark',
            'cancelled' => 'danger',
            default => 'secondary',
        };
    }

    public function getStatusLabel(): string
    {
        return ucfirst($this->status);
    }

    public function canPick(): bool
    {
        return $this->status === 'draft';
    }

    public function canPack(): bool
    {
        return $this->status === 'picking' && $this->items()->where('is_picked', false)->count() === 0;
    }

    public function canValidate(): bool
    {
        return $this->status === 'packed' && $this->items()->where('is_packed', false)->count() === 0;
    }

    public function canCancel(): bool
    {
        return in_array($this->status, ['draft', 'picking', 'packed']);
    }
}
