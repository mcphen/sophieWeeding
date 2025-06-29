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
                'contact_phone' => Setting::get('contact_phone', '(+221) 78 538 30 69'),
                'whatsapp_number' => Setting::get('contact_phone', '+221785383069'),
                'contact_phone_fixed' => Setting::get('contact_phone_fixed', '(+221) 33 865 27 11'),
                'contact_email' => Setting::get('contact_email', 'sophieweddings5@gmail.com'),
                'contact_address' => Setting::get('contact_address', 'Rue NG-70, 91 Ngor Almadies, Dakar 12000'),
                'social_facebook' => Setting::get('social_facebook', 'https://www.facebook.com/Sophieweddingsdreams22/'),
                'social_instagram' => Setting::get('social_instagram', 'https://www.instagram.com/sophie_weddings_dreams/'),
                'opening_hours' => Setting::get('opening_hours', 'Lundi - Vendredi: 8am - 6pm'),
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
