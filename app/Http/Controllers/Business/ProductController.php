<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Helper: negocio actual del usuario admin.
     */
    protected function currentBusiness(): Business
    {
        $user = Auth::user();

        // Si en tu User tienes algo como current_business_id
        if ($user->current_business_id) {
            $business = Business::find($user->current_business_id);
        } else {
            // Primer negocio de la relación many-to-many
            $business = $user->businesses()->first();
        }

        if (!$business) {
            abort(403, 'No tienes un comercio asignado.');
        }

        return $business;
    }

    public function index(Request $request)
    {
        $business = $this->currentBusiness();

        $products = Product::with('category')
            ->where('business_id', $business->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $categories = Category::where('business_id', $business->id)
            ->orderBy('position')
            ->get();

        return view('business.products.index', [
            'business'   => $business,
            'products'   => $products,
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $business = $this->currentBusiness();

        $data = $request->validate([
            'name'        => ['required', 'string', 'max:190'],
            'category_id' => ['required', 'exists:categories,id'],
            'price'       => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:500'],
            'image'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_active'   => ['nullable'],
        ]);

        $data['business_id'] = $business->id;
        $data['is_active']   = $request->has('is_active') ? 1 : 0;

        // Subir imagen si viene
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $data['image_path'] = $path;
        }

        unset($data['image']); // no existe columna image

        Product::create($data);

        return redirect()
            ->route('products.index')
            ->with('success', 'Producto creado correctamente.');
    }

    public function update(Request $request, Product $product)
    {
        $business = $this->currentBusiness();

        // Seguridad: el producto debe pertenecer al negocio actual
        if ($product->business_id !== $business->id) {
            abort(403, 'No puedes editar este producto.');
        }

        $data = $request->validate([
            'name'        => ['required', 'string', 'max:190'],
            'category_id' => ['required', 'exists:categories,id'],
            'price'       => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:500'],
            'image'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_active'   => ['nullable'],
        ]);

        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        // ¿Hay nueva imagen?
        if ($request->hasFile('image')) {
            // borrar la anterior si existe
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }

            $path = $request->file('image')->store('products', 'public');
            $data['image_path'] = $path;
        }

        unset($data['image']);

        $product->update($data);

        return redirect()
            ->route('products.index')
            ->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(Product $product)
    {
        $business = $this->currentBusiness();

        if ($product->business_id !== $business->id) {
            abort(403, 'No puedes eliminar este producto.');
        }

        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }

        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('success', 'Producto eliminado.');
    }
}
