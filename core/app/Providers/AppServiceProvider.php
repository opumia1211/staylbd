<?php

namespace App\Providers;

use App\Constants\Status;
use App\Models\AdminNotification;
use App\Models\Deposit;
use App\Models\Frontend;
use App\Models\NotificationLog;
use App\Models\Order;
use App\Models\Product;
use App\Models\SupportTicket;
use App\Models\User;
use App\Observers\FrontendObserver;
use App\Observers\ProductObserver;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Event;
use Illuminate\Database\Events\MigrationsEnded;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(\App\Services\Courier\CourierManager::class, function () {
            return new \App\Services\Courier\CourierManager();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->applySubdirectoryPublicAssetRootsIfNeeded();

        try {
            // XAMPP/সাবফোল্ডার: ব্রাউজারে index.php ছাড়া পরিষ্কার লিঙ্ক (route()/asset URL জেনারেশন)
            if (!$this->app->runningInConsole()) {
                try {
                    $root = rtrim(request()->root(), '/');
                    if ($root !== '' && str_ends_with($root, '/index.php')) {
                        URL::forceRootUrl(substr($root, 0, -strlen('/index.php')));
                    }
                } catch (\Throwable $e) {
                    // ignore
                }
            }

            $general                         = gs();
            $activeTemplate                  = activeTemplate();
            $viewShare['general']            = $general;
            $viewShare['activeTemplate']     = $activeTemplate;
            $viewShare['activeTemplateTrue'] = activeTemplate(true);
            $viewShare['emptyMessage']       = 'Data not found';
            $viewShare['assetVersion']       = Cache::get('asset_version') ?? config('app.version'); // Bump on "Clear cache" so old browser cache is dropped
            if (Schema::hasTable('ui_settings')) {
                try {
                    $viewShare['uiSettings'] = \App\Models\UiSetting::getSettings();
                } catch (\Throwable $e) {
                    $viewShare['uiSettings'] = null;
                }
            } else {
                $viewShare['uiSettings'] = null;
            }

            view()->share($viewShare);

            // Admin login captcha: default off (same light UX as storefront login). Set ADMIN_LOGIN_CAPTCHA=true in .env or use Security dashboard.
            $envCaptcha = env('ADMIN_LOGIN_CAPTCHA');
            if ($envCaptcha === false || $envCaptcha === 'false' || $envCaptcha === '0') {
                config(['admin.admin_login_captcha' => false]);
            } elseif ($envCaptcha === true || $envCaptcha === 'true' || $envCaptcha === '1') {
                config(['admin.admin_login_captcha' => true]);
            } elseif (Schema::hasTable('security_settings')) {
                try {
                    $captcha = \App\Models\SecuritySetting::getBool(
                        'admin_login_captcha',
                        (bool) config('admin.admin_login_captcha', false)
                    );
                    config(['admin.admin_login_captcha' => $captcha]);
                } catch (\Throwable $e) {
                    // ignore
                }
            }

            // Admin 2FA at login: default on; turn off from Security dashboard or .env ADMIN_TWO_FACTOR_ENABLED=false
            $env2fa = env('ADMIN_TWO_FACTOR_ENABLED');
            if ($env2fa === false || $env2fa === 'false' || $env2fa === '0') {
                config(['admin.admin_two_factor_enabled' => false]);
            } elseif ($env2fa === true || $env2fa === 'true' || $env2fa === '1') {
                config(['admin.admin_two_factor_enabled' => true]);
            } elseif (Schema::hasTable('security_settings')) {
                try {
                    $twoFa = \App\Models\SecuritySetting::getBool(
                        'admin_two_factor_enabled',
                        (bool) config('admin.admin_two_factor_enabled', true)
                    );
                    config(['admin.admin_two_factor_enabled' => $twoFa]);
                } catch (\Throwable $e) {
                    // ignore
                }
            }

            // Cache admin sidebar counts for 60 seconds (fresh data, no sensitive info)
            view()->composer('admin.partials.sidenav', function ($view) {
                try {
                    $view->with([
                        'bannedUsersCount'           => Cache::remember('admin.counts.banned_users', 60, fn() => User::banned()->count()),
                        'emailUnverifiedUsersCount'  => Cache::remember('admin.counts.email_unverified', 60, fn() => User::emailUnverified()->count()),
                        'mobileUnverifiedUsersCount' => Cache::remember('admin.counts.mobile_unverified', 60, fn() => User::mobileUnverified()->count()),
                        'pendingTicketCount'         => Cache::remember('admin.counts.pending_tickets', 60, fn() => SupportTicket::whereIN('status', [Status::TICKET_OPEN, Status::TICKET_REPLY])->count()),
                        'pendingDepositsCount'       => Cache::remember('admin.counts.pending_deposits', 60, fn() => Deposit::pending()->count()),
                        'pendingOrderCount'          => Cache::remember('admin.counts.pending_orders', 60, fn() => Order::pending()->count()),
                        'activeCourierProviders'     => Cache::remember('admin.courier.providers', 60, fn() => \App\Models\Courierapi::active()->orderBy('sort_order')->orderBy('name')->get()),
                    ]);
                } catch (\Exception $e) {
                    // Database connection failed, provide default values
                    $view->with([
                        'bannedUsersCount'           => 0,
                        'emailUnverifiedUsersCount'  => 0,
                        'mobileUnverifiedUsersCount' => 0,
                        'pendingTicketCount'         => 0,
                        'pendingDepositsCount'       => 0,
                        'pendingOrderCount'          => 0,
                        'activeCourierProviders'     => collect([]),
                    ]);
                }
            });

            // Admin header notifications: short cache (2 sec) so new orders/messages show quickly in bell; cache cleared on create/update/delete
            view()->composer('admin.partials.topnav', function ($view) {
                try {
                    $view->with([
                        'adminNotifications'     => Cache::remember('admin.notifications.list', 2, fn() => AdminNotification::where('is_read', Status::NO)->with('user')->orderBy('id', 'desc')->take(15)->get()),
                        'adminNotificationCount' => Cache::remember('admin.notifications.count', 2, fn() => AdminNotification::where('is_read', Status::NO)->count()),
                    ]);
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::channel('daily')->warning('Admin notification view composer failed', ['message' => $e->getMessage()]);
                    $view->with([
                        'adminNotifications'     => collect([]),
                        'adminNotificationCount' => 0,
                    ]);
                }
            });

            // User header + user dashboard: notification count for logged-in users
            $userNotificationComposer = function ($view) {
                if (auth()->check()) {
                    try {
                        $view->with('userNotificationCount', NotificationLog::where('user_id', auth()->id())->unread()->count());
                    } catch (\Exception $e) {
                        $view->with('userNotificationCount', 0);
                    }
                } else {
                    $view->with('userNotificationCount', 0);
                }
            };
            view()->composer($activeTemplate . 'partials.header', $userNotificationComposer);
            view()->composer($activeTemplate . 'layouts.master', $userNotificationComposer);

            // Header icons admin-control bootstrap: keep default icon set pre-filled in Frontend->Manage Section.
            if (Schema::hasTable('frontends')) {
                try {
                    Frontend::firstOrCreate(
                        ['data_keys' => 'header_icons.content'],
                        ['data_values' => (object) [
                            'search_icon' => 'search',
                            'voice_search_icon' => 'microphone',
                            'image_search_icon' => 'scan',
                            'home_icon' => 'home',
                            'categories_icon' => 'th-large',
                            'products_icon' => 'box',
                            'contact_icon' => 'phone',
                            'track_order_icon' => 'shipping-fast',
                            'language_icon' => 'language',
                            'notification_icon' => 'bell',
                            'wishlist_icon' => 'heart',
                            'compare_icon' => 'exchange-alt',
                            'cart_icon' => 'shopping-cart',
                            'buy_now_icon' => 'cart-plus',
                            'orders_icon' => 'list-alt',
                            'login_icon' => 'user',
                            'register_icon' => 'user-plus',
                            'transactions_icon' => 'money-bill-wave',
                            'messages_icon' => 'comments',
                            'mail_icon' => 'envelope',
                            'review_icon' => 'star',
                            'profile_icon' => 'user-tie',
                            'change_password_icon' => 'key',
                            'logout_icon' => 'sign-out-alt',
                            'quick_view_icon' => 'eye',
                            'policy_payment_icon' => 'credit-card',
                            'policy_shipping_icon' => 'shipping-fast',
                            'policy_order_icon' => 'list-alt',
                            'section_brand_icon' => 'tag',
                            'scroll_top_icon' => 'angle-double-up',
                        ]]
                    );
                    $headerIconRow = Frontend::where('data_keys', 'header_icons.content')->orderBy('id', 'desc')->first();
                    if ($headerIconRow && $headerIconRow->data_values) {
                        $vals = (array) $headerIconRow->data_values;
                        $currentOrders = trim((string) ($vals['orders_icon'] ?? ''));
                        if ($currentOrders === '' || in_array($currentOrders, ['shopping-bag', 'clipboard-list'], true)) {
                            $vals['orders_icon'] = 'list-alt';
                            $headerIconRow->data_values = (object) $vals;
                            $headerIconRow->save();
                        }
                    }
                } catch (\Throwable $e) {
                    // fail-safe: do not block storefront rendering
                }
            }

            // Cache SEO data for 1 hour (rarely changes)
            view()->composer('partials.seo', function ($view) {
                try {
                    $seo = Cache::remember('seo.data', 3600, function() {
                        return Frontend::where('data_keys', 'seo.data')->first();
                    });
                    $view->with([
                        'seo' => $seo ? $seo->data_values : $seo,
                    ]);
                } catch (\Exception $e) {
                    // Database connection failed, provide default value
                    $view->with([
                        'seo' => null,
                    ]);
                }
            });

            if (isset($general->force_ssl) && $general->force_ssl) {
                \URL::forceScheme('https');
            }
        } catch (\Exception $e) {
            // If database connection fails completely, set minimal defaults
            $viewShare['general']            = (object) [];
            $viewShare['activeTemplate']     = 'basic';
            $viewShare['activeTemplateTrue']  = 'basic';
            $viewShare['emptyMessage']       = 'Data not found';
            $viewShare['assetVersion']      = Cache::get('asset_version') ?? config('app.version');
            view()->share($viewShare);
        }

        Paginator::useBootstrapFour();

        /*
         * Route-scoped storefront CSS: home uses a smaller bundle; other storefront pages load full product CSS.
         */
        View::composer('templates.basic.layouts.app', function ($view) {
            $route = request()->route();
            $bundle = 'tailwind-product';
            if ($route && $route->getName() === 'home') {
                $bundle = 'tailwind-homepage';
            }
            $view->with('storefrontCssBundle', $bundle);
        });

        // @adminCan('products.edit') for permission-based admin UI
        Blade::if('adminCan', function (string $permission) {
            $admin = auth()->guard('admin')->user();
            return $admin && \App\Models\Permission::has($admin, $permission);
        });

        // Auto cache invalidation: product create/update/delete -> user & admin see fresh data immediately
        Product::observe(ProductObserver::class);
        Frontend::observe(FrontendObserver::class);

        // Horizon dashboard: allow only authenticated admin (when Horizon is installed)
        if (class_exists(\Laravel\Horizon\Horizon::class)) {
            \Laravel\Horizon\Horizon::auth(function ($request) {
                return auth()->guard('admin')->check();
            });
        }
    }



    /**
     * When the app boots from htdocs/{app}/index.php but Laravel public/ is
     * htdocs/{app}/core/public, relative mix()/asset() URLs 404. Set ASSET_URL
     * in .env to http://localhost/{app}/core/public (or CDN); if unset, detect
     * this layout via parent index.php and wire asset_url, mix_url, and the
     * public disk URL for storage-backed images (banners, products).
     */
    private function applySubdirectoryPublicAssetRootsIfNeeded(): void
    {
        $explicitAsset = env('ASSET_URL');
        if (is_string($explicitAsset) && $explicitAsset !== '') {
            $root = rtrim($explicitAsset, '/');
            $mixExplicit = env('MIX_ASSET_URL');
            $mix = (is_string($mixExplicit) && $mixExplicit !== '') ? rtrim($mixExplicit, '/') : $root;
            config([
                'app.asset_url' => $root,
                'app.mix_url' => $mix,
            ]);
            config(['filesystems.disks.public.url' => $root . '/storage']);

            return;
        }

        $mixOnly = env('MIX_ASSET_URL');
        if (is_string($mixOnly) && $mixOnly !== '') {
            config(['app.mix_url' => rtrim($mixOnly, '/')]);
        }

        if ($this->app->runningInConsole()) {
            return;
        }

        $parentIndex = dirname(base_path()) . DIRECTORY_SEPARATOR . 'index.php';
        if (! is_file($parentIndex)) {
            return;
        }

        $head = @file_get_contents($parentIndex, false, null, 0, 2500) ?: '';
        if (! str_contains($head, 'core/bootstrap')) {
            return;
        }

        $appUrl = rtrim((string) config('app.url'), '/');
        if ($appUrl === '') {
            return;
        }

        $publicRoot = $appUrl . '/core/public';
        $mix = (is_string($mixOnly) && $mixOnly !== '') ? rtrim($mixOnly, '/') : $publicRoot;

        config([
            'app.asset_url' => $publicRoot,
            'app.mix_url' => $mix,
        ]);
        config(['filesystems.disks.public.url' => $publicRoot . '/storage']);
    }
}
