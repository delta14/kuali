<?php

namespace App\Models;

use App\Models\Concerns\HasBusinessScope;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    use HasBusinessScope;

    protected $fillable = [
        'business_id',
        'name',
        'code',
        'qr_token',
        'capacity',
        'is_active',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function scopeForBusiness($query, $businessId)
    {
        return $query->where('business_id', $businessId);
    }
}
