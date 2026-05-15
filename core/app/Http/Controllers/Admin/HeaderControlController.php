<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\HeaderControlService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class HeaderControlController extends Controller
{
    public function index(): View
    {
        return view('admin.frontend.header_control.index', [
            'draftConfig' => HeaderControlService::getDraftConfig(),
            'liveConfig' => HeaderControlService::getLiveConfig(),
        ]);
    }

    public function preview(): View
    {
        return view('admin.frontend.header_control.preview', [
            'headerControl' => HeaderControlService::getDraftConfig(),
        ]);
    }

    public function saveDraft(Request $request): RedirectResponse
    {
        $draft = HeaderControlService::getDraftConfig();
        $draft['appearance'] = $this->validatedAppearance($request);
        $draft['top_bar'] = $this->validatedTopBar($request);
        $draft['main_bar'] = $this->validatedMainBar($request);
        $incomingMenuBar = $this->validatedMenuBar($request);
        $draft['menu_bar'] = $incomingMenuBar;
        HeaderControlService::saveDraft($draft);

        return back()->with('success', 'Header draft saved successfully');
    }

    public function publish(Request $request): RedirectResponse
    {
        HeaderControlService::publishDraft();

        return back()->with('success', 'Header configuration published');
    }


    private function validatedMenuBar(Request $request): array
    {
        $validated = $request->validate([
            'menu_bar.enabled' => ['nullable'],
            'menu_bar.is_public' => ['nullable'],
            'menu_bar.show_sidebar_trigger' => ['nullable'],
            'menu_bar.show_category_button' => ['nullable'],
            'menu_bar.category_button_label' => ['required', 'string', 'max:60'],
            'menu_bar.show_seller_button' => ['nullable'],
            'menu_bar.seller_text' => ['required', 'string', 'max:60'],
            'menu_bar.seller_url' => ['required', 'string', 'max:255'],
        ]);

        $menuBar = (array) ($validated['menu_bar'] ?? []);
        $menuBar['enabled'] = $request->has('menu_bar.enabled');
        $menuBar['is_public'] = $request->has('menu_bar.is_public');
        $menuBar['show_sidebar_trigger'] = $request->has('menu_bar.show_sidebar_trigger');
        $menuBar['show_category_button'] = $request->has('menu_bar.show_category_button');
        $menuBar['show_seller_button'] = $request->has('menu_bar.show_seller_button');

        $menuBar['category_items'] = $this->parseSimpleItems($request->input('menu_bar.category_items_text'));
        $menuBar['nav_links'] = $this->parseButtons($request->input('menu_bar.nav_links', []));
        $menuBar['custom_buttons'] = $this->parseButtons($request->input('menu_bar.custom_buttons', []));
        $menuBar['seller_url'] = $this->sanitizeUrl((string) ($menuBar['seller_url'] ?? '/seller/apply'));
        $menuBar = $this->applyGlobalDisplayOrder($menuBar);

        return $menuBar;
    }

    private function validatedAppearance(Request $request): array
    {
        $validated = $request->validate([
            'appearance.top_bg' => ['required', 'string', 'max:255'],
            'appearance.main_bg' => ['required', 'string', 'max:255'],
            'appearance.menu_bg' => ['required', 'string', 'max:255'],
            'appearance.top_height' => ['required', 'integer', 'min:30', 'max:80'],
            'appearance.main_height' => ['required', 'integer', 'min:40', 'max:100'],
            'appearance.menu_height' => ['required', 'integer', 'min:30', 'max:80'],
            'appearance.width_desktop' => ['required', 'integer', 'min:1280', 'max:1920'],
            'appearance.width_laptop' => ['required', 'integer', 'min:1024', 'max:1800'],
            'appearance.width_tablet' => ['required', 'integer', 'min:768', 'max:1400'],
            'appearance.width_mobile' => ['required', 'integer', 'min:320', 'max:900'],
        ]);

        $appearance = (array) ($validated['appearance'] ?? []);
        $appearance['top_bg'] = $this->sanitizeColor((string) ($appearance['top_bg'] ?? ''), '#0f172a');
        $appearance['main_bg'] = $this->sanitizeColor((string) ($appearance['main_bg'] ?? ''), '#f8fafc');
        $appearance['menu_bg'] = $this->sanitizeColor((string) ($appearance['menu_bg'] ?? ''), '#c7eafe');
        $appearance['width_desktop'] = $this->clampInt($appearance['width_desktop'] ?? 1920, 1280, 1920);
        $appearance['width_laptop'] = $this->clampInt($appearance['width_laptop'] ?? 1600, 1024, 1800);
        $appearance['width_tablet'] = $this->clampInt($appearance['width_tablet'] ?? 1200, 768, 1400);
        $appearance['width_mobile'] = $this->clampInt($appearance['width_mobile'] ?? 100, 320, 900);

        return $appearance;
    }

    private function validatedTopBar(Request $request): array
    {
        $validated = $request->validate([
            'top_bar.enabled' => ['nullable'],
            'top_bar.is_public' => ['nullable'],
            'top_bar.show_language' => ['nullable'],
            'top_bar.show_currency' => ['nullable'],
            'top_bar.show_apps' => ['nullable'],
            'top_bar.show_seller_button' => ['nullable'],
            'top_bar.language_mode' => ['required', 'in:code,name'],
            'top_bar.currency_mode' => ['required', 'in:code,name'],
            'top_bar.support_label' => ['required', 'string', 'max:60'],
            'top_bar.support_phone' => ['nullable', 'string', 'max:60'],
            'top_bar.support_email' => ['nullable', 'email:rfc'],
            'top_bar.seller_text' => ['required', 'string', 'max:60'],
            'top_bar.seller_url' => ['required', 'string', 'max:255'],
        ]);

        $topBar = (array) ($validated['top_bar'] ?? []);
        $topBar['enabled'] = $request->has('top_bar.enabled');
        $topBar['is_public'] = $request->has('top_bar.is_public');
        $topBar['show_language'] = $request->has('top_bar.show_language');
        $topBar['show_currency'] = $request->has('top_bar.show_currency');
        $topBar['show_apps'] = $request->has('top_bar.show_apps');
        $topBar['show_seller_button'] = $request->has('top_bar.show_seller_button');

        $topBar['seller_url'] = $this->sanitizeUrl((string) ($topBar['seller_url'] ?? '/seller/apply'));
        $topBar['custom_buttons'] = $this->parseButtons($request->input('top_bar.custom_buttons', []));

        return $topBar;
    }

    private function validatedMainBar(Request $request): array
    {
        $validated = $request->validate([
            'main_bar.enabled' => ['nullable'],
            'main_bar.is_public' => ['nullable'],
            'main_bar.logo_max_height' => ['required', 'integer', 'min:28', 'max:90'],
            'main_bar.icon_size' => ['required', 'integer', 'min:28', 'max:72'],
            'main_bar.show_language_icon' => ['nullable'],
        ]);

        $mainBar = (array) ($validated['main_bar'] ?? []);
        $mainBar['enabled'] = $request->has('main_bar.enabled');
        $mainBar['is_public'] = $request->has('main_bar.is_public');
        $mainBar['show_language_icon'] = $request->has('main_bar.show_language_icon');

        return $mainBar;
    }

    private function parseSimpleItems($raw): array
    {
        $out = [];
        $raw = is_string($raw) ? $raw : '';
        $lines = preg_split('/\r\n|\r|\n/', trim($raw)) ?: [];
        foreach ($lines as $line) {
            $line = trim((string) $line);
            if ($line === '') {
                continue;
            }
            [$label, $url] = array_pad(explode('|', $line, 2), 2, '#');
            $label = trim((string) $label);
            $url = $this->sanitizeUrl(trim((string) $url) ?: '#');
            if ($label === '') {
                continue;
            }
            $out[] = ['label' => $label, 'url' => $url];
        }

        return array_slice($out, 0, 30);
    }

    private function parseButtons($rawButtons): array
    {
        if (!is_array($rawButtons)) {
            return [];
        }
        $buttons = [];
        foreach (array_values($rawButtons) as $position => $btn) {
            if (!is_array($btn)) {
                continue;
            }
            $label = trim((string) ($btn['label'] ?? ''));
            if ($label === '') {
                continue;
            }
            $type = (string) ($btn['type'] ?? 'link');
            if (!in_array($type, ['link', 'dropdown'], true)) {
                $type = 'link';
            }
            $button = [
                'label' => $label,
                'url' => $this->sanitizeUrl(trim((string) ($btn['url'] ?? '#')) ?: '#'),
                'type' => $type,
                'is_active' => (int) (!empty($btn['is_active'] ?? 0)),
                'display_order' => $position + 1,
                'tracking_key' => $this->sanitizeTrackingKey((string) ($btn['tracking_key'] ?? $label)),
                'dropdown_style' => in_array((string) ($btn['dropdown_style'] ?? 'dropdown'), ['dropdown', 'mega'], true)
                    ? (string) ($btn['dropdown_style'] ?? 'dropdown')
                    : 'dropdown',
                'items' => [],
            ];

            $itemsText = trim((string) ($btn['items_text'] ?? ''));
            if ($type === 'dropdown' && $itemsText !== '') {
                $button['items'] = $this->parseNestedDropdownItems($itemsText);
            }
            $buttons[] = $button;
        }

        return $buttons;
    }

    private function parseNestedDropdownItems(string $itemsText): array
    {
        $root = [];
        $refs = [];
        $lines = preg_split('/\r\n|\r|\n/', $itemsText) ?: [];

        foreach ($lines as $line) {
            $line = trim((string) $line);
            if ($line === '') {
                continue;
            }

            [$token, $itemUrl] = array_pad(explode('|', $line, 2), 2, '#');
            $token = trim((string) $token);
            $itemUrl = $this->sanitizeUrl(trim((string) $itemUrl) ?: '#');
            if ($token === '') {
                continue;
            }
            if (preg_match('/^\/+\s+/', $token)) {
                continue;
            }

            $slashDepth = 0;
            if (preg_match('/^(\/+)(.+)$/', $token, $m)) {
                $slashDepth = strlen($m[1]);
                $token = trim((string) $m[2]);
            }
            if ($token === '') {
                continue;
            }

            $depth = max(1, $slashDepth);
            $node = [
                'label' => $token,
                'url' => $itemUrl,
                'children' => [],
            ];

            if ($depth === 1 || empty($refs)) {
                $root[] = $node;
                $lastIdx = array_key_last($root);
                $refs = [1 => &$root[$lastIdx]];
                continue;
            }

            $parentDepth = $depth - 1;
            while ($parentDepth > 0 && !isset($refs[$parentDepth])) {
                $parentDepth--;
            }

            if ($parentDepth <= 0 || !isset($refs[$parentDepth])) {
                $root[] = $node;
                $lastIdx = array_key_last($root);
                $refs = [1 => &$root[$lastIdx]];
                continue;
            }

            $parent = &$refs[$parentDepth];
            if (!isset($parent['children']) || !is_array($parent['children'])) {
                $parent['children'] = [];
            }
            $parent['children'][] = $node;
            $childIdx = array_key_last($parent['children']);

            foreach (array_keys($refs) as $existingDepth) {
                if ((int) $existingDepth > $parentDepth + 1) {
                    unset($refs[$existingDepth]);
                }
            }
            $refs[$parentDepth + 1] = &$parent['children'][$childIdx];
        }

        return array_slice($root, 0, 40);
    }

    private function applyGlobalDisplayOrder(array $menuBar): array
    {
        $nav = is_array($menuBar['nav_links'] ?? null) ? array_values($menuBar['nav_links']) : [];
        $custom = is_array($menuBar['custom_buttons'] ?? null) ? array_values($menuBar['custom_buttons']) : [];
        $seq = 1;
        foreach ($nav as &$item) {
            if (is_array($item)) {
                $item['display_order'] = $seq++;
            }
        }
        unset($item);
        foreach ($custom as &$item) {
            if (is_array($item)) {
                $item['display_order'] = $seq++;
            }
        }
        unset($item);
        $menuBar['nav_links'] = $nav;
        $menuBar['custom_buttons'] = $custom;
        return $menuBar;
    }

    private function sanitizeColor(string $value, string $fallback): string
    {
        $value = trim($value);
        if (preg_match('/^(#[0-9a-fA-F]{3,8}|(rgb|rgba|hsl|hsla)\(.*?\)|(linear|radial)-gradient\(.*?\)|[a-zA-Z]+)$/i', $value)) {
            return $value;
        }
        // Basic fallback for simple hex regex if no match found
        return preg_match('/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $value) ? strtolower($value) : $fallback;
    }

    private function sanitizeUrl(string $url): string
    {
        $url = trim($url);
        if ($url === '') {
            return '#';
        }
        if (preg_match('/^\s*javascript:/i', $url) || preg_match('/^\s*data:/i', $url)) {
            return '#';
        }

        return mb_substr($url, 0, 255);
    }

    private function clampInt($value, int $min, int $max): int
    {
        return max($min, min($max, (int) $value));
    }

    private function sanitizeTrackingKey(string $value): string
    {
        $slug = Str::slug($value);
        if ($slug === '') {
            return 'header-link';
        }

        return mb_substr($slug, 0, 80);
    }
}

