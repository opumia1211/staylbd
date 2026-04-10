@extends('admin.layouts.app')
@section('panel')
@push('style')

{{-- inline style moved to critical-admin.css --}}

@endpush

<div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent border-0 d-flex flex-wrap justify-content-between align-items-center gap-2 py-3">
        <h5 class="mb-0 fw-bold">{{ $pageTitle }}</h5>
        <a href="{{ route('admin.frontend.sections.homepageAds.create') }}" class="btn btn--primary btn-sm">
            <i class="las la-plus"></i> @lang('Add ad')
        </a>
    </div>
    <div class="card-body p-0">
        @if($ads->isEmpty())
            <div class="text-center py-5 text-muted">@lang('No ads yet')</div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 hp-ads-table">
                    <thead>
                        <tr>
                            <th class="ps-3">@lang('Preview')</th>
                            <th>@lang('Title')</th>
                            <th>@lang('Type')</th>
                            <th>@lang('Size')</th>
                            <th>@lang('Position')</th>
                            <th class="text-center">@lang('Active')</th>
                            <th class="text-end pe-3">@lang('Actions')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ads as $ad)
                            <tr class="{{ (int) session('hp_highlight_ad') === (int) $ad->id ? 'table-success bg-opacity-25' : '' }}">
                                <td class="ps-3">
                                    <img src="{{ $ad->imageUrl() ?: $ad->external_url }}" alt="ad" style="width: 110px; height: 30px; object-fit: cover; border: 0; border-radius: 0;">
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $ad->admin_title }}</div>
                                    <div class="small text-muted">{{ $ad->advertiser_name ?? '—' }}</div>
                                </td>
                                <td class="text-muted">{{ $ad->source_type ?? 'upload' }}</td>
                                <td class="text-muted">
                                    {{ $ad->size_type === 'custom' ? (($ad->custom_width ?: 'auto') . ' × ' . ($ad->custom_height ?: 'auto')) : $ad->width_mode }}
                                </td>
                                <td class="text-muted">{{ $ad->position }} / {{ $ad->side ?: '—' }}</td>
                                <td class="text-center">
                                    <div class="hp-ads-active-wrap">
                                        <div class="form-check form-switch m-0">
                                            <input class="form-check-input js-hp-ad-toggle"
                                                   type="checkbox"
                                                   role="switch"
                                                   data-id="{{ $ad->id }}"
                                                   data-url="{{ route('admin.frontend.sections.homepageAds.toggleActive', $ad->id) }}"
                                                   data-token="{{ csrf_token() }}"
                                                   {{ $ad->is_active ? 'checked' : '' }}>
                                        </div>
                                        <span class="hp-ads-active-text {{ $ad->is_active ? 'on' : 'off' }}" data-active-text-for="{{ $ad->id }}">
                                            {{ $ad->is_active ? __('On') : __('Off') }}
                                        </span>
                                        <noscript>
                                            <form action="{{ route('admin.frontend.sections.homepageAds.toggleActive', $ad->id) }}" method="post" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="is_active" value="{{ $ad->is_active ? 0 : 1 }}">
                                                <button type="submit" class="btn btn-sm btn-outline-secondary">@lang('Save')</button>
                                            </form>
                                        </noscript>
                                    </div>
                                </td>
                                <td class="text-end pe-3 text-nowrap">
                                    <a href="{{ route('admin.frontend.sections.homepageAds.edit', $ad->id) }}" class="btn btn-sm btn-outline--primary">@lang('Edit')</a>
                                    <form action="{{ route('admin.frontend.sections.homepageAds.destroy', $ad->id) }}" method="post" class="d-inline" onsubmit="return confirm('@lang('Delete this ad?')');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline--danger">@lang('Delete')</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    <div class="card-footer bg-transparent border-0 py-3">
        <div class="small text-muted">
            @lang('To show an ad between homepage blocks, add it into Block order and click Save layout.')
            <a href="{{ route('admin.frontend.sections.homepageCustomRows') }}" class="ms-1">@lang('Open Block order')</a>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
(function () {
  function setRowState(id, isActive) {
    var el = document.querySelector('[data-active-text-for="' + id + '"]');
    if (!el) return;
    el.textContent = isActive ? 'On' : 'Off';
    el.classList.toggle('on', !!isActive);
    el.classList.toggle('off', !isActive);
  }

  document.querySelectorAll('.js-hp-ad-toggle').forEach(function (toggle) {
    toggle.addEventListener('change', function () {
      var id = this.getAttribute('data-id');
      var url = this.getAttribute('data-url');
      var token = this.getAttribute('data-token');
      var isActive = this.checked ? 1 : 0;
      var self = this;

      self.disabled = true;
      fetch(url, {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
          'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
          'X-CSRF-TOKEN': token
        },
        body: 'is_active=' + encodeURIComponent(isActive)
      })
        .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, data: d }; }); })
        .then(function (res) {
          if (res.ok && res.data && res.data.success) {
            setRowState(id, res.data.is_active);
          } else {
            self.checked = !self.checked;
            setRowState(id, self.checked);
            alert((res.data && res.data.message) ? res.data.message : 'Failed to update.');
          }
        })
        .catch(function () {
          self.checked = !self.checked;
          setRowState(id, self.checked);
          alert('Request failed.');
        })
        .finally(function () {
          self.disabled = false;
        });
    });
  });
})();
</script>
@endpush

