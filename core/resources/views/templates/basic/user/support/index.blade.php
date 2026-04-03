@extends($activeTemplate . 'layouts.master')
@section('dashboard_page_title')
    @include($activeTemplate . 'partials.dashboard_page_header', ['title' => __('Messages'), 'subtitle' => __('Messages older than 30 days are automatically removed.')])
@endsection
@section('content')
<div class="message-list-page">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                    <a href="{{ route('message.open') }}" class="btn btn-sm btn--base" data-dashboard-link="1">@include($activeTemplate . 'partials.icon', ['name' => 'plus']) @lang('New')</a>
                </div>
                <div class="message-list">
                    @forelse($supports as $support)
                        @php
                            $firstMsg = $support->supportMessage->first();
                            $preview = $firstMsg ? \Illuminate\Support\Str::limit(strip_tags($firstMsg->message ?? ''), 80) : '';
                            $date = $firstMsg ? $firstMsg->created_at : $support->created_at;
                        @endphp
                        <div class="message-item border-bottom py-2 px-2">
                            <div class="d-flex flex-wrap align-items-start justify-content-between gap-2">
                                <div class="flex-grow-1 min-w-0">
                                    <span class="message-token fw-bold text-dark">#{{ $support->ticket }}</span>
                                    <span class="message-subject fw-semibold text-dark ms-1">{{ __($support->subject) }}</span>
                                    <p class="message-preview mb-0 mt-1 small text-muted">{{ $preview }}</p>
                                    <span class="message-date small text-muted">{{ $date->format('d M Y, h:i A') }}</span>
                                </div>
                                <div class="d-flex gap-1 flex-shrink-0">
                                    <a href="{{ route('message.view', $support->ticket) }}" class="btn btn--base btn-sm py-1 px-2" data-dashboard-link="1" title="@lang('View')">@include($activeTemplate . 'partials.icon', ['name' => 'eye'])</a>
                                    @if($support->status != \App\Constants\Status::TICKET_CLOSE)
                                    <form action="{{ route('message.close', $support->id) }}" method="POST" class="d-inline" onsubmit="return confirm('@lang('Clear this message?')');">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-danger btn-sm py-1 px-2" title="@lang('Clear')">@include($activeTemplate . 'partials.icon', ['name' => 'trash'])</button>
                                    </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5 text-muted small">
                            <span style="font-size:2.5rem;">@include($activeTemplate . 'partials.icon', ['name' => 'inbox'])</span>
                            <p class="mt-2 mb-0">{{ $emptyMessage ?? __('No messages.') }}</p>
                        </div>
                    @endforelse
                </div>
                @if($supports->hasPages())
                    <div class="mt-2">{{ paginateLinks($supports) }}</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
@push('style')
<style>
.message-list-page .message-item { transition: background .15s ease; }
.message-list-page .message-item:hover { background: rgba(0,0,0,.03); }
.message-list-page .message-token { font-size: 0.85rem; }
.message-list-page .message-subject { font-size: 0.9rem; }
.message-list-page .message-preview { font-size: 0.8rem; line-height: 1.35; }
.message-list-page .message-date { font-size: 0.75rem; }
@media (max-width: 576px) {
    .message-list-page .message-item { padding: 0.5rem !important; }
    .message-list-page .message-preview { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
}
</style>
@endpush
