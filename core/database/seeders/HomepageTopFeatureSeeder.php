<?php

namespace Database\Seeders;

use App\Models\HomepageTopFeature;
use Illuminate\Database\Seeder;

class HomepageTopFeatureSeeder extends Seeder
{
    /**
     * Seed the top feature boxes as shown on the homepage (Hot Deals, Top Selling, etc.).
     */
    public function run(): void
    {
        if (HomepageTopFeature::count() > 0) {
            return;
        }

        $baseUrl = rtrim(config('app.url'), '/');
        $features = [
            ['title' => 'Hot Deals', 'redirect_url' => $baseUrl . '/product/hot-deal', 'offer_price' => null, 'sort_order' => 1],
            ['title' => 'Top Selling', 'redirect_url' => $baseUrl . '/product/best-selling', 'offer_price' => null, 'sort_order' => 2],
            ['title' => 'New Arrival', 'redirect_url' => $baseUrl . '/product/new-arrival', 'offer_price' => null, 'sort_order' => 3],
            ['title' => 'Featured', 'redirect_url' => $baseUrl . '/product/featured', 'offer_price' => null, 'sort_order' => 4],
            ['title' => 'Discount', 'redirect_url' => $baseUrl . '/product/discount', 'offer_price' => null, 'sort_order' => 5],
            ['title' => 'Categories', 'redirect_url' => $baseUrl . '/category/all', 'offer_price' => null, 'sort_order' => 6],
            ['title' => 'Win TerSMM', 'redirect_url' => '#', 'offer_price' => null, 'sort_order' => 7],
            ['title' => 'Flash Sale', 'redirect_url' => $baseUrl . '/product/hot-deal', 'offer_price' => null, 'sort_order' => 8],
            ['title' => 'RIAZUL ISLAM S...', 'redirect_url' => '#', 'offer_price' => 100.00, 'sort_order' => 9],
            ['title' => 'Trending', 'redirect_url' => $baseUrl . '/product/best-selling', 'offer_price' => null, 'sort_order' => 10],
            ['title' => 'New Arrivals', 'redirect_url' => $baseUrl . '/product/new-arrival', 'offer_price' => null, 'sort_order' => 11],
            ['title' => 'Affordable Cus...', 'redirect_url' => '#', 'offer_price' => 5700.00, 'sort_order' => 12],
            ['title' => 'Affordable Cus...', 'redirect_url' => '#', 'offer_price' => 4275.00, 'sort_order' => 13],
            ['title' => 'Cricket Jersey...', 'redirect_url' => '#', 'offer_price' => 3894.05, 'sort_order' => 14],
            ['title' => 'Win TerSMM', 'redirect_url' => '#', 'offer_price' => 50.00, 'sort_order' => 15],
            ['title' => 'RIAZUL ISLAM S...', 'redirect_url' => '#', 'offer_price' => 100.00, 'sort_order' => 16],
            ['title' => 'Win TerSMM', 'redirect_url' => '#', 'offer_price' => null, 'sort_order' => 17],
            ['title' => 'Win TerSMM', 'redirect_url' => '#', 'offer_price' => null, 'sort_order' => 18],
        ];

        foreach ($features as $item) {
            HomepageTopFeature::create([
                'title' => $item['title'],
                'redirect_url' => $item['redirect_url'],
                'offer_price' => $item['offer_price'],
                'sort_order' => $item['sort_order'],
                'status' => 1,
                'icon_image' => null,
                'background_style' => null,
                'product_id' => null,
                'category_id' => null,
                'discount_percentage' => null,
                'offer_start' => null,
                'offer_end' => null,
            ]);
        }
    }
}
