<?php

namespace App\Helpers;

class StorageHelper
{
    /**
     * Get the URL for a storage path, with environment-specific modifications.
     *
     * @param string|null $path
     * @return string|null
     */
    public static function url($path)
    {
        if (is_null($path)) {
            return null;
        }

        // Generate the standard storage URL
        $storageUrl = '/storage/' . ltrim($path, '/');

        // In production, prepend sophieWeeding/public
        if (app()->environment('production')) {
            return '/sophieWeeding/public' . $storageUrl;
        }

        return $storageUrl;
    }
}
