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

    /**
     * Display color settings form.
     */
    public function colorSettings()
    {
        // Get all settings that are used in the color settings form
        $colorSettings = collect([
            'primary_color' => Setting::get('primary_color', '#1E2F52'),
            'primary_light' => Setting::get('primary_light', '#3E5A8A'),
            'primary_dark' => Setting::get('primary_dark', '#10192E'),
            'primary_bg_light' => Setting::get('primary_bg_light', '#FAF3E6'),
            'accent_light' => Setting::get('accent_light', '#F1E4C0'),
            'text_dark' => Setting::get('text_dark', '#1B2237'),
            'text_light' => Setting::get('text_light', '#ffffff'),
            'gray_light' => Setting::get('gray_light', '#F7F5F0'),
            'gray_medium' => Setting::get('gray_medium', '#E7E2D6'),
            'gray_dark' => Setting::get('gray_dark', '#4B4A47'),
        ])->map(function ($value, $key) {
            return [
                'id' => $key,
                'key' => $key,
                'value' => $value,
                'group' => 'appearance',
                'type' => 'color',
                'label' => ucfirst(str_replace('_', ' ', $key)),
            ];
        })->values()->all();

        return Inertia::render('Admin/Settings/Colors', [
            'settings' => $colorSettings
        ]);
    }

    /**
     * Update color settings.
     */
    public function updateColorSettings(Request $request)
    {
        // Process color settings
        foreach ($request->all() as $key => $value) {
            Setting::set($key, $value);
        }

        // Clear the cache to ensure new settings are used
        Setting::clearCache();

        return redirect()->back()->with('success', 'Color settings updated successfully.');
    }

    /**
     * Display CTA settings form.
     */
    public function ctaSettings()
    {
        // Get all settings that are used in the CTA settings form
        $ctaSettings = collect([
            'cta_from_color' => Setting::get('cta_from_color', '#2A3F66'),
            'cta_to_color' => Setting::get('cta_to_color', '#10192E'),
            'cta_title' => Setting::get('cta_title', 'Ensemble, changeons des vies à Dakar'),
            'cta_description' => Setting::get('cta_description', 'Votre don, même modeste, nous permet d\'agir concrètement auprès des enfants et des familles qui en ont besoin. Rejoignez-nous.'),
            'cta_paragraph_color' => Setting::get('cta_paragraph_color', '#F3ECDD'),
            'cta_link_route' => Setting::get('cta_link_route', 'donate'),
            'cta_button_text' => Setting::get('cta_button_text', 'Faire un don'),
            'cta_button_text_color' => Setting::get('cta_button_text_color', '#1E2F52'),
        ])->map(function ($value, $key) {
            return [
                'id' => $key,
                'key' => $key,
                'value' => $value,
                'group' => 'cta',
                'type' => strpos($key, 'color') !== false ? 'color' : 'text',
                'label' => ucfirst(str_replace(['cta_', '_'], ['', ' '], $key)),
            ];
        })->values()->all();

        return Inertia::render('Admin/Settings/Cta', [
            'settings' => $ctaSettings
        ]);
    }

    /**
     * Update CTA settings.
     */
    public function updateCtaSettings(Request $request)
    {
        // Process CTA settings
        foreach ($request->all() as $key => $value) {
            Setting::set($key, $value);
        }

        // Clear the cache to ensure new settings are used
        Setting::clearCache();

        // Clear the CTA settings cache
        app(\App\Services\CtaService::class)->clearCtaSettingsCache();

        return redirect()->back()->with('success', 'CTA settings updated successfully.');
    }

    /**
     * Display attestation settings form.
     */
    public function attestationSettings()
    {
        $signaturePath = Setting::get('attestation_signature');
        $signatureUrl = $signaturePath && Storage::disk('public')->exists($signaturePath)
            ? Storage::disk('public')->url($signaturePath)
            : null;

        return Inertia::render('Admin/Settings/Attestation', [
            'signature_url'    => $signatureUrl,
            'director_name'    => Setting::get('attestation_director_name') ?? 'Sophie Manca',
            'director_title'   => Setting::get('attestation_director_title') ?? 'Directrice',
            'city'             => Setting::get('attestation_city') ?? 'Dakar (Sénégal)',
        ]);
    }

    /**
     * Update attestation settings.
     */
    public function updateAttestationSettings(Request $request)
    {
        $request->validate([
            'signature'        => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'remove_signature' => 'nullable|boolean',
            'director_name'    => 'nullable|string|max:100',
            'director_title'   => 'nullable|string|max:100',
            'city'             => 'nullable|string|max:100',
        ]);

        if ($request->boolean('remove_signature')) {
            $old = Setting::get('attestation_signature');
            if ($old && Storage::disk('public')->exists($old)) {
                Storage::disk('public')->delete($old);
            }
            Setting::set('attestation_signature', null);
        } elseif ($request->hasFile('signature')) {
            $old = Setting::get('attestation_signature');
            if ($old && Storage::disk('public')->exists($old)) {
                Storage::disk('public')->delete($old);
            }
            $path = $request->file('signature')->store('signatures', 'public');
            Setting::set('attestation_signature', $path);
        }

        if ($request->filled('director_name')) {
            Setting::set('attestation_director_name', $request->director_name);
        }
        if ($request->filled('director_title')) {
            Setting::set('attestation_director_title', $request->director_title);
        }
        if ($request->filled('city')) {
            Setting::set('attestation_city', $request->city);
        }

        return redirect()->back()->with('success', 'Paramètres attestation mis à jour.');
    }
}
