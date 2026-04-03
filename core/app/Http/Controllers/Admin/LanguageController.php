<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Frontend;
use Illuminate\Http\Request;
use App\Models\Language;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class LanguageController extends Controller
{
    private const LANG_KEYS_CACHE_KEY = 'lang.possible.keys';
    private const LANG_KEYS_CACHE_TTL = 300; // 5 min
    private const JSON_ENCODE_OPTIONS = \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES;

    /**
     * Language Manager - Add, edit, delete languages. Translations in JSON; A–Z coverage for site + admin.
     */
    public function langManage($lang = false)
    {
        $pageTitle = 'Language Manager';
        $languages = Language::orderByDesc('is_default')->orderBy('name')->get();
        $emptyMessage = 'No language added yet';
        $defaultLang = $languages->where('is_default', Status::YES)->first();
        return view('admin.language.lang', compact('pageTitle', 'languages', 'emptyMessage', 'defaultLang'));
    }

    public function langStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:40',
            'code' => 'required|string|max:40|unique:languages,code'
        ]);

        $code = strtolower($request->code);
        $basePath = resource_path('lang/');
        $source = $basePath . 'en.json';
        $target = $basePath . $code . '.json';

        $arr = is_file($source) ? (json_decode(file_get_contents($source), true) ?? []) : [];
        if (!is_array($arr)) {
            $arr = [];
        }
        File::put($target, json_encode($arr, self::JSON_ENCODE_OPTIONS));

        if ($request->is_default) {
            Language::where('is_default', Status::YES)->update(['is_default' => Status::NO]);
            Cache::forget('app.default_lang_code');
        }

        Language::create([
            'name' => $request->name,
            'code' => $code,
            'is_default' => $request->is_default ? Status::YES : Status::NO,
        ]);

        Cache::forget(self::LANG_KEYS_CACHE_KEY);
        $notify[] = ['success', 'Language added successfully'];
        return back()->withNotify($notify);
    }

    public function langUpdate(Request $request, $id)
    {
        $request->validate(['name' => 'required|string|max:40']);

        $language = Language::findOrFail($id);
        if (!$request->is_default && !Language::where('is_default', Status::YES)->where('id', '!=', $id)->exists()) {
            $notify[] = ['error', 'Set another language as default first'];
            return back()->withNotify($notify);
        }

        $language->name = $request->name;
        $language->is_default = $request->is_default ? Status::YES : Status::NO;
        $language->save();

        if ($request->is_default) {
            Language::where('id', '!=', $language->id)->where('is_default', Status::YES)->update(['is_default' => Status::NO]);
            Cache::forget('app.default_lang_code');
        }
        $notify[] = ['success', 'Update successfully'];
        return back()->withNotify($notify);
    }

    public function langDelete($id)
    {
        $lang = Language::findOrFail($id);
        $path = resource_path('lang/') . $lang->code . '.json';
        if (is_file($path)) {
            File::delete($path);
        }
        $lang->delete();
        Cache::forget('app.default_lang_code');
        $notify[] = ['success', 'Language deleted successfully'];
        return back()->withNotify($notify);
    }

    public function langEdit($id)
    {
        $lang = Language::findOrFail($id);
        $pageTitle = 'Translate: ' . $lang->name;
        $json = $this->readLangJson($lang->code);
        if ($json === null) {
            $notify[] = ['error', 'Language file not found'];
            return back()->withNotify($notify);
        }
        $list_lang = Language::orderByDesc('is_default')->orderBy('name')->get();
        $emptyMessage = 'No keywords yet';
        $keyCount = count($json);
        return view('admin.language.edit_lang', compact('pageTitle', 'json', 'lang', 'list_lang', 'emptyMessage', 'keyCount'));
    }

    /** Set admin panel session locale and redirect back so admin can use the site in this language immediately. */
    public function setSessionLocale(Request $request)
    {
        $request->validate(['code' => 'required|string|max:10']);
        $code = strtolower(trim($request->code));
        if (!Language::where('code', $code)->exists()) {
            $notify[] = ['error', 'Invalid language'];
            return back()->withNotify($notify);
        }
        session()->put('lang', $code);
        app()->setLocale($code);
        $notify[] = ['success', 'Language switched. Admin panel will use this language now.'];
        return back()->withNotify($notify);
    }

    public function langImport(Request $request)
    {
        $request->validate(['id' => 'required', 'toLangid' => 'required|exists:languages,id']);
        $toLang = Language::findOrFail($request->toLangid);
        $existing = $this->readLangJson($toLang->code);
        if ($existing === null) {
            $existing = [];
        }

        if ((int) $request->id !== 999) {
            $fromLang = Language::findOrFail($request->id);
            $keywords = $this->readLangJson($fromLang->code);
            $keywords = is_array($keywords) ? array_keys($keywords) : [];
        } else {
            $keywords = array_filter(array_map('trim', explode("\n", $this->getKeysText())));
        }

        $added = 0;
        foreach ($keywords as $keyword) {
            $keyword = trim($keyword);
            if ($keyword !== '' && !array_key_exists($keyword, $existing)) {
                $existing[$keyword] = $keyword;
                $added++;
            }
        }
        if ($added > 0) {
            $this->writeLangJson($toLang->code, $existing);
        }
        return response('success', 200, ['Content-Type' => 'text/plain']);
    }

    public function storeLanguageJson(Request $request, $id)
    {
        $request->validate(['key' => 'required|string', 'value' => 'required']);
        $lang = Language::findOrFail($id);
        $data = $this->readLangJson($lang->code);
        if ($data === null) {
            $data = [];
        }
        $key = trim($request->key);
        if (array_key_exists($key, $data)) {
            $notify[] = ['error', 'Key already exists'];
            return back()->withNotify($notify);
        }
        $data[$key] = trim($request->value);
        $this->writeLangJson($lang->code, $data);
        $notify[] = ['success', 'Language key added successfully'];
        return back()->withNotify($notify);
    }

    public function deleteLanguageJson(Request $request, $id)
    {
        $request->validate(['key' => 'required']);
        $lang = Language::findOrFail($id);
        $data = $this->readLangJson($lang->code);
        if (is_array($data)) {
            unset($data[$request->key]);
            $this->writeLangJson($lang->code, $data);
        }
        $notify[] = ['success', 'Language key deleted successfully'];
        return back()->withNotify($notify);
    }

    public function updateLanguageJson(Request $request, $id)
    {
        $request->validate(['key' => 'required', 'value' => 'required']);
        $lang = Language::findOrFail($id);
        $data = $this->readLangJson($lang->code);
        if (!is_array($data)) {
            $data = [];
        }
        $data[trim($request->key)] = $request->value;
        $this->writeLangJson($lang->code, $data);
        $notify[] = ['success', 'Language key updated successfully'];
        return back()->withNotify($notify);
    }

    /** Read language JSON; returns associative array or null on failure. */
    private function readLangJson(string $code): ?array
    {
        $path = resource_path('lang/') . strtolower($code) . '.json';
        if (!is_file($path) || !is_readable($path)) {
            return null;
        }
        $raw = @file_get_contents($path);
        if ($raw === false) {
            return null;
        }
        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : [];
    }

    /** Write language JSON with Unicode support (Bengali, etc.). */
    private function writeLangJson(string $code, array $data): bool
    {
        $path = resource_path('lang/') . strtolower($code) . '.json';
        $encoded = json_encode($data, self::JSON_ENCODE_OPTIONS);
        return file_put_contents($path, $encoded) !== false;
    }

    /** Return key list as plain text for AJAX (Language Keywords modal). Cached for speed. */
    public function getKeys()
    {
        $keyText = Cache::remember(self::LANG_KEYS_CACHE_KEY, self::LANG_KEYS_CACHE_TTL, function () {
            return $this->getKeysText();
        });
        return response($keyText, 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }

    /** Invalidate keys cache (e.g. after adding new views). */
    public function invalidateKeysCache()
    {
        Cache::forget(self::LANG_KEYS_CACHE_KEY);
        return response()->json(['ok' => true], 200);
    }

    /**
     * Collect language keys from .php + .blade.php views and frontend data; newline-separated.
     * Used for import "System" and for keyword list.
     */
    protected function getKeysText(): string
    {
        try {
            $langKeys = [];
            $viewsDir = resource_path('views');
            $extensions = ['php', 'blade.php'];
            $files = $this->getAllFiles($viewsDir, $extensions);
            foreach ($files as $path) {
                $langKeys = array_merge($langKeys, $this->getLangKeysFromFile($path));
            }
            $frontendData = Frontend::where('data_keys', '!=', 'seo.data')->get(['data_values']);
            foreach ($frontendData as $frontend) {
                $dataValues = $frontend->data_values ?? [];
                if (!is_array($dataValues)) {
                    continue;
                }
                foreach ($dataValues as $key => $frontendValue) {
                    if ($key !== 'has_image' && is_string($frontendValue) && !isImage($frontendValue) && !isHtml($frontendValue)) {
                        $langKeys[] = $frontendValue;
                    }
                }
            }
            $langKeys = array_unique(array_filter(array_map('trim', $langKeys)));
            sort($langKeys);
            return implode("\n", $langKeys);
        } catch (\Throwable $e) {
            return '';
        }
    }

    /** Get all files in directory (recursive) with given extensions. */
    private function getAllFiles(string $dir, array $extensions = ['php', 'blade.php']): array
    {
        $files = [];
        if (!is_dir($dir)) {
            return $files;
        }
        $extSet = array_flip(array_map('strtolower', $extensions));
        try {
            $iter = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS | \RecursiveDirectoryIterator::FOLLOW_SYMLINKS),
                \RecursiveIteratorIterator::SELF_FIRST,
                \RecursiveIteratorIterator::CATCH_GET_CHILD
            );
            foreach ($iter as $path => $fileInfo) {
                if (!$fileInfo->isFile()) {
                    continue;
                }
                $base = $fileInfo->getFilename();
                $ext = strtolower($fileInfo->getExtension());
                $isBlade = str_ends_with($base, '.blade.php');
                if ($isBlade || isset($extSet[$ext])) {
                    $files[] = $path;
                }
            }
        } catch (\Throwable $e) {
            // skip on permission error
        }
        return $files;
    }

    /** Extract @lang('key') and __('key') from file content. */
    private function getLangKeysFromFile(string $path): array
    {
        if (!is_readable($path)) {
            return [];
        }
        $code = @file_get_contents($path);
        if ($code === false || $code === '') {
            return [];
        }
        $keys = [];
        if (preg_match_all('/@lang\s*\(\s*[\'"]([^\'"]*)[\'"]\s*\)/', $code, $m)) {
            $keys = array_merge($keys, $m[1]);
        }
        if (preg_match_all('/__\s*\(\s*[\'"]([^\'"]*)[\'"]\s*\)/', $code, $m)) {
            $keys = array_merge($keys, $m[1]);
        }
        return array_filter(array_map('trim', $keys));
    }
}
