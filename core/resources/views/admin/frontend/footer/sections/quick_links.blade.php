<p class="text-muted small mb-2">@lang('Add footer navigation links (About Us, Contact, FAQ, etc.).')</p>
<form method="POST" action="{{ route('admin.frontend.sections.footer.saveQuickLink') }}" class="mb-2 p-2 bg-light rounded">
    @csrf
    <input type="hidden" name="id" id="quick_link_id">
    <div class="row g-2">
        <div class="col-md-4"><input type="text" name="title" class="form-control form-control-sm" placeholder="@lang('Title')" required></div>
        <div class="col-md-4"><input type="text" name="url" class="form-control form-control-sm" placeholder="@lang('URL')"></div>
        <div class="col-md-2"><input type="number" name="display_order" class="form-control form-control-sm" placeholder="@lang('Order')" value="0"></div>
        <div class="col-md-2"><button type="submit" class="btn btn--primary btn-sm w-100">@lang('Add / Update')</button></div>
    </div>
</form>
<div class="table-responsive">
    <table class="table table--light table-sm mb-0">
        <thead><tr><th>@lang('Order')</th><th>@lang('Title')</th><th>@lang('URL')</th><th>@lang('Action')</th></tr></thead>
        <tbody>
            @forelse($quickLinks as $link)
                @php $dv = $link->data_values ?? (object)[]; @endphp
                <tr>
                    <td>{{ $dv->display_order ?? 0 }}</td>
                    <td>{{ __($dv->title ?? '') }}</td>
                    <td><small>{{ $dv->url ?? '#' }}</small></td>
                    <td>
                        <button type="button" class="btn btn-sm btn-outline--primary edit-quick-link" data-id="{{ $link->id }}" data-title="{{ $dv->title ?? '' }}" data-url="{{ $dv->url ?? '' }}" data-order="{{ $dv->display_order ?? 0 }}"><i class="las la-pen"></i></button>
                        <form action="{{ route('admin.frontend.sections.footer.deleteQuickLink', $link->id) }}" method="POST" class="d-inline" onsubmit="return confirm('@lang('Remove this link?')');">@csrf @method('POST')<button type="submit" class="btn btn-sm btn-outline--danger"><i class="las la-trash"></i></button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-muted small">@lang('No quick links yet.')</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
