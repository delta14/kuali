<?php

namespace App\Models;

use App\Models\Concerns\HasBusinessScope;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasBusinessScope;

    protected $fillable = [
        'business_id',
        'order_id',
        'product_id',
        'product_variant_id',
        'variant_name',
        'quantity',
        'unit_price',
        'total_price',
        'notes',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    
}
