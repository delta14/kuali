<?php

namespace App\Models;

use App\Models\Concerns\HasBusinessScope;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasBusinessScope;

    protected $fillable = [
        'business_id',
        'order_number',
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

    /**
     * Folio secuencial propio del negocio (1, 2, 3... por comercio, no global).
     * Debe llamarse dentro de una transacción: bloquea la fila del negocio
     * para evitar que dos pedidos simultáneos obtengan el mismo número.
     */
    public static function nextNumberFor(int $businessId): int
    {
        $business = Business::where('id', $businessId)->lockForUpdate()->first();

        $next = $business->last_order_number + 1;

        $business->last_order_number = $next;
        $business->save();

        return $next;
    }

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
