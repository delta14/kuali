<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Business extends Model
{
    //
    protected $fillable = [
        'name',
        'slug',
        'email_contact',
        'phone',
        'address',
        'logo_path',
        'plan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'business_user')
                    ->withPivot('role_id')
                    ->withTimestamps();
    }

    public function admins()
    {
        // Ajusta el role_id según tu catálogo (ej. 2 = admin de comercio)
        return $this->users()->wherePivot('role_id', 2);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function tables()
    {
        return $this->hasMany(Table::class);
    }

}
