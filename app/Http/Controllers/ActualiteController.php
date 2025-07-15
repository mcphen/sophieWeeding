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

    public function createNew()
    {
        return Inertia::render('Admin/Actualites/ActualitesCreateWithBlocks');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'image'        => 'nullable|image|max:2048',
            'published_at' => 'required|date',
            'blocks'       => 'nullable|array',
            'blocks.*.type' => 'required|in:text,timeline,gallery,image,video,quote,faq,embed,file,alert',
            'blocks.*.content' => 'required',
            'blocks.*.position' => 'required|integer',
        ]);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('actualites', 'public');
        }

        // Create the article
        $actualite = Actualite::create($data);

        // Create blocks if they exist
        if ($request->has('blocks') && is_array($request->blocks)) {
            foreach ($request->blocks as $block) {
                $actualite->blocks()->create([
                    'type' => $block['type'],
                    'content' => $block['content'],
                    'position' => $block['position'],
                ]);
            }
        }

        return redirect()->route('admin.actualites.index')
            ->with('success', 'Actualité créée.');
    }

    public function edit(Actualite $actualite)
    {
        // Load the blocks relationship
        $actualite->load('blocks');

        return Inertia::render('Admin/Actualites/ActualitesEditWithBlocks', [
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
            'blocks'       => 'nullable|array',
            'blocks.*.type' => 'required|in:text,timeline,gallery,image,video,quote,faq,embed,file,alert',
            'blocks.*.content' => 'required',
            'blocks.*.position' => 'required|integer',
        ]);

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($actualite->image_path);
            $data['image_path'] = $request->file('image')->store('actualites', 'public');
        }

        $actualite->update($data);

        // Update blocks
        if ($request->has('blocks')) {
            // Delete existing blocks
            $actualite->blocks()->delete();

            // Create new blocks
            foreach ($request->blocks as $block) {
                $actualite->blocks()->create([
                    'type' => $block['type'],
                    'content' => $block['content'],
                    'position' => $block['position'],
                ]);
            }
        }

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

    /**
     * Get paginated published actualites with filters
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function paginated(Request $request)
    {
        $query = Actualite::query()
            ->with('blocks')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());

        // Search filter
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%");
            });
        }

        // Date filter
        if ($request->has('date') && $request->date) {
            $date = $request->date;

            if ($date === 'this-month') {
                $query->whereMonth('published_at', now()->month)
                    ->whereYear('published_at', now()->year);
            } elseif ($date === 'last-month') {
                $lastMonth = now()->subMonth();
                $query->whereMonth('published_at', $lastMonth->month)
                    ->whereYear('published_at', $lastMonth->year);
            } elseif (is_numeric($date)) {
                // Specific year
                $query->whereYear('published_at', $date);
            }
        }

        // Sorting
        if ($request->has('sort') && $request->sort) {
            $sort = $request->sort;

            if ($sort === 'oldest') {
                $query->orderBy('published_at', 'asc');
            } elseif ($sort === 'title-asc') {
                $query->orderBy('title', 'asc');
            } elseif ($sort === 'title-desc') {
                $query->orderBy('title', 'desc');
            }
        } else {
            // Default: newest first
            $query->orderBy('published_at', 'desc');
        }

        // Pagination
        $actualites = $query->paginate(10)->withQueryString();

        // Add image_url to each actualite
        $actualites->getCollection()->transform(function ($actualite) {
            if ($actualite->image_path) {
                $actualite->image_url = url('storage/' . $actualite->image_path);
            }
            return $actualite;
        });

        return response()->json($actualites);
    }
}
