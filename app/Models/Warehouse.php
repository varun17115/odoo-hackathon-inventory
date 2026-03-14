<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warehouse extends Model
{
    protected $fillable = ['name', 'location', 'description', 'manager_name', 'manager_phone', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function racks(): HasMany
    {
        return $this->hasMany(Rack::class);
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class);
    }

    public function getActiveRacksCount(): int
    {
        return $this->racks()->where('is_active', true)->count();
    }
}
