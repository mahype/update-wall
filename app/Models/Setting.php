<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    public $timestamps = false;

    protected $fillable = ['key', 'value'];

    const NOTIFICATIONS_ENABLED  = 'notifications.enabled';
    const NOTIFICATIONS_STATUSES = 'notifications.statuses';

    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();

        return $setting ? $setting->value : $default;
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => is_array($value) ? json_encode($value) : (string) $value]
        );
    }

    public static function getJson(string $key, mixed $default = null): mixed
    {
        $raw = static::get($key);

        if ($raw === null) {
            return $default;
        }

        return json_decode($raw, true) ?? $default;
    }
}
