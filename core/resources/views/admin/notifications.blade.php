@extends('admin.layouts.app')
@section('panel')
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body py-3">
            <p class="mb-0 small text-muted">
                <i class="las la-info-circle me-1"></i>
                @lang('This page is under') <strong>@lang('Manage Orders')</strong>. @lang('Click any notification to open the related section (e.g. order detail, product edit, deposit).')
            </p>
        </div>
    </div>
    <div class="notify__area">
    	@forelse($notifications as $notification)
        <a class="notify__item @if($notification->is_read == Status::NO) unread--notification @endif d-block text-decoration-none" href="{{ route('admin.notification.read', $notification->id) }}" title="@lang('Click to go to this item')">
            <div class="notify__content">
                <h6 class="title">{{ __($notification->title) }}</h6>
                <span class="date"><i class="las la-clock"></i> {{ $notification->created_at->diffForHumans() }}</span>
            </div>
        </a>
        @empty
        <div class="card">
            <div class="card-body">
                <h3 class="text-center">{{ __($emptyMessage) }}</h3>
            </div>
        </div>
        @endforelse
        <div class="mt-3">
            {{ paginateLinks($notifications) }}
        </div>
    </div>
@endsection
@push('breadcrumb-plugins')
    <a href="{{ route('admin.notifications.readAll') }}" class="btn btn-sm btn-outline--primary">@lang('Mark All as Read')</a>
@endpush
