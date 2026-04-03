@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <div class="row justify-content-center card--wrapper">
        <div class="col-md-6">
            <div class="card custom--card text-center">
                <div class="card-body">
                    <h3 class="text-center text-danger">@lang('You are banned')</h3>
                    <p class="fw-bold mb-1">@lang('Reason'):</p>
                    <p>{{ $user->ban_reason }}</p>
                    <a href="{{ route('user.logout') }}" class="btn btn--danger btn-sm">@include($activeTemplate . 'partials.icon', ['name' => 'sign-out-alt'])@lang('Logout')</a>
                </div>
            </div>
        </div>
    </div>
@endsection
