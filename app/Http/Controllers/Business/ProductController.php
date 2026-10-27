<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /**
     * Helper: negocio actual del usuario admin.
     */
    protected function currentBusiness(): Business
    {
        $user = Auth::user();

        $business = $user->businesses()
            ->wherePivot('is_active', true)
            ->orderBy('business_user.created_at')
            ->first();

        if (!$business) {
            abort(403, 'No tienes un comercio asignado o activo.');
        }

        return $business;
    }

    public function index(Request $request)
    {
        $business = $this->currentBusiness();

        $products = Product::with('category', 'variants')
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

    /**
     * Reglas comunes de variantes (nombre + precio), usadas en store/update.
     * El precio base del producto es opcional si se manda al menos una
     * variante con nombre y precio válidos (ej. Chica/Mediana/Grande).
     */
    protected function validateWithVariants(Request $request, Business $business): array
    {
        $validator = Validator::make($request->all(), [
            'name'            => ['required', 'string', 'max:190'],
            'category_id'     => ['required', Rule::exists('categories', 'id')->where('business_id', $business->id)],
            'price'           => ['nullable', 'numeric', 'min:0'],
            'description'     => ['nullable', 'string', 'max:500'],
            'image'           => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_active'       => ['nullable'],
            'variants'        => ['nullable', 'array'],
            'variants.*.name' => ['nullable', 'string', 'max:60'],
            'variants.*.price' => ['nullable', 'numeric', 'min:0'],
        ]);

        $validator->after(function ($validator) use ($request) {
            if ($this->validVariantRows($request)->isEmpty() && !$request->filled('price')) {
                $validator->errors()->add(
                    'price',
                    'Ingresa un precio único o agrega al menos una variante con nombre y precio (ej. Chica/Mediana/Grande).'
                );
            }
        });

        return $validator->validate();
    }

    protected function validVariantRows(Request $request)
    {
        return collect($request->input('variants', []))
            ->filter(fn ($v) => filled($v['name'] ?? null) && ($v['price'] ?? '') !== '')
            ->map(fn ($v) => [
                'name'  => trim($v['name']),
                'price' => (float) $v['price'],
            ])
            ->values();
    }

    /**
     * Reemplaza por completo las variantes del producto con las filas
     * recibidas del formulario. Si hay variantes, el precio base del
     * producto se fija al precio mínimo de ellas (para que donde todavía
     * se lea $product->price directamente se muestre algo representativo).
     */
    protected function syncVariants(Product $product, Business $business, $variantRows): void
    {
        $product->variants()->delete();

        foreach ($variantRows as $i => $row) {
            $product->variants()->create([
                'business_id' => $business->id,
                'name'        => $row['name'],
                'price'       => $row['price'],
                'position'    => $i,
                'is_active'   => true,
            ]);
        }

        if ($variantRows->isNotEmpty()) {
            $product->update(['price' => $variantRows->min('price')]);
        }
    }

    public function store(Request $request)
    {
        $business = $this->currentBusiness();

        $data = $this->validateWithVariants($request, $business);
        $variantRows = $this->validVariantRows($request);

        $data['business_id'] = $business->id;
        $data['is_active']   = $request->has('is_active') ? 1 : 0;
        $data['price']       = $data['price'] ?? 0;

        // Subir imagen si viene
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $data['image_path'] = $path;
        }

        unset($data['image'], $data['variants']); // no son columnas de products

        $product = Product::create($data);

        $this->syncVariants($product, $business, $variantRows);

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

        $data = $this->validateWithVariants($request, $business);
        $variantRows = $this->validVariantRows($request);

        $data['is_active'] = $request->has('is_active') ? 1 : 0;
        $data['price']     = $data['price'] ?? 0;

        // ¿Hay nueva imagen?
        if ($request->hasFile('image')) {
            // borrar la anterior si existe
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }

            $path = $request->file('image')->store('products', 'public');
            $data['image_path'] = $path;
        }

        unset($data['image'], $data['variants']);

        $product->update($data);

        $this->syncVariants($product, $business, $variantRows);

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
