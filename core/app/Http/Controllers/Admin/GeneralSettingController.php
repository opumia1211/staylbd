<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Frontend;
use App\Models\GeneralSetting;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rules\Password;

class GeneralSettingController extends Controller
{
    public function index()
    {
        return redirect()->route('admin.frontend.sections.general');
    }

    public function adminIndex()
    {
        $admin = auth()->guard('admin')->user();
        if (!$admin instanceof Admin || !$admin->isOwner()) {
            abort(403, __('Only the project Owner can access Admin Management.'));
        }
        $pageTitle = __('Admin Management');
        $query = Admin::query();
        if (Schema::hasColumn('admins', 'role')) {
            $query->orderByRaw("CASE WHEN role = 'owner' THEN 0 WHEN role = 'super_admin' THEN 1 ELSE 2 END")->orderBy('id');
        } else {
            $query->orderBy('id');
        }
        $admins = $query->get();
        $adminSections = config('admin_sections.sections', []);
        $sectionRoutes = config('admin_sections.section_routes', []);
        $hasRole = Schema::hasColumn('admins', 'role');
        $hasMobile = Schema::hasColumn('admins', 'mobile');
        $hasAllowedSections = Schema::hasColumn('admins', 'allowed_sections');
        return view('admin.setting.admin', compact('pageTitle', 'admins', 'adminSections', 'sectionRoutes', 'hasRole', 'hasMobile', 'hasAllowedSections'));
    }

    public function adminStore(Request $request)
    {
        $this->authorizeAdminManagement();
        $rules = [
            'name'     => 'required|string|max:40',
            'username' => 'required|string|max:40|unique:admins,username',
            'email'    => 'required|email|unique:admins,email',
            'password' => ['required', 'confirmed', Password::defaults()],
            'admin_notes' => 'nullable|string|max:500',
        ];
        if (Schema::hasColumn('admins', 'mobile')) {
            $rules['mobile'] = 'nullable|string|max:20';
        }
        if (Schema::hasColumn('admins', 'role')) {
            $rules['role'] = 'required|in:super_admin,admin,manager,support';
        }
        $request->validate($rules);
        $admin = new Admin();
        $admin->name = $request->name;
        $admin->username = $request->username;
        $admin->email = $request->email;
        if (Schema::hasColumn('admins', 'mobile')) {
            $admin->mobile = $request->mobile ?: null;
        }
        if (Schema::hasColumn('admins', 'admin_notes')) {
            $admin->admin_notes = $request->admin_notes ?: null;
        }
        $admin->password = Hash::make($request->password);
        if (Schema::hasColumn('admins', 'role')) {
            $admin->role = $request->role;
            if ($request->role === Admin::ROLE_OWNER) {
                $admin->allowed_sections = null;
            }
        }
        if (Schema::hasColumn('admins', 'allowed_sections') && ($request->role ?? '') !== Admin::ROLE_OWNER) {
            $validSections = array_keys(config('admin_sections.sections', []));
            $allowed = $request->input('allowed_sections', []);
            $allowed = is_array($allowed) ? array_intersect($allowed, $validSections) : [];
            $admin->allowed_sections = array_values($allowed);
        }
        $admin->save();
        $notify[] = ['success', __('Admin appointed successfully. Login credentials are shown below once—save them securely.')];
        return back()->withNotify($notify)
            ->with('new_admin_credentials', ['name' => $admin->name, 'email' => $admin->email, 'username' => $admin->username, 'password' => $request->password]);
    }

    /**
     * Reset admin password. Only Owner can reset. New password is shown once.
     */
    public function adminPasswordReset(Request $request, $id)
    {
        $current = auth()->guard('admin')->user();
        if (!$current instanceof Admin || !$current->isOwner()) {
            abort(403, __('Only the project Owner can reset admin passwords.'));
        }
        $admin = Admin::findOrFail($id);
        if ($admin->id === $current->id) {
            $notify[] = ['error', __('Use Profile / Change Password to change your own password.')];
            return back()->withNotify($notify);
        }
        if ($admin->isOwner()) {
            $notify[] = ['error', __('Owner password cannot be reset from here.')];
            return back()->withNotify($notify);
        }
        $request->validate([
            'new_password' => ['required', 'confirmed', Password::defaults()],
        ]);
        $admin->password = Hash::make($request->new_password);
        $admin->force_password_change = false;
        $admin->save();
        $notify[] = ['success', __('Password reset. New password is shown below once—share it securely with the admin.')];
        return back()->withNotify($notify)
            ->with('reset_admin_credentials', ['name' => $admin->name, 'email' => $admin->email, 'username' => $admin->username, 'password' => $request->new_password]);
    }

    public function adminEdit($id)
    {
        $this->authorizeAdminManagement();
        $admin = Admin::findOrFail($id);
        $pageTitle = __('Edit Admin');
        $hasRole = Schema::hasColumn('admins', 'role');
        $hasMobile = Schema::hasColumn('admins', 'mobile');
        $hasSections = Schema::hasColumn('admins', 'allowed_sections');
        $canChangeRole = $hasRole && ($admin->role ?? 'admin') !== Admin::ROLE_OWNER && $admin->id != auth()->guard('admin')->id();
        $canEditSections = $hasSections && ($admin->role ?? 'admin') !== Admin::ROLE_OWNER;
        $adminSections = config('admin_sections.sections', []);
        $sectionRoutes = config('admin_sections.section_routes', []);
        return view('admin.setting.admin_edit', compact('pageTitle', 'admin', 'hasRole', 'hasMobile', 'hasSections', 'canChangeRole', 'canEditSections', 'adminSections', 'sectionRoutes'));
    }

