<?php

namespace App\Console\Commands;

use App\Models\Frontend;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

/**
 * Copy Lucide SVG pack from New1/ok into public header_icons and bind filenames in header_icons.content.
 * Missing files can be fetched from lucide-icons/lucide (same source as download_lucide_icons.ps1).
 */
class SyncHeaderIconsPackCommand extends Command
{
    protected $signature = 'staylbd:sync-header-icons-pack
                            {--path= : Absolute or relative path to SVG folder (default: ../New1/ok from core)}
                            {--dry-run : Show actions without writing files or DB}';

    protected $description = 'Install header/dashboard/product icons from New1/ok (or custom folder) into public and Frontend header_icons.content';

    /** @var array<string, string> field => lucide icon basename without .svg */
    private array $lucideFallback = [
        'search_icon' => 'search',
        'voice_search_icon' => 'mic',
        'image_search_icon' => 'scan-search',
        'home_icon' => 'house',
        'categories_icon' => 'layout-grid',
        'products_icon' => 'package',
        'contact_icon' => 'phone',
        'track_order_icon' => 'truck',
        'language_icon' => 'languages',
        'notification_icon' => 'bell',
        'wishlist_icon' => 'heart',
        'compare_icon' => 'arrow-left-right',
        'cart_icon' => 'shopping-cart',
        'buy_now_icon' => 'zap',
        'orders_icon' => 'clipboard-list',
        'login_icon' => 'user',
        'register_icon' => 'user-plus',
        'transactions_icon' => 'banknote',
        'messages_icon' => 'message-square',
        'mail_icon' => 'mail',
        'review_icon' => 'star',
        'profile_icon' => 'user',
        'change_password_icon' => 'key-round',
        'logout_icon' => 'log-out',
        'quick_view_icon' => 'eye',
        'policy_payment_icon' => 'credit-card',
        'policy_shipping_icon' => 'truck',
        'policy_order_icon' => 'clipboard-list',
        'section_brand_icon' => 'tag',
        'scroll_top_icon' => 'chevron-up',
    ];

    /** Same physical file as another slot (matches admin “shared default” behaviour). */
    private array $dbImageAlias = [
        'policy_shipping_icon' => 'track_order_icon.svg',
        'policy_order_icon' => 'orders_icon.svg',
    ];

    public function handle(): int
    {
        $dry = (bool) $this->option('dry-run');
        $packArg = $this->option('path');
        $pack = $packArg
            ? (str_starts_with((string) $packArg, DIRECTORY_SEPARATOR) || preg_match('#^[A-Za-z]:[/\\\\]#', (string) $packArg)
                ? (string) $packArg
                : base_path('../' . ltrim((string) $packArg, '/')))
            : base_path('../New1/ok');

        $pack = realpath($pack);
        if ($pack === false || !is_dir($pack)) {
            $this->error('Pack folder not found: ' . ($this->option('path') ?: base_path('../New1/ok')));

            return self::FAILURE;
        }

        $destDir = public_path('assets/images/frontend/header_icons');
        if (!$dry && !is_dir($destDir)) {
            File::makeDirectory($destDir, 0755, true);
        }

        $fields = array_keys($this->lucideFallback);
        $copied = 0;
        $downloaded = 0;
        $usedExisting = 0;

        foreach ($fields as $field) {
            if (isset($this->dbImageAlias[$field])) {
                continue;
            }
            $basename = $field . '.svg';
            $src = $pack . DIRECTORY_SEPARATOR . $basename;
            $dest = $destDir . DIRECTORY_SEPARATOR . $basename;

            if (!is_file($src)) {
                $lucide = $this->lucideFallback[$field] ?? null;
                if (!$lucide) {
                    $this->warn("Skip {$field}: no source and no Lucide mapping");

                    continue;
                }
                $url = 'https://raw.githubusercontent.com/lucide-icons/lucide/main/icons/' . $lucide . '.svg';
                if ($dry) {
                    $this->line("[dry-run] Would download {$url} -> {$basename}");
                    $downloaded++;

                    continue;
                }
                try {
                    $resp = Http::timeout(25)->get($url);
                    if (!$resp->successful()) {
                        $this->warn("HTTP {$resp->status()} for {$url}");

                        continue;
                    }
                    File::put($dest, $resp->body());
                    $this->info("Downloaded Lucide {$lucide}.svg as {$basename}");
                    $downloaded++;
                } catch (\Throwable $e) {
                    $this->warn("Download failed {$basename}: " . $e->getMessage());
                }

                continue;
            }

            if ($dry) {
                $this->line("[dry-run] Would copy {$src} -> {$dest}");
                $copied++;

                continue;
            }
            if (!@copy($src, $dest)) {
                $this->error("Copy failed: {$src}");

                return self::FAILURE;
            }
            $copied++;
        }

        if ($dry) {
            $this->info("Dry run: {$copied} copies, {$downloaded} downloads (simulated). No DB write.");

            return self::SUCCESS;
        }

        $row = Frontend::where('data_keys', 'header_icons.content')->orderBy('id', 'desc')->first();
        if (!$row) {
            $row = new Frontend();
            $row->data_keys = 'header_icons.content';
            $row->data_values = (object) [];
        }
        $vals = (array) ($row->data_values ?? []);

        foreach ($fields as $field) {
            $fname = $this->dbImageAlias[$field] ?? ($field . '.svg');
            $full = $destDir . DIRECTORY_SEPARATOR . $fname;
            if (is_file($full)) {
                $vals[$field . '_image'] = $fname;
            } else {
                $this->warn("DB not set for {$field}: file missing {$fname}");
            }
        }

        $row->data_values = (object) $vals;
        $row->save();

        mirror_header_icons_public_to_legacy();

        $this->info("Header icons synced: {$copied} copied from pack, {$downloaded} downloaded from Lucide. DB row updated.");

        return self::SUCCESS;
    }
}
