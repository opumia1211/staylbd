<?php

namespace App\Http\Controllers\Admin;

use App\Models\Frontend;
use App\Models\Division;
use App\Models\District;
use App\Models\Thana;
use App\Services\ScrollbarService;
use Illuminate\Support\Facades\Schema;
use App\Services\BannerService;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;

class FrontendController extends Controller
{

    public function templates()
    {
        $pageTitle = 'Templates';
        $templatesPath = resource_path('views/templates');
        $temPaths = is_dir($templatesPath) ? array_filter(glob($templatesPath . '/*'), 'is_dir') : [];
        $templates = [];
        $customPreviewBase = base_path('../assets/images/template-previews');
        foreach ($temPaths as $key => $temp) {
            $tempname = basename($temp);
            $customPreviewPath = $customPreviewBase . DIRECTORY_SEPARATOR . $tempname . '.jpg';
            $customPreviewPathAlt = $customPreviewBase . DIRECTORY_SEPARATOR . $tempname . '.png';
            $defaultPreviewPath = $temp . DIRECTORY_SEPARATOR . 'preview.jpg';
            $hasCustomPreview = false;
            $imageUrl = route('placeholder.image', '400x300');
            if (file_exists($customPreviewPath)) {
                $imageUrl = asset('assets/images/template-previews/' . $tempname . '.jpg') . '?v=' . filemtime($customPreviewPath);
                $hasCustomPreview = true;
            } elseif (file_exists($customPreviewPathAlt)) {
                $imageUrl = asset('assets/images/template-previews/' . $tempname . '.png') . '?v=' . filemtime($customPreviewPathAlt);
                $hasCustomPreview = true;
            } elseif (file_exists($defaultPreviewPath)) {
                $imageUrl = route('admin.frontend.template.preview', $tempname);
            }
            $templates[] = [
                'name' => $tempname,
                'image' => $imageUrl,
                'hasCustomPreview' => $hasCustomPreview,
            ];
        }
        $templatesSource = getTemplates();
        if (is_string($templatesSource)) {
            $extraTemplates = json_decode($templatesSource, true) ?: [];
        } elseif (is_array($templatesSource)) {
            $extraTemplates = $templatesSource;
        } else {
            $extraTemplates = [];
        }
        return view('admin.frontend.templates', compact('pageTitle', 'templates', 'extraTemplates'));
    }

    /**
     * Division → District → Thana (from DB). Labels from Frontend.
     */
    public function district(Request $request)
    {
        $pageTitle = 'Division / District / Thana (Checkout)';
        $row = Frontend::where('data_keys', 'district.settings')->first();
        $data = $row && isset($row->data_values) ? (object) $row->data_values : (object) [];
        $labels = (object) [
            'label_en' => $data->label_en ?? 'District',
            'label_bn' => $data->label_bn ?? 'জেলা',
            'help_en' => $data->help_en ?? 'All Bangladesh districts — select for delivery charge',
            'help_bn' => $data->help_bn ?? 'বাংলাদেশের সব জেলা — ডেলিভারি চার্জের জন্য নির্বাচন করুন',
        ];
        $useDb = Schema::hasTable('divisions') && Schema::hasTable('districts');
        $divisions = $useDb ? Division::orderBy('sort_order')->orderBy('name_en')->get() : collect();
        $districts = $useDb ? District::with('division')->orderBy('division_id')->orderBy('sort_order')->orderBy('name_en')->get() : collect();
        $divisionId = (int) $request->get('division_id');
        $districtId = (int) $request->get('district_id');

        // When DB has divisions but no division selected, redirect to first division so page isn't blank
        if ($useDb && $divisions->isNotEmpty() && !$divisionId) {
            return redirect()->route('admin.frontend.sections.district', ['division_id' => $divisions->first()->id]);
        }

        $thanas = collect();
        if ($districtId && Schema::hasTable('thanas')) {
            $thanas = Thana::where('district_id', $districtId)->orderBy('sort_order')->orderBy('name_en')->get();
        }
        $selectedDistrict = $districtId ? District::find($districtId) : null;
        return view('admin.frontend.district', compact('pageTitle', 'labels', 'useDb', 'divisions', 'districts', 'divisionId', 'districtId', 'thanas', 'selectedDistrict'));
    }

    public function districtUpdate(Request $request)
    {
        $request->validate([
            'label_en' => 'nullable|string|max:100',
            'label_bn' => 'nullable|string|max:100',
            'help_en' => 'nullable|string|max:300',
            'help_bn' => 'nullable|string|max:300',
        ]);
        if ($request->has('label_en') || $request->has('label_bn')) {
            $row = Frontend::where('data_keys', 'district.settings')->first();
            $data = $row && isset($row->data_values) ? (array) $row->data_values : [];
            Frontend::updateOrCreate(
                ['data_keys' => 'district.settings'],
                [
                    'data_values' => array_merge($data, [
                        'label_en' => $request->input('label_en', $data['label_en'] ?? 'District'),
                        'label_bn' => $request->input('label_bn', $data['label_bn'] ?? 'জেলা'),
                        'help_en' => $request->input('help_en', ''),
                        'help_bn' => $request->input('help_bn', ''),
                    ])
                ]
            );
        }
        $districtId = (int) $request->input('district_id');
        if (Schema::hasTable('thanas') && $districtId && $request->has('thanas') && is_array($request->thanas)) {
            $district = District::find($districtId);
            if ($district) {
                $list = [];
                foreach ($request->thanas as $r) {
                    $en = trim($r['en'] ?? '');
                    $bn = trim($r['bn'] ?? '');
                    if ($en !== '' || $bn !== '') {
                        $list[] = ['name_en' => $en ?: $bn, 'name_bn' => $bn ?: $en];
                    }
                }
                Thana::where('district_id', $districtId)->delete();
                $sort = 0;
                foreach ($list as $t) {
                    Thana::create([
                        'district_id' => $districtId,
                        'name_en' => $t['name_en'],
                        'name_bn' => $t['name_bn'],
                        'sort_order' => ++$sort,
                    ]);
                }
            }
        }
        if (Schema::hasTable('divisions') && $request->has('divisions') && is_array($request->divisions)) {
            foreach ($request->divisions as $id => $d) {
                $id = (int) $id;
                $en = trim($d['en'] ?? '');
                $bn = trim($d['bn'] ?? '');
                if ($id && ($en !== '' || $bn !== '')) {
                    Division::where('id', $id)->update(['name_en' => $en ?: $bn, 'name_bn' => $bn ?: $en]);
                }
            }
        }
        if (Schema::hasTable('districts') && $request->has('districts') && is_array($request->districts)) {
            foreach ($request->districts as $id => $d) {
                $id = (int) $id;
                $en = trim($d['en'] ?? '');
                $bn = trim($d['bn'] ?? '');
                if ($id && ($en !== '' || $bn !== '')) {
                    District::where('id', $id)->update(['name_en' => $en ?: $bn, 'name_bn' => $bn ?: $en]);
                }
            }
        }
        Cache::forget('GeneralSetting');
        $notify[] = ['success', 'Settings updated.'];
        $back = back();
        if ($districtId) {
            $district = District::find($districtId);
            if ($district) {
                $back = redirect()->route('admin.frontend.sections.district', ['division_id' => $district->division_id, 'district_id' => $districtId]);
            }
        }
        return $back->withNotify($notify);
    }

    public function templatePreview($name)
    {
        $path = resource_path('views/templates/' . $name . '/preview.jpg');
        if (!file_exists($path) || !is_readable($path)) {
            abort(404);
        }
        return response()->file($path, ['Content-Type' => 'image/jpeg']);
    }

