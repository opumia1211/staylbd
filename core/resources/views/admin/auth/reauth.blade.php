@extends('admin.layouts.app')

@section('panel')
<div class="row justify-content-center">
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="las la-shield-alt me-2"></i>@lang('Re-authentication Required')</h5>
            </div>
            <div class="card-body">
                @include('partials.notify')
                <p class="text-muted small">@lang('For security, please confirm your identity to proceed.')</p>
                <form action="{{ route('admin.reauth.verify') }}" method="POST">
                    @csrf
                    <input type="hidden" name="next" value="{{ $next ?? route('admin.dashboard') }}">
                    <div class="form-group mb-3">
                        <label>@lang('Password') <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    @if(auth()->guard('admin')->user()?->hasTwoFactorEnabled())
                    <div class="form-group mb-3">
                        <label>@lang('2FA Code')</label>
                        <input type="text" name="code" class="form-control" placeholder="000000" maxlength="6" inputmode="numeric">
                    </div>
                    @endif
                    <button type="submit" class="btn btn-primary">@lang('Verify')</button>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">@lang('Cancel')</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
