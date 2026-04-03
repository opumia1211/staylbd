<?php

use Illuminate\Support\Facades\Route;

Route::namespace('User\Auth')->name('user.')->group(function () {

    Route::controller('LoginController')->group(function () {
        Route::get('/login', 'showLoginForm')->name('login');
        Route::post('/login', 'login')->middleware('throttle:login');
        Route::match(['get', 'post'], 'logout', 'logout')->name('logout');
        // Social Login
        Route::get('social-login/redirect/{provider}', 'redirectToProvider')->name('social.login');
        Route::get('social-login/{provider}/callback', 'handleProviderCallback')->name('social.callback');
    });

    Route::controller('RegisterController')->group(function () {
        Route::get('register', 'showRegistrationForm')->name('register');
        Route::post('register', 'register')->middleware(['registration.status', 'throttle:register']);
        Route::post('check-mail', 'checkUser')->name('checkUser');
    });

    Route::controller('ForgotPasswordController')->prefix('password')->name('password.')->group(function () {
        Route::get('reset', 'showLinkRequestForm')->name('request');
        Route::post('email', 'sendResetCodeEmail')->name('email')->middleware('throttle:password-reset');
        Route::get('code-verify', 'codeVerify')->name('code.verify');
        Route::post('verify-code', 'verifyCode')->name('verify.code')->middleware('throttle:password-reset');
    });

    Route::controller('ResetPasswordController')->group(function () {
        Route::post('password/reset', 'reset')->name('password.update');
        Route::get('password/reset/{token}', 'showResetForm')->name('password.reset');
    });
});

// User area: /user → guest: cart page, logged-in: dashboard
// Cart, Wishlist, Compare – accessible without login (guest + logged-in)
Route::name('user.')->group(function () {
    Route::get('/', function () {
        return auth()->check() ? redirect()->route('user.home') : redirect()->route('user.cart');
    })->name('index');
    Route::get('cart', [\App\Http\Controllers\CartController::class, 'cartProductsDashboard'])->name('cart');
    Route::get('guest-order', [\App\Http\Controllers\GuestCheckoutController::class, 'showOrderPage'])->name('guest.order');
    Route::get('cart/quickorder', function () {
        return redirect()->route('user.guest.order', [], 302);
    })->name('cart.quickorder');
    Route::get('wishlist', [\App\Http\Controllers\WishController::class, 'wishListProductDashboard'])->name('wishlist');
    Route::get('compare', [\App\Http\Controllers\ProductComparisonController::class, 'indexDashboard'])->name('compare');
});

Route::middleware('auth')->name('user.')->group(function () {
    //authorization
    Route::namespace('User')->controller('AuthorizationController')->group(function () {
        Route::get('authorization', 'authorizeForm')->name('authorization');
        Route::get('resend-verify/{type}', 'sendVerifyCode')->name('send.verify.code');
        Route::post('verify-email', 'emailVerification')->name('verify.email');
        Route::post('verify-mobile', 'mobileVerification')->name('verify.mobile');
        Route::post('verify-g2fa', 'g2faVerification')->name('go2fa.verify');
    });

    Route::middleware(['check.status'])->group(function () {
        Route::get('user-data', 'User\UserController@userData')->name('data');
        Route::post('user-data-submit', 'User\UserController@userDataSubmit')->name('data.submit');

        Route::middleware('registration.complete')->namespace('User')->group(function () {
            Route::controller('UserController')->group(function () {
                Route::get('dashboard', 'home')->name('home');
                Route::get('notifications', 'notifications')->name('notifications');
                Route::get('notification/read/{id}', 'notificationRead')->name('notification.read')->whereNumber('id');
                Route::post('notifications/read-all', 'notificationReadAll')->name('notifications.read.all');
                Route::post('notifications/clear-all', 'notificationClearAll')->name('notifications.clear.all');

                //Report
                Route::get('payments', 'transactions')->name('transactions');
                Route::get('attachment-download/{fil_hash}', 'attachmentDownload')->name('attachment.download');
            });

            Route::get('track-order', [\App\Http\Controllers\SiteController::class, 'trackOrderDashboard'])->name('track.order');

            //Profile setting
            Route::controller('ProfileController')->group(function () {
                Route::get('profile-setting', 'profile')->name('profile.setting');
                Route::post('profile-setting', 'submitProfile');
                Route::post('saved-address', 'storeSavedAddress')->name('saved.address.store');
                Route::post('saved-address/{id}', 'updateSavedAddress')->name('saved.address.update');
                Route::post('saved-address/{id}/delete', 'destroySavedAddress')->name('saved.address.destroy');
                Route::post('saved-address/{id}/default', 'setDefaultSavedAddress')->name('saved.address.default');
                Route::get('change-password', 'changePassword')->name('change.password');
                Route::post('change-password', 'submitPassword');
            });

            Route::controller('CheckoutController')->prefix('checkout')->name('checkout.')->group(function () {
                Route::get('/', 'checkout')->name('index');
                Route::get('shipping-options', 'shippingOptions')->name('shipping.options');
                Route::post('order', 'order')->middleware('throttle:checkout')->name('order');
            });

            Route::controller('OrderController')->prefix('order')->name('order.')->group(function () {
                Route::get('/', 'order')->name('index');
                Route::get('details/{id}', 'details')->name('detail');
            });

            Route::controller('ReviewController')->prefix('review')->name('review.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('create/{slug}/{id}', 'create')->name('create');
                Route::post('store/{id}', 'store')->name('store')->middleware('throttle:review');
                Route::put('update/{id}', 'update')->name('update');
                Route::delete('destroy/{id}', 'destroy')->name('destroy');
            });
        });

        // Payment
        Route::middleware('registration.complete')->prefix('deposit')->name('deposit.')->controller('Gateway\PaymentController')->group(function () {
            Route::get('confirm', 'depositConfirm')->name('confirm');
            Route::get('autopay-return', 'autopayReturn')->name('autopay.return');
            Route::get('manual', 'manualDepositConfirm')->name('manual.confirm');
            Route::post('manual', 'manualDepositUpdate')->name('manual.update');
            Route::get('insert/{orderId}', 'deposit')->name('insert.get');
            Route::post('insert/{orderId}', 'depositInsert')->name('insert');
            Route::get('{orderId}', 'deposit')->name('index');
        });
    });
});
