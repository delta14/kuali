<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Helper: obtener el comercio actual del admin logueado.
     */
    protected function currentBusiness(): Business
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Si algún día agregas current_business_id al usuario, lo usamos:
        if ($user->current_business_id ?? false) {
            $business = Business::find($user->current_business_id);
        } else {
            // Primer negocio al que esté vinculado
            $business = $user->businesses()->first();
        }

        if (!$business) {
            abort(403, 'No tienes un comercio asignado.');
        }

        return $business;
    }

    public function index()
    {
        $business = $this->currentBusiness();

        $categories = Category::where('business_id', $business->id)
            ->orderBy('position')          // 👈 usamos position
            ->paginate(10);

        return view('business.categories.index', [
            'business'   => $business,
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $business = $this->currentBusiness();

        $data = $request->validate([
            'name'        => ['required', 'string', 'max:100', 'unique:categories,name,' . ($category->id ?? 'NULL') . ',id,business_id,' . $business->id],
            'description' => ['nullable', 'string', 'max:255'],
            'is_active'   => ['nullable', 'boolean'],
        ]);

        $data['business_id'] = $business->id;
        $data['is_active']   = $request->has('is_active') ? 1 : 0;

        // 👇 calculamos la siguiente posición
        $maxPosition = Category::where('business_id', $business->id)->max('position') ?? 0;
        $data['position'] = $maxPosition + 1;

        Category::create($data);

        return redirect()
            ->route('categories.index')
            ->with('success', 'Categoría creada correctamente.');
    }

    public function update(Request $request, Category $category)
    {
        $business = $this->currentBusiness();

        // por seguridad: que la categoría sea de este negocio
        if ($category->business_id !== $business->id) {
            abort(403, 'No puedes editar esta categoría.');
        }

        $data = $request->validate([
            'name'        => ['required', 'string', 'max:100', 'unique:categories,name,' . ($category->id ?? 'NULL') . ',id,business_id,' . $business->id],
            'description' => ['nullable', 'string', 'max:255'],
            'is_active'   => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        $category->update($data);

        return redirect()
            ->route('categories.index')
            ->with('success', 'Categoría actualizada correctamente.');
    }

    public function destroy(Category $category)
    {
        $business = $this->currentBusiness();

        if ($category->business_id !== $business->id) {
            abort(403, 'No puedes eliminar esta categoría.');
        }

        $name = $category->name;
        $category->delete();

        return redirect()
            ->route('categories.index')
            ->with('success', "La categoría \"{$name}\" fue eliminada.");
    }
}
