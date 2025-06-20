<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BannerController extends Controller
{
    /**
     * Display a listing of banner photos for admin management.
     */
    public function index()
    {
        $photos = Photo::with('album')
            ->orderBy('is_banner', 'desc')
            ->orderBy('banner_order')
            ->get();

        return Inertia::render('Admin/Banner/BannerIndex', [
            'photos' => $photos
        ]);
    }

    /**
     * Update the banner status of photos.
     */
    public function update(Request $request)
    {
        $request->validate([
            'photos' => 'required|array',
            'photos.*.id' => 'required|exists:photos,id',
            'photos.*.is_banner' => 'required|boolean',
            'photos.*.banner_order' => 'nullable|integer'
        ]);

        // First, reset all banner photos
        Photo::where('is_banner', true)
            ->update(['is_banner' => false, 'banner_order' => null]);

        // Then set the selected photos as banner photos
        foreach ($request->photos as $photoData) {
            if ($photoData['is_banner']) {
                Photo::where('id', $photoData['id'])
                    ->update([
                        'is_banner' => true,
                        'banner_order' => $photoData['banner_order']
                    ]);
            }
        }

        return redirect()->back()->with('success', 'Banner photos updated successfully');
    }

    /**
     * Get banner photos for the welcome page.
     */
    public function getBannerPhotos()
    {
        $bannerPhotos = Photo::where('is_banner', true)
            ->orderBy('banner_order')
            ->limit(4)
            ->get(['id', 'image_path', 'caption']);

        return response()->json($bannerPhotos);
    }
}
