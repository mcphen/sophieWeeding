<?php

namespace App\Http\Controllers;

use App\Models\Actualite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ActualiteController extends Controller
{
    public function index()
    {
        $actualites = Actualite::orderBy('published_at', 'desc')->paginate(10);
        return Inertia::render('Admin/Actualites/ActualiteIndex', [
            'actualites' => $actualites,
            'flash' => ['success' => session('success')],
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Actualites/ActualitesCreate');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'image'        => 'nullable|image|max:2048',
            'published_at' => 'required|date',
        ]);
        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('actualites', 'public');
        }
        Actualite::create($data);
        return redirect()->route('admin.actualites.index')
            ->with('success', 'Actualité créée.');
    }

    public function edit(Actualite $actualite)
    {
        return Inertia::render('Admin/Actualites/ActualitesEdit', [
            'actualite' => $actualite,
        ]);
    }

    public function update(Request $request, Actualite $actualite)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'image'        => 'nullable|image|max:2048',
            'published_at' => 'required|date',
        ]);
        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($actualite->image_path);
            $data['image_path'] = $request->file('image')->store('actualites', 'public');
        }
        $actualite->update($data);
        return redirect()->route('admin.actualites.index')
            ->with('success', 'Actualité mise à jour.');
    }

    public function destroy(Actualite $actualite)
    {
        Storage::disk('public')->delete($actualite->image_path);
        $actualite->delete();
        return redirect()->route('admin.actualites.index')
            ->with('success', 'Actualité supprimée.');
    }

    /**
     * Get the latest published actualites
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function latest()
    {
        $actualites = Actualite::query()
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->orderBy('published_at', 'desc')
            ->take(3)
            ->get();

        return response()->json($actualites);
    }
}
