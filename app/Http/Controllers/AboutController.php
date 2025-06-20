<?php

namespace App\Http\Controllers;

use App\Models\About;
use Illuminate\Http\Request;
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
        ]);

        About::updateOrCreate([], ['content' => $data['content']]);

        $about = About::first();
        return Inertia::render('Admin/About/Edit', [
            'about' => $about,
            'flash' => ['success' => 'Contenu mis à jour.'],
        ]);
    }
}
