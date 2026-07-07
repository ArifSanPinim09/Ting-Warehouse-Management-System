<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'ktp_number',
        'address',
        'role',
        'status',
        'line_id',
        'password',
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
        ];
    }

    // ─── Role Checks ───────────────────────────────────────────────

    /**
     * Check if user has owner role.
     */
    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }

    /**
     * Check if user has admin role (owner is superset of admin).
     */
    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'owner']);
    }

    /**
     * Check if user has customer role.
     */
    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    // ─── Relationships (ERD §18.1) ─────────────────────────────────

    /**
     * Boxes owned by this user.
     */
    public function boxes(): HasMany
    {
        return $this->hasMany(Box::class, 'customer_id');
    }

    /**
     * Items input by this user.
     */
    public function items(): HasMany
    {
        return $this->hasMany(Item::class, 'customer_id');
    }

    /**
     * Invoices for this user.
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'customer_id');
    }

    /**
     * Checkouts by this user.
     */
    public function checkouts(): HasMany
    {
        return $this->hasMany(Checkout::class, 'customer_id');
    }

    /**
     * Complains filed by this user.
     */
    public function complains(): HasMany
    {
        return $this->hasMany(Complain::class, 'customer_id');
    }
}
