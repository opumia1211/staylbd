<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\HeaderControlService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

    public function saveDraft(Request $request): RedirectResponse
    {
        $draft = HeaderControlService::getDraftConfig();
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

    public function preview(): View
    {
        return view('admin.frontend.header_control.preview', [
            'config' => HeaderControlService::getDraftConfig(),
        ]);
    }

    private function validatedMenuBar(Request $request): array
    {
        $validated = $request->validate([
            'menu_bar.enabled' => ['nullable', 'boolean'],
            'menu_bar.show_sidebar_trigger' => ['nullable', 'boolean'],
            'menu_bar.show_category_button' => ['nullable', 'boolean'],
            'menu_bar.category_button_label' => ['required', 'string', 'max:60'],
            'menu_bar.show_seller_button' => ['nullable', 'boolean'],
            'menu_bar.seller_text' => ['required', 'string', 'max:60'],
            'menu_bar.seller_url' => ['required', 'string', 'max:255'],
        ]);

        $validated['menu_bar']['category_items'] = $this->parseSimpleItems($request->input('menu_bar.category_items_text'));
        $validated['menu_bar']['nav_links'] = $this->parseButtons($request->input('menu_bar.nav_links', []));
        $validated['menu_bar']['custom_buttons'] = $this->parseButtons($request->input('menu_bar.custom_buttons', []));
        $validated['menu_bar'] = $this->applyGlobalDisplayOrder($validated['menu_bar'] ?? []);

        return $validated['menu_bar'] ?? [];
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
            $url = trim((string) $url) ?: '#';
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
                'url' => trim((string) ($btn['url'] ?? '#')) ?: '#',
                'type' => $type,
                'is_active' => (int) (!empty($btn['is_active'] ?? 0)),
                'display_order' => $position + 1,
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
            $itemUrl = trim((string) $itemUrl) ?: '#';
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
}

