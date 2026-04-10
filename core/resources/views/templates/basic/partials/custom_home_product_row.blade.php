@php
    $r = $rowModel;
    $sectionKey = $r->sectionKey();
    $link = $r->view_all_url ? trim((string) $r->view_all_url) : null;
    $linkLabel = $r->view_all_label ? __($r->view_all_label) : __('View All');
    if (!$link && $r->source_type === 'category' && $r->category_id) {
        $cat = $r->relationLoaded('category') ? $r->category : \App\Models\Category::query()->find($r->category_id);
        if ($cat) {
            $link = route('category.products', [slug($cat->name), $cat->id]);
        }
    }
    if (isset($sectionLabel) && trim((string) $sectionLabel) !== '') {
        $titleHtml = (string) $sectionLabel;
    } else {
        $titleHtml = view($activeTemplate . 'partials.icon', ['name' => 'layer-group', 'class' => 'w-5 h-5 text-teal-500'])->render() . ' ' . e((string) $r->title);
    }
    if (!empty($r->subtitle)) {
        $titleHtml .= ' <span class="pro-section__subtitle d-block small text-muted fw-normal mt-1">' . e($r->subtitle) . '</span>';
    }
    $interval = isset($carouselIntervalSec) && $carouselIntervalSec !== null && $carouselIntervalSec !== ''
        ? max(2, min(30, (int) $carouselIntervalSec))
        : ($r->interval_seconds ? max(2, min(30, (int) $r->interval_seconds)) : null);
    $speedMs = isset($carouselSpeedMs) && $carouselSpeedMs !== null && $carouselSpeedMs !== ''
        ? max(300, min(2000, (int) $carouselSpeedMs))
        : null;
    $splitRaw = $r->split_banner_json ?? null;
    if (is_string($splitRaw)) {
        $splitRaw = json_decode($splitRaw, true);
    }
    $splitCfg = is_array($splitRaw) ? $splitRaw : [];
    $hasLarge = is_array($splitCfg)
        && !empty($splitCfg['large'])
        && is_array($splitCfg['large'])
        && count($splitCfg['large']) > 0;
    $hasSmall = is_array($splitCfg)
        && isset($splitCfg['small']['image'])
        && $splitCfg['small']['image'] !== '';
    $splitPublic = !array_key_exists('is_public', $splitCfg)
        || $splitCfg['is_public'] === true
        || $splitCfg['is_public'] === 1
        || $splitCfg['is_public'] === '1';
    $showRowSplit = is_array($splitCfg) && !empty($splitCfg['enabled']) && ($hasLarge || $hasSmall) && $splitPublic;
@endphp
@if($showRowSplit)
    @include($activeTemplate . 'partials.row_split_promo', ['rowModel' => $r])
@endif
@include($activeTemplate . 'partials.product_carousel_section', [
    'products' => $products,
    'sectionKey' => $sectionKey,
    'sectionTitle' => $titleHtml,
    'sectionLink' => $link,
    'sectionLinkText' => $linkLabel,
    'sectionClass' => 'pro-section pro-section--tight custom-home-product-row wow fadeInUp',
    'sectionId' => 'custom-home-row-' . $r->id,
    'carouselIntervalSec' => $interval,
    'carouselSpeedMs' => $speedMs,
])
