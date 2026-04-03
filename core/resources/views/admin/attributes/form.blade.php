@extends('admin.layouts.app')

@section('panel')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="alert alert-info small mb-4">
            <strong>@lang('Tip'):</strong> @lang('Name') = যেমন Size, Color। @lang('Values') = কমা দিয়ে লিখুন, যেমন: S, M, L, XL বা Red, Blue, Green। Type = Select সাধারণ ড্রপডাউনের জন্য।
        </div>
        <div class="card b-radius--10">
            <div class="card-body">
                <form action="{{ $attribute->id ? route('admin.attributes.update', $attribute->id) : route('admin.attributes.store') }}" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">@lang('Name') <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" value="{{ old('name', $attribute->name) }}" placeholder="e.g. Size, Color" required maxlength="100">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">@lang('Slug') <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="slug" value="{{ old('slug', $attribute->slug) }}" placeholder="e.g. size, color" required maxlength="100">
                                <small class="text-muted">@lang('Unique; used in filters and variants.')</small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">@lang('Type') <span class="text-danger">*</span></label>
                                <select name="type" class="form-select" required>
                                    <option value="select" {{ old('type', $attribute->type) === 'select' ? 'selected' : '' }}>@lang('Select (dropdown)')</option>
                                    <option value="color" {{ old('type', $attribute->type) === 'color' ? 'selected' : '' }}>@lang('Color')</option>
                                    <option value="text" {{ old('type', $attribute->type) === 'text' ? 'selected' : '' }}>@lang('Text')</option>
                                    <option value="number" {{ old('type', $attribute->type) === 'number' ? 'selected' : '' }}>@lang('Number')</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">@lang('Sort Order')</label>
                                <input type="number" class="form-control" name="sort_order" value="{{ old('sort_order', $attribute->sort_order ?? 0) }}" min="0">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">@lang('Values')</label>
                        <input type="text" class="form-control" name="values" value="{{ old('values', is_array($attribute->values) ? implode(', ', $attribute->values) : '') }}" placeholder="e.g. S, M, L, XL or Red, Blue, Green">
                        <small class="text-muted">@lang('Comma-separated. For Select/Color type. Leave empty for Text/Number.')</small>
                    </div>
                    <div class="form-group">
                        <label class="form-label">@lang('Status')</label>
                        <select name="status" class="form-select">
                            <option value="1" {{ old('status', $attribute->status ?? 1) == 1 ? 'selected' : '' }}>@lang('Active')</option>
                            <option value="0" {{ old('status', $attribute->status) == 0 ? 'selected' : '' }}>@lang('Inactive')</option>
                        </select>
                    </div>
                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn--primary">@lang($attribute->id ? 'Update' : 'Create')</button>
                        <a href="{{ route('admin.attributes.index') }}" class="btn btn--dark">@lang('Cancel')</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var nameEl = document.querySelector('input[name="name"]');
    var slugEl = document.querySelector('input[name="slug"]');
    if (nameEl && slugEl && !slugEl.value) {
        nameEl.addEventListener('input', function() {
            var v = this.value.trim().toLowerCase()
                .replace(/\s+/g, '-').replace(/[^a-z0-9\-]/g, '');
            if (v && !slugEl.dataset.manual) slugEl.value = v;
        });
        slugEl.addEventListener('input', function() { this.dataset.manual = '1'; });
    }
});
</script>
@endpush
