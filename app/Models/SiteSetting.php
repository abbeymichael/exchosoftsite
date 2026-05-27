<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $fillable = [
        'key', 'value', 'type', 'group', 'label', 'description',
    ];

    // ── Static helpers ─────────────────────────────────────────────────────────

    /**
     * Get a setting value by key, with optional default.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();
        if (! $setting) {
            return $default;
        }
        return match ($setting->type) {
            'json'    => json_decode($setting->value, true),
            'boolean' => (bool) $setting->value,
            default   => $setting->value,
        };
    }

    /**
     * Set a setting value by key.
     */
    public static function set(string $key, mixed $value, string $type = 'text', string $group = 'general', string $label = ''): static
    {
        $storedValue = is_array($value) ? json_encode($value) : $value;

        return static::updateOrCreate(
            ['key' => $key],
            ['value' => $storedValue, 'type' => $type, 'group' => $group, 'label' => $label]
        );
    }

    /**
     * Get all settings for a group as key => value array.
     */
    public static function getGroup(string $group): array
    {
        return static::where('group', $group)->get()
            ->mapWithKeys(fn($s) => [$s->key => match ($s->type) {
                'json'    => json_decode($s->value, true),
                'boolean' => (bool) $s->value,
                default   => $s->value,
            }])->toArray();
    }
}
