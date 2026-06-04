<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Middleware\EnsureSuperAdmin;

class SetCurrentBusiness
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user) {

            // SUPERADMIN
            $isSuperAdmin = (bool) ($user->is_super_admin ?? false);

            if ($isSuperAdmin) {

                // Súper admin solo usa negocio si está en sesión (impersonación)
                $businessId = session('current_business_id');

                if ($businessId) {
                    app()->instance('currentBusinessId', (int) $businessId);
                }

            } else {

                // ADMIN O STAFF DE COMERCIO
                $businessId = session('current_business_id');

                if (! $businessId) {

                    // Relación correcta → belongsToMany
                    // Tomamos el PRIMER negocio del usuario (si tiene)
                    $businessId = $user->businesses()
                        ->select('businesses.id')
                        ->value('businesses.id');

                    if ($businessId) {
                        session(['current_business_id' => $businessId]);
                    }
                }

                // Ya debemos tener un negocio
                if ($businessId) {
                    app()->instance('currentBusinessId', (int) $businessId);
                } else {
                    // Aquí podrías redirigir a una pantalla de error
                    // return redirect()->route('no-business');
                }
            }

            // Compartimos a las vistas si existe
            if (app()->bound('currentBusinessId')) {
                view()->share('currentBusinessId', app('currentBusinessId'));
            }
        }

        return $next($request);
    }
}
