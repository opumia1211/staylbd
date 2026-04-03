<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AutoResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class AutoResponseController extends Controller
{
    public function index()
    {
        $this->ensureAutoResponsesTable();
        $pageTitle = 'Auto AI Reply';
        $items = AutoResponse::orderBy('id', 'desc')->paginate(getPaginate());
        $totalRules = AutoResponse::count();
        $activeRules = AutoResponse::active()->count();
        $publicRules = AutoResponse::active()->where('is_public', true)->count();
        $totalKeywords = AutoResponse::get()->sum(fn ($r) => count($r->getKeywordsList()));
        return view('admin.auto_responses.index', compact('pageTitle', 'items', 'totalRules', 'activeRules', 'publicRules', 'totalKeywords'));
    }

    public function create()
    {
        $this->ensureAutoResponsesTable();
        $pageTitle = 'Add Auto AI Reply';
        return view('admin.auto_responses.create', compact('pageTitle'));
    }

    protected function ensureAutoResponsesTable(): void
    {
        if (Schema::hasTable('auto_responses')) {
            return;
        }
        if (config('app.env') === 'production') {
            return;
        }
        $migrationPath = base_path('database/migrations/2025_02_04_100005_create_auto_responses_table.php');
        if (is_file($migrationPath)) {
            try {
                Artisan::call('migrate', [
                    '--path' => 'database/migrations/2025_02_04_100005_create_auto_responses_table.php',
                    '--force' => true,
                ]);
            } catch (\Throwable $e) {
                // fallback to creating table directly
            }
        }
        if (!Schema::hasTable('auto_responses')) {
            Schema::create('auto_responses', function (Blueprint $table) {
                $table->id();
                $table->string('name', 191)->nullable();
                $table->string('trigger_type', 32)->comment('welcome, offline, keyword, scheduled');
                $table->string('keyword', 255)->nullable()->comment('Legacy single keyword');
                $table->json('keywords')->nullable()->comment('Multiple keywords; any match triggers reply');
                $table->text('message');
                $table->string('channel', 32)->nullable()->comment('Null = all channels');
                $table->json('config')->nullable()->comment('Business hours, schedule, etc.');
                $table->boolean('is_active')->default(true);
                $table->boolean('is_public')->default(true);
                $table->timestamps();
                $table->index(['trigger_type', 'is_active']);
            });
        }
    }

    /** Parse keywords string (comma or newline separated) into array of trimmed non-empty values */
    protected function parseKeywordsInput(?string $input): array
    {
        if ($input === null || $input === '') {
            return [];
        }
        $parts = preg_split('/[\s*,\n\r]+/u', $input, -1, PREG_SPLIT_NO_EMPTY);
        return array_values(array_unique(array_filter(array_map('trim', $parts))));
    }

    public function store(Request $request)
    {
        $this->ensureAutoResponsesTable();
        $request->validate([
            'keywords_input' => 'nullable|string|max:4000',
            'message' => 'required|string|max:2000',
            'name' => 'nullable|string|max:191',
            'is_active' => 'nullable|in:0,1',
            'is_public' => 'nullable|in:0,1',
        ]);

        $keywordsArray = $this->parseKeywordsInput($request->keywords_input);
        if (count($keywordsArray) === 0) {
            $notify[] = ['error', __('Add at least one keyword (one per line or comma-separated).')];
            return back()->withInput()->withNotify($notify);
        }

        $firstKeyword = $keywordsArray[0];
        AutoResponse::create([
            'name' => $request->filled('name') ? trim($request->name) : null,
            'trigger_type' => AutoResponse::TRIGGER_KEYWORD,
            'keyword' => $firstKeyword,
            'keywords' => $keywordsArray,
            'message' => trim($request->message),
            'channel' => null,
            'is_active' => (bool) $request->input('is_active', 0),
            'is_public' => (int) $request->input('is_public', 1) === 1,
        ]);

        $notify[] = ['success', __('Auto AI reply created successfully.')];
        return to_route('admin.autoai.index')->withNotify($notify);
    }

    public function edit($id)
    {
        $this->ensureAutoResponsesTable();
        $item = AutoResponse::findOrFail($id);
        $pageTitle = 'Edit Auto AI Reply';
        return view('admin.auto_responses.edit', compact('pageTitle', 'item'));
    }

    public function update(Request $request, $id)
    {
        $item = AutoResponse::findOrFail($id);
        $request->validate([
            'keywords_input' => 'nullable|string|max:4000',
            'message' => 'required|string|max:2000',
            'name' => 'nullable|string|max:191',
            'is_active' => 'nullable|in:0,1',
            'is_public' => 'nullable|in:0,1',
        ]);

        $keywordsArray = $this->parseKeywordsInput($request->keywords_input);
        if (count($keywordsArray) === 0) {
            $notify[] = ['error', __('Add at least one keyword (one per line or comma-separated).')];
            return back()->withInput()->withNotify($notify);
        }

        $item->update([
            'name' => $request->filled('name') ? trim($request->name) : null,
            'keyword' => $keywordsArray[0],
            'keywords' => $keywordsArray,
            'message' => trim($request->message),
            'is_active' => (bool) $request->input('is_active', 0),
            'is_public' => (int) $request->input('is_public', 1) === 1,
        ]);

        $notify[] = ['success', __('Auto AI reply updated successfully.')];
        return to_route('admin.autoai.index')->withNotify($notify);
    }

    public function destroy($id)
    {
        $this->ensureAutoResponsesTable();
        $item = AutoResponse::findOrFail($id);
        $item->delete();
        $notify[] = ['success', __('Auto AI reply deleted successfully.')];
        return to_route('admin.autoai.index')->withNotify($notify);
    }

    /** Toggle Active/Inactive from list page */
    public function toggleActive($id)
    {
        $item = AutoResponse::findOrFail($id);
        $item->update(['is_active' => !$item->is_active]);
        $status = $item->is_active ? __('Active') : __('Inactive');
        $notify[] = ['success', __('Status set to :status.', ['status' => $status])];
        return back()->withNotify($notify);
    }

    /** Toggle Public/Private from list page */
    public function toggleVisibility($id)
    {
        $item = AutoResponse::findOrFail($id);
        $item->update(['is_public' => !$item->is_public]);
        $visibility = $item->is_public ? __('Public') : __('Private');
        $notify[] = ['success', __('Visibility set to :visibility.', ['visibility' => $visibility])];
        return back()->withNotify($notify);
    }
}
