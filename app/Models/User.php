<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
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

    /**
     * Comercios (businesses) a los que pertenece este usuario.
     */
    public function businesses()
    {
        return $this->belongsToMany(Business::class, 'business_user')
                    ->withPivot('role_id', 'is_active')
                    ->withTimestamps();
    }

    /**
     * Helper: regresa el comercio "actual" del usuario admin.
     * Por ahora: el primero activo en la pivot.
     */
    public function getCurrentBusinessAttribute()
    {
        return $this->businesses()
            ->wherePivot('is_active', 1)
            ->orderBy('business_user.created_at', 'desc')
            ->first();
    }

    public function mainBusiness()
    {
        return $this->businesses()->wherePivot('is_active', true)->first();
    }
}
