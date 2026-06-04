<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicOrderController;
use App\Http\Controllers\PublicMenuController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Super\BusinessController;
use App\Http\Controllers\Business\AdminDashboardController;
use App\Http\Controllers\Business\CategoryController;
use App\Http\Controllers\Business\ProductController;
use App\Http\Controllers\Business\TableController;
use App\Http\Controllers\Business\OrderController;
use App\Http\Controllers\Business\ReportController;

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Rutas públicas del menú (QR)
|--------------------------------------------------------------------------
*/

// Splash por slug del negocio: /m/{slug}
Route::get('/m/{slug}', [PublicMenuController::class, 'splash'])
    ->name('public.menu.splash');

// Menú por slug: /m/{slug}/menu?table=CODIGO
Route::get('/m/{slug}/menu', [PublicMenuController::class, 'menu'])
    ->name('public.menu.show');

// Menú por QR de mesa: /qr/{token}
Route::get('/qr/{token}', [PublicMenuController::class, 'showByTable'])
    ->name('public.menu.table');

// Descargar PNG del QR de la mesa
Route::get('tables/{table}/qr/download', [TableController::class, 'downloadQr'])
    ->name('tables.qr.download');

// Ver flyer imprimible de la mesa
Route::get('tables/{table}/flyer', [TableController::class, 'flyer'])
    ->name('tables.flyer');

// Guardar pedido desde menú público
Route::post('/m/orders', [PublicOrderController::class, 'store'])
    ->name('public.orders.store');


// Rutas para usuarios autenticados (admin de comercio, etc.)
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', [AdminDashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::resource('products', ProductController::class)->except(['show']);
    Route::resource('tables', TableController::class)->except(['show']);

    // Acciones adicionales de mesas (QR / flyer)
    Route::get('tables/{table}/download-qr', [TableController::class, 'downloadQr'])
        ->name('tables.download-qr');

    Route::get('tables/{table}/flyer', [TableController::class, 'flyer'])
        ->name('tables.flyer');

    Route::get('tables/{table}/flyer-download', [TableController::class, 'downloadFlyer'])
        ->name('tables.flyer-download');

    Route::post('tables/{table}/regenerate-qr', [TableController::class, 'regenerateQr'])
        ->name('tables.regenerate-qr');

    /*
    |--------------------------------------------------------------------------
    | Pedidos (panel del comercio)
    |--------------------------------------------------------------------------
    */

    // Lista de pedidos
    Route::get('/orders', [OrderController::class, 'index'])
        ->name('orders.index');

    // Checar nuevos pedidos (real-time)
    Route::get('/orders/check-new', [OrderController::class, 'checkNew'])
        ->name('orders.check-new');

    // ===== Pantalla POS (tomar pedido en mostrador) =====
    Route::get('/orders/pos', [OrderController::class, 'createPos'])
        ->name('orders.pos');

    Route::post('/orders/pos', [OrderController::class, 'storePos'])
        ->name('orders.pos.store');

    // Detalle de pedido
    Route::get('/orders/{order}', [OrderController::class, 'show'])
        ->name('orders.show')
        ->whereNumber('order');

    // Actualizar estado (no POS, lo que ya tenías)
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])
        ->name('orders.update-status')
        ->whereNumber('order');

    // Marcar como completado (versión vieja por PATCH, la dejamos igual)
    Route::patch('/orders/{order}/complete', [OrderController::class, 'markAsCompleted'])
        ->name('orders.mark-completed')
        ->whereNumber('order');

    // Completar pedido (versión nueva que usa el JS del panel)
    Route::post('/orders/{order}/complete', [OrderController::class, 'complete'])
        ->name('orders.complete')
        ->whereNumber('order');
        
    // ===== Reportes (ventas) =====
    Route::get('/reports/sales', [ReportController::class, 'sales'])
        ->name('reports.sales');
});


// Rutas SÚPER ADMIN
Route::middleware(['auth', 'superadmin'])
    ->prefix('super')
    ->name('super.')
    ->group(function () {

        // DASHBOARD SÚPER ADMIN
        Route::get('/dashboard', [BusinessController::class, 'dashboard'])
            ->name('dashboard');

        // CRUD de comercios
        Route::resource('businesses', BusinessController::class)
            ->except(['show']);

        // TAB 2: crear / asignar admin
        Route::post('businesses/assign-admin', [BusinessController::class, 'assignAdmin'])
            ->name('businesses.assign-admin');
    });

require __DIR__.'/auth.php';
