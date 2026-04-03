@extends('admin.layouts.app')
@php $pageTitle = $pageTitle ?? __('Setup Required'); $corrupt = $corrupt ?? false; @endphp

@section('panel')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm b-radius--10">
                <div class="card-body text-center py-5 px-4">
                    <div class="mb-4">
                        <i class="las la-database text--primary" style="font-size: 4rem;"></i>
                    </div>
                    <h4 class="mb-3">@lang('Payment Tables Not Set Up')</h4>
                    <p class="text-muted mb-4">
                        @if($corrupt)
                            @lang('The deposits table is corrupt or not available in the database engine. Run the repair command below.')
                        @else
                            @lang('The deposits table is missing. Run the database migrations to create payment and deposit tables.')
                        @endif
                    </p>
                    <div class="bg-light rounded p-4 mb-4 text-start">
                        <p class="mb-2 small fw-bold">@lang('Option 1 – Recommended (repair or create table):')</p>
                        <p class="small text-muted mb-1">@lang('From project folder') <strong>core</strong>, @lang('run:')</p>
                        <code class="d-block p-3 rounded bg-dark text-light mb-3">php artisan deposit:ensure-table</code>
                        <p class="mb-2 small fw-bold">@lang('Option 2 – Migration:')</p>
                        <code class="d-block p-2 rounded bg-dark text-light small mb-3">php artisan migrate --path=database/migrations/2026_02_16_160000_create_deposits_table.php</code>
                        <p class="mb-2 small fw-bold">@lang('Option 3 – Manual SQL (phpMyAdmin):')</p>
                        <p class="small text-muted mb-1">@lang('Select database') <strong>wintersm_tt</strong>, @lang('then Import or run:')</p>
                        <code class="d-block p-2 rounded bg-dark text-light small">core/database/fix_deposits_table.sql</code>
                    </div>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn--primary">
                        <i class="las la-arrow-left me-1"></i> @lang('Back to Dashboard')
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
