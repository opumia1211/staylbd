@extends('admin.layouts.app')
@section('panel')
<div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent border-0 d-flex flex-wrap justify-content-between align-items-center gap-2 py-3">
                            <h5 class="card-title mb-0 fw-bold"><i class="las la-clock me-2"></i>{{ $pageTitle }}</h5>
                            <a href="{{ route('admin.offer-timers.create') }}" class="btn btn--primary btn-sm">
                                <i class="las la-plus"></i> @lang('Add Offer Timer')
                            </a>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small mb-3">@lang('Discount / special offer countdown bars on cart, checkout, product pages. Control style, position and which pages to show.')</p>
                            @include('partials.notify')
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>@lang('Title')</th>
                                            <th>@lang('Ends At')</th>
                                            <th>@lang('Style')</th>
                                            <th>@lang('Position')</th>
                                            <th>@lang('Pages')</th>
                                            <th>@lang('Status')</th>
                                            <th class="text-end">@lang('Action')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($timers as $t)
                                        <tr>
                                            <td class="fw-medium">{{ __($t->title) }}</td>
                                            <td class="small">{{ $t->end_at->format('M d, Y H:i') }}</td>
                                            <td><span class="badge bg-secondary">{{ $t->style }}</span></td>
                                            <td><span class="badge bg-info">{{ $t->position }}</span></td>
                                            <td class="small">
                                                @if(in_array('all', $t->show_on_pages ?? []))
                                                    @lang('All')
                                                @else
                                                    {{ implode(', ', $t->show_on_pages ?? []) }}
                                                @endif
                                            </td>
                                            <td>
                                                @if($t->is_active && $t->end_at->isFuture())
                                                    <span class="badge bg-success">@lang('Active')</span>
                                                @elseif(!$t->is_active)
                                                    <span class="badge bg-danger">@lang('Off')</span>
                                                @else
                                                    <span class="badge bg-secondary">@lang('Expired')</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('admin.offer-timers.edit', $t->id) }}" class="btn btn-outline--primary" title="@lang('Edit')"><i class="las la-pen"></i></a>
                                                    <form action="{{ route('admin.offer-timers.status', $t->id) }}" method="post" class="d-inline">@csrf
                                                        <button type="submit" class="btn btn-outline--{{ $t->is_active ? 'warning' : 'success' }}" title="{{ $t->is_active ? __('Turn off') : __('Turn on') }}"><i class="las la-{{ $t->is_active ? 'eye-slash' : 'eye' }}"></i></button>
                                                    </form>
                                                    <button type="button" class="btn btn-outline--danger confirmationBtn" data-action="{{ route('admin.offer-timers.destroy', $t->id) }}" data-question="@lang('Delete this offer timer?')" title="@lang('Delete')"><i class="las la-trash"></i></button>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-5 text-muted">
                                                <i class="las la-clock fa-3x mb-3 d-block opacity-50"></i>
                                                <p class="mb-2">@lang('No offer timers yet.')</p>
                                                <a href="{{ route('admin.offer-timers.create') }}" class="btn btn--primary btn-sm"><i class="las la-plus"></i> @lang('Add Offer Timer')</a>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
</div>
@endsection
