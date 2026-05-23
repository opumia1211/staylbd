@extends('admin.layouts.app')

@push('style')

<style>
    /* Unified Image Section Container */
    .unified-image-section {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        align-items: flex-start;
    }

    /* Common Image Box Style */
    .img-box {
        width: 140px;
        height: 140px;
        border-radius: 8px;
        border: 1px solid #ced4da;
        background-color: #f8f9fa;
        position: relative;
        flex-shrink: 0;
    }

    /* Hide ALL native file inputs in this section to prevent layout jumping */
    .unified-image-section input[type="file"],
    .image-uploader input[type="file"],
    .profilePicUpload {
        opacity: 0 !important;
        position: absolute !important;
        width: 0.1px !important;
        height: 0.1px !important;
        z-index: -100 !important;
    }

    /* Main Image Styling */
    .main-image-wrapper {
        width: 140px;
        display: flex;
        flex-direction: column;
    }
    .profilePicPreview {
        width: 100%;
        height: 100%;
        background-size: cover;
        background-position: center;
        border-radius: 8px;
        overflow: hidden;
    }
    .avatar-edit {
        position: absolute;
        bottom: -5px;
        right: -5px;
        z-index: 10;
    }
    .avatar-edit label {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #4634ff;
        text-align: center;
        line-height: 32px;
        color: #fff;
        cursor: pointer;
        display: block;
        margin: 0;
        box-shadow: 0 2px 4px rgba(0,0,0,0.15);
    }
    .avatar-edit label i {
        font-size: 18px;
        line-height: 32px;
    }

    /* Gallery Styling */
    .gallery-images-wrapper {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }
    .image-uploader {
        display: flex !important;
        flex-wrap: wrap !important;
        gap: 15px;
        min-height: 140px !important;
        border: none !important;
        background: transparent !important;
        position: relative;
    }
    .input-images .uploaded {
        display: contents !important;
    }
    .input-images .uploaded .uploaded-image {
        width: 140px !important;
        height: 140px !important;
        border-radius: 8px !important;
        border: 1px solid #ced4da !important;
        background-color: #f8f9fa !important;
        padding-bottom: 0 !important;
        margin: 0 !important;
        flex: 0 0 auto !important;
        position: relative;
        overflow: hidden;
    }
    .input-images .uploaded .uploaded-image img {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
        position: static !important;
    }

    /* Delete Button Styling (Always visible) */
    .input-images .uploaded .uploaded-image .delete-image {
        display: block !important;
        position: absolute;
        top: 5px;
        right: 5px;
        background: #ff4747;
        color: white;
        border: none;
        border-radius: 50%;
        width: 28px;
        height: 28px;
        line-height: 28px;
        text-align: center;
        cursor: pointer;
        z-index: 10;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
    .input-images .uploaded .uploaded-image .delete-image i {
        display: none !important;
    }
    .input-images .uploaded .uploaded-image .delete-image::before {
        content: '×';
        font-size: 20px;
        font-weight: bold;
        line-height: 28px;
        color: #fff;
    }

    /* Empty Drag and Drop area acts as 'Add New' Card */
    .image-uploader .upload-text {
        position: static !important;
        display: flex !important;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        width: 140px !important;
        height: 140px !important;
        border: 1px dashed #ced4da;
        border-radius: 8px;
        background: #f8f9fa;
        cursor: pointer;
        margin: 0 !important;
        flex-shrink: 0;
    }
    .image-uploader .upload-text span {
        display: none !important;
    }
    .image-uploader .upload-text::after {
        content: 'Add Image';
        font-size: 12px;
        color: #6c757d;
        font-weight: 600;
        margin-top: 5px;
    }
    .image-uploader.has-files .upload-text {
        display: flex !important; /* Keep it visible to add more */
    }
    
    .section-title {
        font-size: 13px;
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
        white-space: nowrap;
    }
</style>

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

                {{-- Advanced Features Card --}}
                <div class="card mb-3 border-secondary">
                    <div class="card-header bg-secondary text-white">
                        <i class="las la-sliders-h me-1"></i> @lang('Advanced Pricing, Inventory & Attributes')
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @if(\Illuminate\Support\Facades\Schema::hasColumn('products', 'original_price'))
                            <div class="form-group col-md-3">
                                <label>@lang('Original/MSRP Price') <i class="las la-info-circle text-muted" title="Will show as strike-through if higher than Selling Price"></i></label>
                                <div class="input-group">
                                    <input type="number" step="any" min="0" class="form-control" name="original_price" value="{{ old('original_price', getAmount($product->original_price ?? 0)) }}">
                                    <span class="input-group-text">{{ __($general->cur_text) }}</span>
                                </div>
                            </div>
                            @endif
                            @if(\Illuminate\Support\Facades\Schema::hasColumn('products', 'profit_margin'))
                            <div class="form-group col-md-3">
                                <label>@lang('Profit Margin') <i class="las la-info-circle text-muted" title="For internal reporting only"></i></label>
                                <div class="input-group">
                                    <input type="number" step="any" min="0" class="form-control" name="profit_margin" value="{{ old('profit_margin', getAmount($product->profit_margin ?? 0)) }}">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            @endif
                            @if(\Illuminate\Support\Facades\Schema::hasColumn('products', 'low_stock_alert'))
                            <div class="form-group col-md-3">
                                <label>@lang('Low Stock Alert')</label>
                                <input type="number" name="low_stock_alert" min="0" class="form-control" value="{{ old('low_stock_alert', $product->low_stock_alert ?? 5) }}" />
                            </div>
                            @endif
                            @if(\Illuminate\Support\Facades\Schema::hasColumn('products', 'warehouse_location'))
                            <div class="form-group col-md-3">
                                <label>@lang('Warehouse Location')</label>
                                <input type="text" name="warehouse_location" class="form-control" value="{{ old('warehouse_location', $product->warehouse_location ?? '') }}" placeholder="e.g. A1-B2" />
                            </div>
                            @endif
                            
                            <div class="w-100 mb-2"></div>
                            
                            @if(\Illuminate\Support\Facades\Schema::hasColumn('products', 'shipping_weight'))
                            <div class="form-group col-md-3">
                                <label>@lang('Shipping Weight (kg)')</label>
                                <input type="number" step="any" min="0" name="shipping_weight" class="form-control" value="{{ old('shipping_weight', $product->shipping_weight ?? '') }}" />
                            </div>
                            @endif
                            @if(\Illuminate\Support\Facades\Schema::hasColumn('products', 'product_type'))
                            <div class="form-group col-md-3">
                                <label>@lang('Product Type')</label>
                                <select name="product_type" class="form-control">
                                    <option value="physical" @selected(($product->product_type ?? '') == 'physical')>Physical Goods</option>
                                    <option value="clothing" @selected(($product->product_type ?? '') == 'clothing')>Clothing / Apparel</option>
                                    <option value="digital" @selected(($product->product_type ?? '') == 'digital')>Digital Download</option>
                                    <option value="service" @selected(($product->product_type ?? '') == 'service')>Service</option>
                                </select>
                            </div>
                            @endif
                            @if(\Illuminate\Support\Facades\Schema::hasColumn('products', 'material'))
                            <div class="form-group col-md-3">
                                <label>@lang('Material')</label>
                                <input type="text" name="material" class="form-control" value="{{ old('material', $product->material ?? '') }}" placeholder="e.g. Cotton, Leather" />
                            </div>
                            @endif
                            @if(\Illuminate\Support\Facades\Schema::hasColumn('products', 'target_gender'))
                            <div class="form-group col-md-3">
                                <label>@lang('Target Gender')</label>
                                <select name="target_gender" class="form-control">
                                    <option value="">@lang('Unisex / All')</option>
                                    <option value="male" @selected(($product->target_gender ?? '') == 'male')>Male</option>
                                    <option value="female" @selected(($product->target_gender ?? '') == 'female')>Female</option>
                                    <option value="kids" @selected(($product->target_gender ?? '') == 'kids')>Kids</option>
                                </select>
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
                            <label>@lang('Key Features')</label>
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

                <div class="card mb-3 mt-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>@lang('Image Section (Main & Gallery)')</span>
                        <span class="badge bg-primary" id="galleryImageCount">0 @lang('Gallery Uploaded')</span>
                    </div>
                    <div class="card-body">
                        <div class="unified-image-section">
                            
                            <!-- Main Image Area -->
                            <div class="main-image-wrapper">
                                <div class="section-title text-center">@lang('Main Image')</div>
                                <div class="img-box" onclick="document.getElementById('image').click()" style="cursor: pointer;">
                                    <div class="profilePicPreview" style="background-image: url('{{ $product->imageShow() }}')"></div>
                                    <div class="avatar-edit">
                                        <input type="file" name="image" class="profilePicUpload" id="image" accept="image/*, video/*">
                                        <label for="image" class="bg--primary" title="@lang('Upload Main Image')"><i class="la la-pencil"></i></label>
                                    </div>
                                </div>
                            </div>

                            <!-- Separator -->
                            <div style="width: 1px; background: #e5e5e5; height: 160px; margin: 0 5px;"></div>
                            
                            <!-- Gallery Area -->
                            <div class="gallery-images-wrapper">
                                <div class="section-title text-start">@lang('Gallery Images') <small class="text-muted fw-normal">(Max 6)</small></div>
                                <div class="input-field">
                                    <div class="input-images"></div>
                                    <small class="form-text text-muted mt-2 d-block">
                                        <i class="las la-info-circle"></i> @lang('Click empty area to upload more. Images can be edited or deleted.')
                                    </small>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn--primary w-100 h-45 mb-3">@lang('Update')</button>
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

            function updateGalleryCount() {
                var count = $('.input-images .uploaded-image').length;
                $('#galleryImageCount').text(count + ' @lang("Uploaded")');
            }

            function initImageUploader() {
                if (typeof $.fn.imageUploader !== 'function') return;
                $('.input-images').imageUploader({
                    preloaded: preloaded,
                    imagesInputName: 'gallery',
                    preloadedInputName: 'old',
                    maxFiles: 6
                });

                updateGalleryCount();
                var observer = new MutationObserver(function() {
                    updateGalleryCount();
                });
                if ($('.input-images')[0]) {
                    observer.observe($('.input-images')[0], { childList: true, subtree: true });
                }
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
