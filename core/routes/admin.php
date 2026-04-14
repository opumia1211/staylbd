<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\FrontendController;
use App\Http\Controllers\Admin\HeaderControlController;

Route::namespace('Auth')->group(function () {
            Route::controller('LoginController')->group(function () {
                Route::get('/', 'showLoginForm')->name('login');
                Route::get('captcha-refresh', 'refreshCaptcha')->name('login.captcha.refresh');
                Route::get('captcha-image', 'captchaImage')->name('login.captcha.image');
                Route::post('/', 'login')->name('login.submit')->middleware('throttle:admin-login');
                Route::get('logout', 'logout')->name('logout');
            });
            // 2FA: throttle POST only (GET form loads must not consume the budget — avoids 429 after a few tries)
            Route::controller('TwoFactorController')->prefix('2fa')->name('2fa.')->group(function () {
                Route::get('verify', 'verify')->name('verify');
                Route::post('verify', 'confirmVerify')->name('verify.submit')->middleware('throttle:admin_2fa');
                Route::get('setup', 'setup')->name('setup');
                Route::post('setup', 'confirmSetup')->name('setup.confirm')->middleware('throttle:admin_2fa');
            });

            // Admin Password Reset
            Route::controller('ForgotPasswordController')->prefix('password')->name('password.')->group(function () {
                Route::get('reset', 'showLinkRequestForm')->name('reset');
                Route::post('reset', 'sendResetCodeEmail')->middleware('throttle:admin-password-reset');
                Route::get('code-verify', 'codeVerify')->name('code.verify');
                Route::post('verify-code', 'verifyCode')->name('verify.code')->middleware('throttle:admin-password-reset');
            });

            Route::controller('ResetPasswordController')->group(function () {
                Route::get('password/reset/{token}', 'showResetForm')->name('password.reset.form');
                Route::post('password/reset/change', 'reset')->name('password.change');
            });
        });

