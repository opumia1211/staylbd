<?php

namespace App\Events;

use App\Models\Product;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired when a product is created, updated, or removed (storefront real-time).
 * Uses the Pusher protocol (Pusher Cloud, Soketi, or Laravel Reverb after framework upgrade).
 */
class ProductUpdated implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public Product $product,
        public string $action = 'updated'
    ) {
    }

    /**
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('products'),
            new Channel('product.'.$this->product->id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'product.updated';
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        $general = gs();
        $curSym  = $general->cur_sym ?? '';

        if ($this->action === 'deleted') {
            return [
                'action' => 'deleted',
                'product' => [
                    'id' => $this->product->id,
                    'slug' => (string) ($this->product->slug ?? ''),
                    'name' => (string) ($this->product->name ?? ''),
                    'quantity' => 0,
                    'price' => 0.0,
                    'has_variants' => false,
                    'stock_qty' => 0,
                    'max_order_qty' => 0,
                ],
                'display' => [
                    'cur_sym' => $curSym,
                    'effective' => 0.0,
                    'effective_formatted' => showAmount(0.0),
                    'compare_at' => null,
                    'compare_formatted' => null,
                    'has_savings' => false,
                    'save_percent' => 0,
                    'save_amount_formatted' => null,
                ],
                'variants' => [],
            ];
        }

        $this->product->refresh();
        $this->product->loadMissing(['activeVariants']);

        $pricing    = productDisplayPricing($this->product);
        $effective  = $pricing['effective'];
        $compareAt  = $pricing['compare_at'];
        $hasSavings = $pricing['has_savings'];
        $savePct    = $pricing['save_percent'];
        $saveAmt    = $pricing['save_amount'];

        $hasVariants = (bool) $this->product->has_variants
            && $this->product->activeVariants->isNotEmpty();
        $stockQty = $hasVariants
            ? (int) $this->product->activeVariants->sum('quantity')
            : (int) $this->product->quantity;
        $maxOrderQty = $hasVariants
            ? (int) $this->product->activeVariants->max('quantity')
            : (int) $this->product->quantity;

        $variants = [];
        if ($hasVariants) {
            foreach ($this->product->activeVariants as $v) {
                $dispSize = is_array($v->attributes) ? ($v->attributes['size'] ?? $v->id) : $v->id;
                $variants[] = [
                    'id' => $v->id,
                    'quantity' => (int) $v->quantity,
                    'final_price' => (float) $v->final_price,
                    'size' => (string) $dispSize,
                ];
            }
        }

        return [
            'action' => $this->action,
            'product' => [
                'id' => $this->product->id,
                'slug' => $this->product->slug,
                'name' => $this->product->name,
                'quantity' => (int) $this->product->quantity,
                'price' => (float) ($this->product->price ?? 0),
                'has_variants' => $hasVariants,
                'stock_qty' => $stockQty,
                'max_order_qty' => $maxOrderQty,
            ],
            'display' => [
                'cur_sym' => $curSym,
                'effective' => $effective,
                'effective_formatted' => showAmount($effective),
                'compare_at' => $compareAt,
                'compare' => $compareAt,
                'compare_formatted' => $compareAt !== null ? showAmount((float) $compareAt) : null,
                'has_savings' => $hasSavings,
                'save_percent' => $savePct,
                'save_amount_formatted' => $hasSavings ? showAmount($saveAmt) : null,
            ],
            'variants' => $variants,
        ];
    }
}
