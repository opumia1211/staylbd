@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-12 mb-3">
            <a href="{{ route('admin.product.index') }}" class="btn btn-sm btn-outline--primary"><i class="las la-arrow-left"></i> @lang('All Products')</a>
        </div>
        <div class="col-md-12">
            <div class="card b-radius--10">
                <div class="card-body">
                    <form method="get" action="{{ route('admin.product.reviews.index') }}" class="row g-3 mb-4 flex-wrap align-items-end">
                        <div class="col-auto">
                            <label class="form-label">@lang('Product')</label>
                            <select name="product_id" class="form-select form-select-sm" style="min-width: 180px;">
                                <option value="">@lang('All Products')</option>
                                @foreach($products as $id => $name)
                                    <option value="{{ $id }}" {{ request('product_id') == $id ? 'selected' : '' }}>{{ strLimit($name, 40) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-auto">
                            <label class="form-label">@lang('Rating')</label>
                            <select name="rating" class="form-select form-select-sm">
                                <option value="">@lang('All')</option>
                                @for($i = 5; $i >= 1; $i--)
                                    <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>{{ $i }} @lang('Star')</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-auto">
                            <label class="form-label">@lang('Status')</label>
                            <select name="status" class="form-select form-select-sm">
                                <option value="">@lang('All')</option>
                                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>@lang('Approved')</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>@lang('Pending')</option>
                                <option value="private" {{ request('status') === 'private' ? 'selected' : '' }}>@lang('Private')</option>
                            </select>
                        </div>
                        <div class="col-auto">
                            <label class="form-label">@lang('Search')</label>
                            <input type="text" name="search" class="form-control form-control-sm" placeholder="@lang('Review text')" value="{{ request('search') }}" style="min-width: 140px;">
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn--primary btn-sm">@lang('Filter')</button>
                            <a href="{{ route('admin.product.reviews.index') }}" class="btn btn-outline--dark btn-sm">@lang('Reset')</a>
                        </div>
                    </form>
                </div>
                <div class="card-body p-0">
                    <form action="{{ route('admin.product.review.bulk.delete') }}" method="post" id="bulkReviewForm">
                        @csrf
                        <div class="table-responsive--sm">
                            <table class="table table--light style--two">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="selectAllReviews" class="form-check-input"></th>
                                        <th>@lang('Product')</th>
                                        <th>@lang('User')</th>
                                        <th>@lang('Title')</th>
                                        <th>@lang('Review')</th>
                                        <th>@lang('Rating')</th>
                                        <th>@lang('Status')</th>
                                        <th>@lang('Action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($reviews as $review)
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="ids[]" value="{{ $review->id }}" class="form-check-input review-check">
                                            </td>
                                            <td>
                                                @if($review->product)
                                                    <a href="{{ route('admin.product.reviews', $review->product_id) }}" class="text--primary">{{ strLimit($review->product->name, 30) }}</a>
                                                    <br><a href="{{ route('admin.product.edit', $review->product_id) }}" class="small text-muted">@lang('Edit product')</a>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="user">
                                                    <div class="thumb">
                                                        <img src="{{ getImage(getFilePath('userProfile') . '/' . @$review->user->image, getFileSize('userProfile')) }}" alt="@lang('image')">
                                                    </div>
                                                    <span class="name"><a href="{{ route('admin.users.detail', $review->user->id) }}">{{ $review->user->username ?? '-' }}</a></span>
                                                </div>
                                            </td>
                                            <td>{{ strLimit($review->title ?? '-', 25) }}</td>
                                            <td>
                                                {{ strLimit($review->review_comment, 35) }}
                                                <button type="button" data-review="{{ $review->review_comment }}" data-title="{{ $review->title ?? '' }}" class="icon-btn btn--info btn-sm reviewBtn"><i class="las la-eye"></i></button>
                                            </td>
                                            <td>{{ $review->stars }} @lang('stars')</td>
                                            <td>
                                                @if($review->is_approved)
                                                    <span class="badge bg-success">@lang('Approved')</span>
                                                @else
                                                    <span class="badge bg-warning">@lang('Pending')</span>
                                                @endif
                                                @if(!empty($review->is_private))
                                                    <span class="badge bg-secondary">@lang('Private')</span>
                                                @endif
                                                @if(!empty($review->is_featured))
                                                    <span class="badge bg-info">@lang('Featured')</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if(!$review->is_approved)
                                                    <form action="{{ route('admin.product.review.approve', $review->id) }}" method="post" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline--success" title="@lang('Approve')"><i class="las la-check"></i></button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('admin.product.review.reject', $review->id) }}" method="post" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline--warning" title="@lang('Hide')"><i class="las la-eye-slash"></i></button>
                                                    </form>
                                                @endif
                                                <form action="{{ route('admin.product.review.toggle.private', $review->id) }}" method="post" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline--secondary" title="{{ $review->is_private ? __('Make Public') : __('Make Private (Admin Only)') }}"><i class="las la-lock{{ $review->is_private ? '-open' : '' }}"></i></button>
                                                </form>
                                                <form action="{{ route('admin.product.review.featured', $review->id) }}" method="post" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline--info" title="{{ $review->is_featured ? __('Unmark Featured') : __('Mark Featured') }}"><i class="las la-star"></i></button>
                                                </form>
                                                <a href="{{ route('admin.product.reviews', $review->product_id) }}" class="btn btn-sm btn-outline--primary" title="@lang('All reviews of this product')"><i class="las la-list"></i></a>
                                                <button type="button" class="btn btn-sm btn-outline--danger confirmationBtn" data-action="{{ route('admin.product.review.remove', $review->id) }}" data-question="@lang('Are you sure to remove this review?')">
                                                    <i class="las la-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="text-muted text-center" colspan="8">
                                                @lang('No reviews found.')
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if($reviews->count() > 0)
                            <div class="card-footer">
                                <button type="submit" name="bulk_delete" class="btn btn-sm btn-outline--danger" onclick="return confirm('@lang('Delete selected reviews?')');">
                                    <i class="las la-trash"></i> @lang('Bulk Delete Selected')
                                </button>
                            </div>
                        @endif
                    </form>
                </div>
                @if($reviews->hasPages())
                    <div class="card-footer">
                        {{ $reviews->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div id="reviewModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Review')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="review-detail-title font-weight-bold mb-2"></p>
                    <p class="text-muted review-detail"></p>
                </div>
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('script')
    <script>
        (function($) {
            "use strict";
            $('#selectAllReviews').on('change', function() {
                $('.review-check').prop('checked', this.checked);
            });
            $('.reviewBtn').on('click', function() {
                var modal = $('#reviewModal');
                modal.find('.review-detail').text($(this).data('review'));
                modal.find('.review-detail-title').text($(this).data('title') || '').toggle(!!$(this).data('title'));
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
