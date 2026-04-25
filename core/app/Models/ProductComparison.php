<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductComparison extends Model
{
    /** Maximum products allowed in compare list. */
    public const COMPARE_MAX = 200;

    /** Cookie name for guest compare persistence (survives reload when session driver changes). */
    public const GUEST_COOKIE_NAME = 'compare_guest_id';

    /** Cookie lifetime in minutes (30 days). */
    public const GUEST_COOKIE_TTL = 60 * 24 * 30;

    /**
     * Cookie path so compare_guest_id is sent for all app routes (e.g. /staylbd when APP_URL has path).
     * Ensures /user/compare and compare/count receive the same cookie as when adding products.
     */
    public static function getCookiePath(): string
    {
        $url = config('app.url', '');
        if ($url === '') {
            return '/';
        }
        $path = parse_url($url, PHP_URL_PATH);
        $path = ($path !== null && $path !== '') ? rtrim($path, '/') : '';
        return $path === '' ? '/' : $path;
    }

    protected $fillable = [
        'user_id',
        'session_id',
        'product_id'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get stable guest identifier: cookie first (persists across reloads), then session.
     * Ensures compare list survives browser refresh even when SESSION_DRIVER=array or session changes.
     */
    public static function getGuestCompareId(): ?string
    {
        $cookie = request()->cookie(self::GUEST_COOKIE_NAME);
        if ($cookie !== null && $cookie !== '' && strlen($cookie) <= 256) {
            return $cookie;
        }
        $sessionId = session()->getId();
        return ($sessionId !== null && $sessionId !== '') ? $sessionId : null;
    }

    /**
     * Get comparison items for current user/session.
     * For guests, uses cookie-based or session ID so the list persists on reload.
     */
    public static function getItems()
    {
        $query = self::with(['product' => function ($q) {
            $q->with(['category:id,name', 'brand:id,name', 'activeVariants'])
              ->withCount(['reviews' => fn ($r) => $r->visibleOnProduct()]);
        }]);

        if (auth()->check()) {
            $query->where('user_id', auth()->id());
        } else {
            $guestId = self::getGuestCompareId();
            if ($guestId === null) {
                return collect([]);
            }
            $query->where('session_id', $guestId);
        }

        return $query->latest()->get();
    }

    /**
     * Add product to comparison.
     * For guests, uses getGuestCompareId() so the same list is found after reload.
     */
    public static function addProduct($productId)
    {
        $data = [
            'product_id' => $productId,
        ];

        if (auth()->check()) {
            $data['user_id'] = auth()->id();

            $exists = self::where('user_id', auth()->id())
                ->where('product_id', $productId)
                ->exists();
        } else {
            $guestId = self::getGuestCompareId();
            $guestTokenNew = false;
            if ($guestId === null) {
                $guestId = Str::random(40);
                $guestTokenNew = true;
            }
            $data['session_id'] = $guestId;

            $exists = self::where('session_id', $guestId)
                ->where('product_id', $productId)
                ->exists();
        }

        if ($exists) {
            return false;
        }

        $created = DB::transaction(function () use ($data) {
            $query = auth()->check()
                ? self::where('user_id', auth()->id())
                : self::where('session_id', $data['session_id']);
            if ($query->lockForUpdate()->count() >= self::COMPARE_MAX) {
                return null;
            }
            return self::create($data);
        });

        if ($created === null) {
            return false;
        }
        if (!auth()->check() && !empty($guestTokenNew)) {
            return ['result' => $created, 'guest_token' => $data['session_id']];
        }
        return $created;
    }

    /**
     * Remove product from comparison
     */
    public static function removeProduct($productId)
    {
        $query = self::where('product_id', $productId);

        if (auth()->check()) {
            $query->where('user_id', auth()->id());
        } else {
            $guestId = self::getGuestCompareId();
            if ($guestId !== null) {
                $query->where('session_id', $guestId);
            } else {
                return 0;
            }
        }

        return $query->delete();
    }

    /**
     * Clear all comparison items
     */
    public static function clearAll()
    {
        $query = self::query();

        if (auth()->check()) {
            $query->where('user_id', auth()->id());
        } else {
            $guestId = self::getGuestCompareId();
            if ($guestId !== null) {
                $query->where('session_id', $guestId);
            } else {
                return 0;
            }
        }

        return $query->delete();
    }

    /**
     * Get comparison count (same source as getItems for consistent real-time count).
     */
    public static function getCount()
    {
        $query = self::query();

        if (auth()->check()) {
            $query->where('user_id', auth()->id());
        } else {
            $guestId = self::getGuestCompareId();
            if ($guestId === null) {
                return 0;
            }
            $query->where('session_id', $guestId);
        }

        return $query->count();
    }

    /**
     * Get product IDs in compare list (lightweight for header badge + button state).
     */
    public static function getCompareProductIds(): array
    {
        $query = self::query()->select('product_id');

        if (auth()->check()) {
            $query->where('user_id', auth()->id());
        } else {
            $guestId = self::getGuestCompareId();
            if ($guestId === null) {
                return [];
            }
            $query->where('session_id', $guestId);
        }

        return $query->pluck('product_id')->map(function ($id) {
            return (int) $id;
        })->values()->all();
    }

    /**
     * Move guest/session comparison items to a logged-in user after login.
     *
     * This keeps products that were added to compare before login and
     * attaches them to the user's profile, respecting the 4-item limit.
     *
     * Optionally accepts a specific $oldSessionId, but if not provided it
     * will first look for a session-stored `compare_old_session_id` (set
     * before login) and fall back to the current session id.
     */
    public static function migrateSessionToUser(int $userId, ?string $oldSessionId = null): void
    {
        $sessionId = $oldSessionId
            ?? session()->pull('compare_old_session_id')
            ?? session()->getId();

        if (!$sessionId) {
            return;
        }

        /** @var \Illuminate\Support\Collection<int,self> $sessionItems */
        $sessionItems = self::whereNull('user_id')
            ->where('session_id', $sessionId)
            ->get();

        if ($sessionItems->isEmpty()) {
            return;
        }

        $existingIds = self::where('user_id', $userId)
            ->pluck('product_id')
            ->map(function ($id) {
                return (int) $id;
            })
            ->all();

        foreach ($sessionItems as $item) {
            $productId = (int) $item->product_id;

            // Already in user's comparison list – just drop the session row.
            if (in_array($productId, $existingIds, true)) {
                $item->delete();
                continue;
            }

            // Enforce the compare limit per user.
            if (self::where('user_id', $userId)->count() >= self::COMPARE_MAX) {
                $item->delete();
                continue;
            }

            $item->user_id = $userId;
            $item->session_id = null;
            $item->save();

            $existingIds[] = $productId;
        }
    }
}
