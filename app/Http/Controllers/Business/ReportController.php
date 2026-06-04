<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    // Mismo helper que en OrderController
    protected function currentBusiness(Request $request): Business
    {
        $user = $request->user();

        $business = $user->businesses()
            ->wherePivot('is_active', true)
            ->orderBy('business_user.created_at')
            ->first();

        if (! $business) {
            abort(403, 'No tienes un comercio asignado o activo.');
        }

        return $business;
    }
    public function sales(Request $request)
    {
        $business = $this->currentBusiness($request);
    
        // ---------------------------
        // 1) Filtro de fechas (con defaults y validación)
        // ---------------------------
        $fromInput = $request->input('from');
        $toInput   = $request->input('to');
    
        // Si no mandan fechas, usamos HOY
        if (! $fromInput && ! $toInput) {
            $fromInput = now()->toDateString();
            $toInput   = now()->toDateString();
        }
    
        try {
            // Parse seguro (por si viene formato raro)
            $from = $fromInput ? Carbon::parse($fromInput)->toDateString() : null;
            $to   = $toInput   ? Carbon::parse($toInput)->toDateString()   : null;
    
            // Validar rango
            if ($from && $to && $from > $to) {
                return back()
                    ->withInput()
                    ->with('error', 'La fecha inicial no puede ser mayor que la final.');
            }
    
            // ---------------------------
            // 2) Traer pedidos completados del rango
            // ---------------------------
            $ordersQuery = Order::where('business_id', $business->id)
                ->where('status', 'completed');
    
            if ($from) {
                $ordersQuery->whereDate('created_at', '>=', $from);
            }
            if ($to) {
                $ordersQuery->whereDate('created_at', '<=', $to);
            }
    
            $orders = $ordersQuery->get();
    
            // ---------------------------
            // 3) KPIs
            // ---------------------------
            $totalSales  = $orders->sum('total');
            $totalOrders = $orders->count();
            $avgTicket   = $totalOrders > 0 ? $totalSales / $totalOrders : 0;
    
            // Ventas por origen (QR vs Mostrador)
            $bySource = $orders->groupBy('source')->map(function ($group) {
                return [
                    'orders' => $group->count(),
                    'total'  => $group->sum('total'),
                ];
            });
    
            // Ventas por tipo de servicio (para comer aquí / para llevar)
            $byType = $orders->groupBy('order_type')->map(function ($group) {
                return [
                    'orders' => $group->count(),
                    'total'  => $group->sum('total'),
                ];
            });
    
            // ---------------------------
            // 4) Top productos (order_items)
            // ---------------------------
            $topProducts = DB::table('order_items')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->leftJoin('products', 'order_items.product_id', '=', 'products.id')
                ->where('orders.business_id', $business->id)
                ->where('orders.status', 'completed')
                ->when($from, fn ($q) => $q->whereDate('orders.created_at', '>=', $from))
                ->when($to,   fn ($q) => $q->whereDate('orders.created_at', '<=', $to))
                ->select(
                    DB::raw('COALESCE(products.name, CONCAT("Producto #", order_items.product_id)) as product_name'),
                    DB::raw('SUM(order_items.quantity) as qty'),
                    DB::raw('SUM(order_items.total_price) as total')
                )
                ->groupBy('order_items.product_id', 'products.name')
                ->orderByDesc('qty')
                ->limit(5)
                ->get();
    
            // ---------------------------
            // 5) Devolver vista
            // ---------------------------
            return view('business.reports.sales', compact(
                'business',
                'from',
                'to',
                'totalSales',
                'totalOrders',
                'avgTicket',
                'bySource',
                'byType',
                'topProducts'
            ));
        } catch (\Throwable $e) {
            report($e);
    
            // Redirige atrás con un mensaje bonito (tus toasts lo mostrarán)
            return back()
                ->withInput()
                ->with('error', 'No se pudieron cargar los reportes en este momento.');
        }
    }
    
}
