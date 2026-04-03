<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomepageAdSlot extends Model
{
    protected $table = 'homepage_ad_slots';

    protected $fillable = [
        'admin_title',
        'advertiser_name',
        'image',
        'source_type',
        'external_url',
        'link_url',
        'open_new_tab',
        'frame_style',
        'width_mode',
        'position',
        'side',
        'top',
        'bottom',
        'left',
        'right',
        'max_height_px',
        'size_type',
        'custom_width',
        'custom_height',
        'display_pages',
        'custom_path',
        'z_index',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'open_new_tab' => 'boolean',
        'is_active' => 'boolean',
        'max_height_px' => 'integer',
        'sort_order' => 'integer',
        'top' => 'integer',
        'bottom' => 'integer',
        'left' => 'integer',
        'right' => 'integer',
        'z_index' => 'integer',
    ];

    public function sectionKey(): string
    {
        return 'ad_slot_' . $this->id;
    }

    public static function imageDiskPath(): string
    {
        // fileUploader() expects a path relative to /public (e.g. assets/images/...)
        // It also mirrors uploads to the legacy project root assets folder.
        $rel = 'assets/images/homepage_ad';

        try {
            $publicDir = function_exists('public_path') ? public_path($rel) : $rel;
            if (!is_dir($publicDir)) {
                @mkdir($publicDir, 0755, true);
            }
        } catch (\Throwable $e) {
            // best-effort
        }

        try {
            $legacyDir = rtrim(dirname(base_path()), '/\\') . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $rel);
            if (!is_dir($legacyDir)) {
                @mkdir($legacyDir, 0755, true);
            }
        } catch (\Throwable $e) {
            // best-effort
        }

        return $rel;
    }

    public function imageUrl(): string
    {
        $img = trim((string) $this->image, '/');
        if ($img === '') {
            return '';
        }

        return asset('assets/images/homepage_ad/' . $img);
    }
}
