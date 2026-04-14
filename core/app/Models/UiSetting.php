<?php

namespace App\Models;

use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class UiSetting extends Model
{
    protected $fillable = [
        'product_card_bg',
        'product_button_color',
        'product_buy_now_color',
        'product_buy_now_hover',
        'product_price_color',
        'header_bg',
        'footer_bg',
        'rating_color',
        'discount_badge_color',
        'stock_color',
        'shipping_badge_color',
        'header_top_bg',
        'theme_template',
    ];

    /**
     * Get the single UI settings row (id = 1). Cached for frontend.
     */
    public static function getSettings(): self
    {
        if (!static::isTableQueryable()) {
            return static::fallbackDefaults();
        }

        return Cache::remember('ui_settings', 300, function () {
            try {
                $row = static::find(1);
                if ($row) {
                    static::persistSnapshot($row->only(array_keys(static::defaultValues())));
                    return $row;
                }

                $created = static::firstOrCreate(['id' => 1], static::defaultValues());
                static::persistSnapshot($created->only(array_keys(static::defaultValues())));
                return $created;
            } catch (QueryException $e) {
                return static::fallbackDefaults();
            }
        });
    }

    protected static function booted()
    {
        static::saved(function (self $model) {
            Cache::forget('ui_settings');
            static::persistSnapshot($model->only(array_keys(static::defaultValues())));
        });
    }

    public static function isTableQueryable(): bool
    {
        if (!Schema::hasTable('ui_settings')) {
            return false;
        }

        try {
            static::query()->select('id')->limit(1)->get();
            return true;
        } catch (QueryException $e) {
            return false;
        }
    }

    protected static function fallbackDefaults(): self
    {
        $item = new static(static::snapshotOrDefaults());
        $item->id = 1;
        return $item;
    }

    public static function snapshotOrDefaults(): array
    {
        $defaults = static::defaultValues();
        $snapshot = static::readSnapshot();
        return array_merge($defaults, $snapshot);
    }

    protected static function defaultValues(): array
    {
        return [
            'product_card_bg' => '#ffffff',
            'product_button_color' => '#1f2937',
            'product_buy_now_color' => '#0e9f90',
            'product_buy_now_hover' => '#0c8a7d',
            'product_price_color' => '#0e9f90',
            'rating_color' => '#f59e0b',
            'discount_badge_color' => '#dc2626',
            'stock_color' => '#16a34a',
            'shipping_badge_color' => '#2563eb',
            'header_top_bg' => '#0f172a',
            'theme_template' => 'default',
        ];
    }

    protected static function snapshotPath(): string
    {
        return storage_path('app/ui-settings-latest.json');
    }

    protected static function persistSnapshot(array $values): void
    {
        try {
            $path = static::snapshotPath();
            $dir = dirname($path);
            if (!File::exists($dir)) {
                File::makeDirectory($dir, 0755, true);
            }
            File::put($path, json_encode([
                'updated_at' => now()->toDateTimeString(),
                'values' => $values,
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        } catch (\Throwable $e) {
            Log::warning('Unable to write UI settings snapshot', ['message' => $e->getMessage()]);
        }
    }

    protected static function readSnapshot(): array
    {
        try {
            $path = static::snapshotPath();
            if (!File::exists($path)) {
                return [];
            }
            $data = json_decode((string) File::get($path), true);
            if (!is_array($data) || !isset($data['values']) || !is_array($data['values'])) {
                return [];
            }

            $allowed = array_keys(static::defaultValues());
            $values = [];
            foreach ($data['values'] as $key => $value) {
                if (in_array($key, $allowed, true) && is_string($value) && strlen($value) <= 50) {
                    $values[$key] = $value;
                }
            }
            return $values;
        } catch (\Throwable $e) {
            Log::warning('Unable to read UI settings snapshot', ['message' => $e->getMessage()]);
            return [];
        }
    }
}
