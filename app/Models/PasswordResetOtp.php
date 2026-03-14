<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PasswordResetOtp extends Model
{
    protected $fillable = ['user_id', 'otp', 'is_used', 'expires_at'];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_used' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired(): bool
    {
        return now()->isAfter($this->expires_at);
    }

    public function isValid(): bool
    {
        return !$this->is_used && !$this->isExpired();
    }

    public static function generateOtp(): string
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    public static function createForUser($user): self
    {
        // Invalidate previous OTPs
        self::where('user_id', $user->id)->update(['is_used' => true]);

        return self::create([
            'user_id' => $user->id,
            'otp' => self::generateOtp(),
            'expires_at' => now()->addMinutes(10),
        ]);
    }
}
