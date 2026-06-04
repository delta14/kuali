<?php

namespace App\Models;

use App\Models\Concerns\HasBusinessScope;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasBusinessScope;

    protected $fillable = [
        'business_id',
        'table_id',
        'user_id',
        'status',
        'source',
        'order_type',
        'subtotal',
        'discount',
        'total',
        'customer_name',
        'customer_phone',
        'notes',
        'placed_at',
        'closed_at',
    ];

    protected $casts = [
        'placed_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(\App\Models\OrderItem::class);
    }
    
    public function table()
    {
        return $this->belongsTo(\App\Models\Table::class);
    }
    
    public function business()
    {
        return $this->belongsTo(\App\Models\Business::class);
    }
    
}
