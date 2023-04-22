<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use App\Notifications\VerifyEmail;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\ResetPasswordNotification;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail, FilamentUser, HasAvatar
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The attributes of roles.
     */
    public static $roles = ['ADMIN', 'STAFF', 'CUSTOMER'];

    /**
     * The attributes of status.
     */
    public static $status = ['ACTIVE', 'INACTIVE'];

    /**
     * The attributes of user's image directory.
     */
    public static $directory = 'avatars';

    /**
     * Get the orders for the user.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the city that owns the user.
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Set the user's name.
     */
    public function setNameAttribute(string $value): void
    {
        $this->attributes['name'] = ucwords($value);
    }

    /**
     * Set the user's username.
     */
    public function setUsernameAttribute(string $value): void
    {
        $this->attributes['username'] = strtolower($value);
    }

    /**
     * get the user's avatar with custom directory path.
     */
    public function getAvatar(): string
    {
        return $this->avatar
            ? asset('storage/avatars/' . $this->avatar)
            : asset('assets/images/no-user.jpg');
    }

    /**
     * Send the email verification notification.
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmail);
    }

    /**
     * Send the password reset notification.
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * Get the user's avatar only for admin panel (user who has admin or staff role).
     */
    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar ? asset("/storage/$this->avatar") : null;
    }

    /**
     * Authenticate user to admin panel.
     */
    public function canAccessFilament(): bool
    {
        return checkRole(self::$roles[1], $this->role) && $this->status === self::$status[0];
    }
}
