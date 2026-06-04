<?php

namespace App\Http\Controllers\Super;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;


class BusinessController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->get('q');

        $businesses = Business::withCount('admins')       // para saber cuántos admins tiene
            ->with(['admins' => function ($q2) {          // cargamos los admins ya ordenados
                $q2->orderBy('name');
            }])
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('slug', 'like', "%{$q}%")
                        ->orWhere('email_contact', 'like', "%{$q}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('super.businesses.index', [
            'businesses' => $businesses,
            'q'          => $q,
        ]);
    }

    // Alta tradicional no se usa, todo va por modal
    public function create()
    {
        abort(404);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => ['required', 'string', 'max:190'],
            'slug'          => ['nullable', 'string', 'max:190', 'unique:businesses,slug'],
            'email_contact' => ['nullable', 'string', 'max:190'],
            'phone'         => ['nullable', 'string', 'max:50'],
            'address'       => ['nullable', 'string', 'max:255'],
            'plan'          => ['nullable', 'string', 'max:50'],
            'is_active'     => ['nullable'],
        ]);

        // si no mandan slug, lo generamos del nombre
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        // checkbox
        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        Business::create($data);

        return redirect()
            ->route('super.businesses.index')
            ->with('success', 'Comercio creado correctamente.');
    }

    // Edit tradicional no se usa, todo va por modal
    public function edit(Business $business)
    {
        abort(404);
    }

    public function update(Request $request, Business $business)
    {
        $data = $request->validate([
            'name'          => ['required', 'string', 'max:190'],
            'slug'          => ['nullable', 'string', 'max:190', 'unique:businesses,slug,' . $business->id],
            'email_contact' => ['nullable', 'string', 'max:190'],
            'phone'         => ['nullable', 'string', 'max:50'],
            'address'       => ['nullable', 'string', 'max:255'],
            'plan'          => ['nullable', 'string', 'max:50'],
            'is_active'     => ['nullable'],
        ]);

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $data['is_active'] = $request->boolean('is_active');

        $business->update($data);

        return redirect()
            ->route('super.businesses.index')
            ->with('success', 'Comercio actualizado correctamente.');
    }

    public function destroy(Business $business)
    {
        $name = $business->name;

        $business->delete();

        return redirect()
            ->route('super.businesses.index')
            ->with('success', "El comercio \"{$name}\" fue eliminado.");
    }

    /**
     * TAB 2 · crear/asignar admin (por ahora stub para que no truene).
     */
    public function assignAdmin(Request $request)
    {
        // 1. Validar datos del formulario
        $data = $request->validate([
            'business_id'    => ['required', 'exists:businesses,id'],
            'admin_name'     => ['nullable', 'string', 'max:190'],
            'admin_email'    => ['required', 'email', 'max:190'],
            'admin_password' => ['nullable', 'string', 'min:8'],
        ]);

        // 2. Buscar el negocio
        $business = Business::findOrFail($data['business_id']);

        // 3. Buscar usuario por email (si ya existe, lo reutilizamos)
        $user = User::where('email', $data['admin_email'])->first();

        $isNewUser = false;

        if (!$user) {
            // Crear usuario nuevo
            if (empty($data['admin_password'])) {
                // si es nuevo usuario, forzamos contraseña
                return back()
                    ->withInput()
                    ->with('error', 'Para crear un usuario nuevo debes definir una contraseña.')
                    ->withErrors(['admin_password' => 'Ingresa una contraseña para el nuevo usuario.']);
            }

            $user = new User();
            $user->name  = $data['admin_name'] ?: 'Administrador de ' . $business->name;
            $user->email = $data['admin_email'];
            $user->password = Hash::make($data['admin_password']);
            $user->is_super_admin = false;
            $user->save();

            $isNewUser = true;
        } else {
            // Usuario ya existe: opcionalmente actualizamos nombre/contraseña
            if (!empty($data['admin_name'])) {
                $user->name = $data['admin_name'];
            }
            if (!empty($data['admin_password'])) {
                $user->password = Hash::make($data['admin_password']);
            }
            $user->save();
        }

        // 4. Asignar usuario al negocio en la tabla pivot
        //    Ajusta este role_id según tu catálogo de roles
        $adminRoleId = 2; // por ejemplo: 2 = "admin de comercio"

        $business->users()->syncWithoutDetaching([
            $user->id => ['role_id' => $adminRoleId],
        ]);

        // 5. (Opcional) Si quieres que haya SOLO 1 admin por negocio, podrías
        //    limpiar otros admins aquí, pero por ahora lo dejamos así.

        $msg = $isNewUser
            ? 'Se creó el usuario admin y se asignó al comercio.'
            : 'Se asignó el usuario existente como admin de este comercio.';

        return redirect()
            ->route('super.businesses.index')
            ->with('success', $msg);
    }


    public function dashboard()
    {
        // KPIs principales
        $totalBusinesses   = Business::count();
        $activeBusinesses  = Business::where('is_active', true)->count();
        $businessNoAdmin   = Business::doesntHave('admins')->count();

        // Distintos usuarios que son admin (role_id = 2 en tabla pivot)
        $totalAdmins = DB::table('business_user')
            ->where('role_id', 2)
            ->distinct('user_id')
            ->count('user_id');

        // Comercios recientes con admins cargados
        $recentBusinesses = Business::withCount('admins')
            ->with(['admins' => function ($q) {
                $q->orderBy('name');
            }])
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        return view('super.dashboard', [
            'totalBusinesses'   => $totalBusinesses,
            'activeBusinesses'  => $activeBusinesses,
            'businessNoAdmin'   => $businessNoAdmin,
            'totalAdmins'       => $totalAdmins,
            'recentBusinesses'  => $recentBusinesses,
        ]);
    }


}
