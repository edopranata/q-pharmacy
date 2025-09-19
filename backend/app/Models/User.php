<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use Auditable, HasApiTokens, HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'phone',
        'bio',
        'location',
        'last_login',
        'last_activity',
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
            'last_login' => 'datetime',
            'last_activity' => 'datetime',
        ];
    }
    
    /**
     * Get user activities
     */
    public function activities()
    {
        return $this->hasMany(UserActivity::class);
    }
    
    /**
     * Check if user is online (active within last 5 minutes)
     */
    public function isOnline(): bool
    {
        return $this->last_activity && $this->last_activity->diffInMinutes(now()) <= 5;
    }
    
    /**
     * Get user's last activity formatted
     */
    public function getLastActivityFormattedAttribute(): ?string
    {
        return $this->last_activity ? $this->last_activity->diffForHumans() : null;
    }
    
    /**
     * Get user's last login formatted
     */
    public function getLastLoginFormattedAttribute(): ?string
    {
        return $this->last_login ? $this->last_login->diffForHumans() : null;
    }
}
