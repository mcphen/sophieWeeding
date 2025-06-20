<?php

namespace App\Http\Controllers;

use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class TestimonialController extends Controller
{
    public function index()
    {
        $testimonials = Testimonial::orderBy('created_at', 'desc')->paginate(10);
        return Inertia::render('Admin/Testimonials/TestimonialIndex', [
            'testimonials' => $testimonials,
            'flash'        => ['success' => session('success')],
        ]);
    }

    public function getListeDatas(){
        $testimonials = Testimonial::query()->orderBy('created_at','desc')->get();
        return response()->json($testimonials);
    }

    public function create()
    {
        return Inertia::render('Admin/Testimonials/TestimonialCreate');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'author_name'  => 'required|string|max:255',
            'author_title' => 'nullable|string|max:255',
            'content'      => 'required|string',
            'image'        => 'nullable|image|max:2048',
        ]);
        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('testimonials', 'public');
        }
        Testimonial::create($data);
        return redirect()->route('admin.testimonials.index')
            ->with('success', 'Témoignage créé.');
    }

    public function edit(Testimonial $testimonial)
    {
        return Inertia::render('Admin/Testimonials/TestimonialEdit', [
            'testimonial' => $testimonial,
        ]);
    }

    public function update(Request $request, Testimonial $testimonial)
    {
        $data = $request->validate([
            'author_name'  => 'required|string|max:255',
            'author_title' => 'nullable|string|max:255',
            'content'      => 'required|string',
            'image'        => 'nullable|image|max:2048',
        ]);
        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($testimonial->image_path);
            $data['image_path'] = $request->file('image')->store('testimonials', 'public');
        }
        $testimonial->update($data);
        return redirect()->route('admin.testimonials.index')
            ->with('success', 'Témoignage mis à jour.');
    }

    public function destroy(Testimonial $testimonial)
    {
        Storage::disk('public')->delete($testimonial->image_path);
        $testimonial->delete();
        return redirect()->route('admin.testimonials.index')
            ->with('success', 'Témoignage supprimé.');
    }
}
