<?php

namespace App\Services;

use App\Models\Setting;
use App\Helpers\StorageHelper;
use Illuminate\Support\Facades\Cache;

class ContactService
{
    /**
     * Get contact settings for front-end pages
     *
     * @return array
     */
    public function getContactSettings()
    {
        // Cache the entire contact settings array for 24 hours
        return Cache::remember('contact_settings', 60*24, function() {
            return [
                'contact_phone' => Setting::get('contact_phone', '(+221) 78 000 00 00'),
                'whatsapp_number' => Setting::get('contact_phone', '+221780000000'),
                'contact_phone_fixed' => Setting::get('contact_phone_fixed', '(+221) 33 000 00 00'),
                'contact_email' => Setting::get('contact_email', 'contact@amaelfondation.org'),
                'contact_address' => Setting::get('contact_address', 'Dakar, Sénégal'),
                'social_facebook' => Setting::get('social_facebook', '#'),
                'social_instagram' => Setting::get('social_instagram', '#'),
                'opening_hours' => Setting::get('opening_hours', 'Lundi - Vendredi : 9h - 18h'),
                'site_logo' => StorageHelper::url(Setting::get('site_logo', 'images/logo.png')),
            ];
        });
    }

    /**
     * Clear contact settings cache
     */
    public function clearContactSettingsCache()
    {
        Cache::forget('contact_settings');
    }
}
