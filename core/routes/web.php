<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactChannelWebhookController;
use App\Http\Controllers\Api\AutopayIncomingController;
use App\Http\Controllers\ServeAssetController;

// Serve template JS/CSS with correct MIME (fixes 404/MIME when static assets hit Laravel)
Route::get('serve-js/{name}', [ServeAssetController::class, 'js'])->name('serve.js')->where('name', 'fly-to-header|product-carousel|glass-header|storefront-lucide|auth');
Route::get('serve-css/global/{name}', [ServeAssetController::class, 'cssGlobal'])->name('serve.css.global');
Route::get('serve-css/img/{name}', [ServeAssetController::class, 'imageTemplate'])->name('serve.css.img');
Route::get('serve-css/images/{name}', [ServeAssetController::class, 'cssBundleImages'])->name('serve.css.bundle-images');
Route::get('serve-css/webfonts/{name}', [ServeAssetController::class, 'webfonts'])->name('serve.css.webfonts')->where('name', '.*');
Route::get('serve-css/fonts/{name}', [ServeAssetController::class, 'fonts'])->name('serve.css.fonts')->where('name', '.*');
Route::get('serve-css/tailwind-utilities', [ServeAssetController::class, 'tailwindUtilities'])->name('serve.css.tailwind.utilities');
Route::get('serve-css/tailwind-homepage', [ServeAssetController::class, 'tailwindHomepage'])->name('serve.css.tailwind.homepage');
Route::get('serve-css/tailwind-product', [ServeAssetController::class, 'tailwindProduct'])->name('serve.css.tailwind.product');
Route::get('serve-css/tailwind-storefront', [ServeAssetController::class, 'tailwindStorefront'])->name('serve.css.tailwind.storefront');
Route::get('serve-css/tailwind-storefront-deferred', [ServeAssetController::class, 'tailwindStorefrontDeferred'])->name('serve.css.tailwind.storefront.deferred');
Route::get('serve-css/tailwind-storefront-deferred-cart', [ServeAssetController::class, 'tailwindStorefrontDeferredCart'])->name('serve.css.tailwind.storefront.deferred.cart');
Route::get('serve-css/tailwind-storefront-deferred-account', [ServeAssetController::class, 'tailwindStorefrontDeferredAccount'])->name('serve.css.tailwind.storefront.deferred.account');
Route::get('serve-css/tailwind-storefront-deferred-compare', [ServeAssetController::class, 'tailwindStorefrontDeferredCompare'])->name('serve.css.tailwind.storefront.deferred.compare');
Route::get('serve-css/tailwind-storefront-deferred-home', [ServeAssetController::class, 'tailwindStorefrontDeferredHome'])->name('serve.css.tailwind.storefront.deferred.home');
Route::get('serve-css/critical-storefront', [ServeAssetController::class, 'criticalStorefront'])->name('serve.css.critical.storefront');
Route::get('serve-css/tailwind-admin', [ServeAssetController::class, 'tailwindAdmin'])->name('serve.css.tailwind.admin');
Route::get('serve-css/admin-panel', [ServeAssetController::class, 'adminPanel'])->name('serve.css.admin.panel');
Route::get('serve-css/{name}', [ServeAssetController::class, 'css'])->name('serve.css');


// Autopay: app/SMS message bridge (Android etc. send payment confirmation here)
Route::post('api/autopay/incoming-message', [AutopayIncomingController::class, 'incomingMessage'])->name('api.autopay.incoming')->middleware('throttle:60,1');

// Order delivery QR scan (customer scans invoice QR → admin gets notification)
Route::get('order/delivery-scanned/{token}', [App\Http\Controllers\OrderDeliveryScanController::class, 'scanned'])->name('order.delivery.scanned');
// Delivery man scans QR → admin + user notified, redirect to Google Maps
Route::get('order/delivery-driver-scanned/{token}', [App\Http\Controllers\OrderDeliveryScanController::class, 'driverScanned'])->name('order.delivery.driver.scanned');

// Cache clear route moved to admin panel (auth + SuperAdmin only) – see admin.php


// User Support Ticket (primary URL: /message; /ticket redirects to /message)
Route::controller('TicketController')->prefix('message')->name('message.')->group(function () {
    Route::get('/', 'supportTicket')->name('index');
    Route::get('new', 'openSupportTicket')->name('open');
    Route::post('create', 'storeSupportTicket')->name('store');
    Route::get('view/{ticket}', 'viewTicket')->name('view');
    Route::post('reply/{ticket}', 'replyTicket')->name('reply');
    Route::post('close/{ticket}', 'closeTicket')->name('close');
    Route::get('download/{ticket}', 'ticketDownload')->name('download');
});
Route::get('ticket', function () { return redirect()->route('message.index', [], 301); })->name('ticket.index');
Route::redirect('ticket/new', 'message/new', 301);
Route::get('ticket/view/{ticket}', function ($ticket) { return redirect()->route('message.view', $ticket, 301); })->name('ticket.view');
Route::get('ticket/open', function () { return redirect()->route('message.open', [], 301); })->name('ticket.open');
Route::post('ticket/create', [\App\Http\Controllers\TicketController::class, 'storeSupportTicket'])->name('ticket.store');
Route::post('ticket/reply/{ticket}', [\App\Http\Controllers\TicketController::class, 'replyTicket'])->name('ticket.reply');
Route::post('ticket/close/{ticket}', [\App\Http\Controllers\TicketController::class, 'closeTicket'])->name('ticket.close');
Route::get('ticket/download/{ticket}', [\App\Http\Controllers\TicketController::class, 'ticketDownload'])->name('ticket.download');
Route::get('ticket/', function () { return redirect('/message', 301); });

