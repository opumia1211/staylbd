<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Extension;
use App\Services\ExtensionValidationService;
use Illuminate\Http\Request;

class ExtensionController extends Controller
{
    /** Category list for filter tabs (same as Extension model getCategoryAttribute map). */
    public static function extensionCategories(): array
    {
        return [
            'all' => __('All'),
            'Analytics' => __('Analytics'),
            'Marketing' => __('Marketing'),
            'Chat' => __('Chat'),
            'Security' => __('Security'),
            'Custom' => __('Custom'),
            'Compliance' => __('Compliance'),
            'General' => __('General'),
        ];
    }

    public function index(Request $request)
    {
        $pageTitle = __('Extensions');
        $extensions = Extension::orderBy('name')->get();
        $categories = self::extensionCategories();
        $currentCategory = $request->get('category', 'all');
        return view('admin.extension.index', compact('pageTitle', 'extensions', 'categories', 'currentCategory'));
    }

    public function update(Request $request, $id)
    {
        $extension = Extension::findOrFail($id);
        $validationRule = [];
        foreach ($extension->shortcode as $key => $val) {
            $rule = in_array($key, ['custom_script', 'custom_script_head'], true) ? 'nullable|string' : 'required|string';
            $validationRule[$key] = $rule;
        }
        $request->validate($validationRule);

        $shortcode = json_decode(json_encode($extension->shortcode), true);
        foreach ($shortcode as $key => $value) {
            $shortcode[$key]['value'] = $request->$key;
        }

        $extension->shortcode = $shortcode;
        $extension->save();
        $notify[] = ['success', $extension->name . ' updated successfully'];
        return back()->withNotify($notify);
    }

    public function status($id)
    {
        $extension = Extension::findOrFail($id);
        $enabling = !$extension->status;

        if ($enabling) {
            $errors = ExtensionValidationService::validateEnable($extension);
            if (!empty($errors)) {
                $notify[] = ['error', __('Cannot enable: ') . implode(' ', $errors)];
                return back()->withNotify($notify);
            }
        } else {
            $dependents = ExtensionValidationService::getDependents($extension);
            if (!empty($dependents)) {
                $notify[] = ['error', __('Disable these first: ') . implode(', ', $dependents)];
                return back()->withNotify($notify);
            }
        }

        return Extension::changeStatus($id);
    }

    /** Show form to add new extension (so you can add more features from admin without code/SQL). */
    public function create()
    {
        $pageTitle = __('Add New Extension');
        $defaultImage = Extension::DEFAULT_IMAGE;
        return view('admin.extension.create', compact('pageTitle', 'defaultImage'));
    }

    /** Store new extension. Shortcode keys: one per line, format "key|Label" e.g. pixel_id|Pixel ID */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'act' => 'required|string|max:60|regex:/^[a-z0-9\-]+$/|unique:extensions,act',
            'description' => 'nullable|string|max:500',
            'script' => 'required|string',
            'shortcode_keys' => 'nullable|string',
            'image' => 'nullable|string|max:255',
        ]);

        $shortcode = [];
        $lines = array_filter(array_map('trim', explode("\n", $request->shortcode_keys ?? '')));
        foreach ($lines as $line) {
            if (strpos($line, '|') !== false) {
                [$key, $title] = array_map('trim', explode('|', $line, 2));
                $key = preg_replace('/[^a-z0-9_]/', '_', strtolower($key));
                if ($key !== '') {
                    $shortcode[$key] = ['title' => $title ?: $key, 'value' => ''];
                }
            } else {
                $key = preg_replace('/[^a-z0-9_]/', '_', strtolower($line));
                if ($key !== '') {
                    $shortcode[$key] = ['title' => $key, 'value' => ''];
                }
            }
        }

        Extension::create([
            'act' => $request->act,
            'name' => $request->name,
            'description' => $request->description ?? '',
            'image' => $request->image ?: Extension::DEFAULT_IMAGE,
            'script' => $request->script,
            'shortcode' => $shortcode,
            'support' => 'na',
            'status' => 0,
        ]);

        $notify[] = ['success', __('Extension added successfully. You can enable and configure it from the list.')];
        return redirect()->route('admin.extensions.index')->withNotify($notify);
    }

    /** Delete extension (optional – use with care). */
    public function delete($id)
    {
        $extension = Extension::findOrFail($id);
        $name = $extension->name;
        $extension->delete();
        $notify[] = ['success', $name . ' ' . __('removed.')];
        return back()->withNotify($notify);
    }
}
