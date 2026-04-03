<div class="review-item advanced-review-item" data-review-id="{{ $review->id }}">
    <div class="thumb review-item__thumb">
        <img src="{{ getImage(getFilePath('userProfile') . '/' . @$review->user->image, getFileSize('userProfile')) }}" alt="{{ __(@$review->user->username) }}" loading="lazy" decoding="async">
    </div>
    <div class="content review-item__content">
        <div class="review-item__meta">
            <h6 class="review-item__author">
                <span class="review-item__name">{{ __(@$review->user->username) }}</span>
                <span class="review-item__date">@lang('Posted on') {{ showDateTime($review->created_at) }}</span>
            </h6>
            <div class="review-item__rating-row">
                <div class="ratings review-item__stars">
                    @php echo showProductRatings($review->stars); @endphp
                </div>
                @if(!empty($review->is_verified_purchase))
                    <span class="review-item__verified-badge">@include($activeTemplate . 'partials.icon', ['name' => 'check-circle']) @lang('Verified Purchase')</span>
                @endif
            </div>
        </div>
        @if(!empty($review->title))
            <h6 class="review-item__title">{{ __($review->title) }}</h6>
        @endif
        <div class="review-item__body">
            <p>{{ __($review->review_comment) }}</p>
        </div>
        @if(!empty($review->images) && is_array($review->images) && count($review->images) > 0)
            <div class="review-item__gallery">
                @foreach($review->images as $img)
                    @php $imgPath = getFilePath('reviewImage') . '/' . $img; @endphp
                    <a href="{{ getImageWebP($imgPath, getFileSize('reviewImage')) }}" class="review-item__gallery-link" target="_blank" rel="noopener">
                        <img src="{{ getImageWebP($imgPath, getFileSize('reviewImage')) }}" alt="" loading="lazy" decoding="async">
                    </a>
                @endforeach
            </div>
        @endif
        <div class="review-item__actions">
            <button type="button" class="review-item__helpful-btn btn-helpful-review" data-review-id="{{ $review->id }}" title="@lang('Helpful')">
                @include($activeTemplate . 'partials.icon', ['name' => 'thumbs-up'])
                <span class="review-item__helpful-count">{{ $review->helpful_count }}</span>
                <span class="review-item__helpful-text">@lang('people found this helpful')</span>
            </button>
        </div>
    </div>
</div>
