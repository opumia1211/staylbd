@php
    $footerContent = $footerContent ?? null;
    $dv = $footerContent && is_object($footerContent->data_values ?? null) ? $footerContent->data_values : (object)[];
@endphp
<form method="POST" action="{{ route('admin.frontend.sections.footer.saveSection') }}">
    @csrf
    <input type="hidden" name="section" value="footer_content">
    <div class="row g-2">
        <div class="col-12">
            <div class="form-group mb-0">
                <label class="form-label small fw-semibold">@lang('Copyright Text (Bottom Bar)')</label>
                <input type="text" name="copyright_text" class="form-control" value="{{ $dv->copyright_text ?? '' }}" placeholder="@lang('e.g. Copyright © {year} All Right Reserved')">
                <small class="text-muted">@lang('Shows at the bottom of the footer. Use {year} for current year. Leave blank for default:') <code>Copyright © {{ date('Y') }} All Right Reserved</code></small>
            </div>
        </div>
        <div class="col-12 mt-3 pt-2 border-top">
            <span class="text-muted small">@lang('Optional — Newsletter & social titles')</span>
        </div>
        <div class="col-md-6">
            <div class="form-group mb-0">
                <label class="form-label small">@lang('Newsletter Title')</label>
                <input type="text" name="subscribe_title" class="form-control form-control-sm" value="{{ $dv->subscribe_title ?? __('Subscribe to our newsletter') }}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group mb-0">
                <label class="form-label small">@lang('Newsletter Subtitle')</label>
                <input type="text" name="subscribe_subtitle" class="form-control form-control-sm" value="{{ $dv->subscribe_subtitle ?? __('Subscribe for new Offers and updates') }}">
            </div>
        </div>
        <div class="col-12">
            <div class="form-group mb-0">
                <label class="form-label small">@lang('Connect / Social Title')</label>
                <input type="text" name="connect_title" class="form-control form-control-sm" value="{{ $dv->connect_title ?? __('Find Us') }}">
            </div>
        </div>
        <div class="col-12 mt-3 pt-2 border-top">
            <span class="text-muted small fw-semibold d-block mb-2">@lang('Footer — Seller account button')</span>
            <div class="form-group mb-2">
                <div class="form-check form-switch">
                    <input type="checkbox" name="seller_account_enabled" value="1" class="form-check-input" id="seller_account_enabled" {{ (int)($dv->seller_account_enabled ?? 0) === 1 ? 'checked' : '' }}>
                    <label class="form-check-label" for="seller_account_enabled">@lang('Enable seller signup page for the Seller account link')</label>
                </div>
                <small class="text-muted d-block">@lang('When off, the footer Seller account button opens live contact. When on, it opens your custom URL below, or the built-in placeholder page if the URL is empty.')</small>
            </div>
            <div class="form-group mb-0">
                <label class="form-label small">@lang('Seller page URL (optional)')</label>
                <input type="text" name="seller_account_url" class="form-control form-control-sm" value="{{ $dv->seller_account_url ?? '' }}" placeholder="@lang('e.g. https://example.com/sell or seller/apply')">
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn--primary btn-sm mt-3">@lang('Save Newsletter & Copyright')</button>
</form>
