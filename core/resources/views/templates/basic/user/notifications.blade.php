@extends($activeTemplate . 'layouts.master')
@section('dashboard_page_title')
    @php
        $notifActions = '';
        if ($notifications->total() > 0) {
            $notifActions = '<span class="badge bg--base">' . $notifications->total() . ' ' . __('total') . '</span>';
            $notifActions .= '<form action="' . route('user.notifications.read.all') . '" method="post" class="d-inline">' . csrf_field() . '<button type="submit" class="btn btn-sm btn-outline--primary">' . __('Mark all read') . '</button></form>';
            $notifActions .= '<form action="' . route('user.notifications.clear.all') . '" method="post" class="d-inline" onsubmit="return confirm(\'' . __('Remove all notifications?') . '\');">' . csrf_field() . '<button type="submit" class="btn btn-sm btn-outline--danger">' . __('Clear all') . '</button></form>';
        }
    @endphp
    @include($activeTemplate . 'partials.dashboard_page_header', ['title' => __('Notifications'), 'subtitle' => __('All messages and updates sent to you'), 'actions' => $notifActions])
@endsection
@section('content')
    <div class="notifications-page notifications-page--compact">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body p-0">
                        @forelse($notifications as $log)
                            @php
                                $hasLink = !empty($log->click_url);
                                $readUrl = route('user.notification.read', $log->id);
                                $msgRaw = $log->message ?? '';
                                $msgPlain = strip_tags($msgRaw);
                                $previewLen = 100;
                                $showExpand = strlen($msgPlain) > $previewLen;
                            @endphp
                            @if($hasLink)
                                <a href="{{ $readUrl }}" class="notification-item border-bottom border-light d-block text-decoration-none text-body">
                            @else
                                <div class="notification-item border-bottom border-light">
                            @endif
                                <div class="notification-item__inner px-2 py-2 d-flex align-items-start gap-2">
                                    <div class="notification-item__icon flex-shrink-0 rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                        <span class="text--base" style="font-size: 1rem;">@include($activeTemplate . 'partials.icon', ['name' => 'envelope'])</span>
                                    </div>
                                    <div class="notification-item__body flex-grow-1 min-w-0">
                                        <div class="d-flex justify-content-between align-items-center gap-1 mb-0">
                                            <span class="notification-item__subject fw-semibold text-dark" style="font-size: 0.85rem;">{{ __($log->subject) }}</span>
                                            <span class="notification-item__time text-muted flex-shrink-0" style="font-size: 0.7rem;">{{ $log->created_at->format('d/m') }} {{ $log->created_at->format('H:i') }}</span>
                                        </div>
                                        <p class="notification-item__message mb-0 text-dark" style="font-size: 0.8rem; line-height: 1.4;">
                                            @if($showExpand)
                                                <span class="notification-preview">{{ \Illuminate\Support\Str::limit($msgPlain, $previewLen) }}</span>
                                                <span class="notification-full d-none">{!! nl2br(e($msgPlain)) !!}</span>
                                                <button type="button" class="btn-link p-0 border-0 bg-transparent text--base notification-btn-expand" style="font-size: 0.75rem;"><span class="expand-text">@lang('More')</span><span class="collapse-text d-none">@lang('Less')</span></button>
                                            @else
                                                {!! nl2br(e($msgPlain)) !!}
                                            @endif
                                        </p>
                                        @if($hasLink)
                                            <span class="d-inline-block mt-1 small text--base" style="font-size: 0.7rem;">@include($activeTemplate . 'partials.icon', ['name' => 'external-link-alt']) @lang('View')</span>
                                        @endif
                                    </div>
                                </div>
                            @if($hasLink)
                                </a>
                            @else
                                </div>
                            @endif
                        @empty
                            <div class="text-center py-4 px-3">
                                <span class="text-muted" style="font-size: 2.5rem;">@include($activeTemplate . 'partials.icon', ['name' => 'bell-slash'])</span>
                                <h6 class="text-muted mt-2 mb-0" style="font-size: 0.9rem;">@lang('No notifications yet')</h6>
                                <p class="text-muted mb-0" style="font-size: 0.8rem;">@lang('When we send you updates, they will appear here.')</p>
                            </div>
                        @endforelse
                    </div>
                    @if($notifications->hasPages())
                        <div class="card-footer bg-transparent border-0 py-2">
                            {{ paginateLinks($notifications) }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')

{{-- inline style moved to critical-storefront.css --}}

@endpush

@push('script')
<script>
(function() {
    document.querySelectorAll('.notification-btn-expand').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var par = btn.closest('.notification-item__message');
            if (!par) return;
            var preview = par.querySelector('.notification-preview');
            var full = par.querySelector('.notification-full');
            var expandText = par.querySelector('.expand-text');
            var collapseText = par.querySelector('.collapse-text');
            if (full && full.classList.contains('d-none')) {
                if (preview) preview.classList.add('d-none');
                full.classList.remove('d-none');
                if (expandText) expandText.classList.add('d-none');
                if (collapseText) collapseText.classList.remove('d-none');
            } else {
                if (preview) preview.classList.remove('d-none');
                if (full) full.classList.add('d-none');
                if (expandText) expandText.classList.remove('d-none');
                if (collapseText) collapseText.classList.add('d-none');
            }
        });
    });
})();
</script>
@endpush
