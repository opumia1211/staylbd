<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\HomepageCustomProductRow;
use App\Services\HomepageDataService;
use App\Services\HomepageLayoutService;
use Illuminate\Http\Request;
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
        $data = $this->validatedData($request);
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
        $row->update($this->validatedData($request));
        HomepageLayoutService::persistLayoutAfterCustomRowChange();
        HomepageDataService::clearCache();
        HomepageDataService::clearBelowFoldFragmentCache();
        $notify[] = ['success', __('Row updated. Homepage cache cleared.')];

        return redirect()->route('admin.frontend.sections.homepageCustomRows')
            ->withNotify($notify)
            ->with('hp_highlight_row', $id);
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
}
