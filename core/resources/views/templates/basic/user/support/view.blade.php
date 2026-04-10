@extends($activeTemplate . 'layouts.' . $layout)
@section('content')
<div class="message-view-page">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <a href="{{ route('message.index') }}" class="btn btn-sm btn--base mb-2" data-dashboard-link="1">@include($activeTemplate . 'partials.icon', ['name' => 'arrow-left']) @lang('Back')</a>

                <div class="card border-0 shadow-sm mb-2">
                    <div class="card-body py-2 px-3">
                        <span class="message-token fw-bold text-dark">#{{ $myTicket->ticket }}</span>
                        <span class="message-subject fw-semibold text-dark ms-1">{{ $myTicket->subject }}</span>
                        @if($myTicket->status != Status::TICKET_CLOSE)
                        <form action="{{ route('message.close', $myTicket->id) }}" method="POST" class="d-inline ms-2" onsubmit="return confirm('@lang('Clear this message?')');">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-sm py-0 px-2 small">@lang('Clear')</button>
                        </form>
                        @endif
                    </div>
                </div>

                <div class="message-thread">
                    @foreach($messages as $message)
                    <div class="message-block border-bottom py-2 px-3">
                        <p class="message-body mb-1 small">{{ $message->message }}</p>
                        <span class="message-date text-muted" style="font-size:0.75rem;">{{ $message->created_at->format('d M Y, h:i A') }}</span>
                        @if($message->attachments->count() > 0)
                            <div class="mt-1">
                                @foreach($message->attachments as $k => $image)
                                    <a href="{{ route('message.download', encrypt($image->id)) }}" class="small me-2">@include($activeTemplate . 'partials.icon', ['name' => 'file']) @lang('File') {{ ++$k }}</a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    @endforeach
                </div>

                @if($myTicket->status != Status::TICKET_CLOSE)
                <div class="card border-0 shadow-sm mt-2">
                    <div class="card-body py-2 px-3">
                        <form method="post" action="{{ route('message.reply', $myTicket->id) }}" enctype="multipart/form-data">
                            @csrf
                            <textarea name="message" class="form-control form-control-sm" rows="2" placeholder="@lang('Reply...')" required></textarea>
                            <button type="submit" class="btn btn--base btn-sm mt-2">@lang('Reply')</button>
                        </form>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
<x-confirmation-modal class="frontend"/>
@endsection
@push('style')

{{-- inline style moved to critical-storefront.css --}}

@endpush
