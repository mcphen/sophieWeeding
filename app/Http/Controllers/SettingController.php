<?php

namespace App\Http\Controllers;

use App\Helpers\StorageHelper;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $settings = Setting::all();

        return Inertia::render('Admin/Settings/Index', [
            'settings' => $settings
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/Settings/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'key' => 'required|string|max:255|unique:settings',
            'value' => 'nullable|string',
            'group' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'label' => 'required|string|max:255',
        ]);

        Setting::create($validated);

        return redirect()->route('admin.settings.index')
            ->with('success', 'Setting created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $setting = Setting::findOrFail($id);

        return Inertia::render('Admin/Settings/Show', [
            'setting' => $setting
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $setting = Setting::findOrFail($id);

        return Inertia::render('Admin/Settings/Edit', [
            'setting' => $setting
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $setting = Setting::findOrFail($id);

        $validated = $request->validate([
            'value' => 'nullable|string',
            'group' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'label' => 'required|string|max:255',
        ]);

        $setting->update($validated);

        return redirect()->route('admin.settings.index')
            ->with('success', 'Setting updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $setting = Setting::findOrFail($id);
        $setting->delete();

        return redirect()->route('admin.settings.index')
            ->with('success', 'Setting deleted successfully.');
    }

    /**
     * Display contact settings form.
     */
    public function contactSettings()
    {
        // Get all settings that are used in the contact settings form
        $contactSettings = collect([
            'contact_phone' => Setting::get('contact_phone', ''),
            'contact_phone_fixed' => Setting::get('contact_phone_fixed', ''),
            'contact_email' => Setting::get('contact_email', ''),
            'contact_address' => Setting::get('contact_address', ''),
            'social_facebook' => Setting::get('social_facebook', ''),
            'social_twitter' => Setting::get('social_twitter', ''),
            'social_instagram' => Setting::get('social_instagram', ''),
            'social_youtube' => Setting::get('social_youtube', ''),
            'social_linkedin' => Setting::get('social_linkedin', ''),
            'social_tiktok' => Setting::get('social_tiktok', ''),
            'opening_hours' => Setting::get('opening_hours', ''),
            'site_logo' => StorageHelper::url(Setting::get('site_logo', 'images/logo.png')),
        ])->map(function ($value, $key) {
            return [
                'id' => $key,
                'key' => $key,
                'value' => $value,
                'group' => 'contact',
                'type' => 'text',
                'label' => ucfirst(str_replace('_', ' ', $key)),
            ];
        })->values()->all();

        return Inertia::render('Admin/Settings/Contact', [
            'settings' => $contactSettings
        ]);
    }

    /**
     * Update contact settings.
     */
    public function updateContactSettings(Request $request)
    {
        // Handle logo upload if present
        if ($request->hasFile('site_logo')) {
            // Delete old logo if it exists and is not the default
            $oldLogo = Setting::get('site_logo', 'images/logo.png');
            if ($oldLogo && $oldLogo !== 'images/logo.png' && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }

            // Store the new logo
            $logoPath = $request->file('site_logo')->store('logos', 'public');
            Setting::set('site_logo', $logoPath);
        }

        // Process other settings
        foreach ($request->except('site_logo') as $key => $value) {
            Setting::set($key, $value);
        }

        return redirect()->back()->with('success', 'Contact settings updated successfully.');
    }
}
