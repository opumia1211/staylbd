<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Admin AI Assistant: automates product creation, SEO, and business intelligence summaries.
 */
class AdminAiAssistantService
{
    /**
     * Generate product description based on name and category (AI placeholder).
     */
    public function generateDescription(string $name, string $categoryName): string
    {
        // In a real production setup, this would call OpenAI/Gemini API.
        // Falling back to a professional template engine for immediate use.
        $templates = [
            "Experience premium quality with our latest {$name}. Specially curated in the {$categoryName} collection, this product combines modern design with exceptional durability.",
            "Introducing the all-new {$name}. Perfectly suited for those who value style and performance. A standout piece in our {$categoryName} category.",
            "Elevate your lifestyle with {$name}. Crafted with precision and attention to detail, this {$categoryName} essential is a must-have for the season."
        ];

        return $templates[array_rand($templates)];
    }

    /**
     * Generate SEO meta tags (title, description, keywords).
     */
    public function generateSeoMeta(Product $product): array
    {
        $name = $product->name;
        $cat = $product->category->name ?? '';
        
        return [
            'meta_title' => "Buy {$name} online | Best price in {$cat} | Staylbd",
            'meta_description' => "Get the best deal on {$name}. Original quality product from {$cat} category. Free shipping and easy returns available.",
            'meta_keywords' => "{$name}, buy {$name}, {$cat}, online shopping, Staylbd ecommerce",
        ];
    }

    /**
     * AI-based Sales Insights Summary.
     */
    public function getSalesInsights(): string
    {
        $today = Order::whereDate('created_at', today())->sum('total');
        $yesterday = Order::whereDate('created_at', today()->subDay())->sum('total');
        
        $growth = 0;
        if ($yesterday > 0) {
            $growth = (($today - $yesterday) / $yesterday) * 100;
        }

        $topProduct = OrderDetail::select('product_id', DB::raw('count(*) as count'))
            ->groupBy('product_id')
            ->orderByDesc('count')
            ->with('product')
            ->first();

        $insight = "Revenue today is {$today}. ";
        if ($growth > 0) {
            $insight .= "Your sales have increased by " . round($growth, 1) . "% compared to yesterday! ";
        } elseif ($growth < 0) {
            $insight .= "Sales are down by " . abs(round($growth, 1)) . "% compared to yesterday. Consider launching a promo. ";
        } else {
            $insight .= "Sales are steady. ";
        }

        if ($topProduct && $topProduct->product) {
            $insight .= "The current trendsetter is '{$topProduct->product->name}', keep it well-stocked.";
        }

        return $insight;
    }
}
