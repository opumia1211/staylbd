@extends('admin.layouts.app')

@section('panel')
<div class="category-attributes-panel">
    {{-- ব্যাখ্যা: এই পেজে কী করা যায় --}}
    <div class="card b-radius--10 mb-4 border-primary">
        <div class="card-header bg--primary text-white py-2">
            <h6 class="mb-0"><i class="las la-link"></i> @lang('Category Attributes') – কী এবং কীভাবে ব্যবহার করবেন</h6>
        </div>
        <div class="card-body small">
            <p class="mb-2"><strong>এই পেজের কাজ:</strong> আপনি <strong>Product Attributes</strong> পেজে যেসব অ্যাট্রিবিউট বানিয়েছেন (যেমন Size, Color), সেগুলো <strong>কোন কোন ক্যাটাগরিতে</strong> ব্যবহার হবে সেটা এখানে সেট করবেন।</p>
            <ul class="mb-2">
                <li><strong>Use:</strong> এই ক্যাটাগরির জন্য অ্যাট্রিবিউট ব্যবহার করলে টিক দিন।</li>
                <li><strong>Required:</strong> প্রোডাক্ট এডিটে এই ফিল্ড বাধ্যতামূলক করতে চাইলে টিক দিন।</li>
                <li><strong>For variant:</strong> প্রোডাক্ট ভেরিয়েন্ট (যেমন সাইজ/কালার অনুযায়ী প্রাইস বা স্টক) বানাতে ব্যবহার করলে টিক দিন।</li>
                <li><strong>Order:</strong> কত নম্বরে দেখাবে (কম সংখ্যা = আগে)।</li>
            </ul>
            <p class="mb-0"><strong>উদাহরণ:</strong> Fashion ক্যাটাগরিতে Size ও Color চালু করুন; Electronics এ Storage, RAM চালু করুন। আগে <a href="{{ route('admin.attributes.index') }}" class="text--base fw-bold">Product Attributes</a> থেকে অ্যাট্রিবিউট তৈরি করুন।</p>
        </div>
    </div>

    @if($allAttributes->isEmpty())
    <div class="card b-radius--10 border-warning">
        <div class="card-body text-center py-5">
            <i class="las la-list font-size--48px text-warning mb-3 d-block"></i>
            <h6>@lang('No attributes yet')</h6>
            <p class="text-muted mb-0">@lang('First create attributes (Size, Color, etc.) from Product Attributes page, then assign them to categories here.')</p>
            <a href="{{ route('admin.attributes.index') }}" class="btn btn--primary mt-3"><i class="las la-plus"></i> @lang('Go to Product Attributes')</a>
        </div>
    </div>
    @else
        @foreach($categories as $category)
        <div class="card b-radius--10 mb-4 shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2 py-3">
                <h6 class="mb-0 d-flex align-items-center gap-2">
                    <i class="las la-folder text--primary"></i>
                    {{ __($category->name) }}
                </h6>
                <a href="{{ route('admin.category.index') }}" class="btn btn-sm btn--outline-primary">@lang('Manage Categories')</a>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.category.attributes.update') }}" method="post" class="category-attributes-form">
                    @csrf
                    <input type="hidden" name="category_id" value="{{ $category->id }}">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;" class="text-center">@lang('Use')</th>
                                    <th>@lang('Attribute')</th>
                                    <th class="text-center" style="width: 110px;">@lang('Required')</th>
                                    <th class="text-center" style="width: 110px;">@lang('For variant')</th>
                                    <th class="text-center" style="width: 90px;">@lang('Order')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $assigned = $category->attributes->keyBy('id'); @endphp
                                @foreach($allAttributes as $attr)
                                @php
                                    $pivot = $assigned->get($attr->id)?->pivot;
                                    $checked = $assigned->has($attr->id);
                                @endphp
                                <tr>
                                    <td class="text-center">
                                        <input type="checkbox" class="form-check-input attr-check" name="attribute_ids[]" value="{{ $attr->id }}" {{ $checked ? 'checked' : '' }} id="use_{{ $category->id }}_{{ $attr->id }}">
                                    </td>
                                    <td>
                                        <label for="use_{{ $category->id }}_{{ $attr->id }}" class="mb-0 cursor-pointer">
                                            <strong>{{ $attr->name }}</strong>
                                            <code class="small ms-1">{{ $attr->slug }}</code>
                                            @if(!in_array($attr->type, ['select', 'color']))
                                                <span class="badge badge--info small">{{ $attr->type }}</span>
                                            @endif
                                        </label>
                                    </td>
                                    <td class="text-center">
                                        <input type="hidden" name="pivot[{{ $attr->id }}][is_required]" value="0">
                                        <input type="checkbox" class="form-check-input" name="pivot[{{ $attr->id }}][is_required]" value="1" {{ ($pivot && $pivot->is_required) ? 'checked' : '' }}>
                                    </td>
                                    <td class="text-center">
                                        <input type="hidden" name="pivot[{{ $attr->id }}][is_variant]" value="0">
                                        <input type="checkbox" class="form-check-input" name="pivot[{{ $attr->id }}][is_variant]" value="1" {{ ($pivot && $pivot->is_variant) ? 'checked' : '' }}>
                                    </td>
                                    <td class="text-center">
                                        <input type="number" class="form-control form-control-sm d-inline-block" name="pivot[{{ $attr->id }}][sort_order]" value="{{ $pivot ? (int)$pivot->sort_order : 0 }}" min="0" style="width: 70px;">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn--primary btn-sm"><i class="las la-save"></i> @lang('Save for this category')</button>
                    </div>
                </form>
            </div>
        </div>
        @endforeach

        @if($categories->isEmpty())
        <div class="card b-radius--10">
            <div class="card-body text-center py-5 text-muted">
                <i class="las la-folder-open font-size--48px opacity-50 d-block mb-2"></i>
                @lang('No categories found.') <a href="{{ route('admin.category.index') }}">@lang('Add categories first')</a>
            </div>
        </div>
        @endif
    @endif
</div>
@endsection

@push('breadcrumb-plugins')
<a href="{{ route('admin.attributes.index') }}" class="btn btn--primary btn-sm">
    <i class="las la-list"></i> @lang('Product Attributes')
</a>
@endpush

@push('script')
<script>
document.querySelectorAll('.category-attributes-form').forEach(function(form) {
    form.addEventListener('submit', function() {
        var checkboxes = form.querySelectorAll('.attr-check');
        checkboxes.forEach(function(cb) {
            if (!cb.checked) {
                var id = cb.value;
                form.querySelectorAll('input[name^="pivot[' + id + ']"]').forEach(function(inp) {
                    inp.disabled = true;
                });
            }
        });
    });
});
</script>
@endpush

@push('style')

{{-- inline style moved to critical-admin.css --}}

@endpush
