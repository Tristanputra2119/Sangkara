<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class TwoFactorCode extends Model
{
    protected $fillable = [
        'user_id',
        'code',
        'expires_at',
        'used_at',
        'ip_address',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    /**
     * Get the user that owns the code.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Generate a new 2FA code for a user.
     */
    public static function generateFor(User $user, ?string $ipAddress = null): self
    {
        // Check if user has an active (non-expired, non-used) code
        // If yes, return it instead of generating a new one
        $existingCode = self::where('user_id', $user->id)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->first();

        if ($existingCode) {
            return $existingCode;
        }

        // Generate 6-digit code
        $code = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        return self::create([
            'user_id' => $user->id,
            'code' => $code,
            'expires_at' => now()->addHours(24), // Code valid for 24 hours
            'ip_address' => $ipAddress,
        ]);
    }

    /**
     * Verify a code for a user.
     */
    public static function verify(User $user, string $code): bool
    {
        $twoFactorCode = self::where('user_id', $user->id)
            ->where('code', $code)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->first();

        if (!$twoFactorCode) {
            return false;
        }

        $twoFactorCode->update(['used_at' => now()]);

        return true;
    }

    /**
     * Check if code is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Check if code has been used.
     */
    public function isUsed(): bool
    {
        return !is_null($this->used_at);
    }
}