    public function adminUpdate(Request $request, $id)
    {
        $this->authorizeAdminManagement();
        $admin = Admin::findOrFail($id);
        $rules = [
            'name'     => 'required|string|max:40',
            'username' => 'required|string|max:40|unique:admins,username,' . $admin->id,
            'email'    => 'required|email|unique:admins,email,' . $admin->id,
            'admin_notes' => 'nullable|string|max:500',
        ];
        if (Schema::hasColumn('admins', 'mobile')) {
            $rules['mobile'] = 'nullable|string|max:20';
        }
        if ($request->filled('password')) {
            $rules['password'] = ['confirmed', Password::defaults()];
        }
        if (Schema::hasColumn('admins', 'role') && ($admin->role ?? 'admin') !== Admin::ROLE_OWNER && $admin->id != auth()->guard('admin')->id()) {
            $rules['role'] = 'required|in:admin,super_admin';
        }
        $request->validate($rules);
        $admin->name = $request->name;
        $admin->username = $request->username;
        $admin->email = $request->email;
        if (Schema::hasColumn('admins', 'mobile')) {
            $admin->mobile = $request->mobile ?: null;
        }
        if (Schema::hasColumn('admins', 'admin_notes')) {
            $admin->admin_notes = $request->admin_notes ?: null;
        }
        if ($request->filled('password')) {
            $admin->password = Hash::make($request->password);
        }
        if (Schema::hasColumn('admins', 'role') && ($admin->role ?? 'admin') !== Admin::ROLE_OWNER && $admin->id != auth()->guard('admin')->id()) {
            $admin->role = $request->role;
        }
        if (Schema::hasColumn('admins', 'allowed_sections') && ($admin->role ?? 'admin') !== Admin::ROLE_OWNER) {
            $validSections = array_keys(config('admin_sections.sections', []));
            $allowed = $request->input('allowed_sections', []);
            $allowed = is_array($allowed) ? array_intersect($allowed, $validSections) : [];
            $admin->allowed_sections = array_values($allowed);
        }
        $admin->save();
        $notify[] = ['success', __('Admin updated successfully.')];
        return redirect()->route('admin.setting.admin.index')->withNotify($notify);
    }

    public function adminRoleUpdate(Request $request, $id)
    {
        $this->authorizeAdminManagement();
        $admin = Admin::findOrFail($id);
        if ($admin->id === auth()->guard('admin')->id()) {
            $notify[] = ['error', __('You cannot change your own role.')];
            return back()->withNotify($notify);
        }
        if (Schema::hasColumn('admins', 'role') && $admin->role === Admin::ROLE_OWNER) {
            $notify[] = ['error', __('Owner role cannot be changed.')];
            return back()->withNotify($notify);
        }
        $request->validate(['role' => 'required|in:admin,super_admin']);
        $admin->role = $request->role;
        $admin->save();
        $notify[] = ['success', __('Admin role updated.')];
        return back()->withNotify($notify);
    }

    protected function authorizeAdminManagement(): void
    {
        $admin = auth()->guard('admin')->user();
        if (!$admin instanceof Admin) {
            abort(403, __('Only the project Owner can manage admins.'));
        }
        if (!$admin->isOwner()) {
            abort(403, __('Only the project Owner can manage admins.'));
        }
    }

    public function frontendGeneral()
    {
        $pageTitle = __('General Settings');
        $general = gs();
        $timezonePath = resource_path('views/admin/partials/timezone.json');
        $timezones = file_exists($timezonePath)
            ? json_decode(file_get_contents($timezonePath)) ?? []
            : ['UTC', 'Asia/Dhaka'];
        $hasMetaPixel = \Schema::hasColumn('general_settings', 'meta_pixel_id');
        $hasFbToken = \Schema::hasColumn('general_settings', 'facebook_access_token');
        $hasGoogleAds = \Schema::hasColumn('general_settings', 'google_ads_id');
        $hasTiktokPixel = \Schema::hasColumn('general_settings', 'tiktok_pixel_id');
        $trackingReady = $hasMetaPixel || $hasGoogleAds || $hasTiktokPixel;
        return view('admin.frontend.general', compact(
            'pageTitle', 'timezones', 'general',
            'hasMetaPixel', 'hasFbToken', 'hasGoogleAds', 'hasTiktokPixel', 'trackingReady'
        ));
    }

