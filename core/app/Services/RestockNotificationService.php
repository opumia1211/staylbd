<?php

namespace App\Services;

use App\Events\RestockNotifySocial;
use App\Models\Cart;
use App\Models\NotificationLog;
use App\Models\Product;
use App\Models\ProductComparison;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * When a product goes from out-of-stock (0) to in-stock (>0), notify users who have it in cart, wishlist or compare.
 * In-app (batch insert) + optional WhatsApp/Telegram. Controlled from Admin → Manage Orders → Stock & Order Messages.
 */
class RestockNotificationService
{
    public static function notifyUsersForRestock(Product $product): void
    {
        if (!Schema::hasColumn('general_settings', 'restock_notify_enable')) {
            return;
        }
        $general = gs();
        if (!($general && ($general->restock_notify_enable ?? 1))) {
            return;
        }

        $productId = (int) $product->id;
        $userIds = self::getUserIdsWithProductInCartWishlistCompare($productId);
        if (empty($userIds)) {
            return;
        }

        $productName = $product->name;
        $productUrl = product_detail_url($product);
        $template = $general->restock_message_template ?? null;
        if (trim((string) $template) === '') {
            $template = __('Good news! {product_name} is back in stock. You can order now: {product_url}');
        }
        $message = str_replace(
            ['{product_name}', '{product_url}'],
            [$productName, $productUrl],
            $template
        );
        $subject = \Illuminate\Support\Str::limit($message, 100);
        $sender = $general->site_name ?? 'Store';
        $now = now();

        $rows = [];
        foreach ($userIds as $userId) {
            $rows[] = [
                'user_id' => $userId,
                'notification_type' => 'push',
                'sender' => $sender,
                'sent_from' => null,
                'sent_to' => null,
                'subject' => $subject,
                'message' => $message,
                'click_url' => $productUrl,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        if (!empty($rows)) {
            NotificationLog::insert($rows);
        }

        $sendWhatsApp = Schema::hasColumn('general_settings', 'restock_whatsapp_enable') && ($general->restock_whatsapp_enable ?? 0);
        $sendTelegram = Schema::hasColumn('general_settings', 'restock_telegram_enable') && ($general->restock_telegram_enable ?? 0);
        if (!$sendWhatsApp && !$sendTelegram) {
            return;
        }

        $users = User::whereIn('id', $userIds)
            ->select('id', 'whatsapp_identity', 'telegram_username')
            ->get();
        $waTemplate = $sendWhatsApp ? (trim((string)($general->restock_whatsapp_message ?? '')) ?: __('Hi! {product_name} is back in stock. Order now: {product_url}')) : null;
        $tgTemplate = $sendTelegram ? (trim((string)($general->restock_telegram_message ?? '')) ?: __('Good news! {product_name} is back in stock. You can order now: {product_url}')) : null;

        foreach ($users as $user) {
            if ($sendWhatsApp && !empty(trim((string) $user->whatsapp_identity))) {
                event(new RestockNotifySocial([
                    'channel' => 'whatsapp',
                    'to' => $user->whatsapp_identity,
                    'message' => str_replace(['{product_name}', '{product_url}'], [$productName, $productUrl], $waTemplate),
                ]));
            }
            if ($sendTelegram && !empty(trim((string) $user->telegram_username))) {
                event(new RestockNotifySocial([
                    'channel' => 'telegram',
                    'to' => $user->telegram_username,
                    'message' => str_replace(['{product_name}', '{product_url}'], [$productName, $productUrl], $tgTemplate),
                ]));
            }
        }
    }

    /**
     * Single query to get distinct user_ids for product in cart, wishlist or compare (light, scalable).
     */
    private static function getUserIdsWithProductInCartWishlistCompare(int $productId): array
    {
        $union = "
            SELECT user_id FROM carts WHERE product_id = ? AND user_id IS NOT NULL
            UNION
            SELECT user_id FROM wishlists WHERE product_id = ? AND user_id IS NOT NULL
            UNION
            SELECT user_id FROM product_comparisons WHERE product_id = ? AND user_id IS NOT NULL
        ";
        $ids = DB::select($union, [$productId, $productId, $productId]);
        return array_values(array_unique(array_filter(array_column($ids, 'user_id')));
    }
}
