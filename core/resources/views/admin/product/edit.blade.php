@extends('admin.layouts.app')

@push('style')

{{-- inline style moved to critical-admin.css --}}

@endpush

@section('panel')
    <div class="row product-edit-page">
        <div class="col-md-12">
            <div class="mb-3">
                <a href="{{ route('admin.product.index') }}" class="btn btn-outline--primary btn-sm">
                    <i class="las la-arrow-left me-1"></i> @lang('Back to Product List')
                </a>
            </div>
            <form id="productEditForm" action="{{ route('admin.product.store', $product->id) }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="card mb-3">
                    <div class="card-header">@lang('Product Information')</div>
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-3">
                                <label>@lang('Name')</label>
                                <input type="text" name="name" class="form-control" value="{{ $product->name }}" required>
                            </div>

                            <div class="form-group col-md-3">
                                <label>@lang('Brands')</label>
                                <select class="form-control" name="brand_id" required>
                                    <option value="" selected disabled>@lang('Select One')</option>
                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand->id }}" @selected(@$product->brand_id == $brand->id)>{{ __($brand->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <label>@lang('Category')</label>
                                <select name="category_id" class="form-control" required id="editCategoryId">
                                    <option selected disabled>@lang('Select One')</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" @selected(@$product->category_id == $category->id)>{{ __($category->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <label>@lang('Subcategory')</label>
                                <select name="subcategory_id" class="form-control" required>
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <label>@lang('Product SKU')</label>
                                <input type="text" name="product_sku" class="form-control" value="{{ $product->product_sku }}" required />
                            </div>
                            <div class="form-group col-md-3">
                                <label>@lang('Stock Quantity')</label>
                                <input type="number" name="quantity" min="0" class="form-control" value="{{ $product->quantity }}" required />
                            </div>
                            <div class="form-group col-md-3">
                                <label>@lang('Price')</label>
                                <div class="input-group">
                                    <input type="number" name="price" step="any" min="0" class="form-control" value="{{ old('price', getAmount(@$product->price)) }}" required />
                                    <span class="input-group-text"> {{ __($general->cur_text) }} </span>
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label>@lang('Discount') </label>
                                <div class="input-group">
                                    <input type="number" step="any" class="form-control" name="discount" min="0" value="{{ getAmount($product->discount) }}">
                                    <select name="discount_type" class="input-group-text">
                                        <option value="1" @selected($product->discount_type == 1)>{{ __($general->cur_text) }}</option>
                                        <option value="2" @selected($product->discount_type == 2)>@lang('%')</option>
                                    </select>
                                </div>
                            </div>
                            @if(\Schema::hasColumn('products', 'delivery_time'))
                            <div class="form-group col-md-3">
                                <label>@lang('Estimated Delivery Days')</label>
                                <input type="text" name="delivery_time" class="form-control" value="{{ old('delivery_time', $product->delivery_time ?? '') }}" placeholder="@lang('e.g. 2-5 days, 3-7 business days')" maxlength="100">
                            </div>
                            @endif
                            @if(\Schema::hasColumn('products', 'delivery_type'))
                            <div class="form-group col-md-3">
                                <label>@lang('Delivery Type')</label>
                                <select name="delivery_type" class="form-control" id="editDeliveryType">
                                    <option value="free" @selected(($product->delivery_type ?? 'free') === 'free')>@lang('Free Delivery')</option>
                                    <option value="paid" @selected(($product->delivery_type ?? '') === 'paid')>@lang('Paid Delivery')</option>
                                </select>
                            </div>
                            <div class="form-group col-md-3" id="editDeliveryChargeWrap" style="{{ ($product->delivery_type ?? 'free') === 'paid' ? '' : 'display:none;' }}">
                                <label>@lang('Delivery Charge')</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" name="delivery_charge" class="form-control" value="{{ getAmount($product->delivery_charge ?? 0) }}" min="0">
                                    <span class="input-group-text">{{ $general->cur_sym ?? '৳' }}</span>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card mb-3">
                    <div class="card-header">@lang('Make Product Digital')</div>
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label>@lang('Is Digital')</label>
                                <select name="digital_item" class="form-control" required>
                                    <option value="0" @selected($product->digital_item == 0)>@lang('No')</option>
                                    <option value="1" @selected($product->digital_item == 1)>@lang('Yes')</option>
                                </select>
                            </div>
                            @if(\Illuminate\Support\Facades\Schema::hasColumn('products', 'cod_disabled'))
                            <div class="form-group col-md-4 d-flex align-items-end">
                                <div class="form-check form-switch mb-2">
                                    <input type="hidden" name="cod_disabled" value="0">
                                    <input type="checkbox" class="form-check-input" name="cod_disabled" value="1" id="cod_disabled" @checked($product->cod_disabled ?? false)>
                                    <label class="form-check-label" for="cod_disabled">@lang('Disable COD for this product')</label>
                                </div>
                            </div>
                            @endif
                            <div class="form-group col-md-4 typeDiv">
                                @if ($product->digital_item)
                                    <label>@lang('Select Type')</label>
                                    <select name="file_type" class="form-control" required>
                                        <option value="1" @selected($product->file_type == 1)>@lang('File')</option>
                                        <option value="2" @selected($product->file_type == 2)>@lang('Link')</option>
                                    </select>
                                @endif
                            </div>
                            <div class="form-group col-md-4 fileLinkDiv">
                                @if ($product->digital_item)
                                    @if ($product->file_type == 1 && $product->file)
                                        <label class="required">@lang('Upload File')</label>
                                        @if ($product->digital_item)
                                            <a href="{{ route('download', [$product->id, $product->file]) }}" class="mr-3 text--primary">
                                                <i class="las la-file"></i> @lang('Download File')
                                            </a>
                                        @endif

                                        <div class="file-upload-wrapper" data-text="@lang('Upload Your File')">
                                            <input type="file" name="file" id="inputAttachments" accept=".pdf, .docx, .txt, .zip, .xlx, .csv, .ai, .psd, .pptx" class="file-upload-field">
                                        </div>
                                        <small class="mt-2">
                                            @lang('Supported files'): @lang('.pdf'), @lang('.docx'), @lang('.txt'), @lang('.zip'), @lang('.xlx'), @lang('.csv'), @lang('.ai'), @lang('.psd'), @lang('.pptx')
                                        </small>
                                    @elseif ($product->file_type == 2 && $product->link)
                                        <label class="required">@lang('Link')</label>
                                        <input type="url" name="link" class="form-control" value="{{ $product->link }}" required />
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mb-3 border-info">
                    <div class="card-header bg-info bg-opacity-10">
                        <i class="las la-eye me-1"></i> @lang('Visibility & Homepage Badges')
                    </div>
                    <div class="card-body">
                        <div class="alert alert-light border small mb-3 mb-0">
                            <strong class="d-block mb-1"><i class="las la-link me-1"></i> @lang('Admin toggle') → @lang('Homepage section')</strong>
                            <ul class="mb-2 ps-3">
                                <li><strong>@lang('Today Deal')</strong> → <em>@lang('Quick Deals')</em> (@lang('horizontal strip under categories'))</li>
                                <li><strong>@lang('Hot Deal')</strong> → <em>@lang('Hot Deals')</em></li>
                                <li><strong>@lang('Featured')</strong> → <em>@lang('Featured Products')</em></li>
                                <li><strong>@lang('Trending Now')</strong> → <em>@lang('Trending Now')</em></li>
                            </ul>
                            <p class="mb-1 small text-muted"><strong>@lang('New Arrivals')</strong>: @lang('Newest products not marked Featured/Hot/Today — automatic.')</p>
                            <p class="mb-1 small text-muted"><strong>@lang('Best Selling')</strong> / <strong>@lang('Recommended For You')</strong>: @lang('Based on orders (sale count) and popularity — no separate checkbox.')</p>
                            <p class="mb-0 small text-muted"><strong>@lang('Category')</strong> @lang('on home'): @lang('Manage from') <a href="{{ route('admin.category.index') }}" target="_blank">@lang('Categories')</a> (@lang('active categories with image show in Category row').)</p>
                        </div>
                        <p class="text-muted small mb-3 mt-2">@lang('Only one of Featured / Hot Deal / Today Deal at a time. Trending can be ON together with one of them.')</p>
                        @if(\Illuminate\Support\Facades\Schema::hasColumn('products', 'home_section_override'))
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-medium">@lang('Force show in an auto homepage row') <span class="text-muted small">(@lang('optional'))</span></label>
                                <select name="home_section_override" class="form-control">
                                    <option value="" @selected(empty($product->home_section_override))>@lang('No override (normal)')</option>
                                    <option value="new_arrivals" @selected(($product->home_section_override ?? '') === 'new_arrivals')>@lang('New Arrivals')</option>
                                    <option value="best_selling" @selected(($product->home_section_override ?? '') === 'best_selling')>@lang('Best Selling')</option>
                                    <option value="recommended" @selected(($product->home_section_override ?? '') === 'recommended')>@lang('Recommended For You')</option>
                                    <option value="trending" @selected(($product->home_section_override ?? '') === 'trending')>@lang('Trending Now')</option>
                                </select>
                                <small class="text-muted d-block mt-1">@lang('This controls which homepage line shows this product, and helps prevent duplicates across lines.')</small>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-medium">@lang('Priority / Rank')</label>
                                <input type="number" name="home_section_rank" class="form-control" value="{{ old('home_section_rank', $product->home_section_rank ?? 0) }}" min="0" max="1000000">
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="home_exclude_from_auto" value="0">
                                    <input class="form-check-input" type="checkbox" name="home_exclude_from_auto" value="1" id="edit_exclude_auto" @checked(old('home_exclude_from_auto', $product->home_exclude_from_auto ?? 0))>
                                    <label class="form-check-label" for="edit_exclude_auto">@lang('Hide from other auto rows')</label>
                                </div>
                            </div>
                        </div>
                        @endif
                        <div class="row g-3">
                            <div class="col-md-6 col-lg-3">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="featured_product" value="0">
                                    <input class="form-check-input" type="checkbox" name="featured_product" value="1" id="edit_featured" @checked($product->featured_product ?? 0)>
                                    <label class="form-check-label" for="edit_featured"><i class="las la-star text-info me-1"></i> @lang('Featured')</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="hot_deals" value="0">
                                    <input class="form-check-input" type="checkbox" name="hot_deals" value="1" id="edit_hot_deals" @checked($product->hot_deals ?? 0)>
                                    <label class="form-check-label" for="edit_hot_deals"><i class="las la-fire text-warning me-1"></i> @lang('Hot Deal')</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="today_deals" value="0">
                                    <input class="form-check-input" type="checkbox" name="today_deals" value="1" id="edit_today_deals" @checked($product->today_deals ?? 0)>
                                    <label class="form-check-label" for="edit_today_deals"><i class="las la-clock text-danger me-1"></i> @lang('Today Deal')</label>
                                </div>
                            </div>
                            @if(\Illuminate\Support\Facades\Schema::hasColumn('products', 'trending_now'))
                            <div class="col-md-6 col-lg-3">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="trending_now" value="0">
                                    <input class="form-check-input" type="checkbox" name="trending_now" value="1" id="edit_trending_now" @checked($product->trending_now ?? 0)>
                                    <label class="form-check-label" for="edit_trending_now"><i class="las la-fire-alt text-success me-1"></i> @lang('Trending Now')</label>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card mb-3">
                    <div class="card-header">
                        @lang('Product Details')
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>@lang('Summary')</label>
                            <textarea name="summary" class="form-control" cols="2" rows="5" required>{{ $product->summary }}</textarea>
                        </div>
                        <div class="form-group">
                            <label>@lang('Key Features') <span class="text-muted small">(প্রোডাক্ট ভিউ পেজে দেখাবে)</span></label>
                            <textarea name="key_features" class="form-control" cols="2" rows="5" placeholder="@lang('Product key features')">{{ $product->key_features ?? '' }}</textarea>
                        </div>
                        <div class="form-group">
                            <label>@lang('Description')</label>
                            <textarea id="productDescription" rows="5" class="form-control" name="description" data-product-edit-description>{{ $product->description }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="card border--primary mt-3">
                        <div class="card-header bg--primary d-flex justify-content-between">
                            <h5 class="text-white">@lang('Product Specificaiton')</h5>
                            <button type="button" class="btn btn-sm btn-outline-light float-end addFeatureData"> <i class="la la-fw la-plus"></i>@lang('Add New')</button>
                        </div>
                        <div class="card-body">
                            <div class="row addedFeature">
                                @if ($product->features)
                                    @foreach ($product->features as $freature)
                                        @php $featureIndex = $loop->index; @endphp
                                        <div class="col-md-12 service-data">
                                            <div class="row gy-3 ">
                                                <div class="col-md-6">
                                                    <input name="features[{{$loop->iteration}}][title]" class="form-control" type="text" value="{{@$freature['title'] }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="input-group mb-3">
                                                        <input type="text" class="form-control" name="features[{{ $loop->iteration }}][description]" value="{{ @$freature['description'] }}">
                                                        <button type="button" class="input-group-text btn btn--danger removeServiceBtn">
                                                            <i class="las la-times"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-3 border-info">
                    <div class="card-header bg-info bg-opacity-10">
                        <i class="las la-search me-1"></i> @lang('SEO & Metadata')
                    </div>
                    <div class="card-body">
                        <p class="text-muted small mb-3">@lang('Customize title and description for search engines and social sharing. Leave blank to use product name and summary.')</p>
                        <div class="row g-3">
                            <div class="form-group col-md-12">
                                <label>@lang('Meta Title')</label>
                                <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title', $product->meta_title ?? '') }}" placeholder="@lang('Product name + brand for SEO')" maxlength="255">
                            </div>
                            <div class="form-group col-md-12">
                                <label>@lang('Meta Description')</label>
                                <textarea name="meta_description" class="form-control" rows="2" placeholder="@lang('Short description for search results')" maxlength="500">{{ old('meta_description', $product->meta_description ?? '') }}</textarea>
                            </div>
                            <div class="form-group col-md-12">
                                <label>@lang('Meta Keywords') <span class="text-muted small">(@lang('comma separated'))</span></label>
                                <input type="text" name="meta_keywords" class="form-control" value="{{ is_array($product->meta_keywords ?? null) ? implode(', ', $product->meta_keywords) : ($product->meta_keywords ?? '') }}" placeholder="@lang('e.g. t-shirt, cotton, summer')">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">@lang('Image Section')</div>
                    <div class="card-body">
                        <div class="image-uploader-wrapper">
                            <div class="profile-uploader">
                                <label class="form-group">@lang('Main Image') :</label>
                                <div class="payment-method-item">
                                    <div class="payment-method-header d-flex flex-wrap">
                                        <div class="thumb">
                                            <div class="avatar-preview">
                                                <div class="profilePicPreview" style="background-image: url('{{ $product->imageShow() }}')"></div>
                                            </div>
                                            <div class="avatar-edit">
                                                <input type="file" name="image" class="profilePicUpload" id="image" accept=".png, .jpg, .jpeg">
                                                <label for="image" class="bg--primary"><i class="la la-pencil"></i></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="gallery-uploader">
                                <label class="form-label">@lang('Gallery Image') :</label>
                                <div class="input-field">
                                    <div class="input-images"></div>
                                    <small class="form-text text-muted">
                                        <i class="las la-info-circle"></i> @lang('You can only upload a maximum of 6 images')</label>
                                    </small>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn--primary w-100 h-45 mt-2">@lang('Update')</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('script-lib')
    <script src="{{ asset('assets/admin/js/image-uploader.min.js') }}"></script>
@endpush

{{-- Single category→subcategories map (no heavy data on each option) --}}
<script>
    window.__categorySubcategoriesMap = @json($categorySubcategoriesMap ?? []);
</script>

@push('script')
    <script>
        (function($) {
            "use strict";

            var oldSubcategory = "{{ $product->subcategory_id }}";
            var featured = 2;
            @if ($product->features && isset($featureIndex))
                featured = 2 + parseInt("{{ $featureIndex }}", 10);
            @endif

            var preloaded = @json($galleries ?? []);

            function fillSubcategoryDropdown(categoryId) {
                var map = window.__categorySubcategoriesMap || {};
                var subs = map[categoryId] || [];
                var html = '<option disabled selected>@lang("Select one")</option>';
                subs.forEach(function(sub) {
                    var sel = (oldSubcategory == sub.id) ? ' selected' : '';
                    html += '<option value="' + sub.id + '"' + sel + '>' + (sub.name || '') + '</option>';
                });
                $('[name=subcategory_id]').html(html);
            }

            $('#editCategoryId').on('change', function() {
                fillSubcategoryDropdown($(this).val());
            });
            if ($('#editCategoryId').val()) {
                fillSubcategoryDropdown($('#editCategoryId').val());
            }

            function initImageUploader() {
                if (typeof $.fn.imageUploader !== 'function') return;
                $('.input-images').imageUploader({
                    preloaded: preloaded,
                    imagesInputName: 'gallery',
                    preloadedInputName: 'old',
                    maxFiles: 6
                });
            }

            if (typeof requestIdleCallback !== 'undefined') {
                requestIdleCallback(initImageUploader, { timeout: 1500 });
            } else {
                setTimeout(initImageUploader, 100);
            }

            $(document).on('input', 'input[name="gallery[]"]', function() {
                var fileUpload = $("input[type='file']");
                if (fileUpload.get(0) && parseInt(fileUpload.get(0).files.length, 10) > 6) {
                    $('#errorModal').modal('show');
                }
            });

            let linkHtml = `<label class="required">@lang('Link')</label>
                            <input type="url" name="link" class="form-control" required />`;

            $('[name=digital_item]').on('change', function() {
                let value = $(this).val();
                let html;

                if (value == 1) {
                    html = `<label class="required">@lang('Select Type')</label>
                            <select name="file_type" class="form-control" required>
                                <option value="1">@lang('File')</option>
                                <option value="2" selected>@lang('Link')</option>
                            </select>`;
                    $('.fileLinkDiv').html(linkHtml);
                } else {
                    html = ``;
                    $('.fileLinkDiv').empty();
                }

                $('.typeDiv').html(html);
            });

            $(document).on('change', '[name=file_type]', function() {
                let value = $(this).val();
                let html;

                if (value == 1) {
                    html = `<label class="required">@lang('Upload File')</label>
                            <div class="file-upload-wrapper" data-text="@lang('Upload Your File')">
                                <input type="file" name="file" id="inputAttachments" accept=".pdf, .docx, .txt, .zip, .xlx, .csv, .ai, .psd, .pptx" class="file-upload-field" required/>
                            </div>
                            <small class="mt-2">
                                @lang('Supported files'): @lang('.pdf'), @lang('.docx'), @lang('.txt'), @lang('.zip'), @lang('.xlx'), @lang('.csv'), @lang('.ai'), @lang('.psd'), @lang('.pptx')
                            </small>`;
                } else {
                    html = linkHtml;
                }

                $('.fileLinkDiv').html(html);
            });

            $('.addFeatureData').on('click', function() {
                let html = ` <div class="col-md-12 service-data">
                            <div class="row gy-3 ">
                                 <div class="col-md-6">
                                    <input name="features[${featured}][title]" class="form-control" type="text" required placeholder="@lang('Title')">
                                </div>
                                 <div class="col-md-6">
                                    <div class="input-group mb-3">

                                        <input type="text" class="form-control" name="features[${featured}][description]" required placeholder="@lang('Description')">
                                        <button type="button" class="input-group-text btn btn--danger removeServiceBtn">
                                        <i class="las la-times"></i>
                                        </button>
                                    </div>
                                </div>

                         </div>
                    </div>`;
                $('.addedFeature').append(html);
                featured += 1;
            });

            $(document).on('click', '.removeServiceBtn', function() {
                $(this).closest('.service-data').remove();
            });

            $('#editDeliveryType').on('change', function() {
                $('#editDeliveryChargeWrap').toggle($(this).val() === 'paid');
            });

            // Lazy-init rich editor for description (reduces initial lag)
            function initDescriptionEditor() {
                var ta = document.getElementById('productDescription');
                if (!ta || ta.dataset.nicEditInited === '1') return;
                if (typeof nicEditors === 'undefined') return;
                ta.dataset.nicEditInited = '1';
                try {
                    new nicEditor({ fullPanel: true }).panelInstance('productDescription', { hasPanel: true });
                } catch (err) {}
            }
            if (typeof requestIdleCallback !== 'undefined') {
                requestIdleCallback(initDescriptionEditor, { timeout: 2000 });
            } else {
                setTimeout(initDescriptionEditor, 300);
            }

            // Refresh CSRF and sync nicEdit content before submit
            var productEditForm = document.getElementById('productEditForm');
            if (productEditForm) {
                productEditForm.addEventListener('submit', function(e) {
                    var form = this;
                    if (typeof nicEditors !== 'undefined') {
                        var ed = nicEditors.findEditor('productDescription');
                        if (ed && ed.getContent) {
                            var ta = form.querySelector('#productDescription');
                            if (ta) ta.value = ed.getContent();
                        }
                    }
                    if (form.dataset.csrfRefreshed === '1') return;
                    e.preventDefault();
                    var submitBtn = form.querySelector('button[type="submit"]');
                    if (submitBtn) submitBtn.disabled = true;
                    $.get('{{ route("admin.csrf.token") }}')
                        .done(function(r) {
                            var tokenInput = form.querySelector('input[name="_token"]');
                            if (tokenInput && r.token) tokenInput.value = r.token;
                            form.dataset.csrfRefreshed = '1';
                            form.submit();
                        })
                        .fail(function() {
                            if (submitBtn) submitBtn.disabled = false;
                            alert('{{ __("Session expired. Please refresh the page and try again.") }}');
                        });
                });
            }

        })(jQuery);
    </script>
@endpush