    public function frontendGeneralUpdate(Request $request)
    {
        $request->validate([
            'site_name'     => 'required|string|max:40',
            'cur_text'      => 'required|string|max:40',
            'cur_sym'       => 'required|string|max:40',
            'base_color'    => ['nullable', 'regex:/^[a-f0-9]{6}$/i'],
            'timezone'      => 'required',
            'discount'      => 'required|numeric',
            'discount_type' => 'required',
        ]);

        $general                      = gs();
        $general->site_name            = $request->site_name;
        $general->cur_text             = $request->cur_text;
        $general->cur_sym              = $request->cur_sym;
        $general->base_color           = str_replace('#', '', $request->base_color ?? '');
        $general->discount             = $request->discount;
        $general->discount_type        = $request->discount_type;
        if (\Schema::hasColumn('general_settings', 'meta_pixel_id')) {
            $general->meta_pixel_id = $request->meta_pixel_id ? trim($request->meta_pixel_id) : null;
        }
        if (\Schema::hasColumn('general_settings', 'facebook_access_token')) {
            $general->facebook_access_token = $request->facebook_access_token ? trim($request->facebook_access_token) : null;
        }
        if (\Schema::hasColumn('general_settings', 'google_ads_id')) {
            $general->google_ads_id = $request->google_ads_id ? trim($request->google_ads_id) : null;
        }
        if (\Schema::hasColumn('general_settings', 'tiktok_pixel_id')) {
            $general->tiktok_pixel_id = $request->tiktok_pixel_id ? trim($request->tiktok_pixel_id) : null;
        }
        $hexOrRgb = function ($v) {
            if (!$v || !is_string($v)) return null;
            $v = trim($v);
            if (preg_match('/^#?[a-fA-F0-9]{3,8}$/', $v)) return (strpos($v, '#') === 0 ? $v : '#' . $v);
            return strlen($v) <= 30 ? $v : null;
        };
        if (\Schema::hasColumn('general_settings', 'product_card_color')) {
            $general->product_card_color = $hexOrRgb($request->input('product_card_color')) ?? '#ffffff';
        }
        if (\Schema::hasColumn('general_settings', 'button_color')) {
            $general->button_color = $hexOrRgb($request->input('button_color')) ?? '#1f2937';
        }
        if (\Schema::hasColumn('general_settings', 'button_hover_color')) {
            $general->button_hover_color = $hexOrRgb($request->input('button_hover_color')) ?? '#374151';
        }
        if (\Schema::hasColumn('general_settings', 'rating_star_color')) {
            $general->rating_star_color = $hexOrRgb($request->input('rating_star_color')) ?? '#f59e0b';
        }
        if (\Schema::hasColumn('general_settings', 'discount_badge_color')) {
            $general->discount_badge_color = $hexOrRgb($request->input('discount_badge_color')) ?? '#dc2626';
        }
        $general->save();

        $timezoneFile = config_path('timezone.php');
        $content      = '<?php $timezone = ' . $request->timezone . ' ?>';
        if (is_writable(config_path())) {
            @file_put_contents($timezoneFile, $content);
        }
        $notify[] = ['success', __('General setting updated successfully.')];
        return redirect()->route('admin.frontend.sections.general')->withNotify($notify);
    }

    public function update(Request $request)
    {
        $request->validate([
            'site_name'     => 'required|string|max:40',
            'cur_text'      => 'required|string|max:40',
            'cur_sym'       => 'required|string|max:40',
            'base_color'    => 'nullable', 'regex:/^[a-f0-9]{6}$/i',
            'timezone'      => 'required',
            'discount'      => 'required|numeric',
            'discount_type' => 'required',
        ]);

        $general                      = gs();
        $general->site_name           = $request->site_name;
        $general->cur_text            = $request->cur_text;
        $general->cur_sym             = $request->cur_sym;
        $general->base_color          = str_replace('#','',$request->base_color);
        $general->discount            = $request->discount;
        $general->discount_type       = $request->discount_type;
        $general->save();

        $timezoneFile = config_path('timezone.php');
        $content      = '<?php $timezone = ' . $request->timezone . ' ?>';
        file_put_contents($timezoneFile, $content);
        $notify[] = ['success', 'General setting updated successfully'];
        return back()->withNotify($notify);
}

    /**
     * System configuration – toggles for verification, security, registration, etc.
     * Admin-only; response must not be cached (handled by CacheHeaders).
     */
    public function systemConfiguration()
    {
        $pageTitle = 'System Configuration';
        $general = gs();
        $hasFloatingLogin = \Schema::hasColumn('general_settings', 'floating_login');
        $hasFloatingRegister = \Schema::hasColumn('general_settings', 'floating_register');
        $hasAdminOnlineStatus = \Schema::hasColumn('general_settings', 'admin_online_status');
        return view('admin.setting.configuration', compact(
            'pageTitle', 'general', 'hasFloatingLogin', 'hasFloatingRegister', 'hasAdminOnlineStatus'
        ));
    }

    /**
     * Allowed system configuration toggle fields (DB column => request key).
     * Only these are written; prevents unexpected input.
     */
    private function systemConfigurationFields(): array
    {
        $fields = [
            'ev' => 'ev',
            'en' => 'en',
            'sv' => 'sv',
            'sn' => 'sn',
            'force_ssl' => 'force_ssl',
            'secure_password' => 'secure_password',
            'registration' => 'registration',
            'display_stock' => 'display_stock',
            'agree' => 'agree',
            'multi_language' => 'multi_language',
        ];
        if (\Schema::hasColumn('general_settings', 'display_view_count')) {
            $fields['display_view_count'] = 'display_view_count';
        }
        if (\Schema::hasColumn('general_settings', 'floating_login')) {
            $fields['floating_login'] = 'floating_login';
        }
        if (\Schema::hasColumn('general_settings', 'floating_register')) {
            $fields['floating_register'] = 'floating_register';
        }
        if (\Schema::hasColumn('general_settings', 'admin_online_status')) {
            $fields['admin_online_status'] = 'admin_online_status';
        }
        return $fields;
    }

    public function systemConfigurationSubmit(Request $request)
    {
        try {
            $general = gs();
            foreach ($this->systemConfigurationFields() as $column => $key) {
                $general->{$column} = $request->boolean($key) ? Status::ENABLE : Status::DISABLE;
            }
            $general->save();

            Cache::forget('GeneralSetting');
            try {
                Artisan::call('config:clear');
                Artisan::call('view:clear');
            } catch (\Throwable $e) {
                Log::debug('Config/view clear after system config save: ' . $e->getMessage());
            }

            $notify[] = ['success', __('System configuration updated successfully')];
            return back()->withNotify($notify);
        } catch (\Throwable $e) {
            Log::warning('System configuration save failed: ' . $e->getMessage());
            $notify[] = ['error', __('Failed to save configuration. Please try again.')];
            return back()->withNotify($notify);
        }
    }

