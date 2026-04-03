<p class="text-muted small mb-2">@lang('Upload trust badges (SSL Secure, Buyer Protection, etc.). JPG, PNG, WebP, GIF (max 2MB).')</p>
<form method="POST" action="{{ route('admin.frontend.sections.footer.saveSecurityBadge') }}" enctype="multipart/form-data" class="mb-2 p-2 bg-light rounded">
    @csrf
    <input type="hidden" name="id" id="badge_id">
    <div class="row g-2">
        <div class="col-md-2"><input type="file" name="image" class="form-control form-control-sm" accept=".jpg,.jpeg,.png,.webp,.gif,image/jpeg,image/png,image/webp,image/gif"></div>
        <div class="col-md-2"><input type="text" name="title" class="form-control form-control-sm" placeholder="@lang('Title')"></div>
        <div class="col-md-4"><input type="url" name="url" class="form-control form-control-sm" placeholder="@lang('Verification URL')"></div>
        <div class="col-md-2"><input type="number" name="display_order" class="form-control form-control-sm" value="0"></div>
        <div class="col-md-2"><button type="submit" class="btn btn--primary btn-sm w-100">@lang('Add / Update')</button></div>
    </div>
</form>
<div class="table-responsive">
    <table class="table table--light table-sm mb-0">
        <thead><tr><th>@lang('Image')</th><th>@lang('Title')</th><th>@lang('Order')</th><th>@lang('Action')</th></tr></thead>
        <tbody>
            @forelse($securityBadges as $badge)
                @php $dv = $badge->data_values ?? (object)[]; $img = $dv->image ?? null; @endphp
                <tr>
                    <td>@if($img)<img src="{{ getImage('assets/images/frontend/footer/' . $img) }}" alt="" width="40" height="40" style="object-fit:contain">@else &mdash; @endif</td>
                    <td>{{ $dv->title ?? '—' }}</td>
                    <td>{{ $dv->display_order ?? 0 }}</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-outline--primary edit-badge" data-id="{{ $badge->id }}" data-title="{{ $dv->title ?? '' }}" data-url="{{ $dv->url ?? '' }}" data-order="{{ $dv->display_order ?? 0 }}"><i class="las la-pen"></i></button>
                        <form action="{{ route('admin.frontend.sections.footer.deleteSecurityBadge', $badge->id) }}" method="POST" class="d-inline" onsubmit="return confirm('@lang('Remove this badge?')');">@csrf @method('POST')<button type="submit" class="btn btn-sm btn-outline--danger"><i class="las la-trash"></i></button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-muted small">@lang('No security badges yet.')</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
