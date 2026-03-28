<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable implements FilamentUser, HasAvatar
{
    use HasFactory, Notifiable, HasRoles, HasPanelShield;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'avatar',
        'password',
        'oauth_id',
        'oauth_provider',
        'two_factor_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_verified_at' => 'datetime',
        ];
    }

    public function createdMeetings(): HasMany
    {
        return $this->hasMany(Meeting::class, 'created_by');
    }

    public function attendedMeetings(): BelongsToMany
    {
        return $this->belongsToMany(Meeting::class)
            ->withPivot('status')
            ->withTimestamps();
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    /**
     * Check if user is an OAuth user
     */
    public function isOAuthUser(): bool
    {
        return !empty($this->oauth_id) && !empty($this->oauth_provider);
    }

    /**
     * Determine whether the user can skip 2FA challenge within the last 24 hours.
     */
    public function hasRecentTwoFactorVerification(): bool
    {
        if (!$this->two_factor_verified_at) {
            return false;
        }

        return $this->two_factor_verified_at->greaterThan(now()->subHours(24));
    }

    /**
     * Mark current user as having passed 2FA now.
     */
    public function markTwoFactorVerified(): void
    {
        $this->forceFill([
            'two_factor_verified_at' => now(),
        ])->save();
    }

    /**
     * Determine if the user can access the Filament panel
     */
    public function canAccessPanel(Panel $panel): bool
    {
        // Allow access to Sangkara panel for authenticated users
        return true;
    }

    /**
     * Get the user's avatar URL for Filament navigation
     */
    public function getFilamentAvatarUrl(): ?string
    {
        // If user has uploaded avatar
        if ($this->avatar) {
            // If it's a full URL (Cloudinary), return as is
            if (str_starts_with($this->avatar, 'http://') || str_starts_with($this->avatar, 'https://')) {
                return $this->avatar;
            }
            
            // Otherwise, it's a local storage path - prepend base URL
            $url = \Storage::url($this->avatar);
            
            // Make sure it's absolute URL for Filament
            if (!str_starts_with($url, 'http')) {
                return url($url);
            }
            
            return $url;
        }
        
        // Fallback to UI Avatars with user's name initials
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
    }
}
