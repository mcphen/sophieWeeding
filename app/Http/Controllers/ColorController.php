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
            'primary_color' => Setting::get('primary_color', '#AA6808'),
            'primary_light' => Setting::get('primary_light', '#d1922f'),
            'primary_dark' => Setting::get('primary_dark', '#8a5406'),
            'primary_bg_light' => Setting::get('primary_bg_light', '#fcf5ea'),
            'accent_light' => Setting::get('accent_light', '#f3e3c8'),
            'text_dark' => Setting::get('text_dark', '#1b1b18'),
            'text_light' => Setting::get('text_light', '#ffffff'),
            'gray_light' => Setting::get('gray_light', '#f8f8f8'),
            'gray_medium' => Setting::get('gray_medium', '#e5e5e5'),
            'gray_dark' => Setting::get('gray_dark', '#4b4b4b'),
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
            ->header('Cache-Control', 'public, max-age=3600'); // Cache for 1 hour
    }
}
