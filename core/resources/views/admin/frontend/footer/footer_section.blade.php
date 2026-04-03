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
<style>
    .footer-section-form .form-group { margin-bottom: 0.5rem; }
    .footer-section-form .form-label { font-size: 0.8rem; margin-bottom: 0.2rem; }
    .footer-section-form .form-control, .footer-section-form .form-select { font-size: 0.875rem; padding: 0.35rem 0.5rem; }
    .footer-section-form .form-control-sm { font-size: 0.875rem; }
    .footer-section-form .btn:not(.btn-sm) { padding: 0.35rem 0.75rem; font-size: 0.875rem; }
    .footer-section-form .table { font-size: 0.875rem; }
    .footer-section-form .table thead th { font-size: 0.8rem; padding: 0.35rem 0.5rem; }
    .footer-section-form .table td, .footer-section-form .table th { padding: 0.35rem 0.5rem; }
    .footer-section-form .p-3.bg-light, .footer-section-form .border.rounded.bg-light { padding: 0.5rem !important; }
    .footer-section-form .row.g-2 { margin-bottom: 0; }
    .footer-section-form .row.g-3 { --bs-gutter-y: 0.5rem; }
    .footer-section-form small.text-muted { font-size: 0.75rem; }
    .footer-section-form h6 { font-size: 0.9rem; margin-bottom: 0.35rem; }
    .footer-section-form .alert { padding: 0.5rem 0.75rem; font-size: 0.875rem; }
    .footer-section-form .payment-methods-block .payment-icon-form .row { margin-bottom: 0; }
    .footer-section-form .payment-methods-block .table td, .footer-section-form .payment-methods-block .table th { padding: 0.4rem 0.5rem; vertical-align: middle; }
    .footer-section-form .payment-methods-block .table thead th { font-weight: 600; font-size: 0.75rem; text-transform: none; }
</style>
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