Route::get('realtime/product/{id}', [\App\Http\Controllers\Storefront\RealtimePollController::class, 'product'])
    ->name('storefront.realtime.product')
    ->whereNumber('id')
    ->middleware('throttle:90,1');

Route::controller('SiteController')->group(function () {
    // Home/root – GET + HEAD explicitly so "GET method not supported" never appears (even with route cache)
    Route::match(['GET', 'HEAD'], '/', 'index')->name('home');
    Route::get('home-below-fold', 'homeBelowFoldFragment')->name('home.below.fold')->middleware('throttle:90,1');
    Route::get('home-section-products', 'homeSectionProducts')->name('home.section.products');

    // Digital File Download
    Route::get('digital/file/download/{id}/{fileName}', 'download')->name('download');

    Route::get('/contact', 'contact')->name('contact')->middleware('auth');
    Route::get('/contactlive', 'contactLive')->name('contact.live');
    Route::post('/contact', 'contactSubmit')->middleware('auth');
    Route::post('/contact/panel', 'contactPanelSubmit')->name('contact.panel.submit')->middleware('throttle:10,1');
    Route::get('/contact/chat-messages', 'getChatMessages')->name('contact.chat.messages')->middleware('auth');
    Route::get('/contact/chat-unread-count', 'getChatUnreadCount')->name('contact.chat.unread')->middleware('auth');
    Route::post('/contact/channel-redirect', 'getContactChannelRedirect')->name('contact.channel.redirect')->middleware('throttle:15,1');
    Route::get('subscribe', 'subscribePage')->name('subscribe.page');
    Route::post('subscribe', 'subscribe')->name('subscribe')->middleware('throttle:10,1');
    Route::post('footer/return-request', 'submitReturnRequest')->name('footer.return.submit')->middleware('throttle:5,1');
    Route::get('seller/apply', [\App\Http\Controllers\SellerApplyController::class, 'show'])->name('seller.apply');
    Route::get('/change/{lang?}', 'changeLanguage')->name('lang');

    Route::get('cookie-policy', 'cookiePolicy')->name('cookie.policy');
    Route::get('/cookie/accept', 'cookieAccept')->name('cookie.accept');
    Route::get('/cookie/decline', 'cookieDecline')->name('cookie.decline');
    Route::get('/cookie/revoke', 'cookieRevoke')->name('cookie.revoke');
    Route::get('policy/{id}', 'policyPageShort')->name('policy.pages.short')->whereNumber('id');
    Route::get('policy/{slug}/{id}', 'policyPages')->name('policy.pages')->whereNumber('id');
    Route::get('placeholder-image/{size}', 'placeholderImage')->name('placeholder.image');

    Route::get('category/product/{slug}/{id}', 'categoryProduct')->name('category.products');
    Route::get('brand/product/{slug}/{id}', 'brandProduct')->name('brand.products');
    Route::get('brand/all', 'allBrand')->name('brand.all');
    Route::get('subcategory/product/{slug}/{id}', 'subCategoryProduct')->name('subcategory.products');
    Route::get('all/products', 'products')->name('products');
    Route::get('product/quick-view', 'quickView')->name('product.quickView');

    Route::get('reviews/{id}', 'fetchReviews')->name('fetch.reviews');
    Route::post('review/helpful/{id}', 'reviewHelpful')->name('review.helpful')->middleware('throttle:30,1')->whereNumber('id');
    Route::get('product/hot-deal', 'hotDeal')->name('product.hot.deal');
    Route::get('product/featured', 'featured')->name('products.featured');
    Route::get('product/today-deal', 'todayDeal')->name('product.today.deal');
    Route::get('product/best-selling', 'bestSelling')->name('products.best.selling');
    Route::get('product/new-arrival', 'newArrival')->name('products.new');
    Route::get('product/discount', 'discount')->name('products.discount');
    Route::get('category/all', 'categoryAll')->name('category.all');
    Route::get('account/guest-menu', 'guestAccountMenu')->name('guest.account.menu');

    Route::get('product/filter', 'filterProduct')->name('all.products.filter')->middleware('throttle:product_filter');

    Route::get('product/details/{slug}/{id}', function ($slug, $id) {
        $product = \App\Models\Product::query()->findOrFail((int) $id);
        $canonical = trim((string) ($product->slug ?? ''));
        if ($canonical === '' || !preg_match('/-\d+$/', $canonical)) {
            $canonical = \App\Models\Product::buildShortSlugForProduct($product);
            $product->slug = $canonical;
            $product->saveQuietly();
        }

        return redirect()->to(route('product.detail', $canonical), 301);
    })->whereNumber('id');

    Route::get('product/{slug}', 'productDetailsBySlug')->name('product.detail')
        ->where('slug', '[a-zA-Z0-9][a-zA-Z0-9\-]*');

    //Track Order
    Route::get('track/order', 'trackOrder')->name('track.order');
    Route::post('get-track/order', 'getTrackOrder')->name('get.track.order');

    // Banner analytics (impression/click tracking)
    Route::post('banner/analytics', 'recordBannerAnalytics')->name('banner.analytics');
    // Serve banner image from project root or public (works regardless of server static path)
    Route::get('banner-image/{filename}', 'serveBannerImage')->name('banner.image')->where('filename', '[a-zA-Z0-9_.-]+');
    Route::get('row-split-banner/{filename}', 'serveRowSplitBanner')->name('row.split.image')->where('filename', '[a-zA-Z0-9_.-]+');
});