    /** Default messages – shown when DB is empty; admin can edit and save. */
    private const DEFAULT_STOCK_OUT_USER_MESSAGE = 'This product is currently out of stock. Please wait—we are restocking soon. You can keep it in your cart and try again later.';
    private const DEFAULT_STOCK_OUT_ADMIN_MESSAGE = 'Out of stock but customers are trying to order. Please add stock soon.';
    private const DEFAULT_RESTOCK_MESSAGE = 'Good news! {product_name} is back in stock. You can order now: {product_url}';
    private const DEFAULT_RESTOCK_WHATSAPP = 'Hi! {product_name} is back in stock. Order now: {product_url}';
    private const DEFAULT_RESTOCK_TELEGRAM = 'Good news! {product_name} is back in stock. You can order now: {product_url}';

    /**
     * Stock & Order Messages – editable user message (stock-out), admin notification, restock (in-app, WhatsApp, Telegram).
     */
    public function stockOrderMessages()
    {
        $pageTitle = __('Stock & Order Messages');
        $general = gs();
        $hasColumns = Schema::hasColumn('general_settings', 'stock_out_user_message');
        $hasSocialColumns = Schema::hasColumn('general_settings', 'restock_whatsapp_enable');
        $defaults = [
            'stock_out_user_message' => self::DEFAULT_STOCK_OUT_USER_MESSAGE,
            'stock_out_admin_message' => self::DEFAULT_STOCK_OUT_ADMIN_MESSAGE,
            'restock_message_template' => self::DEFAULT_RESTOCK_MESSAGE,
            'restock_whatsapp_message' => self::DEFAULT_RESTOCK_WHATSAPP,
            'restock_telegram_message' => self::DEFAULT_RESTOCK_TELEGRAM,
        ];
        return view('admin.setting.stock_order_messages', compact('pageTitle', 'general', 'hasColumns', 'hasSocialColumns', 'defaults'));
    }

    public function stockOrderMessagesSubmit(Request $request)
    {
        if (!Schema::hasColumn('general_settings', 'stock_out_user_message')) {
            $notify[] = ['error', __('Stock & Order Messages feature is not available. Run migrations.')];
            return back()->withNotify($notify);
        }
        $rules = [
            'stock_out_user_message'    => 'nullable|string|max:2000',
            'stock_out_admin_message'   => 'nullable|string|max:500',
            'restock_notify_enable'     => 'nullable|in:0,1',
            'restock_message_template' => 'nullable|string|max:1000',
        ];
        if (Schema::hasColumn('general_settings', 'restock_whatsapp_enable')) {
            $rules['restock_whatsapp_enable'] = 'nullable|in:0,1';
            $rules['restock_whatsapp_message'] = 'nullable|string|max:1000';
            $rules['restock_telegram_enable'] = 'nullable|in:0,1';
            $rules['restock_telegram_message'] = 'nullable|string|max:1000';
        }
        $request->validate($rules);
        $general = gs();
        $general->stock_out_user_message = $request->stock_out_user_message ?: null;
        $general->stock_out_admin_message = $request->stock_out_admin_message ?: null;
        $general->restock_notify_enable = (int) ($request->restock_notify_enable ?? 1);
        $general->restock_message_template = $request->restock_message_template ?: null;
        if (Schema::hasColumn('general_settings', 'restock_whatsapp_enable')) {
            $general->restock_whatsapp_enable = (int) ($request->restock_whatsapp_enable ?? 0);
            $general->restock_whatsapp_message = $request->restock_whatsapp_message ?: null;
            $general->restock_telegram_enable = (int) ($request->restock_telegram_enable ?? 0);
            $general->restock_telegram_message = $request->restock_telegram_message ?: null;
        }
        $general->save();
        Cache::forget('GeneralSetting');
        $notify[] = ['success', __('Stock & Order messages updated successfully.')];
        return back()->withNotify($notify);
    }

    public function logoIcon()
    {
        return redirect()->route('admin.frontend.sections.icon');
    }

    public function frontendIcon()
    {
        $pageTitle = __('Logo & Favicon');
        return view('admin.frontend.icon', compact('pageTitle'));
    }

    public function frontendIconUpdate(Request $request)
    {
        return $this->logoIconUpdate($request);
    }

    public function logoIconUpdate(Request $request)
    {
        $request->validate([
            'logo'      => ['nullable', 'file', new FileTypeValidate(['svg','png','jpg','jpeg','webp','gif']), 'max:2048'],
            'logo_dark' => ['nullable', 'file', new FileTypeValidate(['svg','png','jpg','jpeg','webp','gif']), 'max:2048'],
            'favicon'   => ['nullable', 'file', new FileTypeValidate(['svg','png','ico','jpg','jpeg','webp']), 'max:512'],
            'invoice_logo' => ['nullable', 'file', new FileTypeValidate(['svg','png','jpg','jpeg','webp','gif']), 'max:2048'],
            'invoice_signature' => ['nullable', 'file', new FileTypeValidate(['svg','png','jpg','jpeg','webp','gif']), 'max:1024'],
            'invoice_authorized_name' => ['nullable', 'string', 'max:191'],
            'invoice_qr_caption_en' => ['nullable', 'string', 'max:500'],
            'invoice_qr_caption_bn' => ['nullable', 'string', 'max:500'],
            'logo_max_width' => ['nullable', 'numeric', 'min:100', 'max:400'],
            'logo_max_height' => ['nullable', 'numeric', 'min:30', 'max:120'],
            'footer_logo_height' => ['nullable', 'numeric', 'min:20', 'max:80'],
        ]);

        $path = getLogoIconPath();
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }

        $general = gs();

        // Handle logo removal
        if ($request->remove_logo == '1') {
            foreach (glob($path . '/logo_*.*') as $oldFile) {
                if (is_file($oldFile) && strpos($oldFile, 'logo_dark_') === false) {
                    @unlink($oldFile);
                }
            }
            $general->logo = null;
        }

        // Handle dark logo removal
        if ($request->remove_logo_dark == '1') {
            foreach (glob($path . '/logo_dark_*.*') as $oldFile) {
                if (is_file($oldFile)) @unlink($oldFile);
            }
            $general->logo_dark = null;
        }

        // Handle favicon removal
        if ($request->remove_favicon == '1') {
            foreach (glob($path . '/favicon_*.*') as $oldFile) {
                if (is_file($oldFile)) @unlink($oldFile);
            }
            $general->favicon = null;
        }

        // Upload Main Logo
        if ($request->hasFile('logo')) {
            try {
                $file = $request->logo;
                $ext = strtolower($file->getClientOriginalExtension());

                // Delete old logos (except dark logos)
                foreach (glob($path . '/logo_*.*') as $oldFile) {
                    if (is_file($oldFile) && strpos(basename($oldFile), 'logo_dark_') === false) {
                        @unlink($oldFile);
                    }
                }

                $filename = $this->saveLogoImage($file, $path, 'logo_', $ext, 500, 120);
                $general->logo = $filename;
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload logo: ' . $exp->getMessage()];
                return back()->withNotify($notify);
            }
        }

        // Upload Dark Logo
        if ($request->hasFile('logo_dark')) {
            try {
                $file = $request->logo_dark;
                $ext = strtolower($file->getClientOriginalExtension());

                // Delete old dark logos
                foreach (glob($path . '/logo_dark_*.*') as $oldFile) {
                    if (is_file($oldFile)) @unlink($oldFile);
                }

                $filename = $this->saveLogoImage($file, $path, 'logo_dark_', $ext, 500, 120);
                $general->logo_dark = $filename;
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload dark logo: ' . $exp->getMessage()];
                return back()->withNotify($notify);
            }
        }

        // Upload Favicon
        if ($request->hasFile('favicon')) {
            try {
                $file = $request->favicon;
                $ext = strtolower($file->getClientOriginalExtension());

                // Delete old favicons
                foreach (glob($path . '/favicon_*.*') as $oldFile) {
                    if (is_file($oldFile)) @unlink($oldFile);
                }

                $filename = $this->saveLogoImage($file, $path, 'favicon_', $ext, 180, 180);

                $general->favicon = $filename;
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload favicon: ' . $exp->getMessage()];
                return back()->withNotify($notify);
            }
        }

        // Invoice logo removal
        if (\Schema::hasColumn('general_settings', 'invoice_logo') && $request->remove_invoice_logo == '1') {
            foreach (glob($path . '/invoice_logo_*.*') as $oldFile) {
                if (is_file($oldFile)) @unlink($oldFile);
            }
            $general->invoice_logo = null;
        }
        // Invoice signature removal
        if (\Schema::hasColumn('general_settings', 'invoice_signature') && $request->remove_invoice_signature == '1') {
            foreach (glob($path . '/invoice_signature_*.*') as $oldFile) {
                if (is_file($oldFile)) @unlink($oldFile);
            }
            $general->invoice_signature = null;
        }

        // Upload Invoice Logo
        if (\Schema::hasColumn('general_settings', 'invoice_logo') && $request->hasFile('invoice_logo')) {
            try {
                $file = $request->invoice_logo;
                $ext = strtolower($file->getClientOriginalExtension());
                foreach (glob($path . '/invoice_logo_*.*') as $oldFile) {
                    if (is_file($oldFile)) @unlink($oldFile);
                }
                $filename = $this->saveLogoImage($file, $path, 'invoice_logo_', $ext, 400, 120);
                $general->invoice_logo = $filename;
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload invoice logo: ' . $exp->getMessage()];
                return back()->withNotify($notify);
            }
        }

        // Upload Invoice Signature
        if (\Schema::hasColumn('general_settings', 'invoice_signature') && $request->hasFile('invoice_signature')) {
            try {
                $file = $request->invoice_signature;
                $ext = strtolower($file->getClientOriginalExtension());
                foreach (glob($path . '/invoice_signature_*.*') as $oldFile) {
                    if (is_file($oldFile)) @unlink($oldFile);
                }
                $filename = $this->saveLogoImage($file, $path, 'invoice_signature_', $ext, 240, 80);
                $general->invoice_signature = $filename;
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload invoice signature: ' . $exp->getMessage()];
                return back()->withNotify($notify);
            }
        }

        if (\Schema::hasColumn('general_settings', 'invoice_authorized_name')) {
            $general->invoice_authorized_name = $request->invoice_authorized_name ? trim($request->invoice_authorized_name) : null;
        }
        if (\Schema::hasColumn('general_settings', 'invoice_qr_caption_en')) {
            $general->invoice_qr_caption_en = $request->invoice_qr_caption_en ? trim($request->invoice_qr_caption_en) : null;
        }
        if (\Schema::hasColumn('general_settings', 'invoice_qr_caption_bn')) {
            $general->invoice_qr_caption_bn = $request->invoice_qr_caption_bn ? trim($request->invoice_qr_caption_bn) : null;
        }

