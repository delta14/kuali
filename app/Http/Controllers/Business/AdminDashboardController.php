<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Business;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Si es súper admin, mándalo a su propio dashboard
        if ($user->is_super_admin) {
            return redirect()->route('super.dashboard');
        }
        // Por ahora asumimos 1 negocio por admin
        $business = $user->businesses()->first();

        if (!$business) {
            // Si por alguna razón no tiene negocio asignado
            return view('business.dashboard_empty');
        }

        // Métricas reales
        $categoriesCount = $business->categories()->count();
        $productsCount   = $business->products()->count();
        $tablesCount     = $business->tables()->count();
        
        // Obtenemos los pedidos scoped al negocio actual
        $ordersQuery = \App\Models\Order::where('business_id', $business->id);
        
        $ordersCount = (clone $ordersQuery)->count();
        $pendingOrdersCount = (clone $ordersQuery)->whereIn('status', ['pending', 'in_progress', 'ready'])->count();
        $totalSales = (clone $ordersQuery)->whereIn('status', ['completed', 'delivered'])->sum('total');
        
        $recentOrders = (clone $ordersQuery)
            ->with(['table'])
            ->latest('placed_at')
            ->take(5)
            ->get();

        $popularDishes = \App\Models\OrderItem::where('business_id', $business->id)
            ->select('product_id', \DB::raw('SUM(quantity) as qty_sum'), \DB::raw('COUNT(*) as order_count'), \DB::raw('SUM(total_price) as total_sales'))
            ->groupBy('product_id')
            ->with('product')
            ->orderByDesc('qty_sum')
            ->take(5)
            ->get();

        return view('business.dashboard', [
            'business'           => $business,
            'categoriesCount'    => $categoriesCount,
            'productsCount'      => $productsCount,
            'tablesCount'        => $tablesCount,
            'ordersCount'        => $ordersCount,
            'pendingOrdersCount' => $pendingOrdersCount,
            'totalSales'         => $totalSales,
            'recentOrders'       => $recentOrders,
            'popularDishes'      => $popularDishes,
        ]);
    }
}