Route::prefix('webhooks/contact')->group(function () {
    Route::match(['GET', 'POST'], 'whatsapp', [ContactChannelWebhookController::class, 'whatsapp'])->name('webhook.contact.whatsapp');
    Route::post('telegram', [ContactChannelWebhookController::class, 'telegram'])->name('webhook.contact.telegram');
});

Route::controller('WishController')->prefix('wish-list')->name('wish.list.')->group(function () {
    Route::post('add', 'addWishList')->name('add');
    Route::post('restore-guest', 'restoreGuestWishlist')->name('restore.guest');
    Route::get('count', 'wishListCount')->name('count');
    Route::get('remove', 'removeWishList')->name('remove');
    Route::post('clear', 'clearWishlist')->name('clear');
});
Route::get('wish-list/product', function () { return redirect()->route('user.wishlist', [], 301); })->name('wish.list.product');

Route::controller('CartController')->prefix('cart-list')->name('cart.list.')->group(function () {
    Route::post('add', 'addToCart')->name('add');
    Route::post('restore-guest', 'restoreGuestCart')->name('restore.guest');
    Route::get('buy-now/{id}', 'buyNow')->name('buy.now')->whereNumber('id');
    Route::get('count', 'getCartCount')->name('count');
    Route::post('remove', 'removeCart')->name('remove');
    Route::post('clear', 'clearCart')->name('clear');
    Route::post('update', 'updateCart')->name('update');
    Route::post('coupon-apply', 'couponApply')->name('apply.coupon');
    Route::post('set-checkout-selection', 'setCheckoutSelection')->name('set.checkout.selection')->middleware('auth');
});
Route::get('cart-list/product', function () { return redirect()->route('user.cart', [], 301); })->name('cart.list.product');

// Guest checkout (quick order without login)
Route::controller(\App\Http\Controllers\GuestCheckoutController::class)->prefix('guest-checkout')->name('guest.checkout.')->middleware('throttle:20,1')->group(function () {
    Route::get('location-data', 'locationData')->name('location.data');
    Route::post('order', 'submit')->name('order');
});

// Product Comparison – page moved to /user/compare; API routes kept
Route::get('compare', function () { return redirect()->route('user.compare', [], 301); })->name('compare.index');
Route::controller('ProductComparisonController')->prefix('compare')->name('compare.')->group(function () {
    Route::post('add', 'add')->name('add');
    Route::post('remove', 'remove')->name('remove');
    Route::post('remove-bulk', 'removeBulk')->name('remove.bulk');
    Route::post('clear', 'clear')->name('clear');
    Route::get('count', 'count')->name('count');
    Route::get('data', 'getData')->name('data');
});

// Universal Search Routes (GET for live suggestions; POST for form fallback)
Route::controller('SearchController')->prefix('search')->name('search.')->group(function () {
    Route::get('trending-keywords', 'trendingKeywords')->name('trending');
    Route::match(['get', 'post'], 'universal', 'universalSearch')->name('universal');
    Route::post('voice', 'voiceSearch')->name('voice');
    Route::post('image', 'imageSearch')->name('image');
});

// Abandoned cart recovery (public link from email/SMS)
Route::get('recover-cart/{token}', [App\Http\Controllers\RecoverCartController::class, 'recover'])->name('recover.cart')->where('token', '[a-zA-Z0-9]+');

// SEO: sitemap & robots (lightweight, cached – for top search ranking)
Route::get('sitemap.xml', [App\Http\Controllers\SeoController::class, 'sitemap'])->name('sitemap');
Route::get('robots.txt', [App\Http\Controllers\SeoController::class, 'robots'])->name('robots');
