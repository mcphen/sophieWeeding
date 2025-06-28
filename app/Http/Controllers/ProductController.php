<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
            'images.*'    => 'nullable|image|max:2048',
            'price'       => 'required|numeric|min:0',
        ]);

        // Keep backward compatibility with single image upload
        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('products', 'public');
        }

        // Generate slug from title
        $data['slug'] = Str::slug($data['title']);

        $product = Product::create($data);

        // Handle multiple image uploads
        if ($request->hasFile('images')) {
            $order = 0;
            foreach ($request->file('images') as $image) {
                $imagePath = $image->store('products', 'public');
                $product->images()->create([
                    'image_path' => $imagePath,
                    'order' => $order++,
                ]);
            }
        }

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
            'images.*'    => 'nullable|image|max:2048',
            'price'       => 'required|numeric|min:0',
            'delete_images' => 'nullable|array',
            'delete_images.*' => 'nullable|integer',
        ]);

        // Keep backward compatibility with single image upload
        if ($request->hasFile('image')) {
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            $data['image_path'] = $request->file('image')->store('products', 'public');
        }

        // Update slug if title has changed
        if (isset($data['title']) && $data['title'] !== $product->title) {
            $data['slug'] = Str::slug($data['title']);
        }

        $product->update($data);

        // Delete images if requested
        if ($request->has('delete_images')) {
            foreach ($request->delete_images as $imageId) {
                $image = ProductImage::find($imageId);
                if ($image && $image->product_id == $product->id) {
                    Storage::disk('public')->delete($image->getRawOriginal('image_path'));
                    $image->delete();
                }
            }
        }

        // Handle multiple image uploads
        if ($request->hasFile('images')) {
            $maxOrder = $product->images()->max('order') ?? -1;
            foreach ($request->file('images') as $image) {
                $imagePath = $image->store('products', 'public');
                $product->images()->create([
                    'image_path' => $imagePath,
                    'order' => ++$maxOrder,
                ]);
            }
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Produit mis à jour.');
    }

    // Suppression
    public function destroy(Product $product)
    {
        // Delete main product image if exists
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }

        // Delete all associated product images
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->getRawOriginal('image_path'));
        }

        // The product and its images will be deleted due to the cascade constraint
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
        $products = Product::with('images')
            ->select('id', 'title', 'description', 'image_path', 'price', 'slug')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'title' => $product->title,
                    'description' => $product->description,
                    'image_url' => $product->image_path ? \App\Helpers\StorageHelper::url($product->image_path) : null,
                    'price' => $product->price,
                    'slug' => $product->slug,
                    'images' => $product->images->map(function ($image) {
                        return [
                            'id' => $image->id,
                            'image_url' => $image->image_url,
                            'order' => $image->order,
                        ];
                    }),
                ];
            });

        return response()->json($products);
    }
}
