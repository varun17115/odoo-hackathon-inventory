<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    protected $fillable = ['name', 'contact_person', 'email', 'phone', 'address', 'city', 'country', 'notes', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function receipts(): HasMany
    {
        return $this->hasMany(Receipt::class);
    }

    public function getReceiptCount(): int
    {
        return $this->receipts()->count();
    }
}
