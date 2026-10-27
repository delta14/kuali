<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    protected function currentBusiness(Request $request): ?Business
    {
        return $request->user()->businesses()
            ->wherePivot('is_active', true)
            ->orderBy('business_user.created_at')
            ->first();
    }

    public function edit(Request $request)
    {
        $user = $request->user();

        if ($user->is_super_admin) {
            return redirect()->route('super.dashboard');
        }

        $business = $this->currentBusiness($request);

        if (!$business) {
            return redirect()->route('dashboard');
        }

        return view('business.settings.edit', [
            'business' => $business,
        ]);
    }

    public function update(Request $request)
    {
        $business = $this->currentBusiness($request);

        abort_unless($business, 404);

        $data = $request->validate([
            'name'          => ['required', 'string', 'max:190'],
            'email_contact' => ['nullable', 'email', 'max:190'],
            'phone'         => ['nullable', 'string', 'max:50'],
            'address'       => ['nullable', 'string', 'max:255'],
            'logo'          => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'flyer_bg'      => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        if ($request->hasFile('logo')) {
            if ($business->logo_path) {
                Storage::disk('public')->delete($business->logo_path);
            }
            $data['logo_path'] = $request->file('logo')->store('businesses', 'public');
        }
        unset($data['logo']);

        if ($request->hasFile('flyer_bg')) {
            if ($business->flyer_bg_path) {
                Storage::disk('public')->delete($business->flyer_bg_path);
            }
            $data['flyer_bg_path'] = $request->file('flyer_bg')->store('businesses/flyer-bg', 'public');
        }
        unset($data['flyer_bg']);

        $business->update($data);

        return redirect()
            ->route('settings.edit')
            ->with('success', 'Los datos de tu negocio se actualizaron correctamente.');
    }
}
