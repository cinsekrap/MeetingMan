<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Get a setting value by key.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember("site_setting.{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Set a setting value.
     */
    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        Cache::forget("site_setting.{$key}");
    }

    /**
     * Get the logo URL if set.
     */
    public static function logoUrl(): ?string
    {
        $path = static::get('logo_path');
        return $path ? asset($path) : null;
    }

    /**
     * Get the site name.
     */
    public static function siteName(): string
    {
        return static::get('site_name', config('app.name', 'MeetingMan'));
    }
}