        // Update Logo Effects Settings
        if (\Schema::hasColumn('general_settings', 'logo_effects_enabled')) {
            $general->logo_effects_enabled = $request->has('logo_effects_enabled') ? 1 : 0;
        }
        if (\Schema::hasColumn('general_settings', 'logo_hover_effect')) {
            $general->logo_hover_effect = $request->logo_hover_effect ?? 'none';
        }
        if (\Schema::hasColumn('general_settings', 'logo_animation')) {
            $general->logo_animation = $request->logo_animation ?? 'none';
        }
        if (\Schema::hasColumn('general_settings', 'logo_animation_speed')) {
            $general->logo_animation_speed = $request->logo_animation_speed ?? 'normal';
        }
        if (\Schema::hasColumn('general_settings', 'logo_opacity')) {
            $general->logo_opacity = min(1, max(0.3, (float)($request->logo_opacity ?? 1)));
        }

        // Update Display Settings
        if (\Schema::hasColumn('general_settings', 'logo_max_width')) {
            $general->logo_max_width = (int)($request->logo_max_width ?? 200);
        }
        if (\Schema::hasColumn('general_settings', 'logo_max_height')) {
            $general->logo_max_height = (int)($request->logo_max_height ?? 60);
        }
        if (\Schema::hasColumn('general_settings', 'footer_logo_height')) {
            $general->footer_logo_height = (int)($request->footer_logo_height ?? 35);
        }

        $general->save();

        // Clear caches - ensure favicon/logo shows immediately
        Cache::forget('GeneralSetting');
        if (function_exists('opcache_reset')) {
            @opcache_reset();
        }
        try {
            Artisan::call('cache:clear');
            Artisan::call('view:clear');
            Artisan::call('config:clear');
        } catch (\Exception $e) {
            // Ignore cache clear errors
        }

