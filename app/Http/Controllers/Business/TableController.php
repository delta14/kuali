<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TableController extends Controller
{
    /**
     * Negocio actual del usuario usando business_user.
     */
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

    public function index(Request $request)
    {
        $business = $this->currentBusiness($request);

        $tables = Table::forBusiness($business->id)
            ->orderBy('name')
            ->get();

        return view('business.tables.index', compact('business', 'tables'));
    }

    public function store(Request $request)
    {
        // $request->user()->currentBusiness devuelve un Business (su PK es
        // "id", no "business_id"); usamos el helper ya correcto de este
        // controller para no depender de que el trait de negocio rellene
        // business_id en silencio al guardar.
        $businessId = $this->currentBusiness($request)->id;

        $table = new Table();
        $table->business_id = $businessId;
        $table->name        = $request->name;
        $table->code        = $this->uniqueCodeFor($businessId, $request->name);
        $table->capacity    = $request->capacity;
        $table->is_active   = $request->boolean('is_active', true);
        $table->qr_token    = Str::uuid(); // token único
        $table->save();

        // === Generar QR en SVG (no requiere Imagick) ===
        $qrUrl  = route('public.menu.table', $table->qr_token);
        $qrPath = "qrs/table-{$table->id}.svg";

        // Crear carpeta si no existe (storage/app/public/qrs)
        if (! Storage::disk('public')->exists('qrs')) {
            Storage::disk('public')->makeDirectory('qrs');
        }

        // Generar contenido SVG y guardarlo en el disco 'public'
        $svg = QrCode::format('svg')
            ->size(300)   // 300px es suficiente para impresión en mesa
            ->margin(1)
            ->generate($qrUrl);

        Storage::disk('public')->put($qrPath, $svg);

        // Guardamos la ruta relativa al disco public
        $table->qr_path = $qrPath;
        $table->save();

        return redirect()->route('tables.index')
            ->with('success', 'Mesa creada correctamente.');
    }

    /**
     * "code" sólo es único por negocio (p.ej. dos negocios distintos pueden
     * tener ambos una "Mesa 1"), así que generamos el slug base y le
     * agregamos un sufijo -2, -3... si ya existe otra mesa con ese código
     * dentro de este mismo negocio.
     */
    protected function uniqueCodeFor(int $businessId, string $name): string
    {
        $base = Str::slug($name);
        $code = $base;
        $i = 2;

        while (Table::where('business_id', $businessId)->where('code', $code)->exists()) {
            $code = "{$base}-{$i}";
            $i++;
        }

        return $code;
    }

    public function update(Request $request, Table $table)
    {
        $business = $this->currentBusiness($request);
        abort_unless($table->business_id === $business->id, 403);

        $data = $request->validate([
            'name'  => ['required', 'string', 'max:50'],
            'code'  => ['required', 'string', 'max:50', 'unique:tables,code,NULL,id,business_id,' . $business->id],
            'capacity'  => ['nullable', 'integer', 'min:1', 'max:999'],
            'is_active' => ['nullable'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        // Validar code único si viene y cambió
        if (! empty($data['code']) && $data['code'] !== $table->code) {
            $exists = Table::where('business_id', $business->id)
                ->where('code', $data['code'])
                ->where('id', '<>', $table->id)
                ->exists();

            if ($exists) {
                return back()
                    ->withErrors(['code' => 'Ya existe otra mesa con ese código.'])
                    ->withInput();
            }
        }

        $table->update($data);

        return redirect()
            ->route('tables.index')
            ->with('success', 'Mesa actualizada correctamente.');
    }

    public function destroy(Request $request, Table $table)
    {
        $business = $this->currentBusiness($request);
        abort_unless($table->business_id === $business->id, 403);

        $table->delete();

        return redirect()
            ->route('tables.index')
            ->with('success', 'Mesa eliminada.');
    }

    public function downloadQr(Request $request, Table $table)
    {
        // Validar acceso al negocio
        $business = $this->currentBusiness($request);
        abort_unless($table->business_id === $business->id, 403);

        if (! $table->qr_path || ! Storage::disk('public')->exists($table->qr_path)) {
            abort(404, 'QR no disponible para esta mesa.');
        }

        // Ahora la extensión es SVG
        $filename = 'qr-' . ($table->code ?: 'mesa-' . $table->id) . '.svg';

        return Storage::disk('public')->download($table->qr_path, $filename);
    }

    /**
     * Regenerar QR (nuevo token y nueva imagen).
     */
    public function regenerateQr(Request $request, Table $table)
    {
        $business = $this->currentBusiness($request);
        abort_unless($table->business_id === $business->id, 403);

        // Borrar imagen anterior si existe
        if ($table->qr_path && Storage::disk('public')->exists($table->qr_path)) {
            Storage::disk('public')->delete($table->qr_path);
        }

        // Nuevo token y nueva imagen
        $table->qr_token = Str::uuid();
        $table->save();

        $this->generateQrImage($table);

        return redirect()
            ->route('tables.index')
            ->with('success', 'Código QR regenerado correctamente.');
    }

    /**
     * Descargar el flyer como archivo HTML (simple, para luego imprimir/guardar como PDF).
     */
    public function downloadFlyer(Request $request, Table $table)
    {
        $business = $this->currentBusiness($request);
        abort_unless($table->business_id === $business->id, 403);

        $html = view('business.tables.flyer', compact('table', 'business'))->render();

        $filename = 'flyer-' . ($business->slug ?? Str::slug($business->name)) .
            '-' . ($table->code ?: 'mesa-' . $table->id) . '.html';

        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
    }

        /**
     * Helper para generar el QR en SVG y guardar la ruta en la mesa.
     * (sin usar Imagick)
     */
    protected function generateQrImage(Table $table): void
    {
        $qrUrl  = route('public.menu.table', $table->qr_token);
        $qrPath = "qrs/table-{$table->id}.svg";

        // Crear carpeta si no existe
        if (! Storage::disk('public')->exists('qrs')) {
            Storage::disk('public')->makeDirectory('qrs');
        }

        // Generar SVG (no usa Imagick)
        $svg = QrCode::format('svg')
            ->size(300)
            ->margin(1)
            ->generate($qrUrl);

        // Guardar en el disco public
        Storage::disk('public')->put($qrPath, $svg);

        // Actualizar ruta en la mesa
        $table->qr_path = $qrPath;
        $table->save();
    }


    public function flyer(Request $request, Table $table)
    {
        $business = $this->currentBusiness($request);
        abort_unless($table->business_id === $business->id, 403);

        return view('business.tables.flyer', compact('table', 'business'));
    }
}
