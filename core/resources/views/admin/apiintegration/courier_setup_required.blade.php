@extends('admin.layouts.app')
@php $pageTitle = $pageTitle ?? __('Courier API Setup'); @endphp

@section('panel')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm b-radius--10">
                <div class="card-body text-center py-5 px-4">
                    <div class="mb-4">
                        <i class="las la-truck text--primary" style="font-size: 4rem;"></i>
                    </div>
                    <h4 class="mb-3">@lang('Courier API Tables Not Set Up')</h4>
                    <p class="text-muted mb-4">
                        @lang('The courierapis or courier_logs table is missing. Run migrations to create them.')
                    </p>
                    <div class="bg-light rounded p-4 mb-4 text-start">
                        <p class="mb-2 small fw-bold">@lang('From project folder') <strong>core</strong>, @lang('run:')</p>
                        <code class="d-block p-3 rounded bg-dark text-light">php artisan migrate</code>
                        <p class="small text-muted mt-2 mb-0">@lang('This will create courierapis and courier_logs tables.')</p>
                    </div>
                    <a href="{{ route('admin.api.courier.manage') }}" class="btn btn--primary">
                        <i class="las la-redo me-1"></i> @lang('Retry')
                    </a>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn--dark">
                        <i class="las la-arrow-left me-1"></i> @lang('Back to Dashboard')
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
