<?php

namespace App\Services;

use Illuminate\Support\Str;

/**
 * Advanced search: typo tolerance, multi-language keywords, transliteration, query normalization.
 * Lightweight, no external APIs.
 */
class UniversalSearchService
{
    /** Roman to Bengali (and common English) for search matching */
    protected static array $transliterationMap = [
        'product' => 'প্রোডাক্ট পণ্য',
        'products' => 'প্রোডাক্ট পণ্য',
        'order' => 'অর্ডার',
        'orders' => 'অর্ডার',
        'cart' => 'কার্ট',
        'login' => 'লগইন',
        'register' => 'রেজিস্ট্রেশন',
        'contact' => 'যোগাযোগ',
        'track' => 'ট্র্যাক',
        'home' => 'হোম',
        'wishlist' => 'উইশলিস্ট',
        'category' => 'ক্যাটাগরি',
        'categories' => 'ক্যাটাগরি',
        'brand' => 'ব্র্যান্ড',
        'brands' => 'ব্র্যান্ড',
        'payment' => 'পেমেন্ট',
        'checkout' => 'চেকআউট',
        'profile' => 'প্রোফাইল',
        'dashboard' => 'ড্যাশবোর্ড',
        'subscribe' => 'সাবস্ক্রাইব',
        'newsletter' => 'নিউজলেটার',
    ];

    /** Common typos / variations (typo => correct) for wrong-search results */
    protected static array $typoVariations = [
        'ordr' => 'order', 'ordar' => 'order', 'oder' => 'order',
        'prodact' => 'product', 'prodak' => 'product', 'produc' => 'product', 'produkt' => 'product', 'prodcut' => 'product',
        'login' => 'login', 'log in' => 'login', 'signin' => 'login', 'logi' => 'login',
        'registar' => 'register', 'registr' => 'register', 'regster' => 'register', 'signup' => 'register',
        'contct' => 'contact', 'contac' => 'contact', 'contactus' => 'contact',
        'trak' => 'track', 'traking' => 'track', 'tracking' => 'track', 'trck' => 'track',
        'wishlist' => 'wishlist', 'wish list' => 'wishlist', 'wishlst' => 'wishlist',
        'chekout' => 'checkout', 'check out' => 'checkout', 'checkot' => 'checkout',
        'categry' => 'category', 'catagory' => 'category', 'categery' => 'category',
        'profil' => 'profile', 'profle' => 'profile',
        'paymnt' => 'payment', 'paymet' => 'payment',
        'cart' => 'cart', 'basket' => 'cart', 'karat' => 'cart',
        'featur' => 'feature', 'featues' => 'feature', 'feture' => 'feature',
        'shiping' => 'shipping', 'shippng' => 'shipping', 'delivery' => 'shipping',
    ];

    /**
     * Normalize query: trim, collapse spaces, optional lowercase for comparison.
     */
    public function normalizeQuery(string $query): string
    {
        $q = trim(preg_replace('/\s+/', ' ', $query));
        return $q;
    }

    /**
     * Expand query with typo corrections and transliteration synonyms.
     * Returns list of terms to search (original + alternates).
     */
    public function expandQueryTerms(string $query): array
    {
        $normalized = $this->normalizeQuery($query);
        if ($normalized === '') {
            return [];
        }

        $terms = array_unique(array_filter(array_merge(
            [$normalized],
            $this->getTypoAlternates($normalized),
            $this->getTransliterationTerms($normalized)
        )));

        return array_values($terms);
    }

    /**
     * Get typo-corrected or variant terms for a single word.
     */
    protected function getTypoAlternates(string $word): array
    {
        $lower = mb_strtolower($word);
        $out = [];
        if (isset(self::$typoVariations[$lower])) {
            $out[] = self::$typoVariations[$lower];
        }
        foreach (self::$typoVariations as $typo => $correct) {
            if (Str::contains($lower, $typo) || Str::contains($typo, $lower)) {
                $out[] = $correct;
            }
        }
        return array_unique($out);
    }

    /**
     * Get transliteration-expanded terms (e.g. "product" -> add Bengali equivalents for matching).
     */
    protected function getTransliterationTerms(string $query): array
    {
        $words = preg_split('/\s+/', mb_strtolower(trim($query)), -1, PREG_SPLIT_NO_EMPTY);
        $out = [];
        foreach ($words as $w) {
            if (isset(self::$transliterationMap[$w])) {
                $out = array_merge($out, explode(' ', self::$transliterationMap[$w]));
            }
        }
        return array_unique(array_filter($out));
    }

    /**
     * Build LIKE-safe search terms: original + expanded, each word, 1+ char.
     */
    public function allSearchTerms(string $query): array
    {
        $normalized = $this->normalizeQuery($query);
        $expanded = $this->expandQueryTerms($query);
        $all = array_unique(array_merge([$normalized], $expanded));
        $terms = [];
        foreach ($all as $t) {
            $t = trim($t);
            if ($t !== '') {
                $terms[] = $t;
            }
            foreach (preg_split('/\s+/', $t, -1, PREG_SPLIT_NO_EMPTY) as $word) {
                if (mb_strlen($word) >= 1) {
                    $terms[] = $word;
                }
            }
        }
        return array_values(array_unique(array_filter($terms)));
    }

    /**
     * Escape LIKE special chars: % _ \ (for safe binding).
     */
    public function escapeLike(string $value): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $value);
    }

    /**
     * For MySQL: wrap in % for LIKE.
     */
    public function likeWrap(string $value): string
    {
        return '%' . $this->escapeLike($value) . '%';
    }
}
