<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Receipt extends Model
{
    protected $fillable = ['receipt_number', 'supplier_id', 'warehouse_id', 'user_id', 'receipt_date', 'total_amount', 'status', 'notes'];

    protected $casts = [
        'receipt_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

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
        return $this->hasMany(ReceiptItem::class);
    }

    public static function generateReceiptNumber(): string
    {
        $lastReceipt = self::latest('id')->first();
        $number = ($lastReceipt?->id ?? 0) + 1;
        return 'RCP-' . date('Ymd') . '-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    public function calculateTotal(): void
    {
        $this->total_amount = $this->items()->sum('total_price');
        $this->save();
    }
}
