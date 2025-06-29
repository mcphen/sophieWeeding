<?php

namespace App\Http\Controllers;

use App\Models\About;
use App\Helpers\StorageHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class AboutController extends Controller
{
    // Affiche la page About via Inertia
    public function index()
    {
        $about = About::first();
        return Inertia::render('About', [
            'about' => $about
        ]);
    }

    // Affiche la page d'édition en back-office
    public function edit()
    {
        $about = About::first();
        return Inertia::render('Admin/About/Edit', [
            'about' => $about
        ]);
    }

    // Mise à jour du contenu depuis le BO
    public function update(Request $request)
    {
        $data = $request->validate([
            'content' => 'required|string',
            'image' => 'nullable|image|max:2048',
        ]);

        $about = About::first();

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($about && $about->image_path) {
                Storage::disk('public')->delete($about->image_path);
            }

            // Store new image
            $data['image_path'] = $request->file('image')->store('about', 'public');
        }

        About::updateOrCreate([], $data);

        $about = About::first();
        return Inertia::render('Admin/About/Edit', [
            'about' => $about,
            'flash' => ['success' => 'Contenu mis à jour.'],
        ]);
    }
}
