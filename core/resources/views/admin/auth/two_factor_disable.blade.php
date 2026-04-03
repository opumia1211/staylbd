@extends('admin.layouts.master')
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">{{ $pageTitle ?? __('Disable Two-Factor Authentication') }}</h5>
            </div>
            <div class="card-body">
                @include('partials.notify')
                <p class="text-muted">@lang('Enter your password to disable 2FA.')</p>
                <form action="{{ route('admin.2fa.disable.confirm') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label>@lang('Password') <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-danger">@lang('Disable 2FA')</button>
                    <a href="{{ route('admin.profile') }}" class="btn btn-secondary">@lang('Cancel')</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
