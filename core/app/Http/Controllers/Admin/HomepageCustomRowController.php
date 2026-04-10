<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\HomepageCustomProductRow;
use App\Services\BannerService;
use App\Services\HomepageDataService;
use App\Services\HomepageLayoutService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class HomepageCustomRowController extends Controller
{
    public function index()
    {
        $pageTitle = __('Homepage layout');
        $rows = HomepageCustomProductRow::query()->with('category:id,name')->orderBy('sort_order')->orderBy('id')->get();
        $categories = Category::active()->orderBy('name')->get(['id', 'name']);
        $layoutSections = HomepageLayoutService::getOrderedSections();

        return view('admin.frontend.homepage_custom_rows.index', compact('pageTitle', 'rows', 'categories', 'layoutSections'));
    }

    public function saveLayout(Request $request)
    {
        $request->validate([
            'layout_json' => 'required|string',
        ]);
        $decoded = json_decode($request->input('layout_json'), true);
        if (!is_array($decoded) || $decoded === []) {
            return back()->withErrors(['layout_json' => __('Invalid data. Try again.')])->withInput();
        }
        $sections = [];
        foreach ($decoded as $row) {
            if (!is_array($row) || empty($row['id'])) {
                continue;
            }
            $label = isset($row['label']) ? trim((string) $row['label']) : '';
            $interval = isset($row['interval_seconds']) && $row['interval_seconds'] !== '' && $row['interval_seconds'] !== null
                ? max(2, min(30, (int) $row['interval_seconds']))
                : null;
            $speed = isset($row['speed_ms']) && $row['speed_ms'] !== '' && $row['speed_ms'] !== null
                ? max(300, min(2000, (int) $row['speed_ms']))
                : null;
            $sections[] = [
                'id' => (string) $row['id'],
                'enabled' => !empty($row['enabled']),
                'label' => $label !== '' ? $label : null,
                'interval_seconds' => $interval,
                'speed_ms' => $speed,
            ];
        }
        $allowed = HomepageLayoutService::allowedIds();
        $got = array_column($sections, 'id');
        if (count($got) !== count($allowed) || count(array_diff($allowed, $got)) || count(array_diff($got, $allowed))) {
            return back()->withErrors(['layout_json' => __('Section list mismatch. Reload the page, then drag and save again.')]);
        }
        HomepageLayoutService::saveLayout($sections);
        HomepageDataService::clearCache();

        $notify[] = ['success', __('Layout saved.')];

        return back()->withNotify($notify);
    }

    public function create()
    {
        $pageTitle = __('Add homepage product row');
        $row = new HomepageCustomProductRow([
            'is_active' => true,
            'sort_order' => (int) HomepageCustomProductRow::query()->max('sort_order') + 1,
            'source_type' => 'category',
            'product_limit' => 12,
        ]);
        $categories = Category::active()->orderBy('name')->get(['id', 'name']);

        return view('admin.frontend.homepage_custom_rows.form', compact('pageTitle', 'row', 'categories'));
    }

    public function edit(int $id)
    {
        $row = HomepageCustomProductRow::query()->findOrFail($id);
        $pageTitle = __('Edit homepage product row');
        $categories = Category::active()->orderBy('name')->get(['id', 'name']);

        return view('admin.frontend.homepage_custom_rows.form', compact('pageTitle', 'row', 'categories'));
    }

    public function store(Request $request)
    {
        $this->validateSplitBannerUploads($request);
        $data = $this->validatedData($request);
        $data['split_banner_json'] = $this->buildSplitBannerJson($request, null);
        $created = HomepageCustomProductRow::query()->create($data);
        HomepageLayoutService::persistLayoutAfterCustomRowChange();
        HomepageDataService::clearCache();
        HomepageDataService::clearBelowFoldFragmentCache();
        $notify[] = ['success', __('Row created. Drag the new line in the section list, then save order.')];

        return redirect()->route('admin.frontend.sections.homepageCustomRows')
            ->withNotify($notify)
            ->with('hp_highlight_row', $created->id);
    }

    public function update(Request $request, int $id)
    {
        $row = HomepageCustomProductRow::query()->findOrFail($id);
        $this->validateSplitBannerUploads($request);
        $data = $this->validatedData($request);
        $data['split_banner_json'] = $this->buildSplitBannerJson($request, $row->split_banner_json);
        $row->update($data);
        HomepageLayoutService::persistLayoutAfterCustomRowChange();
        HomepageDataService::clearCache();
        HomepageDataService::clearBelowFoldFragmentCache();
        $notify[] = ['success', __('Row updated. Homepage cache cleared.')];

        return redirect()->route('admin.frontend.sections.homepageCustomRows')
            ->withNotify($notify)
            ->with('hp_highlight_row', $id);
    }

    /**
     * One-click public / private for row split promos (from Banner admin table).
     */
    public function toggleSplitVisibility(Request $request, int $id)
    {
        $request->validate([
            'is_public' => 'required|in:0,1',
        ]);
        $row = HomepageCustomProductRow::query()->findOrFail($id);
        $sjRaw = $row->split_banner_json;
        $sj = is_array($sjRaw) ? $sjRaw : (is_string($sjRaw) ? (json_decode($sjRaw, true) ?: []) : []);
        $sj['is_public'] = $request->input('is_public') === '1' || $request->input('is_public') === 1;
        $row->split_banner_json = $sj;
        $row->save();
        HomepageDataService::clearCache();
        HomepageDataService::clearBelowFoldFragmentCache();
        $notify[] = ['success', __('Row promo visibility saved.')];

        return redirect()->route('admin.frontend.sections.banner')->withNotify($notify);
    }

    public function destroy(int $id)
    {
        HomepageCustomProductRow::query()->where('id', $id)->delete();
        HomepageLayoutService::persistLayoutAfterCustomRowChange();
        HomepageDataService::clearCache();
        HomepageDataService::clearBelowFoldFragmentCache();
        $notify[] = ['success', __('Row removed.')];

        return back()->withNotify($notify);
    }

    protected function validatedData(Request $request): array
    {
        $validated = $request->validate([
            'title' => 'required|string|max:191',
            'subtitle' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0|max:65535',
            'source_type' => 'required|in:category,manual',
            'category_id' => 'nullable|exists:categories,id',
            'product_ids_text' => 'nullable|string|max:2000',
            'product_limit' => 'nullable|integer|min:1|max:24',
            'interval_seconds' => 'nullable|integer|min:2|max:30',
            'view_all_url' => 'nullable|string|max:512',
            'view_all_label' => 'nullable|string|max:120',
        ]);

        $source = $validated['source_type'];
        if ($source === 'category' && empty($validated['category_id'])) {
            throw ValidationException::withMessages(['category_id' => [__('Select a category.')]]);
        }

        $productIds = [];
        if ($source === 'manual') {
            $productIds = $this->parseProductIds($validated['product_ids_text'] ?? '');
            if ($productIds === []) {
                throw ValidationException::withMessages(['product_ids_text' => [__('Enter at least one product ID.')]]);
            }
        }

        return [
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'] ?? null,
            'is_active' => $request->boolean('is_active'),
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'source_type' => $source,
            'category_id' => $source === 'category' ? (int) $validated['category_id'] : null,
            'product_ids' => $source === 'manual' ? $productIds : null,
            'product_limit' => (int) ($validated['product_limit'] ?? 12),
            'interval_seconds' => isset($validated['interval_seconds']) ? (int) $validated['interval_seconds'] : null,
            'view_all_url' => $validated['view_all_url'] ? trim($validated['view_all_url']) : null,
            'view_all_label' => $validated['view_all_label'] ? trim($validated['view_all_label']) : null,
        ];
    }

    /** @return int[] */
    protected function parseProductIds(string $text): array
    {
        $parts = preg_split('/[\s,;]+/', trim($text), -1, PREG_SPLIT_NO_EMPTY);
        $ids = [];
        foreach ($parts as $p) {
            $n = (int) $p;
            if ($n > 0) {
                $ids[] = $n;
            }
        }

        return array_values(array_unique($ids));
    }

    private function validateSplitBannerUploads(Request $request): void
    {
        $rules = [];
        for ($i = 0; $i < 5; $i++) {
            $key = "split_banner_large_{$i}_image";
            if ($request->hasFile($key)) {
                $rules[$key] = ['nullable', 'image', 'max:5120', 'mimes:jpeg,jpg,png,webp'];
            }
        }
        if ($request->hasFile('split_banner_small_image')) {
            $rules['split_banner_small_image'] = ['nullable', 'image', 'max:5120', 'mimes:jpeg,jpg,png,webp'];
        }
        if ($rules !== []) {
            $request->validate($rules);
        }
    }

    private function storeRowSplitBannerFile(UploadedFile $file): string
    {
        $ext = strtolower($file->getClientOriginalExtension() ?: '');
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            throw ValidationException::withMessages(['split_banner' => [__('Invalid image type.')]]);
        }
        $dir = BannerService::rowSplitUploadPath();
        $name = 'rsb_' . bin2hex(random_bytes(8)) . '.' . $ext;
        $file->move($dir, $name);

        return $name;
    }

    /**
     * @param  array<string, mixed>|null  $previous
     * @return array<string, mixed>
     */
    private function buildSplitBannerJson(Request $request, ?array $previous): array
    {
        $prev = is_array($previous) ? $previous : [];
        $enabled = $request->boolean('split_banner_enabled');
        $interval = max(2, min(30, (int) $request->input('split_banner_interval', $prev['interval'] ?? 5)));

        if (!$enabled) {
            $out = $prev;
            $out['enabled'] = false;
            $out['interval'] = $interval;

            return $out;
        }

        $isPublic = $request->boolean('split_banner_is_public');
        $countdownTitle = Str::limit(trim((string) $request->input('split_banner_countdown_title', '')), 120);
        $endInput = trim((string) $request->input('split_banner_countdown_end', ''));
        $countdownEndsAt = null;
        if ($endInput !== '') {
            try {
                $countdownEndsAt = Carbon::parse($endInput, config('app.timezone'))->toIso8601String();
            } catch (\Throwable $e) {
                $countdownEndsAt = $prev['countdown_ends_at'] ?? null;
            }
        }

        $prevLarge = isset($prev['large']) && is_array($prev['large']) ? $prev['large'] : [];
        $large = [];
        for ($i = 0; $i < 5; $i++) {
            $pSlide = $prevLarge[$i] ?? [];
            $image = null;
            if ($request->hasFile("split_banner_large_{$i}_image")) {
                $image = $this->storeRowSplitBannerFile($request->file("split_banner_large_{$i}_image"));
            } elseif ($request->filled("split_banner_large_{$i}_keep")) {
                $image = basename((string) $request->input("split_banner_large_{$i}_keep"));
            } elseif (!empty($pSlide['image']) && is_string($pSlide['image'])) {
                $image = basename($pSlide['image']);
            }
            if (!$image) {
                continue;
            }
            $large[] = [
                'image' => $image,
                'kicker' => Str::limit(trim((string) $request->input("split_banner_large_{$i}_kicker", '')), 160),
                'heading' => Str::limit(trim((string) $request->input("split_banner_large_{$i}_heading", '')), 255),
                'btn' => Str::limit(trim((string) $request->input("split_banner_large_{$i}_btn", 'Shop Now')), 80),
                'url' => Str::limit(trim((string) $request->input("split_banner_large_{$i}_url", '')), 512),
            ];
        }

        $prevSmall = isset($prev['small']) && is_array($prev['small']) ? $prev['small'] : [];
        $smallImage = null;
        if ($request->hasFile('split_banner_small_image')) {
            $smallImage = $this->storeRowSplitBannerFile($request->file('split_banner_small_image'));
        } elseif ($request->filled('split_banner_small_keep')) {
            $smallImage = basename((string) $request->input('split_banner_small_keep'));
        } elseif (!empty($prevSmall['image']) && is_string($prevSmall['image'])) {
            $smallImage = basename($prevSmall['image']);
        }

        $small = null;
        if ($smallImage) {
            $small = [
                'image' => $smallImage,
                'badge' => Str::limit(trim((string) $request->input('split_banner_small_badge', '')), 120),
                'heading' => Str::limit(trim((string) $request->input('split_banner_small_heading', '')), 255),
                'btn' => Str::limit(trim((string) $request->input('split_banner_small_btn', 'Shop Now')), 80),
                'url' => Str::limit(trim((string) $request->input('split_banner_small_url', '')), 512),
            ];
        }

        $hasAny = $large !== [] || $small !== null;

        return [
            'enabled' => $hasAny,
            'interval' => $interval,
            'large' => $large,
            'small' => $small,
            'is_public' => $isPublic,
            'countdown_ends_at' => $countdownEndsAt,
            'countdown_title' => $countdownTitle !== '' ? $countdownTitle : null,
        ];
    }
}