Route::middleware(['admin', 'force.admin.password', 'admin.session.control'])->group(function () {
    // CSRF token for long forms (e.g. product edit) – touch session and return current token to avoid 419
    Route::get('csrf-token', function () {
        return response()->json(['token' => csrf_token()]);
    })->name('csrf.token');

    // Cache clear – SuperAdmin/Owner only; fully disabled when ENABLE_ADMIN_CLEAR=false (no bypass)
    Route::get('clear', function () {
        if (!config('admin.enable_clear', true)) {
            \Illuminate\Support\Facades\Log::channel('security')->warning('Cache clear attempted but disabled', [
                'admin_id'   => auth()->guard('admin')->id(),
                'ip'         => request()->ip(),
                'user_agent' => substr(request()->userAgent() ?? '', 0, 500),
            ]);
            abort(404);
        }
        $admin = auth()->guard('admin')->user();
        try {
            \Illuminate\Support\Facades\Artisan::call('optimize:clear');
            \Illuminate\Support\Facades\Cache::put('asset_version', (string) time()); // Invalidate browser cache: new ?v= so old CSS/JS cache is dropped
            \App\Models\CacheClearLog::create([
                'admin_id'   => $admin->id,
                'admin_name' => $admin->name ?? null,
                'action'     => 'cache_clear',
                'ip'         => request()->ip(),
                'user_agent' => substr(request()->userAgent() ?? '', 0, 500),
                'success'    => true,
            ]);
            \Illuminate\Support\Facades\Log::channel('daily')->info('Admin cache clear success', [
                'admin_id' => $admin->id, 'ip' => request()->ip(),
            ]);
            return response()->json(['message' => __('Cache cleared. Browser cache will refresh on next visit.')], 200);
        } catch (\Throwable $e) {
            \App\Models\CacheClearLog::create([
                'admin_id'      => $admin->id,
                'admin_name'    => $admin->name ?? null,
                'action'        => 'cache_clear',
                'ip'            => request()->ip(),
                'user_agent'    => substr(request()->userAgent() ?? '', 0, 500),
                'success'       => false,
                'error_message' => $e->getMessage(),
            ]);
            \Illuminate\Support\Facades\Log::channel('daily')->error('Admin cache clear failed', [
                'admin_id' => $admin->id, 'ip' => request()->ip(), 'error' => $e->getMessage(),
            ]);
            return response()->json(['message' => 'Cache clear failed'], 500);
        }
    })->name('optimize.clear')->middleware(['role.superadmin', 'throttle:admin_clear']);

    Route::get('session-keepalive', function () {
        return response()->json(['ok' => 1, 'csrf' => csrf_token()]);
    })->name('session.keepalive');

    Route::controller('AdminController')->group(function () {
        Route::get('dashboard', 'dashboard')->name('dashboard');
        Route::get('dashboard/stats', 'dashboardStats')->name('dashboard.stats');
        Route::get('business/insights', 'businessInsights')->name('business.insights');
        Route::get('profile', 'profile')->name('profile');
        Route::post('profile', 'profileUpdate')->name('profile.update');
        Route::get('password', 'password')->name('password');
        Route::post('password', 'passwordUpdate')->name('password.update');

        // Re-auth for high-risk actions
        Route::get('reauth', [\App\Http\Controllers\Admin\Auth\ReAuthController::class, 'form'])->name('reauth.form');
        Route::post('reauth', [\App\Http\Controllers\Admin\Auth\ReAuthController::class, 'verify'])->name('reauth.verify');

        // 2FA recovery codes (one-time after enable)
        Route::get('2fa/recovery-codes', [\App\Http\Controllers\Admin\Auth\TwoFactorController::class, 'showRecoveryCodes'])->name('2fa.recovery-codes');
        // 2FA disable (requires password + reauth)
        Route::get('2fa/disable', [\App\Http\Controllers\Admin\Auth\TwoFactorController::class, 'disable'])->name('2fa.disable')->middleware('admin.reauth:2fa_disable');
        Route::post('2fa/disable', [\App\Http\Controllers\Admin\Auth\TwoFactorController::class, 'confirmDisable'])->name('2fa.disable.confirm')->middleware('admin.reauth:2fa_disable');

        //Notification
        Route::get('notifications', 'notifications')->name('notifications');
        Route::get('notifications/delivery-scan', 'deliveryScanNotifications')->name('notifications.delivery.scan');
        Route::get('notification/read/{id}', 'notificationRead')->name('notification.read');
        Route::get('notifications/read-all', 'readAll')->name('notifications.readAll');

        //Report Bugs & Feature Requests
        Route::get('request-report', 'requestReport')->name('request.report');
        Route::post('request-report', 'reportSubmit');
        Route::post('request-report/{id}/status', 'reportStatusUpdate')->name('request.report.status');
        Route::post('request-report/{id}/delete', 'reportDelete')->name('request.report.delete');

        Route::get('download-attachments/{file_hash}', 'downloadAttachment')->name('download.attachment');
    });

    // Security Dashboard (SuperAdmin/Owner)
    Route::get('security', [\App\Http\Controllers\Admin\SecurityDashboardController::class, 'index'])
        ->name('security.dashboard')
        ->middleware('role.superadmin');
    Route::post('security/clear-lockouts', [\App\Http\Controllers\Admin\SecurityDashboardController::class, 'clearLockouts'])
        ->name('security.clear.lockouts')
        ->middleware('role.superadmin');
    Route::post('security/run-scan', [\App\Http\Controllers\Admin\SecurityDashboardController::class, 'runScan'])
        ->name('security.run.scan')
        ->middleware('role.superadmin');
    Route::post('security/toggle', [\App\Http\Controllers\Admin\SecurityDashboardController::class, 'toggleSetting'])
        ->name('security.toggle')
        ->middleware('role.superadmin');
    Route::post('security/update-admin-prefix', [\App\Http\Controllers\Admin\SecurityDashboardController::class, 'updateAdminPrefix'])
        ->name('security.update.admin.prefix')
        ->middleware('role.superadmin');

    // Admin Search
    Route::controller('AdminSearchController')->prefix('search')->name('search.')->group(function () {
        Route::get('/', 'search')->name('index');
    });

    // Users Manager
    Route::controller('ManageUsersController')->name('users.')->prefix('users')->group(function () {
        Route::get('/', 'allUsers')->name('all');
        Route::get('active', 'activeUsers')->name('active');
        Route::get('active/export', 'activeUsersExport')->name('active.export');
        Route::get('banned', 'bannedUsers')->name('banned');
        Route::get('banned/export', 'bannedUsersExport')->name('banned.export');
        Route::get('email-verified', 'emailVerifiedUsers')->name('email.verified');
        Route::get('email-unverified', 'emailUnverifiedUsers')->name('email.unverified');
        Route::get('email-unverified/export', 'emailUnverifiedExport')->name('email.unverified.export');
        Route::get('mobile-unverified', 'mobileUnverifiedUsers')->name('mobile.unverified');
        Route::get('mobile-unverified/export', 'mobileUnverifiedExport')->name('mobile.unverified.export');
        Route::get('mobile-verified', 'mobileVerifiedUsers')->name('mobile.verified');

        Route::get('detail/{id}', 'detail')->name('detail');
        Route::post('update/{id}', 'update')->name('update');
        Route::get('send-notification/{id}', 'showNotificationSingleForm')->name('notification.single');
        Route::post('send-notification/{id}', 'sendNotificationSingle')->name('notification.single.send');
        Route::get('login/{id}', 'login')->name('login');
        Route::post('status/{id}', 'status')->name('status');
        Route::post('notification-log/{userId}/delete/{logId}', 'deleteNotificationLog')->name('notification.log.delete');

        Route::get('send-notification', 'showNotificationAllForm')->name('notification.all');
        Route::post('send-notification', 'sendNotificationAll')->name('notification.all.send');
        Route::get('notification-log/{id}', 'notificationLog')->name('notification.log');
    });

    // Subscriber
    Route::controller('SubscriberController')->prefix('subscriber')->name('subscriber.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('send-email', 'sendEmailForm')->name('send.email');
        Route::post('remove/{id}', 'remove')->name('remove');
        Route::post('send-email', 'sendEmail')->name('send.email.submit');
    });

    Route::controller('BrandController')->prefix('brand')->name('brand.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('store/{id?}', 'store')->name('store');
        Route::post('status/{id}', 'status')->name('status');
        Route::post('featured/{id}', 'featured')->name('featured');
        Route::post('delete/{id}', 'delete')->name('delete');
    });

    Route::controller('CategoryController')->prefix('category')->name('category.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('store/{id?}', 'store')->name('store');
        Route::post('status/{id}', 'status')->name('status');
        Route::post('featured/{id}', 'featured')->name('featured');
        Route::post('bulk-status', 'bulkStatus')->name('bulk.status');
        Route::post('bulk-featured', 'bulkFeatured')->name('bulk.featured');
    });

    Route::controller('SubcategoryController')->prefix('sub-category')->name('subcategory.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('status/{id}', 'status')->name('status');
        Route::post('store/{id?}', 'store')->name('store');
        Route::post('bulk-status', 'bulkStatus')->name('bulk.status');
    });

    Route::controller('CouponController')->prefix('coupon')->name('coupon.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('store/{id?}', 'store')->name('store');
        Route::post('duplicate/{id}', 'duplicate')->name('duplicate');
        Route::post('status/{id}', 'status')->name('status');
    });

    //Product Controller
    Route::controller('ProductController')->prefix('product')->name('product.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::get('create2', 'generalCreate')->name('create2');
        Route::get('general-create', 'generalCreate')->name('general.create'); // same as create2
        Route::get('stock-alerts', 'stockAlerts')->name('stock.alerts');
        Route::post('store/{id?}', 'store')->name('store');
        Route::post('store2/{id?}', 'generalStore')->name('store2');
        Route::post('general-store/{id?}', 'generalStore')->name('general.store');
        Route::get('edit/{id}', 'edit')->name('edit');
        Route::post('status/{id}', 'status')->name('status');
        Route::post('featured/{id}', 'featured')->name('featured');
        Route::post('hot-deal/{id}', 'hotDeal')->name('hot.deal');
        Route::post('today-deal/{id}', 'todayDeal')->name('today.deal');
        Route::get('today-deal', 'todayDealProduct')->name('todayDeal');
        Route::get('trending', 'trendingProduct')->name('trending');
        Route::get('best-selling', 'bestSellingProduct')->name('bestSelling');
        Route::get('featured', 'featureProduct')->name('feature.index');
        Route::get('hot', 'hotProduct')->name('hot');
        Route::post('trending-deal/{id}', 'trendingDeal')->name('trending.deal');
        Route::get('reviews', 'reviewsIndex')->name('reviews.index');
        Route::get('reviews/{id}', 'reviews')->name('reviews');
        Route::post('review/approve/{id}', 'reviewApprove')->name('review.approve');
        Route::post('review/reject/{id}', 'reviewReject')->name('review.reject');
        Route::post('review/toggle-private/{id}', 'reviewTogglePrivate')->name('review.toggle.private');
        Route::post('review/featured/{id}', 'reviewFeatured')->name('review.featured');
        Route::post('review/remove/{id}', 'reviewRemove')->name('review.remove');
        Route::post('review/bulk-delete', 'reviewBulkDelete')->name('review.bulk.delete');
        Route::post('delete/{id}', 'delete')->name('delete');
        Route::post('bulk-delete', 'bulkDelete')->name('bulk.delete');
        Route::post('bulk-edit', 'bulkEdit')->name('bulk.edit');
    });

    // Product Attributes (Size, Color, etc.) – professional eCommerce
    Route::controller('ProductAttributeController')->prefix('attributes')->name('attributes.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('edit/{id}', 'edit')->name('edit');
        Route::post('update/{id}', 'update')->name('update');
        Route::post('status/{id}', 'status')->name('status');
        Route::post('delete/{id}', 'destroy')->name('destroy');
        Route::post('bulk-status', 'bulkStatus')->name('bulk.status');
        Route::get('duplicate/{id}', 'duplicate')->name('duplicate');
    });

    // Category Attributes (assign attributes to categories)
    Route::controller('CategoryAttributeController')->prefix('category/attributes')->name('category.attributes.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'update')->name('update');
    });

    Route::controller('TopFeatureController')->prefix('product/topbar')->name('product.topbar.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('store', 'store')->name('store');
        Route::post('update/{id}', 'update')->name('update');
        Route::post('status/{id}', 'status')->name('status');
        Route::post('destroy/{id}', 'destroy')->name('destroy');
        Route::post('reorder', 'reorder')->name('reorder');
    });

    Route::controller('OfferTimerController')->prefix('offer-timers')->name('offer-timers.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('edit/{id}', 'edit')->name('edit');
        Route::put('{id}', 'update')->name('update');
        Route::post('status/{id}', 'status')->name('status');
        Route::post('destroy/{id}', 'destroy')->name('destroy');
    });

    Route::controller('PopupAdController')->prefix('popup-ads')->name('popup-ads.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('edit/{id}', 'edit')->name('edit');
        Route::put('{id}', 'update')->name('update');
        Route::post('status/{id}', 'status')->name('status');
        Route::post('destroy/{id}', 'destroy')->name('destroy');
    });

    // Abandoned Carts & Incomplete Orders
    Route::controller(\App\Http\Controllers\Admin\AbandonedOrderController::class)->prefix('abandoned-orders')->name('abandoned-orders.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('settings', 'settings')->name('settings');
        Route::post('settings', 'updateSettings')->name('settings.update');
        Route::post('{id}/send-reminder', 'sendReminder')->name('send-reminder')->whereNumber('id');
    });

    Route::controller('OrderController')->prefix('order')->name('orders.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('pending', 'pending')->name('pending');
        Route::get('confirmed', 'confirmed')->name('confirmed');
        Route::get('processing', 'processing')->name('processing');
        Route::get('packaging', 'packaging')->name('packaging');
        Route::get('shipped', 'shipped')->name('shipped');
        Route::get('delivered', 'delivered')->name('delivered');
        Route::get('cancel', 'cancel')->name('cancel');
        Route::get('export', 'export')->name('export');
        Route::post('status/{id}', 'status')->name('status');
        Route::get('details/{id}', 'details')->name('detail');
        Route::get('invoice/{id}', 'invoice')->name('invoice');
        Route::post('address/{id}', 'updateAddress')->name('address.update');

        // Order Location Tracking
        Route::controller('OrderTrackingController')->prefix('details/{orderId}/tracking')->name('tracking.')->group(function () {
            Route::post('/', 'store')->name('store');
            Route::post('{id}/remove', 'destroy')->name('destroy');
        });

        // Order Enhancements (advance payment, staff notes)
        Route::post('details/{orderId}/enhancements', [\App\Modules\OrderEnhancements\Http\Controllers\OrderEnhancementsController::class, 'update'])->name('enhancements.update');

        // Courier API Routes
        Route::get('bulk-courier/{slug}', 'bulk_courier')->name('bulk.courier');
        Route::post('bulk-courier/send', 'bulk_courier_send')->name('bulk.courier.send');
        Route::get('pathao-city', 'pathaocity')->name('pathao.city');
        Route::get('pathao-zone', 'pathaozone')->name('pathao.zone');
        Route::post('pathao', 'order_pathao')->name('pathao');
        Route::post('steadfast', 'order_steadfast')->name('steadfast');
    });

    Route::prefix('shipping-method')->name('shipping.')->group(function () {
        Route::controller('ShippingMethodController')->group(function () {
            Route::get('/', 'hub')->name('index');
            Route::get('methods', 'index')->name('methods.index');
            Route::post('store/{id?}', 'store')->name('store');
            Route::post('status/{id}', 'status')->name('status');
        });
        Route::controller('ShippingZoneController')->prefix('zones')->name('zones.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('create', 'create')->name('create');
            Route::post('create', 'store')->name('store');
            Route::get('edit/{id}', 'edit')->name('edit');
            Route::post('edit/{id}', 'update')->name('update');
            Route::post('status/{id}', 'status')->name('status');
            Route::post('{zoneId}/country', 'addCountry')->name('country.add');
            Route::post('{zoneId}/country/{countryId}/remove', 'removeCountry')->name('country.remove');
            Route::post('{zoneId}/area', 'addArea')->name('area.add');
            Route::post('{zoneId}/area/{areaId}/remove', 'removeArea')->name('area.remove');
        });
        Route::controller('ShippingRuleController')->prefix('rules')->name('rules.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'update')->name('update');
        });
        Route::controller('CodSettingsController')->prefix('cod')->name('cod.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'update')->name('update');
        });
    });

    // API Integration Routes
    Route::controller('ApiIntegrationController')->prefix('api-integration')->name('api.')->group(function () {
        Route::get('courier', 'courier_manage')->name('courier.manage');
        Route::post('courier/store', 'courier_store')->name('courier.store');
        Route::post('courier/store-custom', 'courier_store_custom')->name('courier.store.custom');
        Route::get('courier/edit/{id}', 'courier_edit_json')->name('courier.edit.json');
        Route::post('courier/update', 'courier_update')->name('courier.update');
        Route::post('courier/test-connection/{id}', 'courier_test_connection')->name('courier.test');
        Route::get('courier/logs', 'courier_logs')->name('courier.logs');
        Route::get('courier/logs/export', 'courier_logs_export')->name('courier.logs.export');
        Route::get('courier/logs/retry/{id}', 'courier_log_retry')->name('courier.log.retry');
        Route::get('courier/reports', 'courier_reports')->name('courier.reports');
        Route::get('courier/reports/export', 'courier_reports_export')->name('courier.reports.export');
    });

    // Payment Gateways Hub (single entry: /payment-gateways)
    Route::get('payment-gateways', [\App\Http\Controllers\Admin\PaymentGatewayHubController::class, 'index'])->name('payment.gateways.hub');

    // Payment Analytics Dashboard
    Route::get('payment/analytics', [\App\Http\Controllers\Admin\PaymentAnalyticsController::class, 'index'])->name('payment.analytics');

    // Deposit Gateway
    Route::name('gateway.')->prefix('gateway')->group(function () {

        // Automatic Gateway
        Route::controller('AutomaticGatewayController')->prefix('automatic')->name('automatic.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('edit/{alias}', 'edit')->name('edit');
            Route::post('update/{code}', 'update')->name('update');
            Route::post('remove/{id}', 'remove')->name('remove');
            Route::post('status/{id}', 'status')->name('status');
        });

        // Manual Methods
        Route::controller('ManualGatewayController')->prefix('manual')->name('manual.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('new', 'create')->name('create');
            Route::post('new', 'store')->name('store');
            Route::get('edit/{alias}', 'edit')->name('edit');
            Route::post('update/{id}', 'update')->name('update');
            Route::post('status/{id}', 'status')->name('status');
        });

        // Autopay (external sites + app/SMS bridge)
        Route::controller('AutopayController')->prefix('autopay')->name('autopay.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('external/new', 'createExternal')->name('external.create');
            Route::post('external/new', 'storeExternal')->name('external.store');
            Route::get('external/edit/{alias}', 'editExternal')->name('external.edit');
            Route::post('external/update/{code}', 'updateExternal')->name('external.update');
            Route::get('message/new', 'createMessage')->name('message.create');
            Route::post('message/new', 'storeMessage')->name('message.store');
            Route::get('message/edit/{alias}', 'editMessage')->name('message.edit');
            Route::post('message/update/{code}', 'updateMessage')->name('message.update');
            Route::post('message/regenerate-key/{code}', 'regenerateApiKey')->name('message.regenerate_key');
            Route::post('status/{id}', 'status')->name('status');
        });
    });

    // DEPOSIT SYSTEM
    Route::controller('DepositController')->prefix('deposit')->name('deposit.')->group(function () {
        Route::get('/', 'deposit')->name('list');
        Route::get('pending', 'pending')->name('pending');
        Route::get('rejected', 'rejected')->name('rejected');
        Route::get('approved', 'approved')->name('approved');
        Route::get('successful', 'successful')->name('successful');
        Route::get('initiated', 'initiated')->name('initiated');
        Route::get('details/{id}', 'details')->name('details');
        Route::get('export', 'export')->name('export');
        Route::post('reject', 'reject')->name('reject')->middleware('admin.reauth:payment_override');
        Route::post('approve/{id}', 'approve')->name('approve')->middleware('admin.reauth:payment_override');
    });

    // Report
    Route::controller('ReportController')->prefix('report')->name('report.')->group(function () {
        Route::get('transaction', 'transaction')->name('transaction');
        Route::get('transaction/export', 'exportTransaction')->name('transaction.export');
        Route::get('login/history', 'loginHistory')->name('login.history');
        Route::get('login/history/export', 'exportLoginHistory')->name('login.history.export');
        Route::get('login/ipHistory/{ip}', 'loginIpHistory')->name('login.ipHistory');
        Route::get('notification/history', 'notificationHistory')->name('notification.history');
        Route::get('notification/history/export', 'exportNotificationHistory')->name('notification.history.export');
        Route::get('email/detail/{id}', 'emailDetails')->name('email.details');
        Route::get('search-analytics', 'searchAnalytics')->name('search.analytics');
        Route::get('search-analytics/export', 'exportSearchAnalytics')->name('search.analytics.export');
        Route::post('search-analytics/delete', 'deleteSearchLogs')->name('search.analytics.delete');
        Route::post('transaction/bulk-delete', 'bulkDeleteTransactions')->name('transaction.bulk_delete');
        Route::post('login/history/bulk-delete', 'bulkDeleteLoginHistory')->name('login.history.bulk_delete');
        Route::post('notification/history/bulk-delete', 'bulkDeleteNotificationHistory')->name('notification.history.bulk_delete');
    });

    // Ad Source Report (Facebook, Google, TikTok orders)
    Route::controller(\App\Http\Controllers\Admin\AdSourceReportController::class)->prefix('report')->name('report.')->group(function () {
        Route::get('ad-source', 'index')->name('ad_source');
        Route::get('ad-source/export', 'export')->name('ad_source.export');
    });

    // Product Report (summary, best seller, stock)
    Route::controller(\App\Http\Controllers\Admin\ProductReportController::class)->prefix('report')->name('report.')->group(function () {
        Route::get('product', 'index')->name('product');
        Route::get('product/export', 'export')->name('product.export');
    });

    // Revenue & Profit Report (module)
    Route::get('report/revenue-profit', [\App\Modules\RevenueProfitReport\Http\Controllers\RevenueProfitReportController::class, 'index'])->name('report.revenue_profit');
    // Employee Performance Report (module)
    Route::get('report/employee-performance', [\App\Modules\EmployeePerformanceReport\Http\Controllers\EmployeePerformanceReportController::class, 'index'])->name('report.employee_performance');

    // User Activity Reports (full tracking: search, product view, cart, wishlist, compare, orders, payments, login, registration, messages, location, all)
    Route::controller(\App\Http\Controllers\Admin\UserActivityReportController::class)->prefix('report')->name('report.activity.')->group(function () {
        Route::get('analytics-dashboard', 'dashboard')->name('dashboard');
        Route::get('search', 'search')->name('search');
        Route::get('search/export', 'searchExport')->name('search.export');
        Route::get('product-views', 'productViews')->name('product_views');
        Route::get('product-views/export', 'productViewsExport')->name('product_views.export');
        Route::get('cart', 'cart')->name('cart');
        Route::get('cart/export', 'cartExport')->name('cart.export');
        Route::get('wishlist', 'wishlist')->name('wishlist');
        Route::get('wishlist/export', 'wishlistExport')->name('wishlist.export');
        Route::get('compare', 'compare')->name('compare');
        Route::get('compare/export', 'compareExport')->name('compare.export');
        Route::post('compare/delete', 'compareDelete')->name('compare.delete');
        Route::post('compare/bulk-delete', 'compareBulkDelete')->name('compare.bulk_delete');
        Route::get('orders', 'orders')->name('orders');
        Route::get('orders/export', 'ordersExport')->name('orders.export');
        Route::get('track-order', 'trackOrder')->name('track_order');
        Route::get('track-order/export', 'trackOrderExport')->name('track_order.export');
        Route::get('payments', 'payments')->name('payments');
        Route::get('payments/export', 'paymentsExport')->name('payments.export');
        Route::get('login', 'login')->name('login');
        Route::get('login/export', 'loginExport')->name('login.export');
        Route::get('registration', 'registration')->name('registration');
        Route::get('registration/export', 'registrationExport')->name('registration.export');
        Route::get('messages', 'messages')->name('messages');
        Route::get('messages/export', 'messagesExport')->name('messages.export');
        Route::get('location', 'location')->name('location');
        Route::get('location/export', 'locationExport')->name('location.export');
        Route::get('all-activity', 'allActivity')->name('all');
        Route::get('all-activity/export', 'allActivityExport')->name('all.export');
        Route::get('live-monitor', 'liveMonitor')->name('live');
        Route::get('live-monitor/data', 'liveMonitorData')->name('live.data');
        Route::get('suspicious', 'suspicious')->name('suspicious');
        Route::post('bulk-delete', 'bulkDelete')->name('bulk_delete');
    });

    // Redirect old Message Center URLs to Support Ticket (backward compatibility)
    Route::redirect('messages', 'ticket', 301);
    Route::redirect('messages/pending', 'ticket/pending', 301);
    Route::redirect('messages/closed', 'ticket/closed', 301);
    Route::redirect('messages/answered', 'ticket/answered', 301);

    // Support Ticket (admin panel: /sajaladminopu/ticket, /ticket/pending, /ticket/closed, /ticket/answered)
    Route::controller('SupportTicketController')->prefix('ticket')->name('ticket.')->group(function () {
        Route::get('/', 'tickets')->name('index');
        Route::get('LiveChat', 'ticketsBySubject')->name('subject.livechat');
        Route::get('GeneralInquiry', 'ticketsBySubject')->name('subject.general');
        Route::get('ReportProblem', 'ticketsBySubject')->name('subject.report');
        Route::get('OrderSupport', 'ticketsBySubject')->name('subject.order');
        Route::get('pending', 'pendingTicket')->name('pending');
        Route::get('closed', 'closedTicket')->name('closed');
        Route::get('answered', 'answeredTicket')->name('answered');
        Route::get('view/{id}', 'ticketReply')->name('view');
        Route::get('view-user/{userId}', 'ticketReplyByUser')->name('view.user');
        Route::get('json/{id}', 'getTicketMessagesJson')->name('messages.json');
        Route::get('json-user/{userId}', 'getTicketMessagesJsonByUser')->name('messages.json.user');
        Route::post('reply/{id}', 'replyTicket')->name('reply');
        Route::post('close/{id}', 'closeTicket')->name('close');
        Route::get('download/{ticket}', 'ticketDownload')->name('download');
        Route::post('delete/{id}', 'ticketDelete')->name('delete');
        Route::post('bulk-delete', 'bulkDeleteMessages')->name('bulk-delete');
        Route::post('bulk-delete-conversations', 'bulkDeleteConversations')->name('bulk-delete-conversations');
        Route::post('bulk-delete-messages-global', 'bulkDeleteMessagesGlobal')->name('bulk-delete-messages-global');
    });

    Route::controller('AutoResponseController')->prefix('auto-response')->name('auto_response.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('edit/{id}', 'edit')->name('edit');
        Route::post('edit/{id}', 'update')->name('update');
        Route::post('delete/{id}', 'destroy')->name('destroy');
    });

    /* Auto AI – same as Auto-Response, new URL /autoai for AI-powered auto messages */
    Route::controller('AutoResponseController')->prefix('autoai')->name('autoai.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('edit/{id}', 'edit')->name('edit');
        Route::post('edit/{id}', 'update')->name('update');
        Route::post('delete/{id}', 'destroy')->name('destroy');
        Route::post('toggle-active/{id}', 'toggleActive')->name('toggle.active');
        Route::post('toggle-visibility/{id}', 'toggleVisibility')->name('toggle.visibility');
    });

    // Language Manager – A–Z translations for frontend + admin; superfast keys cache
    Route::controller('LanguageController')->prefix('language')->name('language.')->group(function () {
        Route::get('/', 'langManage')->name('manage');
        Route::post('/', 'langStore')->name('manage.store');
        Route::post('delete/{id}', 'langDelete')->name('manage.delete');
        Route::post('update/{id}', 'langUpdate')->name('manage.update');
        Route::get('edit/{id}', 'langEdit')->name('key');
        Route::post('set-locale', 'setSessionLocale')->name('set.locale');
        Route::post('import', 'langImport')->name('import.lang');
        Route::post('store/key/{id}', 'storeLanguageJson')->name('store.key');
        Route::post('delete/key/{id}', 'deleteLanguageJson')->name('delete.key');
        Route::post('update/key/{id}', 'updateLanguageJson')->name('update.key');
        Route::get('get-keys', 'getKeys')->name('get.key');
        Route::post('invalidate-keys-cache', 'invalidateKeysCache')->name('invalidate.keys.cache');
    });

    Route::controller('GeneralSettingController')->group(function () {
        // General Setting
        Route::get('general-setting', 'index')->name('setting.index');
        Route::post('general-setting', 'update')->name('setting.update');
        // Admin Management (recruitment, list, roles) - settings/admin
        Route::get('settings/admin', 'adminIndex')->name('setting.admin.index');
        Route::post('settings/admin', 'adminStore')->name('setting.admin.store');
        Route::post('general-setting/admin/store', 'adminStore');
        Route::get('settings/admin/{id}/edit', 'adminEdit')->name('setting.admin.edit')->whereNumber('id');
        Route::post('settings/admin/{id}', 'adminUpdate')->name('setting.admin.update')->whereNumber('id');
        Route::post('general-setting/admin/{id}/role', 'adminRoleUpdate')->name('setting.admin.role')->whereNumber('id')->middleware('admin.reauth:role_change');
        Route::post('settings/admin/{id}/reset-password', 'adminPasswordReset')->name('setting.admin.reset.password')->whereNumber('id');

        // Stock & Order Messages (user + admin messages, restock notify)
        Route::get('setting/stock-order-messages', 'stockOrderMessages')->name('setting.stock.order.messages');
        Route::post('setting/stock-order-messages', 'stockOrderMessagesSubmit')->name('setting.stock.order.messages.submit');

        //configuration
        Route::get('setting/system', 'systemConfiguration')->name('setting.system.configuration');
        Route::post('setting/system', 'systemConfigurationSubmit')->name('setting.system.configuration.submit');
        Route::get('setting/system-configuration', function () {
            return redirect()->route('admin.setting.system.configuration', [], 301); });

        // Logo-Icon
        Route::get('setting/logo-icon', 'logoIcon')->name('setting.logo.icon');
        Route::post('setting/logo-icon', 'logoIconUpdate')->name('setting.logo.icon.update');

        //Custom CSS
        Route::get('custom-css', 'customCss')->name('setting.custom.css');
        Route::post('custom-css', 'customCssSubmit')->name('setting.custom.css.submit');
        Route::post('custom-css/reset', 'customCssReset')->name('setting.custom.css.reset');

        //Cookie
        Route::get('cookie', 'cookie')->name('setting.cookie');
        Route::post('cookie', 'cookieSubmit')->name('setting.cookie.submit');
        Route::post('cookie/custom-message', 'customMessageStore')->name('setting.cookie.custom_message.store');
        Route::post('cookie/custom-message/{id}', 'customMessageUpdate')->name('setting.cookie.custom_message.update')->whereNumber('id');
        Route::post('cookie/custom-message/{id}/delete', 'customMessageDelete')->name('setting.cookie.custom_message.delete')->whereNumber('id');

        //maintenance_mode
        Route::get('maintenance-mode', 'maintenanceMode')->name('maintenance.mode');
        Route::post('maintenance-mode', 'maintenanceModeSubmit')->name('maintenance.mode.submit');

        // Social Login Settings
        Route::get('social-login', 'socialLogin')->name('setting.social.login');
        Route::post('social-login', 'socialLoginUpdate')->name('setting.social.login.update');
    });

    //Notification Setting
    Route::name('setting.notification.')->controller('NotificationController')->prefix('notification')->group(function () {
        //Template Setting
        Route::get('global', 'global')->name('global');
        Route::post('global/update', 'globalUpdate')->name('global.update');
        Route::get('templates', 'templates')->name('templates');
        Route::get('template/edit/{id}', 'templateEdit')->name('template.edit');
        Route::post('template/update/{id}', 'templateUpdate')->name('template.update');

        //Email Setting
        Route::get('email/setting', 'emailSetting')->name('email');
        Route::post('email/setting', 'emailSettingUpdate');
        Route::post('email/test', 'emailTest')->name('email.test');

        //SMS Setting
        Route::get('sms/setting', 'smsSetting')->name('sms');
        Route::post('sms/setting', 'smsSettingUpdate');
        Route::post('sms/test', 'smsTest')->name('sms.test');
    });

    // Plugin / Extensions
    Route::controller('ExtensionController')->prefix('extensions')->name('extensions.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('store', 'store')->name('store');
        Route::post('update/{id}', 'update')->name('update');
        Route::post('status/{id}', 'status')->name('status');
        Route::post('delete/{id}', 'delete')->name('delete');
    });

    //System Information
    Route::controller('SystemController')->name('system.')->prefix('system')->group(function () {
        Route::get('info', 'systemInfo')->name('info');
        Route::get('info/export', 'systemInfoExport')->name('info.export');
        Route::get('server-info', 'systemServerInfo')->name('server.info');
        Route::get('optimize', 'optimize')->name('optimize');
        Route::post('optimize/retention', 'optimizeUpdateRetention')->name('optimize.retention');
        Route::get('optimize-clear', 'optimizeClear')->name('optimize.clear');
        Route::get('optimize-clear-full', 'optimizeClearFull')->name('optimize.clear.full');
        Route::get('optimize-clear-config', 'optimizeClearConfig')->name('optimize.clear.config');
        Route::get('optimize-clear-route', 'optimizeClearRoute')->name('optimize.clear.route');
        Route::get('optimize-clear-view', 'optimizeClearView')->name('optimize.clear.view');
        Route::get('optimize-run', 'optimizeRun')->name('optimize.run');
        Route::get('optimize-cleanup', 'optimizeCleanup')->name('optimize.cleanup');
    });

    // Maintenance Dashboard (Phase 1 & 2) – destructive action via POST only (CSRF-safe)
    Route::controller(\App\Http\Controllers\Admin\MaintenanceDashboardController::class)->prefix('maintenance')->name('maintenance.')->group(function () {
        Route::get('dashboard', 'index')->name('dashboard');
        Route::post('clean-temp-cache', 'cleanTempCache')->name('clean.temp.cache');
    });

    // SEO
    Route::get('seo', 'FrontendController@seoEdit')->name('seo');

    // Location Management (Division / District / Thana / Delivery Zones) — Bangladesh
    Route::prefix('locations')->name('locations.')->controller(\App\Http\Controllers\Admin\LocationController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('divisions/create', 'createDivision')->name('division.create');
        Route::get('divisions/{id}/edit', 'editDivision')->name('division.edit');
        Route::post('divisions', 'storeDivision')->name('division.store');
        Route::put('divisions/{id}', 'updateDivision')->name('division.update');
        Route::delete('divisions/{id}', 'destroyDivision')->name('division.destroy');
        Route::post('divisions/{id}/toggle', 'toggleDivisionStatus')->name('division.toggle');
        Route::get('districts/create', 'createDistrict')->name('district.create');
        Route::get('districts/{id}/edit', 'editDistrict')->name('district.edit');
        Route::post('districts', 'storeDistrict')->name('district.store');
        Route::put('districts/{id}', 'updateDistrict')->name('district.update');
        Route::delete('districts/{id}', 'destroyDistrict')->name('district.destroy');
        Route::post('districts/{id}/toggle', 'toggleDistrictStatus')->name('district.toggle');
        Route::get('thanas/create', 'createThana')->name('thana.create');
        Route::get('thanas/{id}/edit', 'editThana')->name('thana.edit');
        Route::post('thanas', 'storeThana')->name('thana.store');
        Route::put('thanas/{id}', 'updateThana')->name('thana.update');
        Route::delete('thanas/{id}', 'destroyThana')->name('thana.destroy');
        Route::post('thanas/{id}/toggle', 'toggleThanaStatus')->name('thana.toggle');
        Route::get('delivery-zones/create', 'createDeliveryZone')->name('delivery.create');
        Route::get('delivery-zones/{id}/edit', 'editDeliveryZone')->name('delivery.edit');
        Route::post('delivery-zones', 'storeDeliveryZone')->name('delivery.store');
        Route::put('delivery-zones/{id}', 'updateDeliveryZone')->name('delivery.update');
        Route::delete('delivery-zones/{id}', 'destroyDeliveryZone')->name('delivery.destroy');
        Route::post('delivery-zones/{id}/toggle', 'toggleDeliveryZoneStatus')->name('delivery.toggle');
    });

    // UI & Theme Settings – product card, header, footer colors + template selector
    Route::get('ui-settings', [\App\Http\Controllers\Admin\UiSettingsController::class, 'index'])->name('ui.settings');
    Route::post('ui-settings', [\App\Http\Controllers\Admin\UiSettingsController::class, 'update'])->name('ui.settings.update');
    Route::get('theme-settings', function () {
        return redirect()->route('admin.ui.settings', [], 301);
    })->name('theme.settings');

    // Frontend
    Route::name('frontend.')->prefix('frontend')->group(function () {
        Route::get('general', [\App\Http\Controllers\Admin\GeneralSettingController::class, 'frontendGeneral'])->name('sections.general');
        Route::post('general', [\App\Http\Controllers\Admin\GeneralSettingController::class, 'frontendGeneralUpdate'])->name('sections.general.update');
        Route::get('icon', [\App\Http\Controllers\Admin\GeneralSettingController::class, 'frontendIcon'])->name('sections.icon');
        Route::post('icon', [\App\Http\Controllers\Admin\GeneralSettingController::class, 'frontendIconUpdate'])->name('sections.icon.update');

        Route::controller('FrontendController')->group(function () {
            Route::get('templates', 'templates')->name('templates');
            Route::post('templates', 'templatesActive')->name('templates.active');
            Route::get('district', 'district')->name('sections.district');
            Route::post('district', 'districtUpdate')->name('sections.district.update');
            Route::post('template-preview/upload', 'templatePreviewUpload')->name('template.preview.upload');
            Route::post('template-preview/reset', 'templatePreviewReset')->name('template.preview.reset');
            Route::get('template-preview/{name}', 'templatePreview')->name('template.preview');

            // Clean individual routes for each section - add-new MUST be before banner (more specific first)
            Route::match(['get', 'post'], 'banner/add-new', function () {
                return app('App\Http\Controllers\Admin\FrontendController')->addNewBanner(); })->name('sections.banner.addNew');
            Route::get('banner', function () {
                return app('App\Http\Controllers\Admin\FrontendController')->frontendSections('banner'); })->name('sections.banner');
            Route::post('banner/update-field', 'updateBannerField')->name('sections.banner.updateField');
            Route::post('banner/duplicate/{id}', function ($id) {
                return app('App\Http\Controllers\Admin\FrontendController')->bannerDuplicate((int) $id); })->name('sections.banner.duplicate');
            Route::get('banner/preview/{id}', function ($id) {
                return app('App\Http\Controllers\Admin\FrontendController')->bannerPreview((int) $id); })->name('sections.banner.preview');
            Route::post('banner/row-promo/{id}/visibility', [\App\Http\Controllers\Admin\HomepageCustomRowController::class, 'toggleSplitVisibility'])->whereNumber('id')->name('sections.banner.rowPromoVisibility');
            Route::get('contact', function () {
                return app('App\Http\Controllers\Admin\FrontendController')->frontendSections('contact'); })->name('sections.contact');
            Route::get('footer', [\App\Http\Controllers\Admin\FooterBuilderController::class, 'index'])->name('sections.footer');
            Route::get('footer/all', [\App\Http\Controllers\Admin\FooterBuilderController::class, 'fullBuilder'])->name('sections.footer.all');
            Route::get('footer/newsletter-social', function () {
                return redirect()->to(route('admin.frontend.sections.footer.section', 'payment-shipping') . '#payment-methods', 301); });
            Route::get('footer/legal', function () {
                return redirect()->route('admin.frontend.sections.policy', [], 301); });
            Route::get('footer/{section}', [\App\Http\Controllers\Admin\FooterBuilderController::class, 'showSection'])->name('sections.footer.section')->where('section', 'company-info|quick-links|support-center|security-badges|payment-shipping|app-promotion|custom-ads|return-policy|copyright');
            Route::post('footer/builder/section', [\App\Http\Controllers\Admin\FooterBuilderController::class, 'saveSection'])->name('sections.footer.saveSection');
            Route::post('footer/builder/quick-link', [\App\Http\Controllers\Admin\FooterBuilderController::class, 'saveQuickLink'])->name('sections.footer.saveQuickLink');
            Route::post('footer/builder/quick-link/{id}/delete', [\App\Http\Controllers\Admin\FooterBuilderController::class, 'deleteQuickLink'])->name('sections.footer.deleteQuickLink');
            Route::post('footer/builder/security-badge', [\App\Http\Controllers\Admin\FooterBuilderController::class, 'saveSecurityBadge'])->name('sections.footer.saveSecurityBadge');
            Route::post('footer/builder/security-badge/{id}/delete', [\App\Http\Controllers\Admin\FooterBuilderController::class, 'deleteSecurityBadge'])->name('sections.footer.deleteSecurityBadge');
            Route::post('footer/builder/payment-icon', [\App\Http\Controllers\Admin\FooterBuilderController::class, 'savePaymentIcon'])->name('sections.footer.savePaymentIcon');
            Route::post('footer/builder/payment-icon/{id}/delete', [\App\Http\Controllers\Admin\FooterBuilderController::class, 'deletePaymentIcon'])->name('sections.footer.deletePaymentIcon');
            Route::post('footer/builder/custom-ad', [\App\Http\Controllers\Admin\FooterBuilderController::class, 'saveCustomAd'])->name('sections.footer.saveCustomAd');
            Route::post('footer/builder/custom-ad/{id}/delete', [\App\Http\Controllers\Admin\FooterBuilderController::class, 'deleteCustomAd'])->name('sections.footer.deleteCustomAd');
            Route::post('footer/builder/return-policy', [\App\Http\Controllers\Admin\FooterBuilderController::class, 'saveReturnPolicy'])->name('sections.footer.saveReturnPolicy');
            Route::post('footer/builder/app-promotion-item', [\App\Http\Controllers\Admin\FooterBuilderController::class, 'saveAppPromotionItem'])->name('sections.footer.saveAppPromotionItem');
            Route::post('footer/builder/app-promotion-item/{id}/delete', [\App\Http\Controllers\Admin\FooterBuilderController::class, 'deleteAppPromotionItem'])->name('sections.footer.deleteAppPromotionItem');
            Route::get('login', function () {
                return app('App\Http\Controllers\Admin\FrontendController')->frontendSections('login'); })->name('sections.login');
            Route::get('policy', function () {
                return app('App\Http\Controllers\Admin\FrontendController')->frontendSections('policy'); })->name('sections.policy');
            Route::get('register', function () {
                return app('App\Http\Controllers\Admin\FrontendController')->frontendSections('register'); })->name('sections.register');
            Route::get('userprofile', function () {
                return app('App\Http\Controllers\Admin\FrontendController')->userprofilePage();
            })->name('sections.userprofile');
            Route::post('userprofile', function (\Illuminate\Http\Request $request) {
                return app('App\Http\Controllers\Admin\FrontendController')->userprofileSave($request);
            })->name('sections.userprofile.save');
            Route::get('quickorder', [\App\Http\Controllers\Admin\QuickOrderController::class, 'index'])->name('quickorder');
            Route::post('quickorder', [\App\Http\Controllers\Admin\QuickOrderController::class, 'save'])->name('quickorder.save');
            Route::get('service', function () {
                return app('App\Http\Controllers\Admin\FrontendController')->frontendSections('service'); })->name('sections.service');
            Route::get('headericons', [FrontendController::class, 'headerIcons'])->name('sections.headericons');
            Route::get('header', [HeaderControlController::class, 'index'])->name('sections.header.index');
            Route::post('header/draft', [HeaderControlController::class, 'saveDraft'])->name('sections.header.saveDraft');
            Route::post('header/publish', [HeaderControlController::class, 'publish'])->name('sections.header.publish');
            Route::get('header/preview', [HeaderControlController::class, 'preview'])->name('sections.header.preview');
            Route::post('headericons/buttons', [FrontendController::class, 'headerButtonStore'])->name('sections.headericons.buttons.store');
            Route::post('headericons/buttons/{id}/delete', [FrontendController::class, 'headerButtonDelete'])->whereNumber('id')->name('sections.headericons.buttons.delete');
            Route::get('social_icon', function () {
                return app('App\Http\Controllers\Admin\FrontendController')->frontendSections('social_icon'); })->name('sections.social_icon');
            Route::post('social_icon/toggle-public', [FrontendController::class, 'socialIconTogglePublic'])->name('sections.social_icon.toggle_public');
            Route::get('ticker', function () {
                return app('App\Http\Controllers\Admin\FrontendController')->frontendSections('ticker'); })->name('sections.ticker');
            Route::get('scrollbar', [FrontendController::class, 'scrollbarView'])->name('sections.scrollbar');
            Route::get('scrollbar/new', [FrontendController::class, 'scrollbarCreate'])->name('sections.scrollbar.new');
            Route::get('scrollbar/new-custom', [FrontendController::class, 'scrollbarCreateCustom'])->name('sections.scrollbar2.new');
            Route::get('scrollbar/edit/{id}', [FrontendController::class, 'scrollbarEdit'])->whereNumber('id')->name('sections.scrollbar.edit');
            Route::get('scrollbar2/edit/{id}', [FrontendController::class, 'scrollbarEditCustom'])->whereNumber('id')->name('sections.scrollbar2.edit');
            Route::get('scrollbar/edit', [FrontendController::class, 'scrollbarEdit'])->name('sections.scrollbar.edit.query');
            Route::post('scrollbar/save', [FrontendController::class, 'scrollbarSave'])->name('sections.scrollbar.save');
            Route::post('scrollbar/toggle-status/{id}', [FrontendController::class, 'scrollbarToggleStatus'])->whereNumber('id')->name('sections.scrollbar.toggle');
            Route::post('scrollbar/toggle-visibility/{id}', [FrontendController::class, 'scrollbarToggleVisibility'])->whereNumber('id')->name('sections.scrollbar.toggle.visibility');
            Route::get('scrollbar/data/{id}', [FrontendController::class, 'scrollbarData'])->whereNumber('id')->name('sections.scrollbar.data');
            Route::get('scrollbar/preview/{id}', [FrontendController::class, 'scrollbarPreview'])->whereNumber('id')->name('sections.scrollbar.preview');
            Route::get('scrollbar/preview-live', [FrontendController::class, 'scrollbarPreviewLive'])->name('sections.scrollbar.preview.live');
            Route::post('scrollbar/settings', [FrontendController::class, 'scrollbarSettingsSave'])->name('sections.scrollbar.settings');
            Route::get('scrollbar/duplicate/{id}', [FrontendController::class, 'scrollbarDuplicate'])->whereNumber('id')->name('sections.scrollbar.duplicate');
            Route::post('scrollbar/delete/{id}', [FrontendController::class, 'scrollbarDelete'])->whereNumber('id')->name('sections.scrollbar.delete');

            // Homepage Sections (Power Zone, Trust, Flash Sale, Social Proof, etc.)
            Route::get('homepage-sections', [\App\Http\Controllers\Admin\HomepageSectionController::class, 'index'])->name('sections.homepage');
            Route::post('homepage-sections/settings', [\App\Http\Controllers\Admin\HomepageSectionController::class, 'saveSettings'])->name('sections.homepage.saveSettings');
            Route::post('homepage-sections/product-slider-settings', [\App\Http\Controllers\Admin\HomepageSectionController::class, 'saveProductSliderSettings'])->name('sections.homepage.saveProductSliderSettings');
            Route::post('homepage-sections/trust', [\App\Http\Controllers\Admin\HomepageSectionController::class, 'saveTrustElement'])->name('sections.homepage.saveTrust');
            Route::post('homepage-sections/trust/{id}/delete', [\App\Http\Controllers\Admin\HomepageSectionController::class, 'deleteTrustElement'])->whereNumber('id')->name('sections.homepage.deleteTrust');
            Route::post('homepage-sections/quick-service', [\App\Http\Controllers\Admin\HomepageSectionController::class, 'saveQuickService'])->name('sections.homepage.saveQuickService');
            Route::post('homepage-sections/quick-service/{id}/delete', [\App\Http\Controllers\Admin\HomepageSectionController::class, 'deleteQuickService'])->whereNumber('id')->name('sections.homepage.deleteQuickService');
            Route::post('homepage-sections/promo-banner', [\App\Http\Controllers\Admin\HomepageSectionController::class, 'savePromoBanner'])->name('sections.homepage.savePromoBanner');
            Route::post('homepage-sections/promo-banner/{id}/delete', [\App\Http\Controllers\Admin\HomepageSectionController::class, 'deletePromoBanner'])->whereNumber('id')->name('sections.homepage.deletePromoBanner');
            Route::post('homepage-sections/quick-category', [\App\Http\Controllers\Admin\HomepageSectionController::class, 'saveQuickCategory'])->name('sections.homepage.saveQuickCategory');
            Route::post('homepage-sections/quick-category/{id}/delete', [\App\Http\Controllers\Admin\HomepageSectionController::class, 'deleteQuickCategory'])->whereNumber('id')->name('sections.homepage.deleteQuickCategory');

            Route::get('homepage-custom-rows', [\App\Http\Controllers\Admin\HomepageCustomRowController::class, 'index'])->name('sections.homepageCustomRows');
            Route::get('homepage-custom-rows/create', [\App\Http\Controllers\Admin\HomepageCustomRowController::class, 'create'])->name('sections.homepageCustomRows.create');
            Route::post('homepage-custom-rows', [\App\Http\Controllers\Admin\HomepageCustomRowController::class, 'store'])->name('sections.homepageCustomRows.store');
            Route::get('homepage-custom-rows/{id}/edit', [\App\Http\Controllers\Admin\HomepageCustomRowController::class, 'edit'])->whereNumber('id')->name('sections.homepageCustomRows.edit');
            Route::post('homepage-custom-rows/{id}/update', [\App\Http\Controllers\Admin\HomepageCustomRowController::class, 'update'])->whereNumber('id')->name('sections.homepageCustomRows.update');
            Route::post('homepage-custom-rows/{id}/delete', [\App\Http\Controllers\Admin\HomepageCustomRowController::class, 'destroy'])->whereNumber('id')->name('sections.homepageCustomRows.destroy');
            Route::post('homepage-custom-rows/layout', [\App\Http\Controllers\Admin\HomepageCustomRowController::class, 'saveLayout'])->name('sections.homepageCustomRows.saveLayout');

            // Homepage Ads (ad slots between blocks)
            Route::get('homepage-ads', [\App\Http\Controllers\Admin\HomepageAdSlotController::class, 'index'])->name('sections.homepageAds');
            Route::get('homepage-ads/create', [\App\Http\Controllers\Admin\HomepageAdSlotController::class, 'create'])->name('sections.homepageAds.create');
            Route::post('homepage-ads', [\App\Http\Controllers\Admin\HomepageAdSlotController::class, 'store'])->name('sections.homepageAds.store');
            Route::get('homepage-ads/{id}/edit', [\App\Http\Controllers\Admin\HomepageAdSlotController::class, 'edit'])->whereNumber('id')->name('sections.homepageAds.edit');
            Route::post('homepage-ads/{id}/update', [\App\Http\Controllers\Admin\HomepageAdSlotController::class, 'update'])->whereNumber('id')->name('sections.homepageAds.update');
            Route::post('homepage-ads/{id}/toggle-active', [\App\Http\Controllers\Admin\HomepageAdSlotController::class, 'toggleActive'])->whereNumber('id')->name('sections.homepageAds.toggleActive');
            Route::post('homepage-ads/{id}/delete', [\App\Http\Controllers\Admin\HomepageAdSlotController::class, 'destroy'])->whereNumber('id')->name('sections.homepageAds.destroy');

            // Redirect old routes to new clean routes for backward compatibility (301 permanent redirect)
            Route::get('frontend-sections/banner', function () {
                return redirect()->route('admin.frontend.sections.banner', [], 301); });
            Route::get('frontend-sections/contact_us', function () {
                return redirect()->route('admin.frontend.sections.contact', [], 301); });
            Route::get('frontend-sections/footer', function () {
                return redirect()->route('admin.frontend.sections.footer', [], 301); });
            Route::get('frontend-sections/login', function () {
                return redirect()->route('admin.frontend.sections.login', [], 301); });
            Route::get('frontend-sections/policy_pages', function () {
                return redirect()->route('admin.frontend.sections.policy', [], 301); });
            Route::get('frontend-sections/register', function () {
                return redirect()->route('admin.frontend.sections.register', [], 301); });
            Route::get('frontend-sections/service', function () {
                return redirect()->route('admin.frontend.sections.service', [], 301); });
            Route::get('frontend-sections/social_icon', function () {
                return redirect()->route('admin.frontend.sections.social_icon', [], 301); });
            Route::get('frontend-sections/ticker', function () {
                return redirect()->route('admin.frontend.sections.ticker', [], 301); });
            Route::get('frontend-sections/scrollbar', function () {
                return redirect()->route('admin.frontend.sections.scrollbar', [], 301); });
            Route::get('frontend-sections/header_icons', function () {
                return redirect()->route('admin.frontend.sections.header.index', [], 301); });

            // Generic route for other sections (backward compatibility)
            Route::get('frontend-sections/{key}', 'frontendSections')->name('sections');

            // Content update routes
            Route::post('banner/content', function (\Illuminate\Http\Request $request) {
                return app('App\Http\Controllers\Admin\FrontendController')->frontendContent($request, 'banner'); })->name('sections.content.banner');
            Route::post('contact/content', function (\Illuminate\Http\Request $request) {
                return app('App\Http\Controllers\Admin\FrontendController')->frontendContent($request, 'contact'); })->name('sections.content.contact');
            Route::post('footer/content', function (\Illuminate\Http\Request $request) {
                return app('App\Http\Controllers\Admin\FrontendController')->frontendContent($request, 'footer'); })->name('sections.content.footer');
            Route::post('login/content', function (\Illuminate\Http\Request $request) {
                return app('App\Http\Controllers\Admin\FrontendController')->frontendContent($request, 'login'); })->name('sections.content.login');
            Route::post('policy/content', function (\Illuminate\Http\Request $request) {
                return app('App\Http\Controllers\Admin\FrontendController')->frontendContent($request, 'policy'); })->name('sections.content.policy');
            Route::post('register/content', function (\Illuminate\Http\Request $request) {
                return app('App\Http\Controllers\Admin\FrontendController')->frontendContent($request, 'register'); })->name('sections.content.register');
            Route::post('service/content', function (\Illuminate\Http\Request $request) {
                return app('App\Http\Controllers\Admin\FrontendController')->frontendContent($request, 'service'); })->name('sections.content.service');
            Route::post('headericons/content', [FrontendController::class, 'headerIconsSave'])->name('sections.content.headericons');
            Route::post('social_icon/content', function (\Illuminate\Http\Request $request) {
                return app('App\Http\Controllers\Admin\FrontendController')->frontendContent($request, 'social_icon'); })->name('sections.content.social_icon');
            Route::post('ticker/content', function (\Illuminate\Http\Request $request) {
                return app('App\Http\Controllers\Admin\FrontendController')->frontendContent($request, 'ticker'); })->name('sections.content.ticker');

            // Generic content route (backward compatibility)
            Route::post('frontend-content/{key}', 'frontendContent')->name('sections.content');

            // Element routes
            // Banner element route disabled - redirect to main banner page
            Route::get('banner/element/{id?}', function ($id = null) {
                return redirect()->route('admin.frontend.sections.banner', ['edit' => $id])->with('info', 'Banner management is now available on the main banner page.');
            })->name('sections.element.banner');
            Route::get('contact/element/{id?}', function ($id = null) {
                return app('App\Http\Controllers\Admin\FrontendController')->frontendElement('contact', $id); })->name('sections.element.contact');
            Route::get('footer/element/{id?}', function ($id = null) {
                return app('App\Http\Controllers\Admin\FrontendController')->frontendElement('footer', $id); })->name('sections.element.footer');
            Route::get('login/element/{id?}', function ($id = null) {
                return app('App\Http\Controllers\Admin\FrontendController')->frontendElement('login', $id); })->name('sections.element.login');
            Route::get('policy/element/{id?}', function ($id = null) {
                return app('App\Http\Controllers\Admin\FrontendController')->frontendElement('policy', $id); })->name('sections.element.policy');
            Route::get('register/element/{id?}', function ($id = null) {
                return app('App\Http\Controllers\Admin\FrontendController')->frontendElement('register', $id); })->name('sections.element.register');
            Route::get('service/element/{id?}', function ($id = null) {
                return app('App\Http\Controllers\Admin\FrontendController')->frontendElement('service', $id); })->name('sections.element.service');
            Route::get('social_icon/element/{id?}', function ($id = null) {
                return app('App\Http\Controllers\Admin\FrontendController')->frontendElement('social_icon', $id); })->name('sections.element.social_icon');
            Route::get('ticker/element/{id?}', function ($id = null) {
                return app('App\Http\Controllers\Admin\FrontendController')->frontendElement('ticker', $id); })->name('sections.element.ticker');

            // Generic element route (backward compatibility)
            Route::get('frontend-element/{key}/{id?}', 'frontendElement')->name('sections.element');

            Route::post('remove/{id}', 'remove')->name('remove');
            Route::post('banner/update-order', 'bannerUpdateOrder')->name('banner.updateOrder');
        });
    });

    Route::controller(\App\Http\Controllers\Admin\ContactChannelController::class)
        ->prefix('contact-channels')
        ->name('contact.channels.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('run-migration', 'runMigration')->name('run.migration');
            Route::post('store', 'store')->name('store');
            Route::post('{integration}/toggle', 'toggle')->name('toggle');
            Route::post('{integration}/test', 'test')->name('test');
        });
});
