@extends('admin.layouts.app')
@section('panel')
    @php
        $activeCount = \App\Models\Coupon::where('start_date', '<=', now()->format('Y-m-d'))->where('end_date', '>=', now()->format('Y-m-d'))->count();
        $expiredCount = \App\Models\Coupon::where('end_date', '<', now()->format('Y-m-d'))->count();
        $upcomingCount = \App\Models\Coupon::where('start_date', '>', now()->format('Y-m-d'))->count();
        $typeOptions = ['welcome' => __('Welcome'), 'flash' => __('Flash Sale'), 'seasonal' => __('Seasonal'), 'loyalty' => __('Loyalty'), 'general' => __('General')];
    @endphp

    {{-- Stats cards --}}
    <div class="row mb-3">
        <div class="col-md-4">
            <div class="card b-radius--10 border--success">
                <div class="card-body py-3 px-4 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small">@lang('Active')</span>
                        <h4 class="mb-0 mt-1">{{ $activeCount }}</h4>
                    </div>
                    <i class="las la-tags text--success" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card b-radius--10 border--danger">
                <div class="card-body py-3 px-4 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small">@lang('Expired')</span>
                        <h4 class="mb-0 mt-1">{{ $expiredCount }}</h4>
                    </div>
                    <i class="las la-clock text--danger" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card b-radius--10 border--info">
                <div class="card-body py-3 px-4 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small">@lang('Upcoming')</span>
                        <h4 class="mb-0 mt-1">{{ $upcomingCount }}</h4>
                    </div>
                    <i class="las la-calendar text--info" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter toolbar --}}
    <form action="{{ route('admin.coupon.index') }}" method="GET" class="row mb-3">
        <div class="col-12">
            <div class="d-flex flex-wrap align-items-center gap-2">
                <input type="hidden" name="search" value="{{ request('search') }}">
                <label class="mb-0 text-muted small">@lang('Status')</label>
                <select name="status" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                    <option value="">@lang('All')</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>@lang('Enabled')</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>@lang('Disabled')</option>
                </select>
                <label class="mb-0 text-muted small">@lang('Date')</label>
                <select name="date_filter" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                    <option value="">@lang('All')</option>
                    <option value="active" {{ request('date_filter') === 'active' ? 'selected' : '' }}>@lang('Active')</option>
                    <option value="expired" {{ request('date_filter') === 'expired' ? 'selected' : '' }}>@lang('Expired')</option>
                    <option value="upcoming" {{ request('date_filter') === 'upcoming' ? 'selected' : '' }}>@lang('Upcoming')</option>
                </select>
                <label class="mb-0 text-muted small">@lang('Type')</label>
                <select name="type" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                    <option value="">@lang('All')</option>
                    @foreach($typeOptions as $k => $v)
                        <option value="{{ $k }}" {{ request('type') === $k ? 'selected' : '' }}>{{ $v }}</option>
                    @endforeach
                </select>
                <label class="mb-0 text-muted small">@lang('Per page')</label>
                <select name="per_page" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                    @foreach([10, 20, 50, 100] as $n)
                        <option value="{{ $n }}" {{ (int)request('per_page', getPaginate()) === $n ? 'selected' : '' }}>{{ $n }}</option>
                    @endforeach
                </select>
                <span class="text-muted small ms-2">@lang('Total'): <strong>{{ $coupons->total() }}</strong></span>
            </div>
        </div>
    </form>

    <div class="row">
        <div class="col-md-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--lg table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Min Order')</th>
                                    <th>@lang('Discount')</th>
                                    <th class="text-center">@lang('Used')</th>
                                    <th>@lang('Type')</th>
                                    <th>@lang('Start')</th>
                                    <th>@lang('End')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($coupons as $coupon)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-1">
                                                <code class="coupon-code-copy" data-copy="{{ $coupon->name }}" title="@lang('Click to copy')" style="cursor: pointer; padding: 2px 6px; background: #f0f0f0; border-radius: 4px;">{{ $coupon->name }}</code>
                                                <button type="button" class="btn btn-sm btn-outline--info p-0 px-1 copy-coupon-btn" data-copy="{{ $coupon->name }}" title="@lang('Copy')"><i class="las la-copy"></i></button>
                                            </div>
                                        </td>
                                        <td>{{ $general->cur_sym }}{{ showAmount($coupon->min_order) }}</td>
                                        <td>
                                            {{ showAmount($coupon->discount) }}{{ $coupon->discount_type == 1 ? __($general->cur_text) : '%' }}
                                            @if($coupon->max_discount && $coupon->discount_type == 2)
                                                <small class="text-muted d-block">(max {{ $general->cur_sym }}{{ showAmount($coupon->max_discount) }})</small>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($coupon->usage_limit !== null)
                                                <span class="badge badge--primary">{{ $coupon->used_count }} / {{ $coupon->usage_limit }}</span>
                                            @else
                                                <span class="text-muted">{{ $coupon->used_count }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($coupon->type)
                                                <span class="badge badge--dark">{{ __($typeOptions[$coupon->type] ?? $coupon->type) }}</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                            @if($coupon->is_first_order_only)
                                                <span class="badge badge--primary ms-1">@lang('First order')</span>
                                            @endif
                                        </td>
                                        <td><span>{{ showDateTime($coupon->start_date) }}</span></td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($coupon->end_date)->diffInDays(\Carbon\Carbon::now()->format('Y-m-d')) }}
                                            {{ $coupon->end_date >= now()->format('Y-m-d') ? trans('Days Left') : trans('Days ago expired') }}
                                        </td>
                                        <td>@php echo $coupon->statusBadge; @endphp</td>
                                        <td>
                                            <div class="button--group">
                                                @php $couponResource = array_merge($coupon->toArray(), ['start_date' => $coupon->start_date?->format('Y-m-d'), 'end_date' => $coupon->end_date?->format('Y-m-d')]); @endphp
                                                <button type="button" class="btn btn-sm btn-outline--primary cuModalBtn" data-resource='@json($couponResource)' data-modal_title="@lang('Edit Coupon')">
                                                    <i class="la la-pencil"></i> @lang('Edit')
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline--info confirmationBtn" data-action="{{ route('admin.coupon.duplicate', $coupon->id) }}" data-question="@lang('Duplicate this coupon?')">
                                                    <i class="las la-copy"></i> @lang('Duplicate')
                                                </button>
                                                @if (!$coupon->status)
                                                    <button type="button" class="btn btn-sm btn-outline--success confirmationBtn" data-action="{{ route('admin.coupon.status', $coupon->id) }}" data-question="@lang('Are you sure to enable this coupon?')">
                                                        <i class="la la-eye"></i> @lang('Enable')
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-sm btn-outline--danger confirmationBtn" data-action="{{ route('admin.coupon.status', $coupon->id) }}" data-question="@lang('Are you sure to disable this coupon?')">
                                                        <i class="la la-eye-slash"></i> @lang('Disable')
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="9">{{ __($emptyMessage ?? 'No coupons found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($coupons->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($coupons) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <x-confirmation-modal />

    {{-- Create or Update Modal --}}
    <div id="cuModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="cuModalTitle" aria-hidden="true" data-bs-backdrop="true" data-bs-keyboard="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('admin.coupon.store') }}" method="POST" data-base-action="{{ route('admin.coupon.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>@lang('Name') <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="name" value="{{ old('name') }}" placeholder="e.g. SAVE20" required />
                                </div>
                                <div class="form-group">
                                    <label>@lang('Discount') <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" class="form-control" name="discount" required placeholder="0">
                                        <select name="discount_type" class="input-group-text">
                                            <option value="1" @selected(old('discount_type') == 1)>{{ __($general->cur_text) }}</option>
                                            <option value="2" @selected(old('discount_type') == 2)>@lang('%')</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>@lang('Maximum Discount') <small class="text-muted">(@lang('for % only'))</small></label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" class="form-control" name="max_discount" value="{{ old('max_discount') }}" placeholder="@lang('Unlimited')">
                                        <span class="input-group-text">{{ __($general->cur_text) }}</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>@lang('Minimum Order') <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" class="form-control" name="min_order" value="{{ old('min_order', 0) }}" required>
                                        <span class="input-group-text">{{ __($general->cur_text) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>@lang('Start Date') <span class="text-danger">*</span></label>
                                    <input type="text" class="datepicker-here form-control" data-language='en' data-date-format="yyyy-mm-dd" data-position='bottom left' placeholder="@lang('Select date')" name="start_date" autocomplete="off" value="{{ old('start_date') }}" required>
                                </div>
                                <div class="form-group">
                                    <label>@lang('End Date') <span class="text-danger">*</span></label>
                                    <input type="text" class="datepicker-here form-control" data-language='en' data-date-format="yyyy-mm-dd" data-position='bottom left' placeholder="@lang('Select date')" name="end_date" autocomplete="off" value="{{ old('end_date') }}" required>
                                </div>
                                <div class="form-group">
                                    <label>@lang('Usage Limit') <small class="text-muted">(@lang('total uses'))</small></label>
                                    <input type="number" min="1" class="form-control" name="usage_limit" value="{{ old('usage_limit') }}" placeholder="@lang('Unlimited')">
                                </div>
                                <div class="form-group">
                                    <label>@lang('Per User Limit')</label>
                                    <input type="number" min="1" class="form-control" name="per_user_limit" value="{{ old('per_user_limit') }}" placeholder="@lang('Unlimited')">
                                </div>
                                <div class="form-group">
                                    <label>@lang('Type')</label>
                                    <select name="type" class="form-control">
                                        <option value="">@lang('Select type')</option>
                                        @foreach($typeOptions as $k => $v)
                                            <option value="{{ $k }}" @selected(old('type') === $k)>{{ $v }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="is_first_order_only" id="isFirstOrderOnly" value="1" @checked(old('is_first_order_only'))>
                                        <label class="form-check-label" for="isFirstOrderOnly">
                                            @lang('First order only')
                                        </label>
                                    </div>
                                    <small class="text-muted d-block">
                                        @lang('If enabled, this coupon can be applied only on a customer\'s first non-cancelled order.')
                                    </small>
                                </div>
                                <div class="form-group">
                                    <label>@lang('Description') <small class="text-muted">(@lang('Admin notes'))</small></label>
                                    <textarea class="form-control" name="description" rows="2" placeholder="@lang('Optional notes')">{{ old('description') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="@lang('Search by name...')" />
    <button type="button" class="btn btn-sm btn-outline--primary h-45 cuModalBtn" data-modal_title="@lang('Add New Coupon')">
        <i class="las la-plus"></i> @lang('Add New')
    </button>
@endpush

@push('style')
    <style>
        #cuModal { z-index: 10600 !important; position: fixed !important; }
        #cuModal .modal-dialog { z-index: 10602 !important; position: relative; }
        body.modal-open .modal-backdrop { z-index: 10598 !important; }
        #cuModal .modal-content { position: relative; z-index: 1; pointer-events: auto; }
        .datepickers-container { z-index: 99999999; }
        .coupon-code-copy:hover { background: #e0e0e0 !important; }
    </style>
@endpush

@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/vendor/datepicker.min.css') }}">
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/vendor/datepicker.en.js') }}"></script>
    <script src="{{ asset('assets/admin/js/cu-modal.js') }}"></script>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            $(function() {
                var modal = document.getElementById('cuModal');
                if (modal && modal.parentNode && modal.parentNode !== document.body) {
                    document.body.appendChild(modal);
                }
            });

            function copyToClipboard(text) {
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(text).then(function() {
                        if (typeof notify === 'function') notify('success', '{{ __("Copied!") }}');
                    }).catch(function() { fallbackCopy(text); });
                } else { fallbackCopy(text); }
            }
            function fallbackCopy(text) {
                var ta = document.createElement('textarea');
                ta.value = text; ta.style.position = 'fixed'; ta.style.opacity = '0';
                document.body.appendChild(ta); ta.select();
                try {
                    document.execCommand('copy');
                    if (typeof notify === 'function') notify('success', '{{ __("Copied!") }}');
                } catch (e) {}
                document.body.removeChild(ta);
            }

            $(document).on('click', '.copy-coupon-btn, .coupon-code-copy', function(e) {
                e.preventDefault();
                var text = $(this).data('copy');
                if (text) copyToClipboard(text);
            });

            $('.datepicker-here').on('input keydown keypress keyup', function () { return false; });
            $('.datepicker-here').datepicker({ minDate: new Date() });
        })(jQuery);
    </script>
@endpush
