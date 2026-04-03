@php
    $paymentIconsList = $footerElements ?? collect();
    $editing = $editingPaymentItem ?? null;
    $editId = $editing ? (int) $editing->id : null;
    $editV = (object)[];
    if ($editing && $editing->data_values) {
        $editV = is_array($editing->data_values) ? (object) $editing->data_values : $editing->data_values;
    }
    $sectionSlugForLink = $paymentMethodsSection ?? 'payment-shipping';
    $hiddenId = old('id', $editId ? (string) $editId : '');
@endphp

<div class="payment-methods-block">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
        <span class="ps-section__title">@lang('Payment Method Icons')</span>
        @if($editing)
            <a href="{{ route('admin.frontend.sections.footer.section', ['section' => $sectionSlugForLink]) }}{{ $sectionSlugForLink === 'payment-shipping' ? '#payment-methods' : '' }}" class="btn btn-sm btn-outline-secondary py-0 px-2">@lang('Cancel')</a>
        @endif
    </div>
    <p class="small text-secondary mb-2" style="font-size:0.72rem;line-height:1.35;max-width:40rem">Add one icon per gateway. There is <strong>no limit</strong> on how many you can add—scale to dozens or hundreds as your payment options grow.</p>

    <form method="POST" action="{{ route('admin.frontend.sections.footer.savePaymentIcon') }}" enctype="multipart/form-data" class="payment-icon-form mb-2" id="payment-icon-form">
        @csrf
        <input type="hidden" name="id" id="payment_icon_hidden_id" value="{{ $hiddenId }}">
        <div class="row g-2 align-items-end">
            <div class="col-auto">
                <label class="ps-form__label">@lang('Image')</label>
                <input type="file" name="image" class="form-control form-control-sm py-1" accept=".svg,.png,.webp,.avif,.jpg,.jpeg,.gif,image/svg+xml,image/png,image/webp,image/avif,image/jpeg,image/gif" style="max-width:220px;font-size:0.75rem">
                <p class="text-muted small mt-1 mb-0" style="font-size:0.68rem;line-height:1.35;max-width:34rem">
                    <strong>Formats:</strong> SVG, PNG, WebP, AVIF, JPG, GIF · <strong>max 5 MB</strong> · full quality retained.<br>
                    <strong>Recommended size:</strong> ~240×80 px to 360×120 px (landscape) or SVG for sharp logos in the footer.
                </p>
                @if($editing && !empty($editV->image ?? null))<span class="text-muted" style="font-size:0.7rem">@lang('Current')</span>@endif
            </div>
            <div class="col-auto">
                <label class="ps-form__label">@lang('Title')</label>
                <input type="text" name="title" class="form-control form-control-sm" placeholder="bKash" value="{{ old('title', $editing ? ($editV->title ?? '') : '') }}" style="width:90px">
            </div>
            <div class="col-auto flex-grow-1" style="min-width:0">
                <label class="ps-form__label">@lang('Link URL')</label>
                <input type="text" name="url" class="form-control form-control-sm" placeholder="https://..." value="{{ old('url', $editing ? ($editV->url ?? '') : '') }}">
            </div>
            <div class="col-auto" style="width:52px">
                <label class="ps-form__label">#</label>
                <input type="number" name="display_order" class="form-control form-control-sm text-center" value="{{ old('display_order', $editing ? (int)($editV->display_order ?? 0) : 0) }}" min="0">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn--primary btn-sm" name="submit_payment_icon">{{ $editing ? __('Update') : __('Add') }}</button>
            </div>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-sm table-hover mb-0 align-middle">
            <thead>
                <tr>
                    <th style="width:50px">@lang('Image')</th>
                    <th style="min-width:80px">@lang('Title')</th>
                    <th>@lang('Link URL')</th>
                    <th style="width:44px" class="text-center">#</th>
                    <th style="width:100px" class="text-end">@lang('Action')</th>
                </tr>
            </thead>
            <tbody>
                @forelse($paymentIconsList as $el)
                    @php
                        $dv = $el->data_values ?? (object)[];
                        if (is_array($dv)) $dv = (object)$dv;
                        $img = $dv->image ?? null;
                        $url = $dv->url ?? '';
                        $title = $dv->title ?? '';
                        $order = (int)($dv->display_order ?? 0);
                    @endphp
                    <tr class="{{ $editId && $el->id == $editId ? 'table-primary' : '' }}">
                        <td>@if($img)<img src="{{ getImage('assets/images/frontend/footer/' . $img) }}" alt="" class="rounded" style="width:44px;height:44px;object-fit:contain;background:#f1f5f9">@else<span class="text-muted">—</span>@endif</td>
                        <td>{{ $title ?: '—' }}</td>
                        <td><span class="text-break d-inline-block" style="max-width:180px;font-size:0.75rem">{{ $url ?: '—' }}</span></td>
                        <td class="text-center">{{ $order }}</td>
                        <td class="text-end">
                            <a href="{{ route('admin.frontend.sections.footer.section', ['section' => $sectionSlugForLink, 'edit' => $el->id]) }}{{ $sectionSlugForLink === 'payment-shipping' ? '#payment-methods' : '' }}" class="btn btn-sm btn-outline-primary py-0 px-2">@lang('Edit')</a>
                            <form action="{{ route('admin.frontend.sections.footer.deletePaymentIcon', $el->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __("Remove this icon?") }}');">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2">@lang('Delete')</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-2" style="font-size:0.75rem">@lang('No payment icons yet. Add one above.')</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
