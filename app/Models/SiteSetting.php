<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\MarkdownConverter;

class SiteSetting extends Model
{
    protected $fillable = [
        'key', 'value', 'type', 'group', 'label', 'description',
    ];

    // ── Static helpers ─────────────────────────────────────────────────────────

    /**
     * Get a setting value by key, with optional default.
     * JSON fields are returned as decoded arrays.
     * Markdown fields are returned as raw markdown string (render via renderMarkdown()).
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
     * JSON fields come back as decoded arrays/objects.
     * Markdown/text fields come back as strings.
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

    /**
     * Render a markdown string to safe HTML using league/commonmark.
     * Returns an HtmlString suitable for {!! ... !!} in Blade.
     */
    public static function renderMarkdown(string $markdown, array $options = []): \Illuminate\Support\HtmlString
    {
        $defaults = [
            'html_input'         => 'strip',   // strip raw HTML from input for safety
            'allow_unsafe_links' => false,
            'max_nesting_level'  => 20,
        ];

        $converter = new CommonMarkConverter(array_merge($defaults, $options));
        $html = (string) $converter->convert($markdown);

        return new \Illuminate\Support\HtmlString($html);
    }

    /**
     * Render markdown and wrap in a styled prose container class.
     */
    public static function renderMarkdownProse(string $markdown): \Illuminate\Support\HtmlString
    {
        $html = (string) self::renderMarkdown($markdown);
        return new \Illuminate\Support\HtmlString('<div class="cms-prose">' . $html . '</div>');
    }
}
