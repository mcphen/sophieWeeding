<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class PartnerController extends Controller
{
    public function index()
    {
        $partners = Partner::orderBy('created_at', 'desc')->paginate(10);
        return Inertia::render('Admin/Partners/PartnersIndex', [
            'partners' => $partners,
            'flash' => ['success' => session('success')]
        ]);
    }

    public function getListeDatas(){
        $partners = Partner::query()->get();
        return response()->json($partners);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'website_url' => 'nullable|url',
            'logo'        => 'required|image|max:2048',
        ]);

        $data['logo_path'] = $request->file('logo')->store('partners', 'public');
        Partner::create($data);

        return redirect()->route('admin.partners.index')
            ->with('success', 'Partenaire créé.');
    }

    public function update(Request $request, Partner $partner)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'website_url' => 'nullable|url',
            'logo'        => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            Storage::disk('public')->delete($partner->logo_path);
            $data['logo_path'] = $request->file('logo')->store('partners', 'public');
        }

        $partner->update($data);

        return redirect()->route('admin.partners.index')
            ->with('success', 'Partenaire mis à jour.');
    }

    public function destroy(Partner $partner)
    {
        Storage::disk('public')->delete($partner->logo_path);
        $partner->delete();

        return redirect()->route('admin.partners.index')
            ->with('success', 'Partenaire supprimé.');
    }
}
