<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Response;

class ColorController extends Controller
{
    /**
     * Generate dynamic CSS with color settings from the database
     */
    public function css()
    {
        // Get color settings from the database or use defaults
        $colors = [
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
        ];

        // Generate CSS
        $css = ":root {\n";
        $css .= "    --primary-color: {$colors['primary_color']};\n";
        $css .= "    --primary-light: {$colors['primary_light']};\n";
        $css .= "    --primary-dark: {$colors['primary_dark']};\n";
        $css .= "    --primary-bg-light: {$colors['primary_bg_light']};\n";
        $css .= "    --accent-light: {$colors['accent_light']};\n";
        $css .= "    --text-dark: {$colors['text_dark']};\n";
        $css .= "    --text-light: {$colors['text_light']};\n";
        $css .= "    --gray-light: {$colors['gray_light']};\n";
        $css .= "    --gray-medium: {$colors['gray_medium']};\n";
        $css .= "    --gray-dark: {$colors['gray_dark']};\n";
        $css .= "}\n";

        // Return CSS with appropriate content type
        return response($css)
            ->header('Content-Type', 'text/css')
            ->header('Cache-Control', 'public, max-age=60'); // Short cache so admin color changes show up quickly
    }
}
