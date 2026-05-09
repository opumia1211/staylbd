<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Frontend;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class FooterBuilderController extends Controller
{
    public const FOOTER_CACHE_KEY = 'frontend_footer_data';

    /** Valid URL slugs for each footer section (one link = one feature). Payment Methods & Legal are under payment-shipping and Policy. */
    public const FOOTER_SECTIONS = [
        'company-info'        => ['title' => 'Company Info',             'icon' => 'las la-building'],
        'company-contacts'    => ['title' => 'Company Contacts',         'icon' => 'las la-address-book'],
        'quick-links'         => ['title' => 'Quick Links',             'icon' => 'las la-link'],
        'support-center'      => ['title' => 'Support Center',          'icon' => 'las la-headset'],
        'security-badges'     => ['title' => 'Security Badges',         'icon' => 'las la-shield-alt'],
        'payment-shipping'    => ['title' => 'Payment & Shipping',      'icon' => 'las la-truck'],
        'app-promotion'       => ['title' => 'App Promotion',           'icon' => 'las la-mobile-alt'],
        'custom-ads'          => ['title' => 'Custom Ads',              'icon' => 'las la-ad'],
        'return-policy'       => ['title' => 'Return Policy Form',      'icon' => 'las la-undo'],
        'copyright'           => ['title' => 'Newsletter, Copyright & Seller',  'icon' => 'las la-copyright'],
    ];

    /** Load all footer data (shared by index with tabs and showSection). */
    protected function getFooterData(): array
    {
        return [
            'footerContent'   => Frontend::where('data_keys', 'footer.content')->orderBy('id', 'desc')->first(),
            'footerElements'  => Frontend::where('data_keys', 'footer.element')->orderByRaw('CAST(JSON_UNQUOTE(JSON_EXTRACT(data_values, "$.display_order")) AS UNSIGNED) ASC')->orderBy('id')->get(),
            'companyInfo'     => Frontend::where('data_keys', 'footer.company_info')->orderBy('id', 'desc')->first(),
            'quickLinks'      => Frontend::where('data_keys', 'footer.quick_links')->orderByRaw('CAST(JSON_UNQUOTE(JSON_EXTRACT(data_values, "$.display_order")) AS UNSIGNED) ASC')->orderBy('id')->get(),
            'supportCenter'   => Frontend::where('data_keys', 'footer.support_center')->orderBy('id', 'desc')->first(),
            'securityBadges'  => Frontend::where('data_keys', 'footer.security_badges')->orderByRaw('CAST(JSON_UNQUOTE(JSON_EXTRACT(data_values, "$.display_order")) AS UNSIGNED) ASC')->orderBy('id')->get(),
            'shippingPayment' => Frontend::where('data_keys', 'footer.shipping_payment')->orderBy('id', 'desc')->first(),
            'appPromotion'    => Frontend::where('data_keys', 'footer.app_promotion')->orderBy('id', 'desc')->first(),
            'appPromotionItems' => Frontend::where('data_keys', 'footer.app_promotion_item')->orderByRaw('CAST(JSON_UNQUOTE(JSON_EXTRACT(data_values, "$.display_order")) AS UNSIGNED) ASC')->orderBy('id')->get(),
            'customAds'       => Frontend::where('data_keys', 'footer.custom_ads')->orderByRaw('CAST(JSON_UNQUOTE(JSON_EXTRACT(data_values, "$.display_order")) AS UNSIGNED) ASC')->orderBy('id')->get(),
            'returnPolicy'    => Frontend::where('data_keys', 'footer.return_policy')->orderBy('id', 'desc')->first(),
        ];
    }

    /** Footer builder index: show list of section links + optional full tabbed view. */
    public function index()
    {
        $pageTitle = __('Footer Builder');
        $sections = self::FOOTER_SECTIONS;
        return view('admin.frontend.footer.footer_index', compact('pageTitle', 'sections'));
    }

    /** Full footer builder (all sections in one page with tabs). */
    public function fullBuilder()
    {
        $pageTitle = __('Footer Builder') . ' — ' . __('All Sections');
        $data = $this->getFooterData();
        $data['pageTitle'] = $pageTitle;
        return view('admin.frontend.footer_builder', $data);
    }

    /** Single footer section page (e.g. /frontend/footer/company-info). */
    public function showSection(string $section)
    {
        if (!array_key_exists($section, self::FOOTER_SECTIONS)) {
            abort(404);
        }
        $pageTitle = __(self::FOOTER_SECTIONS[$section]['title']);
        $data = $this->getFooterData();
        $data['pageTitle'] = $pageTitle;
        $data['sectionSlug'] = $section;
        $data['sectionTitle'] = __(self::FOOTER_SECTIONS[$section]['title']);

        if ($section === 'payment-shipping') {
            $editId = request()->query('edit');
            $data['editingPaymentItem'] = null;
            if ($editId !== null && $editId !== '' && (int) $editId > 0) {
                $data['editingPaymentItem'] = Frontend::where('data_keys', 'footer.element')->find((int) $editId);
            }
        }
        if ($section === 'app-promotion') {
            $editId = request()->query('edit');
            $data['editingAppItem'] = null;
            if ($editId !== null && $editId !== '' && (int) $editId > 0) {
                $data['editingAppItem'] = Frontend::where('data_keys', 'footer.app_promotion_item')->find((int) $editId);
            }
        }

        return view('admin.frontend.footer.footer_section', $data);
    }

    public function saveSection(Request $request)
    {
        $section = $request->input('section');
        $validSections = [
            'company_info', 'support_center', 'shipping_payment', 'app_promotion', 'footer_content'
        ];
        if (!in_array($section, $validSections, true)) {
            $notify[] = ['error', 'Invalid section.'];
            return back()->withNotify($notify);
        }

        $key = 'footer.' . $section;
        $content = Frontend::firstOrNew(['data_keys' => $key]);
        $content->data_keys = $key;
        $values = (array) ($content->data_values ?? []);

        if ($section === 'company_info') {
            $values['show'] = (int) $request->input('show', 0);
            $values['about_text'] = $request->input('about_text', '');
            $values['mission_text'] = $request->input('mission_text', '');
            $values['registration_info'] = $request->input('registration_info', '');
            $values['business_license'] = $request->input('business_license', '');
            $values['contact_phone'] = trim((string) $request->input('contact_phone', ''));
            $values['contact_email'] = trim((string) $request->input('contact_email', ''));
        } elseif ($section === 'support_center') {
            $values['enabled'] = (int) $request->input('enabled', 1);
            $values['help_center_url'] = $request->input('help_center_url', '');
            $values['return_policy_url'] = $request->input('return_policy_url', '');
            $values['refund_policy_url'] = $request->input('refund_policy_url', '');
            $values['track_order_url'] = $request->input('track_order_url', '');
            $values['live_chat_enabled'] = (int) $request->input('live_chat_enabled', 0);
            $values['support_ticket_enabled'] = (int) $request->input('support_ticket_enabled', 1);
            $values['support_email'] = $request->input('support_email', '');
        } elseif ($section === 'shipping_payment') {
            $values['show_payment_icons'] = (int) $request->input('show_payment_icons', 1);
            $values['show_shipping_info'] = (int) $request->input('show_shipping_info', 1);
            $values['cod_enabled'] = (int) $request->input('cod_enabled', 1);
            $values['estimated_delivery_text'] = $request->input('estimated_delivery_text', '');
            $values['shipping_partners_text'] = $request->input('shipping_partners_text', '');
            $values['delivery_zones_text'] = $request->input('delivery_zones_text', '');
        } elseif ($section === 'app_promotion') {
            $values['enabled'] = (int) $request->input('enabled', 0);
            // Keep existing android_url, ios_url, qr_image unchanged (managed via App / Software Links items)
        } elseif ($section === 'footer_content') {
            $key = 'footer.content'; // Keep newsletter/social titles and copyright in footer.content for frontend cache
            $content = Frontend::firstOrNew(['data_keys' => $key]);
            $content->data_keys = $key;
            $values = (array) ($content->data_values ?? []);
            $values['subscribe_title'] = $request->input('subscribe_title', '');
            $values['subscribe_subtitle'] = $request->input('subscribe_subtitle', '');
            $values['connect_title'] = $request->input('connect_title', '');
            $values['copyright_text'] = $request->input('copyright_text', '');
            $values['seller_account_enabled'] = $request->boolean('seller_account_enabled') ? 1 : 0;
            $values['seller_account_url'] = $request->input('seller_account_url', '');
            $content->data_values = (object) $values;
            $content->save();
            $this->clearFooterCache();
            $notify[] = ['success', __('Footer section saved successfully.')];
            return back()->withNotify($notify);
        }

        $content->data_values = (object) $values;
        $content->save();
        $this->clearFooterCache();
        $notify[] = ['success', __('Footer section saved successfully.')];
        return back()->withNotify($notify);
    }

    public function saveQuickLink(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:150',
            'url' => 'nullable|string|max:500',
            'display_order' => 'nullable|integer|min:0',
        ]);
        $id = $request->input('id');
        $url = $request->input('url', '#');
        $order = (int) $request->input('display_order', 0);

        if ($id) {
            $item = Frontend::where('data_keys', 'footer.quick_links')->findOrFail($id);
        } else {
            $item = new Frontend();
            $item->data_keys = 'footer.quick_links';
            $item->data_values = (object) [];
        }
        $item->data_values = (object) [
            'title' => $request->input('title'),
            'url' => $url,
            'display_order' => $order,
        ];
        $item->save();
        $this->clearFooterCache();
        $notify[] = ['success', $id ? __('Quick link updated.') : __('Quick link added.')];
        return back()->withNotify($notify);
    }

    public function deleteQuickLink($id)
    {
        $item = Frontend::where('data_keys', 'footer.quick_links')->findOrFail($id);
        $item->delete();
        $this->clearFooterCache();
        $notify[] = ['success', __('Quick link removed.')];
        return back()->withNotify($notify);
    }

    public function saveSecurityBadge(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:150',
            'url' => 'nullable|string|max:500',
            'display_order' => 'nullable|integer|min:0',
            'image' => ['nullable', 'file', 'max:2048', new FileTypeValidate(['jpg', 'jpeg', 'png', 'webp', 'gif'])],
        ]);
        $id = $request->input('id');
        $path = 'assets/images/frontend/footer';

        if ($id) {
            $item = Frontend::where('data_keys', 'footer.security_badges')->findOrFail($id);
            $values = (array) ($item->data_values ?? []);
        } else {
            $item = new Frontend();
            $item->data_keys = 'footer.security_badges';
            $values = [];
        }
        if ($request->hasFile('image')) {
            $values['image'] = fileUploader($request->file('image'), $path, '80x80', $values['image'] ?? null);
        }
        $values['title'] = $request->input('title', '');
        $values['url'] = $request->input('url', '');
        $values['display_order'] = (int) $request->input('display_order', 0);
        $item->data_values = (object) $values;
        $item->save();
        $this->clearFooterCache();
        $notify[] = ['success', $id ? __('Security badge updated.') : __('Security badge added.')];
        return back()->withNotify($notify);
    }

    public function deleteSecurityBadge($id)
    {
        $item = Frontend::where('data_keys', 'footer.security_badges')->findOrFail($id);
        $item->delete();
        $this->clearFooterCache();
        $notify[] = ['success', __('Security badge removed.')];
        return back()->withNotify($notify);
    }

    /** Save payment/bank icon (footer.element) - image + optional URL so icon works as button on footer */
    public function savePaymentIcon(Request $request)
    {
        $idInput = $request->input('id');
        $idInput = is_string($idInput) ? trim($idInput) : $idInput;
        $isUpdate = $idInput !== null && $idInput !== '' && (int) $idInput > 0;
        $id = $isUpdate ? (int) $idInput : null;

        $paymentIconMimes = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg', 'avif'];
        $imageRules = ['file', 'max:5120', new FileTypeValidate($paymentIconMimes)];
        if (!$isUpdate) {
            $imageRules[] = 'required';
        } else {
            array_unshift($imageRules, 'nullable');
        }

        $request->validate([
            'id' => 'nullable',
            'image' => $imageRules,
            'display_order' => 'nullable|integer|min:0',
            'url' => 'nullable|string|max:500',
            'title' => 'nullable|string|max:150',
        ], [], [
            'image' => __('Image'),
            'title' => __('Title'),
            'url' => __('Link URL'),
            'display_order' => __('Order'),
        ]);

        $path = 'assets/images/frontend/footer';

        if ($id) {
            $item = Frontend::where('data_keys', 'footer.element')->findOrFail($id);
            $values = (array) ($item->data_values ?? []);
        } else {
            $item = new Frontend();
            $item->data_keys = 'footer.element';
            $values = [];
        }

        if ($request->hasFile('image')) {
            // null size: keep original pixels/format (SVG/AVIF/WebP-friendly); footer displays via CSS object-fit
            $values['image'] = fileUploader($request->file('image'), $path, null, $values['image'] ?? null);
        }

        $values['display_order'] = (int) $request->input('display_order', 0);
        $values['url'] = trim((string) $request->input('url', ''));
        $values['title'] = trim((string) $request->input('title', ''));
        $item->data_values = (object) $values;
        $item->save();
        $this->clearFooterCache();
        $notify[] = ['success', $id ? __('Payment icon updated.') : __('Payment icon added.')];

        $fromPaymentShipping = $request->headers->get('referer') && str_contains($request->headers->get('referer'), 'payment-shipping');
        if ($fromPaymentShipping) {
            return redirect()->route('admin.frontend.sections.footer.section', 'payment-shipping')->withFragment('payment-methods')->withNotify($notify);
        }
        return back()->withNotify($notify);
    }

    /** Custom Ads in footer */
    public function saveCustomAd(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:150',
            'url' => 'nullable|string|max:500',
            'display_order' => 'nullable|integer|min:0',
            'image' => ['required_without:id', 'nullable', 'file', 'max:2048', new FileTypeValidate(['jpg', 'jpeg', 'png', 'webp', 'gif'])],
        ]);
        $id = $request->input('id');
        $path = 'assets/images/frontend/footer';

        if ($id) {
            $item = Frontend::where('data_keys', 'footer.custom_ads')->findOrFail($id);
            $values = (array) ($item->data_values ?? []);
        } else {
            $item = new Frontend();
            $item->data_keys = 'footer.custom_ads';
            $values = [];
        }
        if ($request->hasFile('image')) {
            $values['image'] = fileUploader($request->file('image'), $path, null, $values['image'] ?? null);
        }
        $values['title'] = $request->input('title', '');
        $values['url'] = $request->input('url', '');
        $values['display_order'] = (int) $request->input('display_order', 0);
        $item->data_values = (object) $values;
        $item->save();
        $this->clearFooterCache();
        $notify[] = ['success', $id ? __('Custom ad updated.') : __('Custom ad added.')];
        return back()->withNotify($notify);
    }

    public function deleteCustomAd($id)
    {
        $item = Frontend::where('data_keys', 'footer.custom_ads')->findOrFail($id);
        $item->delete();
        $this->clearFooterCache();
        $notify[] = ['success', __('Custom ad removed.')];
        return back()->withNotify($notify);
    }

    /** Return policy form settings */
    public function saveReturnPolicy(Request $request)
    {
        $request->validate([
            'show_form' => 'nullable|in:0,1',
            'form_title' => 'nullable|string|max:200',
            'success_message' => 'nullable|string|max:500',
        ]);
        $content = Frontend::firstOrNew(['data_keys' => 'footer.return_policy']);
        $content->data_keys = 'footer.return_policy';
        $content->data_values = (object) [
            'show_form' => (int) $request->input('show_form', 1),
            'form_title' => $request->input('form_title', __('Product Return Request')),
            'success_message' => $request->input('success_message', __('We have received your return request. Our team will contact you shortly.')),
        ];
        $content->save();
        $this->clearFooterCache();
        $notify[] = ['success', __('Return policy settings saved.')];
        return back()->withNotify($notify);
    }

    public function deletePaymentIcon($id)
    {
        $item = Frontend::where('data_keys', 'footer.element')->where('id', $id)->firstOrFail();
        $item->delete();
        $this->clearFooterCache();
        $notify[] = ['success', __('Payment icon removed.')];
        return back()->withNotify($notify);
    }

    /** Save single app/software promotion item (create or update). Fields: platform (e.g. Android, iOS, Desktop), name, image, link, display_order. */
    public function saveAppPromotionItem(Request $request)
    {
        $request->validate([
            'id' => 'nullable',
            'platform' => 'nullable|string|max:120',
            'name' => 'nullable|string|max:200',
            'link' => 'nullable|string|max:500',
            'display_order' => 'nullable|integer|min:0',
            'image' => ['nullable', 'file', 'max:2048', new FileTypeValidate(['jpg', 'jpeg', 'png', 'webp', 'gif'])],
            'app_file' => ['nullable', 'file', 'max:153600', new FileTypeValidate(['apk', 'exe', 'dmg', 'zip', 'ipa'])],
        ], [], [
            'platform' => __('Platform'),
            'name' => __('Name'),
            'link' => __('Link'),
            'image' => __('Photo'),
            'app_file' => __('App file'),
        ]);
        $idInput = $request->input('id');
        $idInput = is_string($idInput) ? trim($idInput) : $idInput;
        $isUpdate = $idInput !== null && $idInput !== '' && (int) $idInput > 0;
        $id = $isUpdate ? (int) $idInput : null;
        $path = 'assets/images/frontend/footer';
        $appFilePath = public_path(fileManager()->appPromotionFile()->path);
        if (!is_dir($appFilePath)) {
            @mkdir($appFilePath, 0755, true);
        }

        if ($id) {
            $item = Frontend::where('data_keys', 'footer.app_promotion_item')->findOrFail($id);
            $values = (array) ($item->data_values ?? []);
        } else {
            $item = new Frontend();
            $item->data_keys = 'footer.app_promotion_item';
            $values = [];
        }
        if ($request->hasFile('image')) {
            $values['image'] = fileUploader($request->file('image'), $path, null, $values['image'] ?? null);
        }
        if ($request->hasFile('app_file')) {
            $values['app_file'] = fileUploader($request->file('app_file'), fileManager()->appPromotionFile()->path, null, $values['app_file'] ?? null);
        }
        $values['platform'] = trim((string) $request->input('platform', ''));
        $values['name'] = trim((string) $request->input('name', ''));
        $values['link'] = trim((string) $request->input('link', ''));
        $values['display_order'] = (int) $request->input('display_order', 0);
        $item->data_values = (object) $values;
        $item->save();
        $this->clearFooterCache();
        $notify[] = ['success', $id ? __('App link updated.') : __('App link added.')];
        $fromAppPromotion = $request->headers->get('referer') && str_contains($request->headers->get('referer'), 'app-promotion');
        if ($fromAppPromotion) {
            return redirect()->route('admin.frontend.sections.footer.section', 'app-promotion')->withFragment('app-items')->withNotify($notify);
        }
        return back()->withNotify($notify);
    }

    public function deleteAppPromotionItem($id)
    {
        $item = Frontend::where('data_keys', 'footer.app_promotion_item')->where('id', $id)->firstOrFail();
        $item->delete();
        $this->clearFooterCache();
        $notify[] = ['success', __('App link removed.')];
        return back()->withNotify($notify);
    }

    protected function clearFooterCache()
    {
        Cache::forget(self::FOOTER_CACHE_KEY);
        if (function_exists('clearFooterCache')) {
            clearFooterCache();
        }
    }
}
