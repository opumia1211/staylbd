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
        $config = $this->validatedConfig($request);
        HeaderControlService::saveDraft($config);

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

    private function validatedConfig(Request $request): array
    {
        $validated = $request->validate([
            'appearance.top_bg' => ['required', 'string', 'max:20'],
            'appearance.main_bg' => ['required', 'string', 'max:20'],
            'appearance.menu_bg' => ['required', 'string', 'max:20'],
            'appearance.top_height' => ['required', 'integer', 'min:30', 'max:80'],
            'appearance.main_height' => ['required', 'integer', 'min:40', 'max:100'],
            'appearance.menu_height' => ['required', 'integer', 'min:30', 'max:80'],

            'top_bar.enabled' => ['nullable', 'boolean'],
            'top_bar.show_language' => ['nullable', 'boolean'],
            'top_bar.show_currency' => ['nullable', 'boolean'],
            'top_bar.show_apps' => ['nullable', 'boolean'],
            'top_bar.language_mode' => ['required', 'in:code,name'],
            'top_bar.currency_mode' => ['required', 'in:code,name'],
            'top_bar.support_label' => ['required', 'string', 'max:60'],
            'top_bar.support_phone' => ['nullable', 'string', 'max:60'],
            'top_bar.show_seller_button' => ['nullable', 'boolean'],
            'top_bar.seller_text' => ['required', 'string', 'max:60'],
            'top_bar.seller_url' => ['required', 'string', 'max:255'],

            'main_bar.enabled' => ['nullable', 'boolean'],
            'main_bar.logo_max_height' => ['required', 'integer', 'min:28', 'max:90'],
            'main_bar.icon_size' => ['required', 'integer', 'min:28', 'max:72'],
            'main_bar.show_language_icon' => ['nullable', 'boolean'],

            'menu_bar.enabled' => ['nullable', 'boolean'],
            'menu_bar.show_seller_button' => ['nullable', 'boolean'],
        ]);

        $validated['top_bar']['custom_buttons'] = $this->parseButtons($request->input('top_bar.custom_buttons', []));
        $validated['menu_bar']['custom_buttons'] = $this->parseButtons($request->input('menu_bar.custom_buttons', []));

        return $validated;
    }

    private function parseButtons($rawButtons): array
    {
        if (!is_array($rawButtons)) {
            return [];
        }
        $buttons = [];
        foreach ($rawButtons as $btn) {
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
                'items' => [],
            ];

            $itemsText = trim((string) ($btn['items_text'] ?? ''));
            if ($type === 'dropdown' && $itemsText !== '') {
                $lines = preg_split('/\r\n|\r|\n/', $itemsText) ?: [];
                foreach ($lines as $line) {
                    $line = trim((string) $line);
                    if ($line === '') {
                        continue;
                    }
                    [$itemLabel, $itemUrl] = array_pad(explode('|', $line, 2), 2, '#');
                    $itemLabel = trim($itemLabel);
                    $itemUrl = trim($itemUrl) ?: '#';
                    if ($itemLabel === '') {
                        continue;
                    }
                    $button['items'][] = [
                        'label' => $itemLabel,
                        'url' => $itemUrl,
                    ];
                }
            }
            $buttons[] = $button;
        }

        return $buttons;
    }
}

