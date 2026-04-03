{{-- Back link: only on user subpages (not dashboard home). Keeps navigation order so Back goes to the page user came from. --}}
@if(!request()->routeIs('user.home'))
    @php
        $prev = url()->previous();
        $current = url()->current();
        $appUrl = url('/');
        $isInternal = $prev && $prev !== $current && str_starts_with($prev, $appUrl);
        $backUrl = $isInternal ? $prev : route('user.home');
        $backIsDashboard = str_contains($backUrl, '/user/') || str_contains($backUrl, '/track-order') || str_contains($backUrl, '/ticket');
    @endphp
    <div class="dashboard-back-link-wrap mb-2 mb-md-3">
        <a href="{{ $backUrl }}" class="btn btn-sm btn-outline-secondary text-decoration-none dashboard-back-link" @if($backIsDashboard) data-dashboard-link="1" @endif>
            @include($activeTemplate . 'partials.icon', ['name' => 'arrow-left', 'class' => 'me-1'])@lang('Back')
        </a>
    </div>
@endif
