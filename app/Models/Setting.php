<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'group',
        'type',
        'label'
    ];

    /**
     * Get a setting value by key
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        return Cache::remember('setting_'.$key, 60*24, function() use ($key, $default) {
            $setting = self::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Set a setting value
     *
     * @param string $key
     * @param mixed $value
     * @return Setting
     */
    public static function set(string $key, $value)
    {
        $setting = self::firstOrNew(['key' => $key]);
        $setting->value = $value;
        $setting->save();

        // Clear the cache for this setting
        Cache::forget('setting_'.$key);

        return $setting;
    }

    /**
     * Get all settings by group
     *
     * @param string $group
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getByGroup(string $group)
    {
        return Cache::remember('settings_group_'.$group, 60*24, function() use ($group) {
            return self::where('group', $group)->get();
        });
    }

    /**
     * Get all settings as key-value pairs
     *
     * @return array
     */
    public static function getAllAsArray()
    {
        return Cache::remember('settings_all', 60*24, function() {
            return self::all()->pluck('value', 'key')->toArray();
        });
    }

    /**
     * Clear all settings cache
     */
    public static function clearCache()
    {
        Cache::forget('settings_all');
        // You might want to implement a more sophisticated cache clearing
        // for group and individual settings if needed
    }
}
