{{-- /contactlive – ভাসমান লাইভ চ্যাট প্যানেল অটো ওপেন; মেসেজ অ্যাডমিন টিকেটে /contact এর মত একই জায়গায় --}}
@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="py-5" style="min-height: 50vh; background: linear-gradient(180deg, #f8f9fa 0%, #fff 100%);">
        <div class="container text-center py-4">
            <h4 class="text--base mb-2">@lang('Live Chat')</h4>
            <p class="text-muted small mb-3">@lang('Opening the message panel for you...')</p>
            <p class="small text-muted">@lang('If the panel did not open,') <button type="button" class="btn btn-primary btn-sm js-contact-panel-open" onclick="if(window.openContactPanel){window.openContactPanel();} return false;">@lang('click here')</button>.</p>
        </div>
    </section>
    @push('script')
    <script>
    (function(){
        function open() {
            if (window.openContactPanel) { window.openContactPanel(); return; }
            setTimeout(open, 80);
        }
        if (document.readyState === 'complete') setTimeout(open, 300);
        else document.addEventListener('DOMContentLoaded', function() { setTimeout(open, 300); });
    })();
    </script>
    @endpush
@endsection