    public function templatePreviewUpload(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'preview' => 'required|file|mimes:jpg,jpeg,png|max:2048',
        ]);
        $name = preg_replace('/[^a-zA-Z0-9_-]/', '', $request->name);
        if (empty($name)) {
            $notify[] = ['error', 'Invalid template name'];
            return back()->withNotify($notify);
        }
        $path = base_path('../assets/images/template-previews');
        if (!is_dir($path)) {
            if (!@mkdir($path, 0755, true)) {
                $notify[] = ['error', 'Could not create preview directory'];
                return back()->withNotify($notify);
            }
        }
        $ext = strtolower($request->file('preview')->getClientOriginalExtension());
        $ext = in_array($ext, ['jpg', 'jpeg']) ? 'jpg' : $ext;
        $filename = $name . '.' . $ext;
        $targetPath = $path . DIRECTORY_SEPARATOR . $filename;
        foreach (['.jpg', '.jpeg', '.png'] as $oldExt) {
            $oldPath = $path . DIRECTORY_SEPARATOR . $name . $oldExt;
            if (file_exists($oldPath)) {
                @unlink($oldPath);
            }
        }
        if (!$request->file('preview')->move($path, $filename)) {
            $notify[] = ['error', 'Upload failed'];
            return back()->withNotify($notify);
        }
        $notify[] = ['success', 'Preview image updated successfully'];
        return back()->withNotify($notify);
    }

    public function templatePreviewReset(Request $request)
    {
        $name = preg_replace('/[^a-zA-Z0-9_-]/', '', $request->name ?? '');
        if (empty($name)) {
            $notify[] = ['error', 'Invalid template name'];
            return back()->withNotify($notify);
        }
        $path = base_path('../assets/images/template-previews');
        $deleted = false;
        foreach (['.jpg', '.jpeg', '.png'] as $ext) {
            $file = $path . DIRECTORY_SEPARATOR . $name . $ext;
            if (file_exists($file) && @unlink($file)) {
                $deleted = true;
            }
        }
        if ($deleted) {
            $notify[] = ['success', 'Preview image reset to default'];
        } else {
            $notify[] = ['info', 'No custom preview found'];
        }
        return back()->withNotify($notify);
    }

    public function templatesActive(Request $request)
    {
        $general = gs();

        $general->active_template = $request->name;
        $general->save();

        $notify[] = ['success', strtoupper($request->name) . ' template activated successfully'];
        return back()->withNotify($notify);
    }

    public function seoEdit()
    {
        $pageTitle = 'SEO Configuration';
        $seo = Frontend::where('data_keys', 'seo.data')->first();
        if (!$seo) {
            $data_values = '{"keywords":[],"description":"","social_title":"","social_description":"","image":null}';
            $data_values = json_decode($data_values, true);
            $frontend = new Frontend();
            $frontend->data_keys = 'seo.data';
            $frontend->data_values = $data_values;
            $frontend->save();
        }
        return view('admin.frontend.seo', compact('pageTitle', 'seo'));
    }




    public function frontendContent(Request $request, $key = null)
    {
        // Get key from route parameter or route name
        if ($key === null) {
            $key = request()->route('key');
            if ($key === null) {
                $routeName = request()->route()->getName();
                if ($routeName && strpos($routeName, 'content.') !== false) {
                    $routeKey = str_replace('admin.frontend.sections.content.', '', $routeName);
                    $key = $this->mapRouteToKey($routeKey);
                }
            }
        }

        // Map clean route names to internal keys
        $key = $this->mapRouteToKey($key);

        $type = $request->type;
        if (!$type) {
            abort(404);
        }

        $purifier = new \HTMLPurifier();
        $inputContentValue = [];
        $valInputs = $request->except('_token', 'image_input', 'key', 'status', 'type', 'id');
        foreach ($valInputs as $keyName => $input) {
            if (gettype($input) == 'array') {
                $inputContentValue[$keyName] = $input;
                continue;
            }
            // Social Icons: icon picker sends <i class="fab fa-facebook"></i> — HTMLPurifier often strips it → empty footer icons.
            if ($keyName === 'icon' && $type === 'element' && $key === 'social_icon') {
                $raw = is_string($input) ? trim($input) : '';
                if ($raw === '') {
                    $inputContentValue[$keyName] = '';
                } elseif (preg_match('/<i\b[^>]*\bclass\s*=\s*["\']([^"\']+)["\']/i', $raw, $m)) {
                    $inputContentValue[$keyName] = trim(preg_replace('/\s+/', ' ', html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5, 'UTF-8')));
                } else {
                    $t = trim(strip_tags(html_entity_decode($raw, ENT_QUOTES | ENT_HTML5, 'UTF-8')));
                    $inputContentValue[$keyName] = $t;
                }
                continue;
            }
            if ($keyName === 'custom_icon_svg' && $type === 'element' && $key === 'social_icon') {
                $inputContentValue[$keyName] = sanitizeSocialIconInlineMarkup(is_string($input) ? $input : '');
                continue;
            }
            $inputContentValue[$keyName] = $purifier->purify($input);
        }
        $imgJson = @getPageSections()->$key->$type->images;
        $validationRule = [];
        $validation_message = [];

        // Handle image validation - check if images exist in section config
        if ($imgJson) {
            foreach ($imgJson as $imgValKey => $imgJsonVal) {
                // Professional Banner: 5MB max, allowed formats, MIME check
                if ($key == 'banner') {
                    $allowedTypes = BannerService::ALLOWED_EXTENSIONS;
                    $validationRule['image_input.' . $imgValKey] = ['nullable', 'file', 'max:5120', new FileTypeValidate($allowedTypes)];
                    $validation_message['image_input.' . $imgValKey . '.max'] = 'Banner file must not exceed 5MB.';
                } elseif ($key === 'contact_us' && $type === 'content' && in_array($imgValKey, ['contact_phone_icon', 'contact_email_icon'], true)) {
                    $validationRule['image_input.' . $imgValKey] = ['nullable', 'file', 'max:2048', new FileTypeValidate(['jpg', 'jpeg', 'png', 'webp', 'svg'])];
                    $validation_message['image_input.' . $imgValKey . '.max'] = 'Contact icon must not exceed 2MB.';
                } elseif ($key === 'social_icon' && $type === 'element') {
                    $validationRule['image_input.' . $imgValKey] = ['nullable', 'file', 'max:2048', new FileTypeValidate(['jpg', 'jpeg', 'png', 'webp', 'svg'])];
                    $validation_message['image_input.' . $imgValKey . '.max'] = 'Custom logo must not exceed 2MB.';
                } else {
                    $validationRule['image_input.' . $imgValKey] = ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])];
                }
                $validation_message['image_input.' . $imgValKey . '.file'] = keyToTitle($imgValKey) . ' must be a valid file';
            }
        }

        // For banner: require image when (a) creating new, or (b) updating empty slot (first upload)
        if ($key == 'banner' && $type == 'element' && isset($imgJson->image)) {
            $hasFile = $request->hasFile('image_input.image');
            $needImage = false;
            if (!$request->id) {
                $needImage = true; // New record
            } else {
                $existing = Frontend::find($request->id);
                if ($existing && empty($existing->data_values->image ?? null)) {
                    $needImage = true; // Empty slot - first upload
                }
            }
            if ($needImage && !$hasFile) {
                $validationRule['image_input.image'] = ['required', 'file', 'max:5120', new FileTypeValidate(['jpg', 'jpeg', 'png', 'webp'])];
            }
        }

        // Handle other fields validation
        foreach ($request->except('_token', 'video', 'image_input', 'has_image', 'has_image[]') as $input_field => $val) {
            if ($input_field == 'seo_image') {
                $validationRule['image_input'] = ['nullable', 'image', new FileTypeValidate(['jpeg', 'jpg', 'png', 'webp'])];
                continue;
            }
            // Make display_order and url optional for banner
            if ($input_field == 'display_order') {
                $validationRule[$input_field] = 'nullable|integer|min:1|max:50';
            } elseif ($input_field == 'url') {
                if ($key === 'social_icon' && $type === 'element') {
                    $validationRule[$input_field] = 'nullable|string|max:2000';
                } else {
                    $validationRule[$input_field] = 'nullable|url';
                }
            } elseif ($input_field == 'animation_type') {
                // Animation type is required for banner
                if ($key == 'banner') {
                    $validationRule[$input_field] = 'required|string|in:none,fadeIn,slideInLeft,slideInRight,slideInUp,slideInDown,zoomIn,rotateIn,bounceIn,flipInX,flipInY,lightSpeedIn,rollIn';
                } else {
                    $validationRule[$input_field] = 'nullable|string';
                }
            } elseif (in_array($input_field, ['banner_width', 'banner_height']) && $key == 'banner') {
                $validationRule[$input_field] = 'nullable|integer|min:50|max:4000';
            } elseif ($key == 'banner' && $type == 'content' && $input_field == 'slide_interval_seconds') {
                $validationRule[$input_field] = 'nullable|integer|min:1|max:60';
            } elseif ($key == 'banner' && $type == 'content' && $input_field == 'autoplay') {
                $validationRule[$input_field] = 'nullable|in:0,1';
            } elseif ($key == 'banner' && $type == 'content' && in_array($input_field, ['banner_width', 'banner_height'])) {
                $validationRule[$input_field] = 'nullable|integer|min:50|max:4000';
            } elseif ($key == 'register' && $type == 'content' && $input_field == 'registration_fields') {
                // Admin register: registration_fields is an array of field keys => 0|1
                $validationRule[$input_field] = 'nullable|array';
            } elseif ($key == 'register' && $type == 'content' && $input_field == 'profile_fields') {
                $validationRule[$input_field] = 'nullable|array';
            } elseif ($key == 'register' && $type == 'content' && $input_field == 'login_captcha_enabled') {
                // Login form: show captcha on floating & full-page login
                $validationRule[$input_field] = 'nullable|in:0,1';
            } elseif ($key == 'login' && $type == 'content' && $input_field == 'login_fields') {
                $validationRule[$input_field] = 'nullable|array';
            } elseif ($key == 'login' && $type == 'content' && $input_field == 'captcha_enabled') {
                $validationRule[$input_field] = 'nullable|in:0,1';
            } elseif ($key == 'login' && $type == 'content' && $input_field == 'social_login_buttons') {
                $validationRule[$input_field] = 'nullable|array';
            } elseif ($key === 'social_icon' && $type === 'element' && $input_field === 'custom_icon_svg') {
                $validationRule[$input_field] = 'nullable|string|max:65535';
            } elseif ($key === 'social_icon' && $type === 'element' && $input_field === 'show_on_public') {
                $validationRule[$input_field] = 'nullable|in:0,1';
            } else {
                // Other text fields
                $validationRule[$input_field] = 'nullable|string';
            }
        }

        // Only validate if there are rules
        if (!empty($validationRule)) {
            try {
                $request->validate($validationRule, $validation_message);
            } catch (\Illuminate\Validation\ValidationException $e) {
                $notify[] = ['error', $e->getMessage()];
                return back()->withNotify($notify)->withInput();
            }
        }
        $expectedDataKeys = $key . '.' . $request->type;
        if ($request->id) {
            $content = Frontend::findOrFail($request->id);
            // Ensure we don't update wrong section when id is passed
            if ($content->data_keys !== $expectedDataKeys) {
                $content = Frontend::where('data_keys', $expectedDataKeys)->orderBy('id', 'desc')->first();
                if (!$content) {
                    $content = new Frontend();
                    $content->data_keys = $expectedDataKeys;
                    $content->data_values = (object) [];
                    $content->save();
                }
            }
        } else {
            // Sections with multiple rows per data_keys (… .element): without id, always create NEW record.
            // Otherwise the latest row is reused and "Add new" overwrites instead of appending (e.g. social_icon).
            $multiElementKeys = ['banner', 'contact_us', 'social_icon', 'policy_pages', 'service', 'footer'];
            if (in_array($key, $multiElementKeys, true) && $request->type == 'element') {
                $content = new Frontend();
                $content->data_keys = $key . '.element';
                $content->data_values = (object) [];
                $content->save();
            } else {
                $content = Frontend::where('data_keys', $expectedDataKeys)->orderBy('id', 'desc')->first();
                if (!$content) {
                    $content = new Frontend();
                    $content->data_keys = $expectedDataKeys;
                    $content->data_values = (object) [];
                    $content->save();
                }
            }
        }
        if ($type == 'data') {
            $inputContentValue['image'] = @$content->data_values->image;
            if ($request->hasFile('image_input')) {
                try {
                    $inputContentValue['image'] = fileUploader($request->image_input, getFilePath('seo'), getFileSize('seo'), @$content->data_values->image);
                } catch (\Exception $exp) {
                    $notify[] = ['error', 'Couldn\'t upload the image'];
                    return back()->withNotify($notify);
                }
            }
        } else {
            if ($imgJson) {
                foreach ($imgJson as $imgKey => $imgValue) {
                    $removeRequested = (string) $request->input('remove_image.' . $imgKey, '0') === '1';
                    // Check if file is uploaded for this image key
                    if ($request->hasFile('image_input.' . $imgKey)) {
                        try {
                            $uploadedFile = $request->file('image_input.' . $imgKey);
                            // Ensure directory exists
                            $uploadPath = public_path('assets/images/frontend/' . $key);
                            if (!is_dir($uploadPath)) {
                                mkdir($uploadPath, 0755, true);
                            }

                            // Professional banner: store in desktop/ or mobile/ + thumb/; 5MB validated above
                            if ($key == 'banner') {
                                $sub = ($imgKey === 'mobile_image') ? BannerService::MOBILE_DIR : BannerService::DESKTOP_DIR;
                                $inputContentValue[$imgKey] = $this->storeBannerImage($uploadedFile, @$content->data_values->$imgKey, $sub);
                            } else {
                                $inputContentValue[$imgKey] = $this->storeImage($imgJson, $type, $key, $uploadedFile, $imgKey, @$content->data_values->$imgKey);
                            }
                        } catch (\Exception $exp) {
                            $notify[] = ['error', 'Couldn\'t upload the image: ' . $exp->getMessage()];
                            return back()->withNotify($notify)->withInput();
                        }
                    } else if ($removeRequested && $key === 'social_icon' && $type === 'element') {
                        $oldImage = trim((string) (@$content->data_values->$imgKey ?? ''));
                        if ($oldImage !== '') {
                            @unlink(public_path('assets/images/frontend/' . $key . '/' . $oldImage));
                        }
                        $inputContentValue[$imgKey] = '';
                    } else if ($removeRequested && $key === 'contact_us' && $type === 'content' && in_array($imgKey, ['contact_phone_icon', 'contact_email_icon'], true)) {
                        $oldImage = trim((string) (@$content->data_values->$imgKey ?? ''));
                        if ($oldImage !== '') {
                            @unlink(public_path('assets/images/frontend/' . $key . '/' . $oldImage));
                        }
                        $inputContentValue[$imgKey] = '';
                    } else if (isset($content->data_values->$imgKey)) {
                        // Keep existing image if no new file uploaded
                        $inputContentValue[$imgKey] = $content->data_values->$imgKey;
                    }
                }
            }
        }
        // Professional banner element: animation, layout, schedule, visibility, overlay content
        if ($key == 'banner' && $type == 'element') {
            if (!isset($inputContentValue['animation_type']) || empty($inputContentValue['animation_type'])) {
                $inputContentValue['animation_type'] = 'none';
            }
            $existing = $request->id && $content && $content->data_values ? (array) $content->data_values : [];
            foreach (['url', 'display_order', 'layout_type', 'start_date', 'end_date', 'visibility', 'is_active'] as $preserveKey) {
                if (!array_key_exists($preserveKey, $inputContentValue) && isset($existing[$preserveKey])) {
                    $inputContentValue[$preserveKey] = $existing[$preserveKey];
                }
            }
            if ($request->has('layout_type')) {
                $inputContentValue['layout_type'] = in_array($request->layout_type, BannerService::LAYOUT_TYPES, true) ? $request->layout_type : 'hero_full_width';
            }
            if ($request->has('start_date')) {
                $inputContentValue['start_date'] = $request->start_date ?: null;
            }
            if ($request->has('end_date')) {
                $inputContentValue['end_date'] = $request->end_date ?: null;
            }
            if ($request->has('visibility')) {
                $inputContentValue['visibility'] = in_array($request->visibility, BannerService::VISIBILITY_OPTIONS, true) ? $request->visibility : 'public';
            }
            if (array_key_exists('is_active', $request->all())) {
                $inputContentValue['is_active'] = (int) $request->input('is_active', 1);
            }
            $contentFields = ['title', 'subtitle', 'description', 'badge', 'button_text', 'button_url', 'icon', 'overlay_color', 'overlay_opacity', 'title_font_size', 'title_font_weight', 'title_align', 'text_color'];
            $bannerContent = $existing['banner_content'] ?? [];
            if (is_object($bannerContent)) {
                $bannerContent = (array) $bannerContent;
            }
            foreach ($contentFields as $f) {
                $keyName = 'banner_' . $f;
                if ($request->has($keyName)) {
                    $bannerContent[$f] = $request->input($keyName, '');
                }
            }
            $inputContentValue['banner_content'] = (object) array_merge(BannerService::defaultBannerContent(), $bannerContent);
        }

        // Banner content: merge existing data_values; ensure slider settings and dimensions always valid
        if ($key == 'banner' && $type == 'content') {
            $existing = (array) ($content->data_values ?? (object) []);
            foreach ($existing as $ek => $ev) {
                if (!array_key_exists($ek, $inputContentValue) && $ev !== null && $ev !== '') {
                    $inputContentValue[$ek] = $ev;
                }
            }
            $sec = (int) ($inputContentValue['slide_interval_seconds'] ?? $request->input('slide_interval_seconds', 5));
            $inputContentValue['slide_interval_seconds'] = ($sec >= 1 && $sec <= 60) ? $sec : 5;
            $inputContentValue['autoplay'] = isset($inputContentValue['autoplay']) ? (int) $inputContentValue['autoplay'] : (int) ($request->input('autoplay', 1));
            if ($inputContentValue['autoplay'] !== 0)
                $inputContentValue['autoplay'] = 1;
            if (empty($inputContentValue['banner_width']) || (int) ($inputContentValue['banner_width'] ?? 0) < 100) {
                $inputContentValue['banner_width'] = 2560;
            }
            if (empty($inputContentValue['banner_height']) || (int) ($inputContentValue['banner_height'] ?? 0) < 50) {
                $inputContentValue['banner_height'] = 600;
            }
        }

        // Register section: save which registration form fields are enabled (admin control, including captcha)
        if ($key == 'register' && $type == 'content') {
            $allowedKeys = array_keys(defaultRegistrationFields());
            $submitted = $request->input('registration_fields', []);
            $registrationFields = [];
            foreach ($allowedKeys as $fk) {
                $val = isset($submitted[$fk]) ? $submitted[$fk] : 0;
                $registrationFields[$fk] = ((int) $val === 1 || $val === '1') ? 1 : 0;
            }
            $inputContentValue['registration_fields'] = $registrationFields;

            if ($request->has('profile_fields') && is_array($request->input('profile_fields'))) {
                $profileSubmitted = $request->input('profile_fields', []);
                $profileFields = [];
                foreach ($allowedKeys as $fk) {
                    $val = isset($profileSubmitted[$fk]) ? $profileSubmitted[$fk] : 0;
                    $profileFields[$fk] = ((int) $val === 1 || $val === '1') ? 1 : 0;
                }
                $inputContentValue['profile_fields'] = $profileFields;
            } else {
                $existingProfile = $content->data_values->profile_fields ?? null;
                if ($existingProfile !== null) {
                    $inputContentValue['profile_fields'] = is_array($existingProfile) ? $existingProfile : (array) $existingProfile;
                }
            }
        }

        // Register section: save login captcha toggle (floating & full-page login)
        if ($key == 'register' && $type == 'content') {
            $inputContentValue['login_captcha_enabled'] = (int) $request->input('login_captcha_enabled', 0);
        }

        // Register: preserve existing heading/subheading/image/login_captcha_enabled when empty or missing so Save always works
        if ($key == 'register' && $type == 'content') {
            $existing = $content->data_values ? (array) $content->data_values : [];
            foreach (['heading', 'subheading', 'image', 'login_captcha_enabled', 'registration_fields', 'profile_fields'] as $preserveKey) {
                $incoming = $inputContentValue[$preserveKey] ?? '';
                if (($incoming === '' || $incoming === null) && isset($existing[$preserveKey]) && $existing[$preserveKey] !== '') {
                    $inputContentValue[$preserveKey] = $existing[$preserveKey];
                }
            }
        }

        // Login section: save login credential options (all three: username, email, mobile). User login page shows only those also enabled in Registration.
        if ($key == 'login' && $type == 'content') {
            $allowedLoginKeys = loginCredentialCapableKeys();
            $submitted = $request->input('login_fields');
            $loginFields = [];
            if (is_array($submitted) && !empty($submitted)) {
                foreach ($allowedLoginKeys as $fk) {
                    $val = isset($submitted[$fk]) ? $submitted[$fk] : 0;
                    $loginFields[$fk] = ((int) $val === 1 || $val === '1') ? 1 : 0;
                }
            } else {
                $existing = $content->data_values ? (array) $content->data_values : [];
                $existingLogin = isset($existing['login_fields']) ? (array) $existing['login_fields'] : [];
                foreach ($allowedLoginKeys as $fk) {
                    $loginFields[$fk] = isset($existingLogin[$fk]) && ((int) $existingLogin[$fk] === 1 || $existingLogin[$fk] === '1') ? 1 : 0;
                }
            }
            // Ensure at least one credential type is enabled
            if (array_sum($loginFields) === 0) {
                $loginFields['username'] = 1;
            }
            $inputContentValue['login_fields'] = $loginFields;
            $inputContentValue['captcha_enabled'] = (int) $request->input('captcha_enabled', 0);
        }

        // Login section: which social login buttons to show on user login page (only those configured in Settings)
        if ($key == 'login' && $type == 'content') {
            $allowedSocial = array_keys(defaultSocialLoginButtons());
            $submitted = $request->input('social_login_buttons');
            $socialButtons = [];
            if (is_array($submitted) && !empty($submitted)) {
                foreach ($allowedSocial as $pk) {
                    $val = isset($submitted[$pk]) ? $submitted[$pk] : 0;
                    $socialButtons[$pk] = ((int) $val === 1 || $val === '1') ? 1 : 0;
                }
            } else {
                $existing = $content->data_values ? (array) $content->data_values : [];
                $existingSocial = isset($existing['social_login_buttons']) ? (array) $existing['social_login_buttons'] : [];
                $defaults = defaultSocialLoginButtons();
                foreach ($allowedSocial as $pk) {
                    if (isset($existingSocial[$pk])) {
                        $socialButtons[$pk] = ((int) $existingSocial[$pk] === 1 || $existingSocial[$pk] === '1') ? 1 : 0;
                    } else {
                        $socialButtons[$pk] = isset($defaults[$pk]) ? (int) $defaults[$pk] : 0;
                    }
                }
            }
            $inputContentValue['social_login_buttons'] = $socialButtons;
        }

        // Login: preserve existing heading/subheading/image when empty
        if ($key == 'login' && $type == 'content') {
            $existing = $content->data_values ? (array) $content->data_values : [];
            foreach (['heading', 'subheading', 'image'] as $preserveKey) {
                $incoming = $inputContentValue[$preserveKey] ?? '';
                if (($incoming === '' || $incoming === null) && isset($existing[$preserveKey]) && $existing[$preserveKey] !== '') {
                    $inputContentValue[$preserveKey] = $existing[$preserveKey];
                }
            }
            if (!isset($inputContentValue['social_login_buttons']) && !empty($existing['social_login_buttons'])) {
                $inputContentValue['social_login_buttons'] = $existing['social_login_buttons'];
            }
        }

        if ($key == 'seo' && $type == 'data') {
            $additional = preg_split('/[\s,]+/m', $request->input('additional_keywords', ''), -1, PREG_SPLIT_NO_EMPTY);
            $additional = array_map('trim', array_filter($additional));
            $main = $inputContentValue['keywords'] ?? [];
            $main = is_array($main) ? $main : [];
            $inputContentValue['keywords'] = array_values(array_unique(array_merge($main, $additional)));
            unset($inputContentValue['additional_keywords']);
        }

        if ($key === 'social_icon' && $type === 'element') {
            $inputContentValue['show_on_public'] = ($request->input('show_on_public') === '0' || (int) $request->input('show_on_public') === 0) ? 0 : 1;
        }

        // Social Icons: keep stored icon / custom_icon when edit modal posts an empty icon (picker not synced) so the footer SVG branch still runs.
        if ($key === 'social_icon' && $type === 'element' && $request->id) {
            $existing = (array) ($content->data_values ?? (object) []);
            $incomingIcon = trim((string) ($inputContentValue['icon'] ?? ''));
            if ($incomingIcon === '' && !empty($existing['icon'])) {
                $prev = $existing['icon'];
                $inputContentValue['icon'] = is_scalar($prev) ? trim((string) $prev) : '';
            }
            $incomingCustom = trim((string) ($inputContentValue['custom_icon'] ?? ''));
            if ($incomingCustom === '' && !empty($existing['custom_icon'])) {
                $prevC = $existing['custom_icon'];
                $inputContentValue['custom_icon'] = is_scalar($prevC) ? trim((string) $prevC) : '';
            }
            foreach (['title', 'url', 'custom_icon_svg', 'show_on_public'] as $preserveKey) {
                if (!array_key_exists($preserveKey, $inputContentValue) && array_key_exists($preserveKey, $existing)) {
                    $inputContentValue[$preserveKey] = $existing[$preserveKey];
                }
            }
        }

        $content->data_values = $inputContentValue;
        $content->save();

        if (in_array($key, ['footer', 'contact_us', 'social_icon', 'policy_pages', 'service'])) {
            clearFooterCache();
        }
        if ($key == 'banner') {
            Cache::forget('homepage.banner.guest');
            Cache::forget('homepage.banner.auth');
        }
        if ($key == 'seo') {
            Cache::forget('seo.data');
            Cache::forget('seo.sitemap.xml');
            $notify[] = ['success', 'SEO settings saved successfully'];
        } elseif ($key == 'banner' && $type == 'content') {
            $notify[] = ['success', __('Slider settings saved. পাবলিক পেজে ব্যানার এই সেকেন্ড পর পর পরিবর্তন হবে। হোমপেজ রিফ্রেশ করুন।')];
        } elseif ($key == 'register' && $type == 'content') {
            \Cache::forget('GeneralSetting');
            \Cache::forget('registration_fields_config');
            \Cache::forget('profile_fields_config');
            $notify[] = ['success', __('Registration form fields saved. User registration page will show the selected fields. Profile fields control what users can edit after signup.')];
        } elseif ($key == 'login' && $type == 'content') {
            \Cache::forget('GeneralSetting');
            \Cache::forget('frontend_login_content');
            \Cache::forget('frontend_login_social_buttons');
            \Cache::forget('registration_fields_config');
            try {
                \Artisan::call('view:clear');
                \Artisan::call('config:clear');
            } catch (\Throwable $e) {
                // ignore
            }
            $notify[] = ['success', __('Login settings saved. Do a hard refresh (Ctrl+F5 or Cmd+Shift+R) on the user login page to see ON/OFF changes.')];
        } else {
            $notify[] = ['success', 'Banner ' . ($request->id ? 'updated' : 'added') . ' successfully'];
        }
        return back()->withNotify($notify);
    }

    /**
     * One-click public/private for a social icon row (admin table switch).
     */
    public function socialIconTogglePublic(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:frontends,id',
            'show_on_public' => 'required|in:0,1',
        ]);
        $row = Frontend::findOrFail((int) $request->id);
        if ($row->data_keys !== 'social_icon.element') {
            return response()->json(['success' => false, 'message' => __('Invalid item')], 422);
        }
        $dv = (array) ($row->data_values ?? []);
        $dv['show_on_public'] = (int) $request->input('show_on_public');
        $row->data_values = $dv;
        $row->save();
        clearFooterCache();

        return response()->json([
            'success' => true,
            'show_on_public' => $dv['show_on_public'],
            'message' => $dv['show_on_public'] === 1 ? __('Visible in footer') : __('Hidden from footer'),
        ]);
    }

    public function frontendElement($key = null, $id = null)
    {
        // Get key from route parameter or route name
        if ($key === null) {
            $key = request()->route('key');
            if ($key === null) {
                $routeName = request()->route()->getName();
                if ($routeName && strpos($routeName, 'element.') !== false) {
                    $routeKey = str_replace('admin.frontend.sections.element.', '', $routeName);
                    $key = $this->mapRouteToKey($routeKey);
                }
            }
        }

        // Map clean route names to internal keys
        $key = $this->mapRouteToKey($key);

        $section = @getPageSections()->$key;
        if (!$section) {
            return abort(404);
        }

        // Use custom view for banner
        if ($key == 'banner') {
            $pageTitle = $section->name . ' Items';
            // Only load data if editing (when edit parameter is present or id is provided)
            $data = null;
            if ($id || request()->get('edit')) {
                $editId = $id ?: request()->get('edit');
                $data = Frontend::findOrFail($editId);
            }
            return view('admin.frontend.banner_element', compact('section', 'key', 'pageTitle', 'data'));
        }

        unset($section->element->modal);
        $pageTitle = $section->name . ' Items';
        if ($id) {
            $data = Frontend::findOrFail($id);
            return view('admin.frontend.element', compact('section', 'key', 'pageTitle', 'data'));
        }
        return view('admin.frontend.element', compact('section', 'key', 'pageTitle'));
    }




    public function updateBannerField(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:frontends,id',
            'field' => 'required|in:display_order,url,animation_type,is_active',
            'value' => 'required'
        ]);

        $banner = Frontend::findOrFail($request->id);

        if ($banner->data_keys !== 'banner.element') {
            return response()->json([
                'success' => false,
                'message' => 'Invalid banner'
            ], 400);
        }

        $dataValues = $banner->data_values ?? (object) [];

        if ($request->field === 'display_order') {
            $dataValues->display_order = (int) $request->value;
        } elseif ($request->field === 'url') {
            $dataValues->url = $request->value;
        } elseif ($request->field === 'animation_type') {
            $dataValues->animation_type = $request->value;
        } elseif ($request->field === 'is_active') {
            $dataValues->is_active = (int) $request->value;
        }

        $banner->data_values = $dataValues;
        $banner->save();

        Cache::forget('homepage.banner.guest');
        Cache::forget('homepage.banner.auth');

        return response()->json([
            'success' => true,
            'message' => 'Banner updated successfully'
        ]);
    }

    protected function storeImage($imgJson, $type, $key, $image, $imgKey, $old_image = null)
    {
        $path = 'assets/images/frontend/' . $key;
        if ($type == 'element' || $type == 'content') {
            $size = @$imgJson
            ->$imgKey->size;
            $thumb = @$imgJson
            ->$imgKey->thumb;
        } else {
            $path = getFilePath($key);
            $size = getFileSize($key);
            $thumb = @fileManager()->$key()->thumb;
        }
        return fileUploader($image, $path, $size, $old_image, $thumb);
    }

    /**
     * Professional banner upload: save to desktop/ and thumb/.
     * Returns filename (stored under desktop/); thumb created when possible.
     * $old_image can be filename (legacy) or array with desktop/mobile/thumb keys.
     */
    protected function storeBannerImage($file, $old_image = null, $subFolder = BannerService::DESKTOP_DIR)
    {
        $targetPath = BannerService::uploadPath($subFolder);
        $thumbPath = BannerService::uploadPath(BannerService::THUMB_DIR);
        $extension = strtolower($file->getClientOriginalExtension());
        if (!BannerService::isAllowedExtension($extension)) {
            throw new \Exception('Banner file type not allowed. Use: jpg, jpeg, png, webp, mp4.');
        }
        $filename = 'banner_' . uniqid() . time() . '.' . $extension;

        if ($old_image) {
            $oldFiles = is_array($old_image) ? $old_image : ['desktop' => $old_image, 'mobile' => null, 'thumb' => null];
            foreach (['desktop', 'mobile', 'thumb'] as $sub) {
                $f = $oldFiles[$sub] ?? ($sub === $subFolder ? $old_image : null);
                if ($f && is_string($f)) {
                    $p = BannerService::uploadPath($sub);
                    $full = $p . '/' . basename($f);
                    if (file_exists($full) && is_file($full)) {
                        @unlink($full);
                    }
                }
            }
        }

        try {
            $file->move($targetPath, $filename);
            if (!file_exists($targetPath . '/' . $filename)) {
                throw new \Exception('File upload failed - file not found after move');
            }
        } catch (\Exception $e) {
            $tempPath = $file->getRealPath();
            if ($tempPath && file_exists($tempPath) && copy($tempPath, $targetPath . '/' . $filename)) {
                // ok
            } else {
                throw new \Exception('Failed to upload banner: ' . $e->getMessage());
            }
        }
        $isVideo = in_array($extension, ['mp4'], true);
        $srcPath = $targetPath . '/' . $filename;
        if (!$isVideo && file_exists($srcPath) && in_array($extension, ['jpg', 'jpeg', 'png'], true) && function_exists('imagewebp')) {
            $webpFilename = pathinfo($filename, PATHINFO_FILENAME) . '.webp';
            $webpPath = $targetPath . '/' . $webpFilename;
            if ($this->convertImageToWebp($srcPath, $webpPath)) {
                @unlink($srcPath);
                $filename = $webpFilename;
                $srcPath = $webpPath;
            }
        }
        if (!$isVideo && file_exists($srcPath)) {
            $thumbFile = 'thumb_' . $filename;
            $dest = $thumbPath . '/' . $thumbFile;
            $created = $this->createBannerThumbnail($srcPath, $dest, BannerService::THUMB_MAX_WIDTH);
            if (!$created && function_exists('getimagesize') && @getimagesize($srcPath)) {
                @copy($srcPath, $dest);
            }
        }
        return $filename;
    }

    /**
     * Convert image (jpg/png) to WebP. Returns true on success.
     */
    protected function convertImageToWebp(string $srcPath, string $destPath): bool
    {
        if (!function_exists('getimagesize') || !function_exists('imagewebp')) {
            return false;
        }
        $info = @getimagesize($srcPath);
        if (!$info || !isset($info[0], $info[1], $info[2])) {
            return false;
        }
        $img = @imagecreatefromstring(file_get_contents($srcPath));
        if (!$img) {
            return false;
        }
        if (function_exists('imagepalettetotruecolor')) {
            @imagepalettetotruecolor($img);
        }
        if (function_exists('imagealphablending') && function_exists('imagesavealpha')) {
            @imagealphablending($img, true);
            @imagesavealpha($img, true);
        }
        $result = @imagewebp($img, $destPath, 85);
        imagedestroy($img);
        return $result && file_exists($destPath);
    }

    /**
     * Create resized thumbnail for banner (max width). Uses GD when available.
     */
    protected function createBannerThumbnail(string $srcPath, string $destPath, int $maxWidth): bool
    {
        if (!function_exists('getimagesize') || !function_exists('imagecreatefromstring')) {
            return false;
        }
        $info = @getimagesize($srcPath);
        if (!$info || !isset($info[0], $info[1], $info[2])) {
            return false;
        }
        $w = (int) $info[0];
        $h = (int) $info[1];
        if ($w <= 0 || $h <= 0) {
            return false;
        }
        $img = @imagecreatefromstring(file_get_contents($srcPath));
        if (!$img) {
            return false;
        }
        $newW = min($w, $maxWidth);
        $newH = (int) round($h * ($newW / $w));
        $thumb = imagecreatetruecolor($newW, $newH);
        if (!$thumb) {
            imagedestroy($img);
            return false;
        }
        imagecopyresampled($thumb, $img, 0, 0, 0, 0, $newW, $newH, $w, $h);
        imagedestroy($img);
        $ext = strtolower(pathinfo($srcPath, PATHINFO_EXTENSION));
        $saved = false;
        if ($ext === 'png') {
            $saved = imagepng($thumb, $destPath, 8);
        } elseif ($ext === 'webp' && function_exists('imagewebp')) {
            $saved = imagewebp($thumb, $destPath, 85);
        } else {
            $saved = imagejpeg($thumb, $destPath, 88);
        }
        imagedestroy($thumb);
        return $saved;
    }

    public function remove($id)
    {
        $frontend = Frontend::find($id);
        if (!$frontend) {
            $notify[] = ['error', 'Item not found'];
            return back()->withNotify($notify);
        }
        $key = explode('.', (string) ($frontend->data_keys ?? ''))[0] ?? '';
        $type = explode('.', (string) ($frontend->data_keys ?? ''))[1] ?? '';
        if ($key && $type && in_array($type, ['element', 'content'])) {
            $path = 'assets/images/frontend/' . $key;
            try {
                // Scrollbar: delete uploaded images from items
                if ($key === 'scrollbar' && $type === 'element' && $frontend->data_values) {
                    $dv = $frontend->data_values;
                    $items = $dv->items ?? [];
                    if (is_object($items)) {
                        $items = (array) $items;
                    }
                    $scrollbarPath = base_path('../assets/images/frontend/scrollbar');
                    foreach ($items as $item) {
                        $it = is_array($item) ? $item : (array) $item;
                        if (($it['type'] ?? '') === 'image' && !empty($it['content'])) {
                            $f = $scrollbarPath . '/' . $it['content'];
                            if (file_exists($f) && is_file($f)) {
                                @unlink($f);
                            }
                        }
                    }
                } else {
                    $sections = getPageSections();
                    $imgJson = $sections && isset($sections->$key->$type->images) ? $sections->$key->$type->images : null;
                    if ($imgJson && $frontend->data_values) {
                        $dataValues = $frontend->data_values;
                        foreach ($imgJson as $imgKey => $imgValue) {
                            $filename = $dataValues->$imgKey ?? null;
                            if (!empty($filename) && is_string($filename)) {
                                if ($key === 'banner') {
                                    foreach (['desktop', 'mobile', 'thumb'] as $sub) {
                                        $p = BannerService::uploadPath($sub);
                                        $f = ($sub === 'thumb') ? 'thumb_' . $filename : $filename;
                                        $full = $p . '/' . $f;
                                        if (file_exists($full) && is_file($full)) {
                                            @unlink($full);
                                        }
                                        // Legacy cleanup
                                        $legacy = base_path('../' . BannerService::UPLOAD_BASE . '/' . $sub . '/' . $f);
                                        if (file_exists($legacy) && is_file($legacy)) {
                                            @unlink($legacy);
                                        }
                                    }
                                    // Root legacy cleanup
                                    $rootLegacy = base_path('../' . BannerService::UPLOAD_BASE . '/' . $filename);
                                    if (file_exists($rootLegacy) && is_file($rootLegacy)) {
                                        @unlink($rootLegacy);
                                    }
                                    continue;
                                }
                                $fullPath = public_path($path . '/' . $filename);
                                $thumbPath = public_path($path . '/thumb_' . $filename);
                                if (file_exists($fullPath) && is_file($fullPath)) {
                                    @unlink($fullPath);
                                }
                                if (file_exists($thumbPath) && is_file($thumbPath)) {
                                    @unlink($thumbPath);
                                }
                            }
                        }
                    }
                }
            } catch (\Throwable $e) {
                // Continue to delete DB record even if file removal fails
            }
        }
        $frontend->delete();
        if (in_array($key, ['footer', 'contact_us', 'social_icon', 'policy_pages', 'service'])) {
            clearFooterCache();
        }
        if ($frontend->data_keys === 'banner.element') {
            Cache::forget('homepage.banner.guest');
            Cache::forget('homepage.banner.auth');
        }
        $notify[] = ['success', 'Content removed successfully'];
        return back()->withNotify($notify);
    }

    public function bannerUpdateOrder(Request $request)
    {
        // Handle both array format (from drag-drop) and single update format
        if ($request->has('order') && is_array($request->order)) {
            // Array format for bulk updates
            $request->validate([
                'order' => 'required|array',
                'order.*.id' => 'required|exists:frontends,id',
                'order.*.order' => 'required|integer|min:1',
            ]);

            foreach ($request->order as $item) {
                $banner = Frontend::findOrFail($item['id']);
                $dataValues = $banner->data_values;
                $dataValues->display_order = $item['order'];
                $banner->data_values = $dataValues;
                $banner->save();
            }
        } else {
            // Single update format (from inline edit) – Position 1 = first on public page, max 30
            $request->validate([
                'id' => 'required|exists:frontends,id',
                'display_order' => 'required|integer|min:1|max:30',
            ]);

            $banner = Frontend::findOrFail($request->id);
            $dataValues = $banner->data_values;
            $dataValues->display_order = $request->display_order;
            $banner->data_values = $dataValues;
            $banner->save();
        }

        Cache::forget('homepage.banner.guest');
        Cache::forget('homepage.banner.auth');

        return response()->json([
            'success' => true,
            'message' => 'Banner order updated successfully'
        ]);
    }

    /**
     * Map clean route names to internal section keys
     */
    private function mapRouteToKey($routeKey)
    {
        $mapping = [
            'banner' => 'banner',
            'contact' => 'contact_us',
            'footer' => 'footer',
            'headericons' => 'header_icons',
            'login' => 'login',
            'policy' => 'policy_pages',
            'register' => 'register',
            'service' => 'service',
            'social_icon' => 'social_icon',
            'ticker' => 'ticker',
            'scrollbar' => 'scrollbar',
        ];

        return $mapping[$routeKey] ?? $routeKey;
    }

    public function frontendSections($key = null)
    {
        // Get key from route parameter or route name
        if ($key === null) {
            // Try to get from route parameter first
            $key = request()->route('key');

            // If still null, try to get from route name
            if ($key === null) {
                $routeName = request()->route()->getName();
                if ($routeName && strpos($routeName, 'sections.') !== false) {
                    $routeKey = str_replace('admin.frontend.sections.', '', $routeName);
                    $key = $this->mapRouteToKey($routeKey);
                }
            }
        }

        // Map clean route names to internal keys
        $key = $this->mapRouteToKey($key);

        // Scrollbar: custom headline/ticker (does not depend on sections.json)
        if ($key == 'scrollbar') {
            return $this->scrollbarView();
        }

        // Banner: use banner_element view (does not depend on sections.json for view)
        if ($key == 'banner') {
            return $this->bannerElementView();
        }

        $section = @getPageSections()->$key;
        if (!$section) {
            return abort(404);
        }

        $content = Frontend::where('data_keys', $key . '.content')->orderBy('id', 'desc')->first();
        if ($key == 'login' && !$content) {
            $content = new Frontend();
            $content->data_keys = 'login.content';
            $content->data_values = (object) [
                'heading' => 'Login',
                'subheading' => '',
                'login_fields' => array_fill_keys(loginCredentialCapableKeys(), 1),
                'captcha_enabled' => 1,
                'social_login_buttons' => defaultSocialLoginButtons(),
            ];
            $content->save();
        }
        $elements = Frontend::where('data_keys', $key . '.element')->orderByRaw('CAST(JSON_EXTRACT(data_values, "$.display_order") AS UNSIGNED) ASC')->orderBy('id', 'asc')->get();
        $pageTitle = $section->name;

        if ($key == 'register') {
            return view('admin.frontend.register', compact('section', 'content', 'elements', 'key', 'pageTitle'));
        }
        if ($key == 'login') {
            return view('admin.frontend.login', compact('section', 'content', 'elements', 'key', 'pageTitle'));
        }
        return view('admin.frontend.index', compact('section', 'content', 'elements', 'key', 'pageTitle'));
    }

    /**
     * Single source of truth for storefront icon keys (navbar, mobile menu, product UI).
     * field = DB key for SVG/fallback name; image stored as {field}_image.
     */
    protected function headerIconSlotDefinitions(): array
    {
        return [
            'search' => ['label' => __('Search (submit)'), 'default' => 'search', 'field' => 'search_icon', 'group' => 'search'],
            'voice_search' => ['label' => __('Voice search'), 'default' => 'microphone', 'field' => 'voice_search_icon', 'group' => 'search'],
            'image_search' => ['label' => __('Image / camera search'), 'default' => 'scan', 'field' => 'image_search_icon', 'group' => 'search'],
            'home' => ['label' => __('Home'), 'default' => 'home', 'field' => 'home_icon', 'group' => 'nav'],
            'categories' => ['label' => __('Categories (mobile bar)'), 'default' => 'th-large', 'field' => 'categories_icon', 'group' => 'nav'],
            'products' => ['label' => __('Products'), 'default' => 'box', 'field' => 'products_icon', 'group' => 'nav'],
            'contact' => ['label' => __('Contact'), 'default' => 'phone', 'field' => 'contact_icon', 'group' => 'nav'],
            'track_order' => ['label' => __('Track order'), 'default' => 'shipping-fast', 'field' => 'track_order_icon', 'group' => 'nav'],
            'language' => ['label' => __('Language'), 'default' => 'language', 'field' => 'language_icon', 'group' => 'nav'],
            'notification' => ['label' => __('Notifications'), 'default' => 'bell', 'field' => 'notification_icon', 'group' => 'nav'],
            'wishlist' => ['label' => __('Wishlist'), 'default' => 'heart', 'field' => 'wishlist_icon', 'group' => 'nav'],
            'compare' => ['label' => __('Compare'), 'default' => 'exchange-alt', 'field' => 'compare_icon', 'group' => 'nav'],
            'cart' => ['label' => __('Cart'), 'default' => 'shopping-cart', 'field' => 'cart_icon', 'group' => 'nav'],
            'buy_now' => ['label' => __('Buy now / add to cart'), 'default' => 'cart-plus', 'field' => 'buy_now_icon', 'group' => 'product'],
            'orders' => ['label' => __('My orders'), 'default' => 'list-alt', 'field' => 'orders_icon', 'group' => 'nav'],
            'login' => ['label' => __('Login / account chip'), 'default' => 'user', 'field' => 'login_icon', 'group' => 'nav'],
            'register' => ['label' => __('Register'), 'default' => 'user-plus', 'field' => 'register_icon', 'group' => 'account'],
            'transactions' => ['label' => __('Transactions'), 'default' => 'money-bill-wave', 'field' => 'transactions_icon', 'group' => 'account'],
            'messages' => ['label' => __('Messages'), 'default' => 'comments', 'field' => 'messages_icon', 'group' => 'account'],
            'mail' => ['label' => __('Email / envelope'), 'default' => 'envelope', 'field' => 'mail_icon', 'group' => 'nav'],
            'review' => ['label' => __('Review products'), 'default' => 'star', 'field' => 'review_icon', 'group' => 'account'],
            'profile' => ['label' => __('Profile'), 'default' => 'user-tie', 'field' => 'profile_icon', 'group' => 'account'],
            'change_password' => ['label' => __('Change password'), 'default' => 'key', 'field' => 'change_password_icon', 'group' => 'account'],
            'logout' => ['label' => __('Logout'), 'default' => 'sign-out-alt', 'field' => 'logout_icon', 'group' => 'account'],
            'quick_view' => ['label' => __('Quick view (product card)'), 'default' => 'eye', 'field' => 'quick_view_icon', 'group' => 'product'],
            'policy_payment' => ['label' => __('Product page: payment policy link'), 'default' => 'credit-card', 'field' => 'policy_payment_icon', 'group' => 'product'],
            'policy_shipping' => ['label' => __('Product page: shipping policy link'), 'default' => 'shipping-fast', 'field' => 'policy_shipping_icon', 'group' => 'product'],
            'policy_order' => ['label' => __('Product page: order procedure link'), 'default' => 'list-alt', 'field' => 'policy_order_icon', 'group' => 'product'],
            'section_brand' => ['label' => __('Section title: brand / tag'), 'default' => 'tag', 'field' => 'section_brand_icon', 'group' => 'product'],
            'scroll_top' => ['label' => __('Scroll to top button'), 'default' => 'angle-double-up', 'field' => 'scroll_top_icon', 'group' => 'nav'],
            'all_categories_page' => ['label' => __('All categories page'), 'default' => 'th-large', 'field' => 'all_categories_page_icon', 'group' => 'nav'],
            'all_brands_page' => ['label' => __('All brands page'), 'default' => 'tags', 'field' => 'all_brands_page_icon', 'group' => 'nav'],
            'blog_page' => ['label' => __('Blog page'), 'default' => 'newspaper', 'field' => 'blog_page_icon', 'group' => 'nav'],
            'guest_order_page' => ['label' => __('Guest order page'), 'default' => 'clipboard-list', 'field' => 'guest_order_page_icon', 'group' => 'nav'],
            'checkout_page' => ['label' => __('Checkout page'), 'default' => 'credit-card', 'field' => 'checkout_page_icon', 'group' => 'nav'],
        ];
    }

    public function headerIcons()
    {
        $pageTitle = __('Header Icons');
        $key = 'header_icons';
        $section = @getPageSections()->$key;
        $content = Frontend::where('data_keys', 'header_icons.content')->orderBy('id', 'desc')->first();
        if (!$content) {
            $content = new Frontend();
            $content->data_keys = 'header_icons.content';
            $content->data_values = (object) [];
            $content->save();
        }

        $slots = $this->headerIconSlotDefinitions();
        $customButtons = Frontend::where('data_keys', 'custom_buttons.element')
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.frontend.header_icons', compact('pageTitle', 'section', 'content', 'slots', 'customButtons'));
    }

    public function headerIconsSave(Request $request)
    {
        $slots = $this->headerIconSlotDefinitions();
        $rules = [];
        foreach ($slots as $slot) {
            $iconField = $slot['field'];
            $rules[$iconField] = 'nullable|string|max:100';
            $rules[$iconField . '_image'] = ['nullable', new FileTypeValidate(['jpg', 'jpeg', 'png', 'svg', 'webp'])];
        }
        $request->validate($rules);

        $content = Frontend::where('data_keys', 'header_icons.content')->orderBy('id', 'desc')->first();
        if (!$content) {
            $content = new Frontend();
            $content->data_keys = 'header_icons.content';
            $content->data_values = (object) [];
        }

        $values = (array) ($content->data_values ?? []);
        $uploadPath = 'assets/images/frontend/header_icons';
        if (!is_dir(public_path($uploadPath))) {
            @mkdir(public_path($uploadPath), 0755, true);
        }

        // 1. Identify which icons have fresh uploads or requested deletions
        $iconToNewFileMap = [];
        $iconToDeleteMap = [];
        foreach ($slots as $slot) {
            $iconField = $slot['field'];
            $imageField = $iconField . '_image';
            $deleteField = $imageField . '_delete';
            $defaultIcon = $slot['default'] ?? null;

            if ($request->hasFile($imageField)) {
                $old = trim((string) ($values[$imageField] ?? ''));
                $newFile = fileUploader($request->file($imageField), $uploadPath, null, $old);
                $values[$imageField] = $newFile;
                if ($defaultIcon) {
                    $iconToNewFileMap[$defaultIcon] = $newFile;
                }
            }

            if ((string) $request->input($deleteField, '0') === '1') {
                if ($defaultIcon) {
                    $iconToDeleteMap[$defaultIcon] = true;
                }
                $old = trim((string) ($values[$imageField] ?? ''));
                if ($old !== '' && !isset($iconToNewFileMap[$defaultIcon])) {
                    $primary = public_path($uploadPath . '/' . $old);
                    $legacy = dirname(base_path()) . '/' . $uploadPath . '/' . $old;
                    @unlink($primary);
                    if ($primary !== $legacy && file_exists($legacy) && is_file($legacy)) {
                        @unlink($legacy);
                    }
                }
                $values[$imageField] = '';
            }
        }

        // 2. Propagate changes to identical icons and update text fields
        foreach ($slots as $slot) {
            $iconField = $slot['field'];
            $imageField = $iconField . '_image';
            $defaultIcon = $slot['default'] ?? null;

            $values[$iconField] = trim((string) $request->input($iconField, ($values[$iconField] ?? '')));

            if ($defaultIcon) {
                if (isset($iconToNewFileMap[$defaultIcon])) {
                    $values[$imageField] = $iconToNewFileMap[$defaultIcon];
                } elseif (isset($iconToDeleteMap[$defaultIcon])) {
                    $values[$imageField] = '';
                }
            }
        }

        $content->data_values = (object) $values;
        $content->save();

        mirror_header_icons_public_to_legacy();

        $notify[] = ['success', __('Header icons updated successfully (shared icons unified)')];
        return back()->withNotify($notify);
    }


    public function headerButtonStore(Request $request)
    {
        $request->validate([
            'target' => 'required|in:header,footer',
            'position' => 'required|string|max:30',
            'button_text' => 'nullable|string|max:50',
            'button_url' => 'nullable|string|max:255',
            'icon_name' => 'nullable|string|max:100',
            'display_order' => 'nullable|integer|min:0|max:9999',
            'icon_image' => ['nullable', new FileTypeValidate(['jpg', 'jpeg', 'png', 'svg', 'webp'])],
        ]);

        $uploadPath = 'assets/images/frontend/custom_buttons';
        if (!is_dir(public_path($uploadPath))) {
            @mkdir(public_path($uploadPath), 0755, true);
        }

        $iconImage = null;
        if ($request->hasFile('icon_image')) {
            $iconImage = fileUploader($request->file('icon_image'), $uploadPath);
        }

        $data = [
            'target' => (string) $request->input('target'),
            'position' => (string) $request->input('position'),
            'button_text' => trim((string) $request->input('button_text', '')),
            'button_url' => trim((string) $request->input('button_url', '')),
            'icon_name' => trim((string) $request->input('icon_name', '')),
            'icon_image' => $iconImage,
            'display_order' => (int) $request->input('display_order', 0),
            'is_active' => 1,
        ];

        $row = new Frontend();
        $row->data_keys = 'custom_buttons.element';
        $row->data_values = (object) $data;
        $row->save();

        $notify[] = ['success', __('Custom button added successfully')];
        return back()->withNotify($notify);
    }

    public function headerButtonDelete($id)
    {
        $row = Frontend::where('data_keys', 'custom_buttons.element')->findOrFail($id);
        $vals = (array) ($row->data_values ?? []);
        $img = trim((string) ($vals['icon_image'] ?? ''));
        if ($img !== '') {
            @unlink(public_path('assets/images/frontend/custom_buttons/' . $img));
        }
        $row->delete();

        $notify[] = ['success', __('Custom button removed')];
        return back()->withNotify($notify);
    }

    /**
     * User profile control page: which fields users can edit on profile after signup.
     * URL: {admin_prefix}/frontend/userprofile
     */
    public function userprofilePage()
    {
        $pageTitle = __('User profile control');
        $content = Frontend::where('data_keys', 'register.content')->orderBy('id', 'desc')->first();
        return view('admin.frontend.userprofile', compact('content', 'pageTitle'));
    }

    /**
     * Save only profile_fields to register.content (merge with existing).
     */
    public function userprofileSave(Request $request)
    {
        $request->validate(['profile_fields' => 'nullable|array']);
        $content = Frontend::where('data_keys', 'register.content')->orderBy('id', 'desc')->first();
        if (!$content) {
            $content = new Frontend();
            $content->data_keys = 'register.content';
            $content->data_values = (object) [];
        }
        $existing = (array) ($content->data_values ?? (object) []);
        $allowedKeys = array_keys(defaultProfileFields());
        $submitted = $request->input('profile_fields', []);
        $profileFields = [];
        foreach ($allowedKeys as $fk) {
            $val = isset($submitted[$fk]) ? $submitted[$fk] : 0;
            $profileFields[$fk] = ((int) $val === 1 || $val === '1') ? 1 : 0;
        }
        $existing['profile_fields'] = $profileFields;
        $content->data_values = (object) $existing;
        $content->save();
        Cache::forget('profile_fields_config');
        $notify[] = ['success', __('User profile fields saved. Selected fields will appear on user profile page.')];
        return back()->withNotify($notify);
    }

    /** Add New Banner: creates new slot - stays on same URL (no page param) */
    public function addNewBanner()
    {
        $totalBanners = Frontend::where('data_keys', 'banner.element')->count();
        if ($totalBanners >= 30) {
            $notify[] = ['error', __('Maximum 30 banners reached.')];
            return redirect()->route('admin.frontend.sections.banner')->withNotify($notify);
        }
        $newOrder = $totalBanners + 1;
        $newBanner = new Frontend();
        $newBanner->data_keys = 'banner.element';
        $newBanner->data_values = (object) [
            'display_order' => $newOrder,
            'animation_type' => 'none',
            'image' => null,
            'url' => null,
            'is_active' => 1,
            'visibility' => 'public',
            'layout_type' => 'hero_full_width',
            'banner_content' => (object) BannerService::defaultBannerContent(),
        ];
        $newBanner->save();
        Cache::forget('homepage.banner.guest');
        Cache::forget('homepage.banner.auth');
        $notify[] = ['success', __('New banner slot added. Upload your banner below.')];
        return redirect()->route('admin.frontend.sections.banner')->withNotify($notify)->with('scroll_to_banner', true);
    }

    /** Duplicate banner: clone record and image file; new display_order. */
    public function bannerDuplicate($id)
    {
        $bar = Frontend::where('data_keys', BannerService::DATA_KEY_ELEMENT)->findOrFail($id);
        $totalBanners = Frontend::where('data_keys', BannerService::DATA_KEY_ELEMENT)->count();
        if ($totalBanners >= 30) {
            $notify[] = ['error', __('Maximum 30 banners reached.')];
            return redirect()->route('admin.frontend.sections.banner')->withNotify($notify);
        }
        $newOrder = $totalBanners + 1;
        $dv = (array) ($bar->data_values ?? (object) []);
        $oldImage = $dv['image'] ?? null;
        if ($oldImage && is_string($oldImage)) {
            $desktopPath = BannerService::uploadPath(BannerService::DESKTOP_DIR);
            $thumbPath = BannerService::uploadPath(BannerService::THUMB_DIR);
            $ext = pathinfo($oldImage, PATHINFO_EXTENSION) ?: 'jpg';
            $newFilename = 'banner_' . uniqid() . time() . '.' . strtolower($ext);
            $oldFile = $desktopPath . '/' . $oldImage;
            $legacyFile = base_path('../' . BannerService::UPLOAD_BASE . '/' . $oldImage);
            if (file_exists($oldFile) && is_file($oldFile)) {
                @copy($oldFile, $desktopPath . '/' . $newFilename);
                $dv['image'] = $newFilename;
            } elseif (file_exists($legacyFile) && is_file($legacyFile)) {
                @copy($legacyFile, $desktopPath . '/' . $newFilename);
                $dv['image'] = $newFilename;
            }
            $thumbOld = $thumbPath . '/thumb_' . $oldImage;
            $thumbNew = $thumbPath . '/thumb_' . $newFilename;
            if (file_exists($thumbOld) && is_file($thumbOld)) {
                @copy($thumbOld, $thumbNew);
            }
        }
        $dv['display_order'] = $newOrder;
        $newBar = new Frontend();
        $newBar->data_keys = BannerService::DATA_KEY_ELEMENT;
        $newBar->data_values = (object) $dv;
        $newBar->save();
        Cache::forget('homepage.banner.guest');
        Cache::forget('homepage.banner.auth');
        $notify[] = ['success', __('Banner duplicated.')];
        return redirect()->route('admin.frontend.sections.banner', ['edit' => $newBar->id])->withNotify($notify);
    }

    /** Banner section: show banner_element (6-cell grid, upload, order) */
    private function bannerElementView()
    {
        try {
            $sections = getPageSections();
            $section = ($sections && isset($sections->banner)) ? $sections->banner : null;
        } catch (\Throwable $e) {
            $section = null;
        }
        if (!$section) {
            $section = (object) ['name' => 'Banner Section', 'builder' => true];
        }
        // Legacy: Add New Banner via ?add_new=1 (redirect to dedicated route)
        if (request()->get('add_new') == '1') {
            return $this->addNewBanner();
        }
        // No pagination: redirect ?page=2 etc to clean URL
        if (request()->has('page')) {
            return redirect()->route('admin.frontend.sections.banner', [], 301);
        }
        // Sync DB: ensure banner.content has slide_interval_seconds, autoplay, banner_width, banner_height
        $bannerContent = Frontend::where('data_keys', 'banner.content')->orderBy('id', 'desc')->first();
        if (!$bannerContent) {
            $bannerContent = new Frontend();
            $bannerContent->data_keys = 'banner.content';
            $bannerContent->data_values = (object) [
                'slide_interval_seconds' => 5,
                'autoplay' => 1,
                'banner_width' => 2560,
                'banner_height' => 600,
            ];
            $bannerContent->save();
        } else {
            $vals = (array) ($bannerContent->data_values ?? (object) []);
            $updated = false;
            if (empty($vals['slide_interval_seconds']) || (int) ($vals['slide_interval_seconds'] ?? 0) < 1 || (int) ($vals['slide_interval_seconds'] ?? 0) > 60) {
                $vals['slide_interval_seconds'] = 5;
                $updated = true;
            }
            if (!isset($vals['autoplay'])) {
                $vals['autoplay'] = 1;
                $updated = true;
            }
            if (empty($vals['banner_width']) || (int) ($vals['banner_width'] ?? 0) < 100) {
                $vals['banner_width'] = 2560;
                $updated = true;
            }
            if (empty($vals['banner_height']) || (int) ($vals['banner_height'] ?? 0) < 50) {
                $vals['banner_height'] = 600;
                $updated = true;
            }
            if ($updated) {
                $bannerContent->data_values = (object) $vals;
                $bannerContent->save();
            }
        }
        $pageTitle = ($section->name ?? 'Banner Section') . ' - ' . __('Items');
        $key = 'banner';
        $data = null;
        if (request()->get('edit')) {
            $editId = request()->get('edit');
            $data = Frontend::find($editId);
        }
        // Load banner elements in controller (with fallback for DB compatibility)
        try {
            $bannerElements = Frontend::where('data_keys', 'banner.element')
                ->orderBy('id', 'asc')
                ->get();
            $bannerElements = $bannerElements->sortBy(function ($b) {
                $order = $b->data_values->display_order ?? 999;
                return is_numeric($order) ? (int) $order : 999;
            })->values();
            $search = trim((string) request()->get('search', ''));
            if ($search !== '') {
                $bannerElements = $bannerElements->filter(function ($b) use ($search) {
                    $dv = $b->data_values ?? (object) [];
                    $bc = $dv->banner_content ?? (object) [];
                    $text = implode(' ', [
                        $bc->title ?? '',
                        $bc->subtitle ?? '',
                        $bc->description ?? '',
                        $bc->badge ?? '',
                        $bc->button_text ?? '',
                        (string) ($dv->display_order ?? ''),
                    ]);
                    return stripos($text, $search) !== false;
                })->values();
            }
        } catch (\Throwable $e) {
            $bannerElements = Frontend::where('data_keys', 'banner.element')->orderBy('id')->get();
        }
        $bannerStats = ['impressions' => 0, 'clicks' => 0, 'ctr' => 0];
        if (class_exists(\App\Models\BannerAnalytics::class)) {
            try {
                $impressions = \App\Models\BannerAnalytics::where('event', 'impression')->count();
                $clicks = \App\Models\BannerAnalytics::where('event', 'click')->count();
                $bannerStats['impressions'] = $impressions;
                $bannerStats['clicks'] = $clicks;
                $bannerStats['ctr'] = $impressions > 0 ? round(100 * $clicks / $impressions, 2) : 0;
            } catch (\Throwable $e) {
                // table may not exist yet
            }
        }
        $homepageProductRows = \App\Models\HomepageCustomProductRow::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['id', 'title', 'is_active', 'split_banner_json']);

        return view('admin.frontend.banner_element', compact('section', 'key', 'pageTitle', 'data', 'bannerElements', 'bannerStats', 'homepageProductRows'));
    }

    /**
     * Scroll Bar - custom view and save (multiple scroll bars supported).
     */
    public function scrollbarView()
    {
        $pageTitle = 'Scroll Bar';
        $bars = collect();
        $customBars = collect();
        $scrollbarEnabled = 1;
        try {
            $settings = Frontend::where('data_keys', ScrollbarService::SETTINGS_KEY)->first();
            if ($settings && isset($settings->data_values->enabled)) {
                $scrollbarEnabled = (int) $settings->data_values->enabled;
            }
            $bars = Frontend::where('data_keys', ScrollbarService::DATA_KEY)
                ->orderBy('id', 'asc')
                ->get();
            $bars = $bars->sortBy(function ($b) {
                try {
                    $dv = $b->data_values ?? (object) [];
                    $order = is_object($dv) ? ($dv->display_order ?? 999) : ($dv['display_order'] ?? 999);
                    return is_numeric($order) ? (int) $order : 999;
                } catch (\Throwable $e) {
                    return 999;
                }
            })->values();
            $search = trim((string) request()->get('search', ''));
            if ($search !== '') {
                $bars = $bars->filter(function ($bar) use ($search) {
                    $dv = $bar->data_values ?? (object) [];
                    $items = is_object($dv) ? ($dv->items ?? []) : ($dv['items'] ?? []);
                    $items = is_array($items) ? $items : (array) $items;
                    foreach ($items as $it) {
                        $it = is_array($it) ? $it : (array) $it;
                        $text = (string) ($it['content'] ?? $it['content_text'] ?? '');
                        if (!empty($it['segments']) && is_array($it['segments'])) {
                            foreach ($it['segments'] as $s) {
                                $s = is_array($s) ? $s : (array) $s;
                                $text .= ' ' . (string) ($s['text'] ?? '');
                            }
                        }
                        if (stripos($text, $search) !== false) {
                            return true;
                        }
                    }
                    $title = is_object($dv) ? ($dv->title ?? '') : ($dv['title'] ?? '');
                    return stripos($title, $search) !== false;
                })->values();
            }
            $customBars = Frontend::where('data_keys', ScrollbarService::CUSTOM_DATA_KEY)
                ->orderBy('id', 'asc')
                ->get()
                ->values();
            // Edit is now via full page scrollbar/edit/{id}
        } catch (\Throwable $e) {
            try {
                $bars = Frontend::where('data_keys', ScrollbarService::DATA_KEY)->orderBy('id')->get();
                $customBars = Frontend::where('data_keys', ScrollbarService::CUSTOM_DATA_KEY)->orderBy('id')->get();
            } catch (\Throwable $e2) {
                $bars = collect();
                $customBars = collect();
            }
        }
        return view('admin.frontend.scrollbar', compact('pageTitle', 'bars', 'customBars', 'scrollbarEnabled'));
    }

    /**
     * Full-page form: create new scroll bar.
     */
    public function scrollbarCreate()
    {
        $pageTitle = __('Scroll Bar') . ' — ' . __('New');
        $bar = null;
        $formData = self::scrollbarDefaultFormData();
        $isCustomMode = false;
        $scrollbarMode = 'default';
        return view('admin.frontend.scrollbar_form', compact('pageTitle', 'bar', 'formData', 'isCustomMode', 'scrollbarMode'));
    }

    public function scrollbarCreateCustom()
    {
        $pageTitle = __('Scroll Bar 2') . ' — ' . __('Custom Page Setup');
        $bar = null;
        $formData = self::scrollbarDefaultFormData();
        $formData['position'] = 'custom';
        $formData['visibility_pages'] = 'custom_urls';
        $isCustomMode = true;
        $scrollbarMode = 'custom';
        return view('admin.frontend.scrollbar_form', compact('pageTitle', 'bar', 'formData', 'isCustomMode', 'scrollbarMode'));
    }

    /**
     * Full-page form: edit existing scroll bar. ID from query: ?id=107
     */
    public function scrollbarEdit(Request $request, $routeId = null)
    {
        $id = (int) ($routeId ?? $request->input('id'));
        if ($id <= 0) {
            $notify[] = ['error', __('Scroll bar ID required.')];
            return redirect()->route('admin.frontend.sections.scrollbar')->withNotify($notify);
        }
        $bar = Frontend::where('data_keys', ScrollbarService::DATA_KEY)->find($id);
        if (!$bar) {
            $notify[] = ['error', __('Scroll bar not found.')];
            return redirect()->route('admin.frontend.sections.scrollbar')->withNotify($notify);
        }
        $pageTitle = __('Edit Scroll Bar');
        $dv = $bar->data_values;
        if (is_object($dv)) {
            $dv = json_decode(json_encode($dv), true);
        }
        $dv = is_array($dv) ? $dv : [];
        $rawItems = $dv['items'] ?? [];
        $rawItems = is_array($rawItems) ? array_values($rawItems) : array_values((array) $rawItems);
        $items = [];
        foreach ($rawItems as $rawIt) {
            $arr = ScrollbarService::itemToArray($rawIt);
            // Ensure edit form always gets content_text (fallback from content / legacy segments)
            if (!isset($arr['content_text']) || $arr['content_text'] === '') {
                $arr['content_text'] = $arr['content'] ?? '';
            }
            if (($arr['content_text'] ?? '') === '' && !empty($arr['segments']) && is_array($arr['segments'])) {
                $parts = [];
                foreach ($arr['segments'] as $s) {
                    $s = is_array($s) ? $s : (array) $s;
                    $txt = trim((string) ($s['text'] ?? ''));
                    if ($txt !== '') {
                        $parts[] = $txt;
                    }
                }
                if (!empty($parts)) {
                    $arr['content_text'] = implode(' ', $parts);
                    $arr['content'] = $arr['content_text'];
                }
            }
            $items[] = $arr;
        }
        $formData = [
            'title' => $dv['title'] ?? '',
            'position' => $dv['position'] ?? 'header_below',
            'template' => $dv['template'] ?? 'glass',
            'status' => (int) ($dv['status'] ?? 1),
            'visibility' => $dv['visibility'] ?? 'public',
            'visibility_users' => $dv['visibility_users'] ?? 'all',
            'visibility_pages' => $dv['visibility_pages'] ?? 'all',
            'custom_urls' => $dv['custom_urls'] ?? '',
            'custom_url_mode' => $dv['custom_url_mode'] ?? 'contains',
            'schedule_start' => $dv['schedule_start'] ?? '',
            'schedule_end' => $dv['schedule_end'] ?? '',
            'display_order' => (int) ($dv['display_order'] ?? $bar->id),
            'scroll_speed' => (int) ($dv['scroll_speed'] ?? 45),
            'page_speeds' => is_array($dv['page_speeds'] ?? null) ? $dv['page_speeds'] : [],
            'scroll_direction' => $dv['scroll_direction'] ?? 'ltr',
            'loop_mode' => $dv['loop_mode'] ?? 'infinite',
            'pause_on_hover' => (int) ($dv['pause_on_hover'] ?? 1),
            'gap_between_items' => (int) ($dv['gap_between_items'] ?? 8),
            'animation_type' => $dv['animation_type'] ?? 'linear',
            'bar_height' => (int) ($dv['bar_height'] ?? 52),
            'bar_size' => $dv['bar_size'] ?? 'medium',
            'bar_thickness' => $dv['bar_thickness'] ?? 'normal',
            'default_text_size' => $dv['default_text_size'] ?? 'normal',
            'default_text_weight' => $dv['default_text_weight'] ?? 'normal',
            'bar_padding' => $dv['bar_padding'] ?? '',
            'width_type' => $dv['width_type'] ?? 'full',
            'width_value' => $dv['width_value'] ?? '',
            'max_width' => $dv['max_width'] ?? '',
            'bar_background_type' => $dv['bar_background_type'] ?? '',
            'bar_background_value' => $dv['bar_background_value'] ?? '',
            'bar_border' => $dv['bar_border'] ?? '',
            'bar_shadow' => $dv['bar_shadow'] ?? '',
            'hide_on_mobile' => (int) ($dv['hide_on_mobile'] ?? 0),
            'hide_on_desktop' => (int) ($dv['hide_on_desktop'] ?? 0),
            'container_mode' => $dv['container_mode'] ?? 'full',
            'align' => $dv['align'] ?? 'center',
            'z_index' => (int) ($dv['z_index'] ?? 10),
            'sticky' => (int) ($dv['sticky'] ?? 0),
            'offset_top' => $dv['offset_top'] ?? '0px',
            'custom_x_percent' => (float) ($dv['custom_x_percent'] ?? 0),
            'custom_y_px' => (int) ($dv['custom_y_px'] ?? 0),
            'custom_width_percent' => (int) ($dv['custom_width_percent'] ?? 100),
            'loop_delay' => (float) ($dv['loop_delay'] ?? 0),
            'item_animation' => $dv['item_animation'] ?? 'none',
            'icon_animation' => $dv['icon_animation'] ?? 'none',
            'hover_effect' => $dv['hover_effect'] ?? 'pause',
            'items' => $items,
        ];
        $isCustomMode = false;
        $scrollbarMode = 'default';
        return view('admin.frontend.scrollbar_form', compact('pageTitle', 'bar', 'formData', 'isCustomMode', 'scrollbarMode'));
    }

    public function scrollbarEditCustom(Request $request, $routeId = null)
    {
        $id = (int) ($routeId ?? $request->input('id'));
        if ($id <= 0) {
            $notify[] = ['error', __('Scroll bar ID required.')];
            return redirect()->route('admin.frontend.sections.scrollbar')->withNotify($notify);
        }
        $bar = Frontend::where('data_keys', ScrollbarService::CUSTOM_DATA_KEY)->find($id);
        if (!$bar) {
            $notify[] = ['error', __('Custom scroll bar not found.')];
            return redirect()->route('admin.frontend.sections.scrollbar')->withNotify($notify);
        }
        $request->merge(['id' => $bar->id]); // reuse existing mapping
        $pageTitle = __('Edit Scroll Bar 2');
        $dv = is_object($bar->data_values) ? json_decode(json_encode($bar->data_values), true) : (array) ($bar->data_values ?? []);
        $rawItems = $dv['items'] ?? [];
        $rawItems = is_array($rawItems) ? array_values($rawItems) : array_values((array) $rawItems);
        $items = [];
        foreach ($rawItems as $rawIt) {
            $arr = ScrollbarService::itemToArray($rawIt);
            if (!isset($arr['content_text']) || $arr['content_text'] === '') {
                $arr['content_text'] = $arr['content'] ?? '';
            }
            $items[] = $arr;
        }
        $formData = array_merge(self::scrollbarDefaultFormData(), [
            'title' => $dv['title'] ?? '',
            'position' => $dv['position'] ?? 'custom',
            'template' => $dv['template'] ?? 'glass',
            'status' => (int) ($dv['status'] ?? 1),
            'visibility' => $dv['visibility'] ?? 'public',
            'visibility_users' => $dv['visibility_users'] ?? 'all',
            'visibility_pages' => $dv['visibility_pages'] ?? 'custom_urls',
            'custom_urls' => $dv['custom_urls'] ?? '',
            'custom_url_mode' => $dv['custom_url_mode'] ?? 'contains',
            'display_order' => (int) ($dv['display_order'] ?? $bar->id),
            'scroll_speed' => (int) ($dv['scroll_speed'] ?? 45),
            'page_speeds' => is_array($dv['page_speeds'] ?? null) ? $dv['page_speeds'] : [],
            'scroll_direction' => $dv['scroll_direction'] ?? 'ltr',
            'loop_mode' => $dv['loop_mode'] ?? 'infinite',
            'pause_on_hover' => (int) ($dv['pause_on_hover'] ?? 1),
            'gap_between_items' => (int) ($dv['gap_between_items'] ?? 8),
            'animation_type' => $dv['animation_type'] ?? 'linear',
            'bar_height' => (int) ($dv['bar_height'] ?? 52),
            'bar_size' => $dv['bar_size'] ?? 'medium',
            'bar_thickness' => $dv['bar_thickness'] ?? 'normal',
            'default_text_size' => $dv['default_text_size'] ?? 'normal',
            'default_text_weight' => $dv['default_text_weight'] ?? 'normal',
            'bar_padding' => $dv['bar_padding'] ?? '',
            'bar_background_type' => $dv['bar_background_type'] ?? '',
            'bar_background_value' => $dv['bar_background_value'] ?? '',
            'bar_border' => $dv['bar_border'] ?? '',
            'bar_shadow' => $dv['bar_shadow'] ?? '',
            'hide_on_mobile' => (int) ($dv['hide_on_mobile'] ?? 0),
            'hide_on_desktop' => (int) ($dv['hide_on_desktop'] ?? 0),
            'container_mode' => $dv['container_mode'] ?? 'full',
            'align' => $dv['align'] ?? 'center',
            'z_index' => (int) ($dv['z_index'] ?? 10),
            'sticky' => (int) ($dv['sticky'] ?? 0),
            'offset_top' => $dv['offset_top'] ?? '0px',
            'custom_x_percent' => (float) ($dv['custom_x_percent'] ?? 0),
            'custom_y_px' => (int) ($dv['custom_y_px'] ?? 0),
            'custom_width_percent' => (int) ($dv['custom_width_percent'] ?? 100),
            'items' => $items,
        ]);
        $isCustomMode = true;
        $scrollbarMode = 'custom';
        return view('admin.frontend.scrollbar_form', compact('pageTitle', 'bar', 'formData', 'isCustomMode', 'scrollbarMode'));
    }

    private static function scrollbarDefaultFormData()
    {
        return [
            'title' => '',
            'position' => 'header_below',
            'template' => 'glass',
            'status' => 1,
            'visibility' => 'public',
            'visibility_users' => 'all',
            'visibility_pages' => 'all',
            'custom_urls' => '',
            'custom_url_mode' => 'contains',
            'schedule_start' => '',
            'schedule_end' => '',
            'display_order' => 0,
            'scroll_speed' => 45,
            'page_speeds' => [],
            'scroll_direction' => 'ltr',
            'loop_mode' => 'infinite',
            'pause_on_hover' => 1,
            'gap_between_items' => 8,
            'animation_type' => 'linear',
            'bar_height' => 52,
            'bar_size' => 'medium',
            'bar_thickness' => 'normal',
            'default_text_size' => 'normal',
            'default_text_weight' => 'normal',
            'bar_padding' => '',
            'width_type' => 'full',
            'width_value' => '',
            'max_width' => '',
            'bar_background_type' => '',
            'bar_background_value' => '',
            'bar_border' => '',
            'bar_shadow' => '',
            'hide_on_mobile' => 0,
            'hide_on_desktop' => 0,
            'container_mode' => 'full',
            'align' => 'center',
            'z_index' => 10,
            'sticky' => 0,
            'offset_top' => '0px',
            'custom_x_percent' => 0,
            'custom_y_px' => 0,
            'custom_width_percent' => 100,
            'loop_delay' => 0,
            'item_animation' => 'none',
            'icon_animation' => 'none',
            'hover_effect' => 'pause',
            'items' => [],
        ];
    }

    public function scrollbarSave(Request $request)
    {
        $scrollbarMode = (string) $request->input('scrollbar_mode', 'default');
        $isCustomMode = $scrollbarMode === 'custom';
        $dataKey = $isCustomMode ? ScrollbarService::CUSTOM_DATA_KEY : ScrollbarService::DATA_KEY;
        $request->validate([
            'title' => 'nullable|string|max:100',
            'position' => 'required|in:' . implode(',', \App\Services\ScrollbarService::positionValues()),
            'template' => 'required|in:glass,solid,minimal,dark,breaking_news,offer,alert,info',
            'status' => 'nullable|in:0,1',
            'visibility' => 'nullable|in:public,private',
            'visibility_users' => 'nullable|in:all,guest,logged_in',
            'visibility_pages' => 'nullable|in:all,home,product,category,all_products,product_detail,cart,checkout,custom_urls',
            'custom_urls' => 'nullable|string|max:5000',
            'custom_url_mode' => 'nullable|in:contains,exact,path',
            'schedule_start' => 'nullable|date',
            'schedule_end' => 'nullable|date|after_or_equal:schedule_start',
            'scroll_speed' => 'nullable|integer|min:1|max:100',
            'page_speeds' => 'nullable|array',
            'page_speeds.home' => 'nullable|integer|min:1|max:100',
            'page_speeds.all_products' => 'nullable|integer|min:1|max:100',
            'page_speeds.product_detail' => 'nullable|integer|min:1|max:100',
            'page_speeds.category' => 'nullable|integer|min:1|max:100',
            'page_speeds.cart' => 'nullable|integer|min:1|max:100',
            'page_speeds.checkout' => 'nullable|integer|min:1|max:100',
            'scroll_direction' => 'nullable|in:ltr,rtl,ttb,btt',
            'loop_mode' => 'nullable|in:infinite,once',
            'pause_on_hover' => 'nullable|in:0,1',
            'gap_between_items' => 'nullable|integer|min:0|max:100',
            'animation_type' => 'nullable|in:linear,ease,fade,slide,bounce',
            'bar_height' => 'nullable|integer|min:8|max:120',
            'bar_size' => 'nullable|in:extra_small,small,medium,large,extra_large',
            'bar_thickness' => 'nullable|in:ultra_thin,extra_thin,thin,normal,thick,extra_thick,ultra_thick',
            'default_text_size' => 'nullable|in:extra_small,small,normal,large,extra_large',
            'default_text_weight' => 'nullable|in:light,normal,medium,semibold,bold,extrabold',
            'bar_padding' => 'nullable|string|max:20',
            'bar_background_type' => 'nullable|in:solid,gradient,image',
            'bar_background_value' => 'nullable|string|max:500',
            'bar_border' => 'nullable|string|max:100',
            'bar_shadow' => 'nullable|string|max:100',
            'hide_on_mobile' => 'nullable|in:0,1',
            'hide_on_desktop' => 'nullable|in:0,1',
            'items' => 'nullable|array',
            'items.*.type' => 'required_with:items|in:text,emoji,image',
            'items.*.content_text' => 'nullable|string|max:2000',
            'items.*.content' => 'nullable|string|max:2000',
            'items.*.content_image' => 'nullable|string|max:255',
            'items.*.color' => 'nullable|string|max:20',
            'items.*.font_family' => 'nullable|string|max:100',
            'items.*.font_style' => 'nullable|in:normal,bold,italic',
            'items.*.font_size' => 'nullable|string|max:30',
            'items.*.font_weight' => 'nullable|string|max:10',
            'items.*.letter_spacing' => 'nullable|string|max:20',
            'items.*.text_transform' => 'nullable|in:none,uppercase,lowercase,capitalize',
            'items.*.is_active' => 'nullable|in:0,1',
            'width_type' => 'nullable|in:full,custom',
            'width_value' => 'nullable|string|max:50',
            'max_width' => 'nullable|string|max:50',
            'container_mode' => 'nullable|in:full,container',
            'align' => 'nullable|in:left,center,right',
            'z_index' => 'nullable|integer|min:0|max:999',
            'sticky' => 'nullable|in:0,1',
            'offset_top' => 'nullable|string|max:20',
            'custom_x_percent' => 'nullable|numeric|min:0|max:100',
            'custom_y_px' => 'nullable|integer|min:0|max:5000',
            'custom_width_percent' => 'nullable|integer|min:10|max:100',
            'loop_delay' => 'nullable|numeric|min:0|max:30',
            'item_animation' => 'nullable|in:none,fade,slide,zoom',
            'icon_animation' => 'nullable|in:none,pulse,bounce,glow',
            'hover_effect' => 'nullable|in:pause,dim,speed_down,none',
            'items.*.image_file' => 'nullable|image|mimes:jpeg,png,gif,webp|max:2048',
            'rich_content' => 'nullable|string|max:5000',
            'rich_segments_json' => 'nullable|string|max:20000',
        ]);
        $path = ScrollbarService::getScrollbarImagePath();
        $items = [];
        $rawItems = $request->input('items', []);
        $rawItems = is_array($rawItems) ? array_values($rawItems) : [];
        $richContent = trim((string) $request->input('rich_content', ''));
        $richSegmentsJson = trim((string) $request->input('rich_segments_json', ''));
        $isRichMode = false;
        if ($richSegmentsJson !== '') {
            $decoded = json_decode($richSegmentsJson, true);
            if (is_array($decoded)) {
                $segments = [];
                foreach ($decoded as $seg) {
                    $seg = is_array($seg) ? $seg : [];
                    $txtRaw = (string) ($seg['text'] ?? '');
                    $txtRaw = str_replace(["\r\n", "\r", "\n", "\t"], ' ', $txtRaw);
                    if (trim($txtRaw) === '') {
                        continue;
                    }
                    $segments[] = [
                        // Keep user typed spacing to prevent unwanted emoji/text gap.
                        'text' => $txtRaw,
                        'color' => (string) ($seg['color'] ?? '#333333'),
                        'weight' => (string) ($seg['weight'] ?? '400'),
                        'font_family' => (string) ($seg['font_family'] ?? 'inherit'),
                        'font_size' => (string) ($seg['font_size'] ?? ''),
                    ];
                }
                if (!empty($segments) || $richContent !== '') {
                    $rawItems = [
                        [
                            'type' => 'text',
                            'content_text' => $richContent,
                            'content' => $richContent,
                            'color' => '#333333',
                            'font_weight' => '400',
                            'is_active' => 1,
                            'segments' => $segments,
                        ]
                    ];
                    $isRichMode = true;
                }
            }
        } elseif ($richContent !== '' && empty($rawItems)) {
            $rawItems = [
                [
                    'type' => 'text',
                    'content_text' => $richContent,
                    'content' => $richContent,
                    'color' => '#333333',
                    'font_weight' => '400',
                    'is_active' => 1,
                ]
            ];
            $isRichMode = true;
        }
        $existingBar = $request->id ? Frontend::where('data_keys', $dataKey)->find($request->id) : null;
        $existingItems = [];
        if ($existingBar && !empty($existingBar->data_values)) {
            $dv = $existingBar->data_values;
            $raw = is_object($dv) ? ($dv->items ?? []) : ($dv['items'] ?? []);
            $raw = is_array($raw) ? $raw : (array) $raw;
            $existingItems = array_values(array_map(function ($e) {
                return ScrollbarService::itemToArray($e);
            }, $raw));
        }
        foreach ($rawItems as $idx => $item) {
            $item = is_array($item) ? $item : [];
            $idx = (int) $idx;
            if ($isRichMode) {
                $item['is_active'] = isset($item['is_active']) ? (int) $item['is_active'] : 1;
            } else {
                $item['is_active'] = $request->has('items.' . $idx . '.is_active') ? 1 : 0;
            }
            $type = $item['type'] ?? 'text';
            if ($type === 'image') {
                $fileKey = 'items.' . $idx . '.image_file';
                if ($request->hasFile($fileKey)) {
                    $file = $request->file($fileKey);
                    $ext = strtolower($file->getClientOriginalExtension() ?: 'png');
                    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
                        $ext = 'png';
                    }
                    $filename = 'scrollbar_' . time() . '_' . uniqid() . '.' . $ext;
                    if (!$file->move($path, $filename)) {
                        $notify[] = ['error', 'Failed to upload image for item ' . ($idx + 1) . '.'];
                        return back()->withInput()->withNotify($notify);
                    }
                    $item['content_image'] = $filename;
                    $item['content'] = $filename;
                } elseif (!empty($existingItems[$idx]) && ($existingItems[$idx]['type'] ?? '') === 'image') {
                    $item['content'] = (string) ($existingItems[$idx]['content'] ?? '');
                    $item['content_image'] = $item['content'];
                }
            } else {
                // Always read content_text from request so saved text is never lost
                $contentText = $request->input('items.' . $idx . '.content_text');
                if ($contentText !== null && $contentText !== '') {
                    $item['content_text'] = is_string($contentText) ? $contentText : (string) $contentText;
                }
                $item['content'] = trim((string) ($item['content_text'] ?? $item['content'] ?? ''));
            }
            $normalized = ScrollbarService::normalizeItem($item, $idx, $existingItems);
            if ($normalized !== null) {
                if ($type === 'image' && ($normalized['content'] ?? '') === '') {
                    continue;
                }
                $items[] = $normalized;
            }
        }
        if (empty($items)) {
            if (!empty($existingItems)) {
                $items = $existingItems;
            } else {
                $notify[] = ['error', 'Add at least one item (text, emoji or image).'];
                return back()->withInput()->withNotify($notify);
            }
        }
        $displayOrder = (int) $request->input('display_order', 0);
        if ($request->id) {
            $bar = Frontend::where('data_keys', $dataKey)->findOrFail($request->id);
        } else {
            $bar = new Frontend();
            $bar->data_keys = $dataKey;
            $maxId = (int) Frontend::where('data_keys', $dataKey)->max('id');
            $displayOrder = $displayOrder ?: $maxId + 1;
        }
        $scrollSpeed = (int) ($request->input('scroll_speed') ?: 45);
        if ($scrollSpeed < 1 || $scrollSpeed > 100) {
            $scrollSpeed = 45;
        }
        $rawPageSpeeds = $request->input('page_speeds', []);
        $allowedPageKeys = ['home', 'all_products', 'product_detail', 'category', 'cart', 'checkout'];
        $pageSpeeds = [];
        if (is_array($rawPageSpeeds)) {
            foreach ($allowedPageKeys as $pageKey) {
                if (!array_key_exists($pageKey, $rawPageSpeeds) || $rawPageSpeeds[$pageKey] === '' || $rawPageSpeeds[$pageKey] === null) {
                    continue;
                }
                $speedVal = (int) $rawPageSpeeds[$pageKey];
                if ($speedVal >= 1 && $speedVal <= 100) {
                    $pageSpeeds[$pageKey] = $speedVal;
                }
            }
        }
        $barHeight = (int) ($request->input('bar_height') ?: 52);
        if ($barHeight < 8 || $barHeight > 120) {
            $barHeight = 52;
        }
        $visibilityPages = $request->input('visibility_pages', 'all');
        if (!$isCustomMode && $visibilityPages === 'custom_urls') {
            $visibilityPages = 'all';
        }
        $positionLabelMap = \App\Services\ScrollbarService::POSITIONS;
        $selectedPosition = (string) $request->position;
        $positionLabel = (string) ($positionLabelMap[$selectedPosition] ?? $selectedPosition ?: 'Scroll Bar');
        $titleInput = trim((string) $request->input('title', ''));
        $autoTitle = $titleInput !== '' ? $titleInput : ('[' . $positionLabel . '] ' . now()->format('Y-m-d H:i'));
        $bar->data_values = (object) array_merge((array) ($bar->data_values ?? (object) []), [
            'title' => $autoTitle,
            'display_order' => $displayOrder,
            'position' => $request->position,
            'template' => $request->template,
            'status' => (int) ($request->status ?? 1),
            'visibility' => $request->input('visibility', 'public'),
            'visibility_users' => $request->input('visibility_users', 'all'),
            'visibility_pages' => $visibilityPages,
            'custom_urls' => $isCustomMode ? trim((string) $request->input('custom_urls', '')) : '',
            'custom_url_mode' => $isCustomMode ? $request->input('custom_url_mode', 'contains') : 'contains',
            'schedule_start' => $request->input('schedule_start') ?: null,
            'schedule_end' => $request->input('schedule_end') ?: null,
            'scroll_speed' => $scrollSpeed,
            'page_speeds' => $pageSpeeds,
            'scroll_direction' => $request->input('scroll_direction', 'ltr'),
            'loop_mode' => $request->input('loop_mode', 'infinite'),
            'pause_on_hover' => (int) ($request->input('pause_on_hover', 1)),
            'gap_between_items' => (int) ($request->input('gap_between_items', 8)),
            'animation_type' => $request->input('animation_type', 'linear'),
            'bar_height' => $barHeight,
            'bar_size' => $request->input('bar_size', 'medium'),
            'bar_thickness' => $request->input('bar_thickness', 'normal'),
            'default_text_size' => $request->input('default_text_size', 'normal'),
            'default_text_weight' => $request->input('default_text_weight', 'normal'),
            'bar_padding' => $request->input('bar_padding') ?: null,
            'width_type' => $request->input('width_type', 'full'),
            'width_value' => trim((string) $request->input('width_value', '')),
            'max_width' => trim((string) $request->input('max_width', '')),
            'bar_background_type' => $request->input('bar_background_type') ?: null,
            'bar_background_value' => $request->input('bar_background_value') ?: null,
            'bar_border' => $request->input('bar_border') ?: null,
            'bar_shadow' => $request->input('bar_shadow') ?: null,
            'hide_on_mobile' => (int) ($request->input('hide_on_mobile', 0)),
            'hide_on_desktop' => (int) ($request->input('hide_on_desktop', 0)),
            'container_mode' => $request->input('container_mode', 'full'),
            'align' => $request->input('align', 'center'),
            'z_index' => (int) ($request->input('z_index', 10)),
            'sticky' => (int) ($request->input('sticky', 0)),
            'offset_top' => trim((string) ($request->input('offset_top', '0px'))),
            'custom_x_percent' => $isCustomMode ? (float) ($request->input('custom_x_percent', 0)) : 0,
            'custom_y_px' => $isCustomMode ? (int) ($request->input('custom_y_px', 0)) : 0,
            'custom_width_percent' => $isCustomMode ? (int) ($request->input('custom_width_percent', 100)) : 100,
            'loop_delay' => (float) ($request->input('loop_delay', 0)),
            'item_animation' => $request->input('item_animation', 'none'),
            'icon_animation' => $request->input('icon_animation', 'none'),
            'hover_effect' => $request->input('hover_effect', 'pause'),
            'items' => $items,
        ]);
        $bar->save();
        Cache::forget(ScrollbarService::CACHE_KEY_RAW);
        Cache::forget(ScrollbarService::CACHE_KEY_SETTINGS);
        $notify[] = ['success', 'Scroll bar saved successfully.'];
        if ($isCustomMode) {
            return redirect()->route('admin.frontend.sections.scrollbar2.edit', ['id' => $bar->id])->withNotify($notify);
        }
        return redirect()->route('admin.frontend.sections.scrollbar.edit', ['id' => $bar->id])->withNotify($notify);
    }

    /**
     * Toggle scroll bar publish status (Published <-> Draft) without opening edit modal.
     */
    public function scrollbarToggleStatus(Request $request, $id)
    {
        $bar = Frontend::where('data_keys', ScrollbarService::DATA_KEY)->findOrFail($id);
        $dv = $bar->data_values ?? (object) [];
        $current = (int) ($dv->status ?? 1);
        $dv->status = $current === 1 ? 0 : 1;
        $bar->data_values = $dv;
        $bar->save();
        Cache::forget(ScrollbarService::CACHE_KEY_RAW);
        $notify[] = ['success', $dv->status === 1 ? 'Scroll bar is now published.' : 'Scroll bar is now draft (hidden from site).'];
        return redirect()->route('admin.frontend.sections.scrollbar')->withNotify($notify);
    }

    /**
     * Toggle visibility (public/private) quickly from list.
     */
    public function scrollbarToggleVisibility(Request $request, $id)
    {
        $bar = Frontend::where('data_keys', ScrollbarService::DATA_KEY)->findOrFail($id);
        $dv = $bar->data_values ?? (object) [];
        $current = (string) ($dv->visibility ?? 'public');
        $dv->visibility = $current === 'private' ? 'public' : 'private';
        $bar->data_values = $dv;
        $bar->save();
        Cache::forget(ScrollbarService::CACHE_KEY_RAW);
        $notify[] = ['success', $dv->visibility === 'public' ? 'Visibility সেট করা হয়েছে: Public' : 'Visibility সেট করা হয়েছে: Private'];
        return redirect()->route('admin.frontend.sections.scrollbar')->withNotify($notify);
    }

    /**
     * Duplicate an existing scroll bar (creates a new copy).
     */
    public function scrollbarDuplicate($id)
    {
        $bar = Frontend::where('data_keys', ScrollbarService::DATA_KEY)->findOrFail($id);
        $newBar = new Frontend();
        $newBar->data_keys = ScrollbarService::DATA_KEY;
        $dv = $bar->data_values ?? (object) [];
        $dv = is_object($dv) ? (array) $dv : $dv;
        $title = $dv['title'] ?? '';
        $dv['title'] = $title ? $title . ' (Copy)' : 'Scroll Bar (Copy)';
        $dv['display_order'] = (int) (Frontend::where('data_keys', ScrollbarService::DATA_KEY)->max('id') ?? 0) + 1;
        $dv['status'] = 0;
        $newBar->data_values = (object) $dv;
        $newBar->save();
        Cache::forget(ScrollbarService::CACHE_KEY_RAW);
        $notify[] = ['success', 'Scroll bar duplicated. You can edit and publish the copy.'];
        return redirect()->route('admin.frontend.sections.scrollbar.edit', ['id' => $newBar->id])->withNotify($notify);
    }

    /**
     * Return scroll bar data as JSON for edit form. Exact saved structure (single source of truth).
     */
    public function scrollbarData($id)
    {
        $bar = Frontend::where('data_keys', ScrollbarService::DATA_KEY)->find($id);
        if (!$bar) {
            return response()->json(['error' => 'Not found'], 404);
        }
        $dv = $bar->data_values;
        if (is_object($dv)) {
            $dv = json_decode(json_encode($dv), true);
        }
        $dv = is_array($dv) ? $dv : [];
        $rawItems = $dv['items'] ?? [];
        $rawItems = is_array($rawItems) ? $rawItems : (array) $rawItems;
        $items = [];
        foreach ($rawItems as $rawIt) {
            $item = ScrollbarService::itemToArray($rawIt);
            // Ensure content is never empty when we have segments or raw text
            $content = trim((string) ($item['content'] ?? ''));
            if ($content === '' && !empty($item['segments']) && is_array($item['segments'])) {
                $parts = [];
                foreach ($item['segments'] as $s) {
                    $parts[] = trim((string) (is_array($s) ? ($s['text'] ?? '') : ($s->text ?? '')));
                }
                $item['content'] = implode(' ', $parts);
            }
            $items[] = $item;
        }
        $items = array_values($items);
        $safeDv = [
            'title' => $dv['title'] ?? '',
            'position' => $dv['position'] ?? 'header_below',
            'template' => $dv['template'] ?? 'glass',
            'status' => (int) ($dv['status'] ?? 1),
            'visibility' => $dv['visibility'] ?? 'public',
            'visibility_users' => $dv['visibility_users'] ?? 'all',
            'visibility_pages' => $dv['visibility_pages'] ?? 'all',
            'custom_urls' => $dv['custom_urls'] ?? '',
            'custom_url_mode' => $dv['custom_url_mode'] ?? 'contains',
            'schedule_start' => $dv['schedule_start'] ?? '',
            'schedule_end' => $dv['schedule_end'] ?? '',
            'display_order' => (int) ($dv['display_order'] ?? $bar->id),
            'scroll_speed' => (int) ($dv['scroll_speed'] ?? 45),
            'page_speeds' => is_array($dv['page_speeds'] ?? null) ? $dv['page_speeds'] : [],
            'scroll_direction' => $dv['scroll_direction'] ?? 'ltr',
            'loop_mode' => $dv['loop_mode'] ?? 'infinite',
            'pause_on_hover' => (int) ($dv['pause_on_hover'] ?? 1),
            'gap_between_items' => (int) ($dv['gap_between_items'] ?? 8),
            'animation_type' => $dv['animation_type'] ?? 'linear',
            'bar_height' => (int) ($dv['bar_height'] ?? 52),
            'bar_size' => $dv['bar_size'] ?? 'medium',
            'bar_thickness' => $dv['bar_thickness'] ?? 'normal',
            'default_text_size' => $dv['default_text_size'] ?? 'normal',
            'default_text_weight' => $dv['default_text_weight'] ?? 'normal',
            'bar_padding' => $dv['bar_padding'] ?? '',
            'width_type' => $dv['width_type'] ?? 'full',
            'width_value' => $dv['width_value'] ?? '',
            'max_width' => $dv['max_width'] ?? '',
            'bar_background_type' => $dv['bar_background_type'] ?? '',
            'bar_background_value' => $dv['bar_background_value'] ?? '',
            'bar_border' => $dv['bar_border'] ?? '',
            'bar_shadow' => $dv['bar_shadow'] ?? '',
            'hide_on_mobile' => (int) ($dv['hide_on_mobile'] ?? 0),
            'hide_on_desktop' => (int) ($dv['hide_on_desktop'] ?? 0),
            'container_mode' => $dv['container_mode'] ?? 'full',
            'align' => $dv['align'] ?? 'center',
            'z_index' => (int) ($dv['z_index'] ?? 10),
            'sticky' => (int) ($dv['sticky'] ?? 0),
            'offset_top' => $dv['offset_top'] ?? '0px',
            'custom_x_percent' => (float) ($dv['custom_x_percent'] ?? 0),
            'custom_y_px' => (int) ($dv['custom_y_px'] ?? 0),
            'custom_width_percent' => (int) ($dv['custom_width_percent'] ?? 100),
            'loop_delay' => (float) ($dv['loop_delay'] ?? 0),
            'item_animation' => $dv['item_animation'] ?? 'none',
            'icon_animation' => $dv['icon_animation'] ?? 'none',
            'hover_effect' => $dv['hover_effect'] ?? 'pause',
            'items' => $items,
        ];
        return response()->json($safeDv);
    }

    /**
     * Save global scrollbar enable/disable (Quick Control).
     */
    public function scrollbarSettingsSave(Request $request)
    {
        $request->validate(['scrollbar_enabled' => 'nullable|in:0,1']);
        $enabled = (int) $request->input('scrollbar_enabled', 1);
        $settings = Frontend::firstOrNew(['data_keys' => ScrollbarService::SETTINGS_KEY]);
        $dv = (array) ($settings->data_values ?? []);
        $dv['enabled'] = $enabled;
        $settings->data_values = (object) $dv;
        $settings->save();
        Cache::forget(ScrollbarService::CACHE_KEY_RAW);
        Cache::forget(ScrollbarService::CACHE_KEY_SETTINGS);
        $notify[] = ['success', $enabled ? 'Scroll bars are now enabled on the site.' : 'Scroll bars are now disabled on the site.'];
        return redirect()->route('admin.frontend.sections.scrollbar')->withNotify($notify);
    }

    /**
     * Live Banner Preview: single banner as it appears on frontend (new tab).
     */
    public function bannerPreview($id)
    {
        $banner = Frontend::where('data_keys', BannerService::DATA_KEY_ELEMENT)->findOrFail($id);
        if (empty($banner->data_values->image ?? null)) {
            return response(__('Banner has no image yet.'), 404);
        }
        $settings = Frontend::where('data_keys', 'banner.content')->orderBy('id', 'desc')->first();
        $bannerWidth = $settings ? (int) (@$settings->data_values->banner_width ?? 2560) : 2560;
        $bannerHeight = $settings ? (int) (@$settings->data_values->banner_height ?? 600) : 600;
        if ($bannerWidth < 100)
            $bannerWidth = 2560;
        if ($bannerHeight < 50)
            $bannerHeight = 600;
        $slideIntervalSeconds = $settings ? (int) (@$settings->data_values->slide_interval_seconds ?? 5) : 5;
        return view('admin.frontend.banner_preview', [
            'bannerElement' => $banner,
            'bannerWidth' => $bannerWidth,
            'bannerHeight' => $bannerHeight,
            'slideIntervalSeconds' => $slideIntervalSeconds,
        ]);
    }

    /**
     * Return HTML fragment for one scroll bar (WYSIWYG admin preview) — same partial, same CSS.
     */
    public function scrollbarPreview($id)
    {
        $bar = Frontend::where('data_keys', ScrollbarService::DATA_KEY)->find($id);
        if (!$bar) {
            return response('', 404);
        }
        $bars = collect([$bar]);
        $position = is_object($bar->data_values) ? ($bar->data_values->position ?? 'header_below') : ($bar->data_values['position'] ?? 'header_below');
        return view('admin.frontend.scrollbar_preview', [
            'bars' => $bars,
            'position' => $position,
        ]);
    }

    /**
     * Live preview from GET params (for admin modal: see changes before save).
     */
    public function scrollbarPreviewLive(Request $request)
    {
        $items = [];
        $itemsJson = $request->input('items', '[]');
        if (is_string($itemsJson)) {
            $decoded = json_decode($itemsJson, true);
            if (is_array($decoded)) {
                foreach ($decoded as $it) {
                    $it = is_array($it) ? $it : (array) $it;
                    $items[] = [
                        'type' => $it['type'] ?? 'text',
                        'content' => $it['content'] ?? $it['content_text'] ?? '',
                        'color' => $it['color'] ?? '#333333',
                        'font_size' => $it['font_size'] ?? '',
                        'font_weight' => $it['font_weight'] ?? '400',
                        'font_family' => $it['font_family'] ?? 'inherit',
                        'font_style' => $it['font_style'] ?? 'normal',
                        'is_active' => 1,
                        'segments' => (isset($it['segments']) && is_array($it['segments'])) ? $it['segments'] : [],
                    ];
                }
            }
        }
        if (empty($items)) {
            $items = [['type' => 'text', 'content' => __('Preview text'), 'color' => '#ffffff', 'font_size' => '', 'font_weight' => '400', 'font_family' => 'inherit', 'font_style' => 'normal', 'is_active' => 1]];
        }
        $position = $request->input('position', 'header_below');
        $template = $request->input('template', 'offer');
        $barHeight = (int) $request->input('bar_height', 52);
        if ($barHeight < 8 || $barHeight > 120) {
            $barHeight = 52;
        }
        $scrollSpeed = (int) $request->input('scroll_speed', 45);
        if ($scrollSpeed < 1 || $scrollSpeed > 100) {
            $scrollSpeed = 45;
        }
        $barBgType = $request->input('bar_background_type', '');
        $barBgValue = $request->input('bar_background_value', '');
        if ($request->has('bar_color') && $request->input('bar_color') !== '') {
            $barBgType = 'solid';
            $barBgValue = $request->input('bar_color');
        }
        $dataValues = (object) [
            'position' => $position,
            'template' => $template,
            'bar_height' => $barHeight,
            'scroll_speed' => $scrollSpeed,
            'scroll_direction' => $request->input('scroll_direction', 'ltr'),
            'gap_between_items' => (int) $request->input('gap_between_items', 8),
            'bar_background_type' => $barBgType ?: null,
            'bar_background_value' => $barBgValue ?: null,
            'bar_size' => $request->input('bar_size', 'medium'),
            'bar_thickness' => $request->input('bar_thickness', 'normal'),
            'default_text_size' => $request->input('default_text_size', 'normal'),
            'default_text_weight' => $request->input('default_text_weight', 'normal'),
            'width_type' => $request->input('width_type', 'full'),
            'width_value' => $request->input('width_value', ''),
            'max_width' => $request->input('max_width', ''),
            'align' => $request->input('align', 'center'),
            'container_mode' => $request->input('container_mode', 'full'),
            'z_index' => (int) $request->input('z_index', 10),
            'sticky' => (int) $request->input('sticky', 0),
            'offset_top' => $request->input('offset_top', '0px'),
            'custom_x_percent' => (float) $request->input('custom_x_percent', 0),
            'custom_y_px' => (int) $request->input('custom_y_px', 0),
            'custom_width_percent' => (int) $request->input('custom_width_percent', 100),
            'items' => $items,
        ];
        $bar = new \stdClass();
        $bar->id = 0;
        $bar->data_values = $dataValues;
        $bars = collect([$bar]);
        return view('admin.frontend.scrollbar_preview', [
            'bars' => $bars,
            'position' => $position,
        ]);
    }

    /**
     * Delete a scroll bar: remove DB record, all related image files, clear cache.
     */
    public function scrollbarDelete($id)
    {
        $mode = (string) request()->input('scrollbar_mode', 'default');
        $isCustomMode = $mode === 'custom';
        $dataKey = $isCustomMode ? ScrollbarService::CUSTOM_DATA_KEY : ScrollbarService::DATA_KEY;
        $bar = Frontend::where('data_keys', $dataKey)->find($id);
        if (!$bar) {
            $notify[] = ['error', 'Scroll bar not found.'];
            return redirect()->route('admin.frontend.sections.scrollbar')->withNotify($notify);
        }
        $path = rtrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, ScrollbarService::getScrollbarImagePath()), DIRECTORY_SEPARATOR);
        $dv = $bar->data_values ?? (object) [];
        $items = $dv->items ?? $dv['items'] ?? [];
        if (is_object($items)) {
            $items = (array) $items;
        }
        foreach ($items as $item) {
            $it = is_array($item) ? $item : (array) $item;
            if (($it['type'] ?? '') === 'image' && !empty($it['content'])) {
                $filename = preg_replace('/[^a-zA-Z0-9_.-]/', '', (string) $it['content']);
                if ($filename !== '') {
                    $f = $path . DIRECTORY_SEPARATOR . $filename;
                    if (file_exists($f) && is_file($f)) {
                        @unlink($f);
                    }
                }
            }
        }
        $bar->delete();
        Cache::forget(ScrollbarService::CACHE_KEY_RAW);
        Cache::forget(ScrollbarService::CACHE_KEY_SETTINGS);
        $notify[] = ['success', 'Scroll bar removed successfully.'];
        return redirect()->route('admin.frontend.sections.scrollbar')->withNotify($notify);
    }
}