        $notify[] = ['success', 'Logo & favicon updated successfully'];
        return back()->withNotify($notify);
    }

    /**
     * Save logo/favicon image - works with or without GD Library
     * Falls back to simple file move when Intervention Image (GD) is unavailable
     *
     * @return string The saved filename
     */
    protected function saveLogoImage($file, string $path, string $prefix, string $ext, int $maxW, int $maxH): string
    {
        $filename = $prefix . md5(time() . uniqid()) . '.' . $ext;
        $fullPath = $path . '/' . $filename;

        if (in_array($ext, ['svg', 'ico'])) {
            $file->move($path, $filename);
            return $filename;
        }

        try {
            $img = \Intervention\Image\Facades\Image::make($file);
            $w = $img->width();
            $h = $img->height();
            if ($w > $maxW || $h > $maxH) {
                $img->resize($maxW, $maxH, function ($c) {
                    $c->aspectRatio();
                    $c->upsize();
                });
            }
            $img->save($fullPath);
            return $filename;
        } catch (\Throwable $e) {
            if (str_contains($e->getMessage(), 'GD Library') || str_contains($e->getMessage(), 'extension')) {
                $file->move($path, $filename);
                return $filename;
            }
            throw $e;
        }
    }

    /**
     * Custom CSS - Edits are saved to template-specific custom.css file.
     * When deploying: the file deploys with the project; admin can edit from this panel on server.
     */
    public function customCss()
    {
        $pageTitle = 'Custom CSS';
        $file = activeTemplate(true) . 'css/custom.css';
        $fullPath = base_path('../' . $file);
        if (!file_exists($fullPath)) {
            $fullPath = public_path($file);
        }
        if (!file_exists($fullPath)) {
            $fullPath = base_path($file);
        }
        if (!file_exists($fullPath)) {
            $fullPath = $file;
        }
        $fileContent = @file_get_contents($fullPath);
        $fileContent = $fileContent !== false ? $fileContent : '';
        $lastModified = file_exists($fullPath) ? date('Y-m-d H:i:s', filemtime($fullPath)) : null;
        $displayPath = @realpath($fullPath) ?: $fullPath;
        $templateName = activeTemplateName();
        return view('admin.setting.custom_css', compact('pageTitle', 'fileContent', 'lastModified', 'displayPath', 'templateName'));
    }

    public function customCssSubmit(Request $request)
    {
        $file = activeTemplate(true) . 'css/custom.css';
        $fullPath = base_path('../' . $file);
        $dir = dirname($fullPath);
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        if (!file_exists($fullPath)) {
            @touch($fullPath);
        }
        if (is_writable($fullPath)) {
            file_put_contents($fullPath, $request->css ?? '');
            $notify[] = ['success', 'CSS updated successfully'];
        } else {
            $notify[] = ['error', 'File is not writable. Check permissions.'];
        }
        return back()->withNotify($notify);
    }

    public function customCssReset()
    {
        $file = activeTemplate(true) . 'css/custom.css';
        $fullPath = base_path('../' . $file);
        if (!file_exists($fullPath)) {
            $fullPath = public_path($file);
        }
        if (!file_exists($fullPath)) {
            $fullPath = base_path($file);
        }
        if (file_exists($fullPath) && is_writable($fullPath)) {
            file_put_contents($fullPath, '');
            $notify[] = ['success', 'Custom CSS reset to empty.'];
        } else {
            $notify[] = ['error', 'Could not reset CSS.'];
        }
        return back()->withNotify($notify);
    }

    public function maintenanceMode()
    {
        $pageTitle = 'Maintenance Mode';
        $maintenance = Frontend::where('data_keys', 'maintenance.data')->firstOrFail();
        return view('admin.setting.maintenance', compact('pageTitle', 'maintenance'));
    }

    public function maintenanceModeSubmit(Request $request)
    {
        $request->validate([
            'description' => 'required',
        ]);

        $general = GeneralSetting::first();
        $general->maintenance_mode = $request->status ? Status::ENABLE : Status::DISABLE;
        $general->save();

        $maintenance = Frontend::where('data_keys', 'maintenance.data')->firstOrFail();
        $maintenance->data_values = [
            'description'            => $request->description,
            'title'                  => $request->title,
            'short_description'      => $request->short_description,
            'ip_whitelist'           => $request->ip_whitelist,
            'show_countdown'         => (int) ($request->show_countdown ?? 1),
            'countdown_datetime'     => $request->countdown_datetime,
            'progress_percentage'    => min(100, max(0, (int) ($request->progress_percentage ?? 50))),
            'show_progress_bar'      => (int) ($request->show_progress_bar ?? 1),
            'estimated_duration'     => $request->estimated_duration,
            'social_facebook'        => $request->social_facebook,
            'social_twitter'         => $request->social_twitter,
            'social_instagram'       => $request->social_instagram,
            'social_linkedin'        => $request->social_linkedin,
            'contact_email'          => $request->contact_email,
            'contact_phone'          => $request->contact_phone,
            'allow_email_signup'     => (int) ($request->allow_email_signup ?? 1),
            'email_signup_message'   => $request->email_signup_message ?? __('Get notified when we\'re back!'),
        ];

        $maintenance->save();

        $notify[] = ['success', 'Maintenance mode updated successfully'];
        return back()->withNotify($notify);
    }

    public function socialLogin()
    {
        $pageTitle = 'Social Login Settings';
        $base = rtrim(url('/'), '/');
        $callbackUrls = [
            'google'   => $base . '/user/social-login/google/callback',
            'facebook' => $base . '/user/social-login/facebook/callback',
            'twitter'  => $base . '/user/social-login/twitter/callback',
            'apple'    => $base . '/user/social-login/apple/callback',
            'github'   => $base . '/user/social-login/github/callback',
        ];
        return view('admin.setting.social_login', compact('pageTitle', 'callbackUrls'));
    }

    public function socialLoginUpdate(Request $request)
    {
        $request->validate([
            'GOOGLE_CLIENT_ID' => 'nullable|string',
            'GOOGLE_CLIENT_SECRET' => 'nullable|string',
            'FACEBOOK_CLIENT_ID' => 'nullable|string',
            'FACEBOOK_CLIENT_SECRET' => 'nullable|string',
            'TWITTER_CLIENT_ID' => 'nullable|string',
            'TWITTER_CLIENT_SECRET' => 'nullable|string',
            'APPLE_CLIENT_ID' => 'nullable|string',
            'APPLE_CLIENT_SECRET' => 'nullable|string',
            'GITHUB_CLIENT_ID' => 'nullable|string',
            'GITHUB_CLIENT_SECRET' => 'nullable|string',
        ]);

        // Update all providers from unified form
        $this->writeEnv('GOOGLE_CLIENT_ID', $request->GOOGLE_CLIENT_ID ?? '');
        $this->writeEnv('GOOGLE_CLIENT_SECRET', $request->GOOGLE_CLIENT_SECRET ?? '');
        $this->writeEnv('GOOGLE_LOGIN_ENABLED', $request->has('GOOGLE_LOGIN_ENABLED') ? '1' : '0');

        $this->writeEnv('FACEBOOK_CLIENT_ID', $request->FACEBOOK_CLIENT_ID ?? '');
        $this->writeEnv('FACEBOOK_CLIENT_SECRET', $request->FACEBOOK_CLIENT_SECRET ?? '');
        $this->writeEnv('FACEBOOK_LOGIN_ENABLED', $request->has('FACEBOOK_LOGIN_ENABLED') ? '1' : '0');

        $this->writeEnv('TWITTER_CLIENT_ID', $request->TWITTER_CLIENT_ID ?? '');
        $this->writeEnv('TWITTER_CLIENT_SECRET', $request->TWITTER_CLIENT_SECRET ?? '');
        $this->writeEnv('TWITTER_LOGIN_ENABLED', $request->has('TWITTER_LOGIN_ENABLED') ? '1' : '0');

        $this->writeEnv('APPLE_CLIENT_ID', $request->APPLE_CLIENT_ID ?? '');
        $this->writeEnv('APPLE_CLIENT_SECRET', $request->APPLE_CLIENT_SECRET ?? '');
        $this->writeEnv('APPLE_LOGIN_ENABLED', $request->has('APPLE_LOGIN_ENABLED') ? '1' : '0');

        $this->writeEnv('GITHUB_CLIENT_ID', $request->GITHUB_CLIENT_ID ?? '');
        $this->writeEnv('GITHUB_CLIENT_SECRET', $request->GITHUB_CLIENT_SECRET ?? '');
        $this->writeEnv('GITHUB_LOGIN_ENABLED', $request->has('GITHUB_LOGIN_ENABLED') ? '1' : '0');

        if (function_exists('opcache_reset')) {
            opcache_reset();
        }
        $notify[] = ['success', 'Social login settings updated successfully'];
        return back()->withNotify($notify);
    }

    protected function writeEnv(string $key, ?string $value): void
    {
        if ($value === null) return;
        $path = base_path('.env');
        if (!file_exists($path)) return;
        $escaped = '"' . trim($value) . '"';
        $env = file_get_contents($path);
        if (strpos($env, $key . '=') !== false) {
            $pattern = '/^' . preg_quote($key, '/') . "=.*$/m";
            $replacement = $key . '=' . $escaped;
            $env = preg_replace($pattern, $replacement, $env);
        } else {
            $env .= PHP_EOL . $key . '=' . $escaped . PHP_EOL;
        }
        file_put_contents($path, $env);
    }

    public function cookie()
    {
        $pageTitle = 'GDPR Cookie';
        $cookie = Frontend::where('data_keys', 'cookie.data')->firstOrFail();
        $customMessages = Frontend::where('data_keys', 'custom_message.element')
            ->orderByRaw('CAST(JSON_UNQUOTE(JSON_EXTRACT(data_values, "$.display_order")) AS UNSIGNED) ASC')
            ->orderBy('id')
            ->get();
        return view('admin.setting.cookie', compact('pageTitle', 'cookie', 'customMessages'));
    }

    public function cookieSubmit(Request $request)
    {
        $request->validate([
            'short_desc' => 'required|string|max:500',
            'description' => 'required',
        ]);

        $cookie = Frontend::where('data_keys', 'cookie.data')->firstOrFail();
        $cookie->data_values = [
            'short_desc'            => $request->short_desc,
            'description'            => $request->description,
            'status'                => $request->status ? Status::ENABLE : Status::DISABLE,
            'banner_position'        => $request->banner_position ?? 'bottom',
            'banner_style'           => $request->banner_style ?? 'compact',
            'logo_box_style'         => in_array($request->logo_box_style ?? 'light', ['light', 'brand']) ? $request->logo_box_style : 'light',
            'link_text'             => $request->link_text ?? __('learn more'),
            'allow_btn_text'        => $request->allow_btn_text ?? __('Allow'),
            'decline_btn_text'      => $request->decline_btn_text ?? __('Decline'),
            'show_decline_btn'      => (int) ($request->show_decline_btn ?? 1),
            'cookie_expiry_days'    => min(365, max(1, (int) ($request->cookie_expiry_days ?? 365))),
            'show_delay'            => min(60, max(0, (int) ($request->show_delay ?? 2))),
            'show_preferences_link' => (int) ($request->show_preferences_link ?? 1),
            'preferences_link_text' => $request->preferences_link_text ?? __('Cookie Preferences'),
        ];

        $cookie->save();

        $notify[] = ['success', 'Cookie policy updated successfully'];
        return back()->withNotify($notify);
    }

    public function customMessageStore(Request $request)
    {
        $request->validate([
            'message'   => 'required|string|max:1000',
            'link_url'  => 'nullable|string|max:500',
            'link_text' => 'nullable|string|max:120',
            'show_on'   => 'required|in:public_only,user_only,all',
            'position'  => 'required|in:top_bar,bottom_bar,banner_center',
            'route_filter' => 'nullable|string|max:500',
            'status'    => 'nullable',
        ]);
        $maxOrder = (int) Frontend::where('data_keys', 'custom_message.element')
            ->max(DB::raw('CAST(JSON_UNQUOTE(JSON_EXTRACT(data_values, "$.display_order")) AS UNSIGNED)'));
        $frontend = new Frontend();
        $frontend->data_keys = 'custom_message.element';
        $frontend->data_values = [
            'message'      => $request->message,
            'link_url'     => $request->link_url ?: '',
            'link_text'    => $request->link_text ?: __('Read more'),
            'show_on'      => $request->show_on,
            'position'     => $request->position,
            'route_filter' => $request->route_filter ? trim($request->route_filter) : '',
            'status'       => $request->status ? Status::ENABLE : Status::DISABLE,
            'display_order'=> $maxOrder + 1,
        ];
        $frontend->save();
        $notify[] = ['success', __('Custom message added successfully.')];
        return back()->withNotify($notify);
    }

    public function customMessageUpdate(Request $request, $id)
    {
        $request->validate([
            'message'   => 'required|string|max:1000',
            'link_url'  => 'nullable|string|max:500',
            'link_text' => 'nullable|string|max:120',
            'show_on'   => 'required|in:public_only,user_only,all',
            'position'  => 'required|in:top_bar,bottom_bar,banner_center',
            'route_filter' => 'nullable|string|max:500',
            'status'    => 'nullable',
        ]);
        $item = Frontend::where('data_keys', 'custom_message.element')->findOrFail($id);
        $item->data_values = [
            'message'      => $request->message,
            'link_url'     => $request->link_url ?: '',
            'link_text'    => $request->link_text ?: __('Read more'),
            'show_on'      => $request->show_on,
            'position'     => $request->position,
            'route_filter' => $request->route_filter ? trim($request->route_filter) : '',
            'status'       => $request->status ? Status::ENABLE : Status::DISABLE,
            'display_order'=> (int) ($item->data_values->display_order ?? $item->id),
        ];
        $item->save();
        $notify[] = ['success', __('Custom message updated successfully.')];
        return back()->withNotify($notify);
    }

    public function customMessageDelete($id)
    {
        $item = Frontend::where('data_keys', 'custom_message.element')->findOrFail($id);
        $item->delete();
        $notify[] = ['success', __('Custom message removed.')];
        return back()->withNotify($notify);
    }
}
