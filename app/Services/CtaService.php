<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class CtaService
{
    /**
     * Get CTA settings for front-end pages
     *
     * @return array
     */
    public function getCtaSettings()
    {
        // Cache the entire CTA settings array for 24 hours
        return Cache::remember('cta_settings', 60*24, function() {
            return [
                'fromColor' => Setting::get('cta_from_color', '#d1922f'),
                'toColor' => Setting::get('cta_to_color', '#bf8529'),
                'title' => Setting::get('cta_title', 'Prêts à planifier le mariage de vos rêves ?'),
                'description' => Setting::get('cta_description', 'Contactez-nous dès aujourd\'hui pour une consultation gratuite et commencez à transformer votre vision en réalité.'),
                'paragraphColor' => Setting::get('cta_paragraph_color', '#faecd2'),
                'linkRoute' => Setting::get('cta_link_route', 'appointment.create'),
                'buttonText' => Setting::get('cta_button_text', 'Prendre rendez-vous'),
                'buttonTextColor' => Setting::get('cta_button_text_color', '#d1922f'),
            ];
        });
    }

    /**
     * Clear CTA settings cache
     */
    public function clearCtaSettingsCache()
    {
        Cache::forget('cta_settings');
    }
}
