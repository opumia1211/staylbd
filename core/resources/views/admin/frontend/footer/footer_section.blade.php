@extends('admin.layouts.app')
@section('panel')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-2 px-3">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                    <div class="d-flex flex-wrap align-items-center gap-2 small">
                        <a href="{{ route('admin.frontend.sections.footer') }}" class="text-muted"><i class="las la-shoe-prints"></i></a>
                        <span class="text-muted">/</span>
                        <span class="fw-semibold text-dark">{{ $sectionTitle }}</span>
                    </div>
                    <a href="{{ route('admin.frontend.sections.footer') }}" class="btn btn-sm btn-outline--primary"><i class="las la-arrow-left me-1"></i> @lang('Back to list')</a>
                </div>
                <div class="footer-section-form">
                    @include('admin.frontend.footer.sections.' . str_replace('-', '_', $sectionSlug))
                </div>
            </div>
        </div>
    </div>
</div>

@push('style')

{{-- inline style moved to critical-admin.css --}}

@endpush

@push('script')
<script>
(function($){
    $(function(){
        $('.edit-quick-link').on('click', function(){
            var id = $(this).data('id'), title = $(this).data('title'), url = $(this).data('url'), order = $(this).data('order');
            $('#quick_link_id').val(id);
            $('input[name="title"]').val(title);
            $('input[name="url"]').val(url);
            $('input[name="display_order"]').val(order);
        });
        $('.edit-badge').on('click', function(){
            var id = $(this).data('id'), title = $(this).data('title'), url = $(this).data('url'), order = $(this).data('order');
            $('#badge_id').val(id);
            $('input[name="title"]').val(title);
            $('input[name="url"]').val(url);
            $('input[name="display_order"]').val(order);
        });
        $('.edit-custom-ad').on('click', function(){
            var id = $(this).data('id'), title = $(this).data('title'), url = $(this).data('url'), order = $(this).data('order');
            $('#custom_ad_id').val(id);
            $('input[name="title"]').val(title || '');
            $('input[name="url"]').val(url || '');
            $('input[name="display_order"]').val(order || 0);
        });
    });
})(jQuery);
</script>
@endpush
@endsection
