@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="ticket__wrapper">
        <div class="add-review">
            <h5 class="title bold mb-2">@lang('Add a review for the')</h5>
            <h6 class="text--base my-2">{{ __($product->name) }}</h6>
            <form action="{{ route('user.review.store', $product->id) }}" method="POST" class="review-form rating row">
                @csrf
                <div class="review-form-group col-md-6">
                    <label for="your-name" class="review-label">@lang('Your Name')</label>
                    <input type="text" class="form-control bg--section" id="your-name" name="username" value="{{ auth()->user()->username }}" readonly>
                </div>
                <div class="review-form-group col-md-6">
                    <label for="your-email" class="review-label">@lang('Your Email')</label>
                    <input type="text" class="form-control bg--section" id="your-email" name="email" value="{{ auth()->user()->email }}" readonly>
                </div>
                <div class="review-form-group col-md-6 d-flex flex-wrap">
                    <label class="review-label mb-0 me-3">@lang('Your Ratings') :</label>
                    <div class="rating-form-group">
                        <label class="star-label">
                            <input type="radio" name="stars" value="1" />
                            <span class="icon">@include($activeTemplate . 'partials.icon', ['name' => 'star'])</span>
                        </label>
                        <label class="star-label">
                            <input type="radio" name="stars" value="2" />
                            <span class="icon">@include($activeTemplate . 'partials.icon', ['name' => 'star'])</span>
                            <span class="icon">@include($activeTemplate . 'partials.icon', ['name' => 'star'])</span>
                        </label>
                        <label class="star-label">
                            <input type="radio" name="stars" value="3" />
                            <span class="icon">@include($activeTemplate . 'partials.icon', ['name' => 'star'])</span>
                            <span class="icon">@include($activeTemplate . 'partials.icon', ['name' => 'star'])</span>
                            <span class="icon">@include($activeTemplate . 'partials.icon', ['name' => 'star'])</span>
                        </label>
                        <label class="star-label">
                            <input type="radio" name="stars" value="4" />
                            <span class="icon">@include($activeTemplate . 'partials.icon', ['name' => 'star'])</span>
                            <span class="icon">@include($activeTemplate . 'partials.icon', ['name' => 'star'])</span>
                            <span class="icon">@include($activeTemplate . 'partials.icon', ['name' => 'star'])</span>
                            <span class="icon">@include($activeTemplate . 'partials.icon', ['name' => 'star'])</span>
                        </label>
                        <label class="star-label">
                            <input type="radio" name="stars" value="5" />
                            <span class="icon">@include($activeTemplate . 'partials.icon', ['name' => 'star'])</span>
                            <span class="icon">@include($activeTemplate . 'partials.icon', ['name' => 'star'])</span>
                            <span class="icon">@include($activeTemplate . 'partials.icon', ['name' => 'star'])</span>
                            <span class="icon">@include($activeTemplate . 'partials.icon', ['name' => 'star'])</span>
                            <span class="icon">@include($activeTemplate . 'partials.icon', ['name' => 'star'])</span>
                        </label>
                    </div>
                </div>
                <div class="review-form-group col-12 d-flex flex-wrap">
                    <label class="review-label" for="review-comments">
                        @lang('Say something about this products')
                    </label>
                    <textarea name="review_comment" class="form-control bg--section" id="review-comments" placeholder="@lang('Write here')...">{{ old('review_comment') }}</textarea>
                </div>
                <button type="submit" class="btn btn--base cmn--btn w-100">@lang('Submit')</button>
            </form>
        </div>
    </div>
@endsection
