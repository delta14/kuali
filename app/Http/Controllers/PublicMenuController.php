<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Table;
use Illuminate\Http\Request;

class PublicMenuController extends Controller
{
    // Splash por slug: /m/{slug}
    public function splash(string $slug)
    {
        $business = Business::where('slug', $slug)->firstOrFail();

        return view('public.menu.splash', [
            'business' => $business,
        ]);
    }

    /**
     * Menú por slug: /m/{slug}/menu?table=TOKEN
     */
    public function menu(Request $request, string $slug)
    {
        $business = Business::where('slug', $slug)->firstOrFail();

        $categories = $business->categories()
            ->with(['products' => function ($q) {
                // ajusta a tus columnas reales
                $q->where('is_active', true)->orderBy('sort_order');
            }])
            ->get();

        $table      = null;
        $tableLabel = null;

        if ($request->filled('table')) {
            $table = Table::where('business_id', $business->id)
                ->where('token', $request->string('table'))
                ->first();

            if ($table) {
                $tableLabel = $table->label
                    ?? $table->name
                    ?? ('Mesa ' . $table->id);
            }
        }

        return view('public.menu.index', [
            'business'   => $business,
            'categories' => $categories,
            'table'      => $table,
            'tableLabel' => $tableLabel,
        ]);
    }

    /**
     * Menú por QR de mesa: /qr/{token}
     */
    public function showByTable(string $token)
    {
        $table = Table::where('qr_token', $token)
            ->with('business')
            ->firstOrFail();

        $business = $table->business;

        $categories = $business->categories()
            ->with(['products' => function ($q) {
                $q->where('is_active', true)->orderBy('category_id');
            }])
            ->get();

        $tableLabel = $table->label
            ?? $table->name
            ?? ('Mesa ' . $table->id);

        return view('public.menu.index', [
            'business'   => $business,
            'categories' => $categories,
            'table'      => $table,
            'tableLabel' => $tableLabel,
        ]);
    }
}
