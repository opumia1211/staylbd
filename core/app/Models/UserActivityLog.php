<?php

namespace App\Models;

use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;

class UserActivityLog extends Model
{
    use Searchable;

    protected $table = 'user_activity_logs';

    protected $fillable = [
        'user_id', 'session_id', 'action_type', 'description',
        'model_type', 'model_id', 'ip_address', 'device', 'browser', 'os',
        'country', 'city', 'latitude', 'longitude', 'url',
    ];

    protected $casts = [
        'model_id' => 'integer',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    /** Action type constants for filtering */
    const SEARCH_TEXT = 'search_text';
    const SEARCH_VOICE = 'search_voice';
    const SEARCH_IMAGE = 'search_image';
    const PRODUCT_VIEW = 'product_view';
    const CART_ADD = 'cart_add';
    const CART_REMOVE = 'cart_remove';
    const WISHLIST_ADD = 'wishlist_add';
    const WISHLIST_REMOVE = 'wishlist_remove';
    const COMPARE_ADD = 'compare_add';
    const COMPARE_REMOVE = 'compare_remove';
    const TRACK_ORDER = 'track_order';
    const ORDER_PLACE = 'order_place';
    const ORDER_CANCEL = 'order_cancel';
    const CONTACT_SUBMIT = 'contact_submit';
    const LIVE_CHAT = 'live_chat';
    const LOGIN = 'login';
    const LOGOUT = 'logout';
    const LOGIN_FAILED = 'login_failed';
    const REGISTRATION = 'registration';
    const PROFILE_UPDATE = 'profile_update';
    const PASSWORD_CHANGE = 'password_change';
    const PAYMENT_ATTEMPT = 'payment_attempt';
    const PAYMENT_SUCCESS = 'payment_success';
    const PAYMENT_FAILURE = 'payment_failure';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getModelClassAttribute()
    {
        if (!$this->model_type) {
            return null;
        }
        $map = [
            'product' => \App\Models\Product::class,
            'order' => \App\Models\Order::class,
            'deposit' => \App\Models\Deposit::class,
        ];
        return $map[$this->model_type] ?? null;
    }

    public static function actionTypeLabels(): array
    {
        return [
            self::SEARCH_TEXT => __('Search (Text)'),
            self::SEARCH_VOICE => __('Search (Voice)'),
            self::SEARCH_IMAGE => __('Search (Image)'),
            self::PRODUCT_VIEW => __('Product View'),
            self::CART_ADD => __('Cart Add'),
            self::CART_REMOVE => __('Cart Remove'),
            self::WISHLIST_ADD => __('Wishlist Add'),
            self::WISHLIST_REMOVE => __('Wishlist Remove'),
            self::COMPARE_ADD => __('Compare Add'),
            self::COMPARE_REMOVE => __('Compare Remove'),
            self::TRACK_ORDER => __('Track Order'),
            self::ORDER_PLACE => __('Order Place'),
            self::ORDER_CANCEL => __('Order Cancel'),
            self::CONTACT_SUBMIT => __('Contact Submit'),
            self::LIVE_CHAT => __('Live Chat'),
            self::LOGIN => __('Login'),
            self::LOGOUT => __('Logout'),
            self::LOGIN_FAILED => __('Login Failed'),
            self::REGISTRATION => __('Registration'),
            self::PROFILE_UPDATE => __('Profile Update'),
            self::PASSWORD_CHANGE => __('Password Change'),
            self::PAYMENT_ATTEMPT => __('Payment Attempt'),
            self::PAYMENT_SUCCESS => __('Payment Success'),
            self::PAYMENT_FAILURE => __('Payment Failure'),
        ];
    }
}
