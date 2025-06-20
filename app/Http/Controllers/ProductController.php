<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ProductController extends Controller
{
    // Liste des produits
    public function index()
    {
        $products = Product::orderBy('created_at', 'desc')->paginate(10);
        return Inertia::render('Admin/Products/ProductIndex', [
            'products' => $products,
        ]);
    }

    // Formulaire de création
    public function create()
    {
        return Inertia::render('Admin/Products/ProductCreate');
    }

    // Enregistrement d'un nouveau produit
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|max:2048',
            'price'       => 'required|numeric|min:0',
        ]);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('products', 'public');
        }

        Product::create($data);

        return redirect()->route('admin.products.index')
            ->with('success', 'Produit créé.');
    }

    // Formulaire d'édition
    public function edit(Product $product)
    {
        return Inertia::render('Admin/Products/ProductEdit', [
            'product' => $product,
        ]);
    }

    // Mise à jour
    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|max:2048',
            'price'       => 'required|numeric|min:0',
        ]);

        if ($request->hasFile('image')) {
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            $data['image_path'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return redirect()->route('admin.products.index')
            ->with('success', 'Produit mis à jour.');
    }

    // Suppression
    public function destroy(Product $product)
    {
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Produit supprimé.');
    }

    /**
     * Get products for front-end display
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProducts()
    {
        $products = Product::select('id', 'title', 'description', 'image_path', 'price')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'title' => $product->title,
                    'description' => $product->description,
                    'image_url' => $product->image_path ? '/storage/' . $product->image_path : null,
                    'price' => $product->price,
                ];
            });

        return response()->json($products);
    }
}
