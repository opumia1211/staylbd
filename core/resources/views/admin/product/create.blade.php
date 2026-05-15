@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-md-12">
            <form id="productCreateForm" action="{{ route('admin.product.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="product_type" value="clothing">
                <div class="card mb-3 border--primary product-create-toolbar">
                    <div class="card-body py-3 d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center flex-wrap gap-2">
                            <a href="{{ route('admin.product.index') }}" class="btn btn-outline--secondary btn-sm">
                                <i class="las la-arrow-left me-1"></i> @lang('Back to Product List')
                            </a>
                            <a href="{{ route('admin.product.create2') }}" class="btn btn-outline--info btn-sm">
                                <i class="las la-box me-1"></i> @lang('Add Other Products')
                            </a>
                            <span class="text-muted small mb-0 d-none d-md-inline">
                                <i class="las la-info-circle me-1"></i> @lang('Required') <span class="text-danger">*</span>
                            </span>
                        </div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="text-muted small d-none d-md-inline me-1">@lang('Go to section'):</span>
                            <div id="clothingQuickNav" class="d-flex flex-wrap gap-1" role="group">
                                <a href="#section-basic" class="btn btn-sm quick-nav-btn rounded-pill px-3 py-1" data-section="section-basic" data-required="name,brand_id,category_id,subcategory_id,product_sku,quantity,price">
                                    <i class="las la-box me-1"></i> @lang('Basic') <span class="badge bg-danger ms-1 section-badge">*</span>
                                </a>
                                <a href="#section-clothing" class="btn btn-sm quick-nav-btn rounded-pill px-3 py-1" data-section="section-clothing">
                                    <i class="las la-tshirt me-1"></i> @lang('Clothing') <span class="badge bg-secondary ms-1 section-badge">@lang('Opt')</span>
                                </a>
                                <a href="#section-digital" class="btn btn-sm quick-nav-btn rounded-pill px-3 py-1" data-section="section-digital" data-required="digital_item">
                                    <i class="las la-file-alt me-1"></i> @lang('Digital') <span class="badge bg-warning text-dark ms-1 section-badge">*</span>
                                </a>
                                <a href="#section-homepage" class="btn btn-sm quick-nav-btn rounded-pill px-3 py-1" data-section="section-homepage">
                                    <i class="las la-home me-1"></i> @lang('Homepage') <span class="badge bg-info ms-1 section-badge">@lang('Opt')</span>
                                </a>
                                <a href="#section-size" class="btn btn-sm quick-nav-btn rounded-pill px-3 py-1" data-section="section-size" data-required="size_qty">
                                    <i class="las la-ruler me-1"></i> @lang('Size') <span class="badge bg-warning text-dark ms-1 section-badge">*</span>
                                </a>
                                <a href="#section-details" class="btn btn-sm quick-nav-btn rounded-pill px-3 py-1" data-section="section-details" data-required="summary,description">
                                    <i class="las la-align-left me-1"></i> @lang('Details') <span class="badge bg-danger ms-1 section-badge">*</span>
                                </a>
                                <a href="#section-media" class="btn btn-sm quick-nav-btn rounded-pill px-3 py-1" data-section="section-media" data-required="image,gallery">
                                    <i class="las la-images me-1"></i> @lang('Media') <span class="badge bg-danger ms-1 section-badge">*</span>
                                </a>
                                <a href="#section-seo" class="btn btn-sm quick-nav-btn rounded-pill px-3 py-1" data-section="section-seo">
                                    <i class="las la-search me-1"></i> @lang('SEO')
                                </a>
                            </div>
                            <button type="submit" class="btn btn--primary btn-lg px-4 ms-lg-2" id="productSubmitBtn">
                                <i class="las la-save me-1"></i> @lang('Save Product')
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card mb-3 border--primary section-card" id="section-basic" data-section="section-basic">
                    <div class="card-header product-create-card-header d-flex align-items-center justify-content-between">
                        <span><i class="las la-box me-2"></i> @lang('Product Information')</span>
                        <button type="button" class="btn btn-sm btn-outline-danger check-section-btn" data-section="section-basic" title="@lang('Show which fields to fill')"><i class="las la-exclamation-circle me-1"></i> @lang('Check required')</button>
                    </div>
                    <div class="card-body">
                        <div class="section-fill-hint alert alert-danger py-2 px-3 mb-3 d-none" role="alert">
                            <i class="las la-hand-point-right me-2"></i> <strong>@lang('Fill these fields')</strong> — @lang('Red marked fields in this section are required.')
                        </div>
                        <div class="row">
                            <div class="form-group col-md-3 @error('name') product-group-error @enderror">
                                <label>@lang('Name') <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid product-field-error @enderror" value="{{ old('name') }}" required placeholder="@lang('Product name')">
                                @error('name') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-group col-md-3 @error('brand_id') product-group-error @enderror">
                                <label>@lang('Brands') <span class="text-danger">*</span></label>
                                <select class="form-control @error('brand_id') is-invalid product-field-error @enderror" name="brand_id" required>
                                    <option value="" selected disabled>@lang('Select One')</option>
                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand->id }}" @selected(old('brand_id') == $brand->id)>
                                            {{ __($brand->name) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('brand_id') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-group col-md-3 @error('category_id') product-group-error @enderror">
                                <label>@lang('Category') <span class="text-danger">*</span></label>
                                <select name="category_id" class="form-control select2-category @error('category_id') is-invalid product-field-error @enderror" id="productCategory" required>
                                    <option value="" selected disabled>@lang('Select Category')</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ __($category->name) }}</option>
                                    @endforeach
                                </select>
                                @error('category_id') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                                <small class="form-text text-muted">@lang('Select the main category')</small>
                            </div>
                            <div class="form-group col-md-3 @error('subcategory_id') product-group-error @enderror">
                                <label>@lang('Subcategory') <span class="text-danger">*</span> <span class="text-muted small">(সাবক্যাটাগরি)</span></label>
                                <select name="subcategory_id" class="form-control select2-subcategory @error('subcategory_id') is-invalid product-field-error @enderror" id="productSubcategory" required>
                                    <option value="" selected disabled>@lang('Select Subcategory') (বাছুন)</option>
                                </select>
                                @error('subcategory_id') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-group col-md-3 @error('product_sku') product-group-error @enderror">
                                <label>@lang('Product SKU') <span class="text-danger">*</span></label>
                                <input type="text" name="product_sku" class="form-control @error('product_sku') is-invalid product-field-error @enderror" value="{{ old('product_sku') }}" required placeholder="e.g. P001" />
                                @error('product_sku') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                                <small class="form-text text-muted">@lang('Short unique code')</small>
                            </div>
                            <div class="form-group col-md-3" id="singleQuantityWrap">
                                <label>@lang('Stock Quantity')</label>
                                <input type="number" name="quantity" id="quantityInput" class="form-control" value="{{ old('quantity', 1) }}" min="0" />
                                <small class="form-text text-muted">@lang('Leave 0 if using sizes below')</small>
                                <div class="alert alert-warning py-2 small mt-2 d-none" id="createStockWarning" role="alert"><i class="las la-exclamation-triangle me-1"></i> @lang('Low stock: quantity at or below alert threshold.')</div>
                            </div>
                            <div class="form-group col-md-3 @error('price') product-group-error @enderror">
                                <label>@lang('Price') <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" step="any" name="price" min="0" class="form-control @error('price') is-invalid product-field-error @enderror" value="{{ old('price') }}" required />
                                    <span class="input-group-text"> {{ __($general->cur_text) }} </span>
                                </div>
                                @error('price') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-group col-md-3">
                                <label>@lang('Discount') <span class="text-muted small">(ছাড়)</span></label>
                                <div class="input-group">
                                    <input type="number" step="any" class="form-control" name="discount" min="0" value="{{ old('discount') }}">
                                    <select name="discount_type" class="input-group-text">
                                        <option value="1" @selected(old('discount_type') == 1)>{{ __($general->cur_text) }}</option>
                                        <option value="2" @selected(old('discount_type') == 2)>@lang('%')</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label>@lang('Original Price')</label>
                                <div class="input-group">
                                    <input type="number" step="any" name="original_price" class="form-control" value="{{ old('original_price') }}" min="0" placeholder="0">
                                    <span class="input-group-text">{{ __($general->cur_text) }}</span>
                                </div>
                                <small class="text-muted">@lang('For profit calculation')</small>
                            </div>
                            <div class="form-group col-md-3">
                                <label>@lang('Profit Margin') (%)</label>
                                <input type="number" step="any" name="profit_margin" class="form-control" value="{{ old('profit_margin') }}" min="0" placeholder="0">
                            </div>
                            <div class="form-group col-md-3">
                                <label>@lang('Low Stock Alert')</label>
                                <input type="number" name="low_stock_alert" id="createLowStockAlert" class="form-control" value="{{ old('low_stock_alert') }}" min="0" placeholder="e.g. 5">
                                <small class="text-muted">@lang('Alert when stock below')</small>
                            </div>
                            @if(\Schema::hasColumn('products', 'delivery_type'))
                            <div class="form-group col-md-3">
                                <label>@lang('Delivery Type')</label>
                                <select name="delivery_type" class="form-control" id="createDeliveryType">
                                    <option value="free" @selected(old('delivery_type', 'free') === 'free')>@lang('Free Delivery')</option>
                                    <option value="paid" @selected(old('delivery_type') === 'paid')>@lang('Paid Delivery')</option>
                                </select>
                            </div>
                            <div class="form-group col-md-3" id="createDeliveryChargeWrap" style="{{ old('delivery_type') === 'paid' ? '' : 'display:none;' }}">
                                <label>@lang('Delivery Charge')</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" name="delivery_charge" class="form-control" value="{{ old('delivery_charge', 0) }}" min="0">
                                    <span class="input-group-text">{{ $general->cur_sym ?? '৳' }}</span>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card mb-3 border--primary section-card" id="section-clothing" data-section="section-clothing">
                    <div class="card-header product-create-card-header d-flex align-items-center justify-content-between">
                        <span><i class="las la-tshirt me-2"></i> @lang('Clothing Details')</span>
                        <button type="button" class="btn btn-sm btn-outline-secondary check-section-btn" data-section="section-clothing" data-optional="1" title="@lang('View section info')"><i class="las la-info-circle me-1"></i> @lang('Check section')</button>
                    </div>
                    <div class="card-body">
                        <div class="section-fill-hint alert alert-info py-2 px-3 mb-3 d-none" role="alert" id="hint-clothing-optional"><i class="las la-check-circle me-2"></i> @lang('All fields in this section are optional. Fill for better product display.')</div>
                        <div class="row g-3 mb-3">
                            <div class="form-group col-md-4">
                                <label>@lang('Fabric Type')</label>
                                <input type="text" name="fabric_type" class="form-control" value="{{ old('fabric_type') }}" placeholder="e.g. Cotton, Polyester">
                            </div>
                            <div class="form-group col-md-4">
                                <label>@lang('Material')</label>
                                <input type="text" name="material" class="form-control" value="{{ old('material') }}" placeholder="e.g. 100% Cotton">
                            </div>
                            <div class="form-group col-md-4">
                                <label>@lang('Season')</label>
                                <select name="season" class="form-control">
                                    <option value="">@lang('Select')</option>
                                    @foreach($seasons ?? [] as $key => $label)
                                        <option value="{{ $key }}" @selected(old('season') == $key)>{{ __($label) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-12">
                                <label>@lang('Color Variants')</label>
                                <input type="text" name="color_variants_input" id="colorVariantsInput" class="form-control" value="{{ is_array(old('color_variants')) ? implode(', ', old('color_variants')) : old('color_variants') }}" placeholder="e.g. Red, Blue, Black (comma-separated)">
                                <small class="text-muted">@lang('Enter colors separated by commas')</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-3 border--primary section-card" id="section-size" data-section="section-size">
                    <div class="card-header product-create-card-header d-flex align-items-center justify-content-between">
                        <span><i class="las la-ruler me-2"></i> @lang('Size & Stock (for Clothing)')</span>
                        <button type="button" class="btn btn-sm btn-outline-warning check-section-btn" data-section="section-size" title="@lang('Show which fields to fill')"><i class="las la-exclamation-circle me-1"></i> @lang('Check required')</button>
                    </div>
                    <div class="card-body">
                        <div class="section-fill-hint alert alert-warning py-2 px-3 mb-3 d-none" role="alert">
                            <i class="las la-hand-point-right me-2"></i> <strong>@lang('Size section')</strong> — @lang('Either enter Stock Quantity above, or check "has sizes" and fill at least one size quantity below.')
                        </div>
                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="has_sizes" id="hasSizes" value="1" {{ old('has_sizes') ? 'checked' : '' }}>
                                <label class="form-check-label" for="hasSizes">@lang('This product has sizes (e.g. jersey, clothing) – set stock per size')</label>
                            </div>
                        </div>
                        <div id="sizeStockWrap" class="row g-1 mt-2 size-stock-grid" style="display: {{ old('has_sizes') ? 'flex' : 'none' }};">
                            <div class="col-12"><p class="small text-muted mb-1">@lang('Enter quantity in stock for each size. Only sizes with quantity > 0 will be selectable; customer must select a size before ordering.')</p></div>
                            @php $clothingSizes = ['NO','XXS','XS','S','M','L','XL','XXL','XXXL','4XL','5XL']; @endphp
                            @foreach($clothingSizes as $size)
                                <div class="col-4 col-sm-3 col-md-2">
                                    <label class="form-label small mb-0 size-stock-label">{{ $size == 'NO' ? __('Custom Size') : __('Size') . ' ' . $size }}</label>
                                    <input type="number" name="size_qty[{{ $size }}]" class="form-control form-control-sm size-stock-input" value="{{ old('size_qty.'.$size, 0) }}" min="0" placeholder="0">
                                </div>
                            @endforeach
                        </div>
                        <p class="small text-muted mt-2 mb-0">@lang('Customer must select a size before Add to Cart / Buy Now. Order will show selected size.')</p>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header d-flex align-items-center">
                        <i class="las la-users me-2"></i> @lang('Target Audience (optional – for personalized display)')
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label>@lang('Target Gender')</label>
                                <select name="target_gender" class="form-control">
                                    <option value="">@lang('Any / Unisex')</option>
                                    <option value="male" @selected(old('target_gender') == 'male')>@lang('Male')</option>
                                    <option value="female" @selected(old('target_gender') == 'female')>@lang('Female')</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label>@lang('Age from')</label>
                                <input type="number" name="target_age_min" class="form-control" value="{{ old('target_age_min') }}" min="0" max="100" placeholder="@lang('e.g. 18')">
                            </div>
                            <div class="form-group col-md-4">
                                <label>@lang('Age to')</label>
                                <input type="number" name="target_age_max" class="form-control" value="{{ old('target_age_max') }}" min="0" max="100" placeholder="@lang('e.g. 60')">
                            </div>
                        </div>
                        <small class="text-muted">@lang('Products can be prioritized for users by gender and age on home and listing.')</small>
                    </div>
                </div>

                <div class="card mb-3 section-card" id="section-digital" data-section="section-digital">
                    <div class="card-header product-create-card-header d-flex align-items-center justify-content-between">
                        <span><i class="las la-file-alt me-2"></i> @lang('Make Product Digital')</span>
                        <button type="button" class="btn btn-sm btn-outline-warning check-section-btn" data-section="section-digital" title="@lang('Show which fields to fill')"><i class="las la-exclamation-circle me-1"></i> @lang('Check required')</button>
                    </div>
                    <div class="card-body">
                        <div class="section-fill-hint alert alert-warning py-2 px-3 mb-3 d-none" role="alert">
                            <i class="las la-hand-point-right me-2"></i> <strong>@lang('Digital section')</strong> — @lang('Select Yes then choose File or Link and fill the required field.')
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label>@lang('Is Digital') <span class="text-danger">*</span></label>
                                <select name="digital_item" class="form-control" required>
                                    <option value="0" selected>@lang('No')</option>
                                    <option value="1">@lang('Yes')</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4 typeDiv"></div>
                            <div class="form-group col-md-4 fileLinkDiv"></div>
                        </div>
                    </div>
                </div>

                <div class="card mb-3 border-info section-card" id="section-homepage" data-section="section-homepage">
                    <div class="card-header bg-info bg-opacity-10 d-flex align-items-center justify-content-between">
                        <span><i class="las la-home me-2"></i> @lang('Visibility & Homepage')</span>
                        <button type="button" class="btn btn-sm btn-outline-info check-section-btn" data-section="section-homepage" data-optional="1"><i class="las la-info-circle me-1"></i> @lang('Info')</button>
                    </div>
                    <div class="card-body">
                        <div class="section-fill-hint alert alert-info py-2 px-3 mb-3 d-none" role="alert">
                            <strong>@lang('Homepage mapping'):</strong>
                            <ul class="mb-0 small ps-3 mt-1">
                                <li><strong>@lang('Today Deal')</strong> → @lang('Quick Deals strip on home')</li>
                                <li><strong>@lang('Hot Deal')</strong> → @lang('Hot Deals section')</li>
                                <li><strong>@lang('Featured')</strong> → @lang('Featured Products section')</li>
                                <li><strong>@lang('Trending Now')</strong> → @lang('Trending Now section')</li>
                            </ul>
                            <p class="mb-0 small mt-2">@lang('Only one of Featured / Hot Deal / Today Deal at a time. Product must be Active + valid category/brand/subcategory to show on site.')</p>
                        </div>
                        <p class="text-muted small mb-3">@lang('Turn on to show this product in homepage rows. Same options as Edit Product.')</p>
                        @if(\Illuminate\Support\Facades\Schema::hasColumn('products', 'home_section_override'))
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-medium">@lang('Force show in an auto homepage row') <span class="text-muted small">(@lang('optional'))</span></label>
                                <select name="home_section_override" class="form-control">
                                    <option value="">@lang('No override (normal)')</option>
                                    <option value="new_arrivals" @selected(old('home_section_override') === 'new_arrivals')>@lang('New Arrivals')</option>
                                    <option value="best_selling" @selected(old('home_section_override') === 'best_selling')>@lang('Best Selling')</option>
                                    <option value="recommended" @selected(old('home_section_override') === 'recommended')>@lang('Recommended For You')</option>
                                    <option value="trending" @selected(old('home_section_override') === 'trending')>@lang('Trending Now')</option>
                                </select>
                                <small class="text-muted d-block mt-1">@lang('Use this when you want a product to appear in a specific homepage line even if it is not automatically eligible.')</small>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-medium">@lang('Priority / Rank')</label>
                                <input type="number" name="home_section_rank" class="form-control" value="{{ old('home_section_rank', 0) }}" min="0" max="1000000">
                                <small class="text-muted">@lang('Higher shows first')</small>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="home_exclude_from_auto" value="0">
                                    <input class="form-check-input" type="checkbox" name="home_exclude_from_auto" value="1" id="create_exclude_auto" @checked(old('home_exclude_from_auto'))>
                                    <label class="form-check-label" for="create_exclude_auto">@lang('Hide from other auto rows')</label>
                                </div>
                            </div>
                        </div>
                        @endif
                        <div class="row g-3">
                            <div class="col-6 col-lg-3">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="featured_product" value="0">
                                    <input class="form-check-input" type="checkbox" name="featured_product" value="1" id="create_featured" @checked(old('featured_product'))>
                                    <label class="form-check-label small" for="create_featured"><i class="las la-star text-info me-1"></i> @lang('Featured')</label>
                                </div>
                            </div>
                            <div class="col-6 col-lg-3">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="hot_deals" value="0">
                                    <input class="form-check-input" type="checkbox" name="hot_deals" value="1" id="create_hot" @checked(old('hot_deals'))>
                                    <label class="form-check-label small" for="create_hot"><i class="las la-fire text-warning me-1"></i> @lang('Hot Deal')</label>
                                </div>
                            </div>
                            <div class="col-6 col-lg-3">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="today_deals" value="0">
                                    <input class="form-check-input" type="checkbox" name="today_deals" value="1" id="create_today" @checked(old('today_deals'))>
                                    <label class="form-check-label small" for="create_today"><i class="las la-bolt text-danger me-1"></i> @lang('Today Deal') (@lang('Quick Deals'))</label>
                                </div>
                            </div>
                            @if(\Illuminate\Support\Facades\Schema::hasColumn('products', 'trending_now'))
                            <div class="col-6 col-lg-3">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="trending_now" value="0">
                                    <input class="form-check-input" type="checkbox" name="trending_now" value="1" id="create_trending" @checked(old('trending_now'))>
                                    <label class="form-check-label small" for="create_trending"><i class="las la-fire-alt text-success me-1"></i> @lang('Trending Now')</label>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card mb-3 section-card" id="section-details" data-section="section-details">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <span><i class="las la-align-left me-2"></i> @lang('Product Details')</span>
                        <button type="button" class="btn btn-sm btn-outline-danger check-section-btn" data-section="section-details" title="@lang('Show which fields to fill')"><i class="las la-exclamation-circle me-1"></i> @lang('Check required')</button>
                    </div>
                    <div class="card-body">
                        <div class="section-fill-hint alert alert-danger py-2 px-3 mb-3 d-none" role="alert">
                            <i class="las la-hand-point-right me-2"></i> <strong>@lang('Fill these fields')</strong> — @lang('Summary and Description are required. Red marked fields must be filled.')
                        </div>
                        <div class="alert alert-light border mb-3 small">
                            <strong><i class="las la-pen me-1"></i> @lang('Description tips')</strong>: @lang('Include material, care instructions, size guide. Short summary for listing; full description for product page.')
                        </div>
                        <div class="form-group">
                            <label>@lang('Summary') <span class="text-danger">*</span></label>
                            <textarea name="summary" id="summaryField" class="form-control" cols="2" rows="4" required placeholder="@lang('Brief product summary')" maxlength="1000">{{ old('summary') }}</textarea>
                            <small class="text-muted"><span id="summaryCharCount">0</span>/1000 @lang('characters')</small>
                        </div>
                        <div class="form-group">
                            <label>@lang('Key Features')</label>
                            <textarea name="key_features" class="form-control" cols="2" rows="3" placeholder="@lang('Bullet points or short list')">{{ old('key_features') }}</textarea>
                        </div>
                        <div class="form-group">
                            <label>@lang('Full Description') <span class="text-danger">*</span></label>
                            <textarea rows="6" class="form-control nicEdit" name="description" id="descriptionField" placeholder="@lang('Full product description')">{{ old('description') }}</textarea>
                            <small class="text-muted"><span id="descriptionWordCount">0</span> @lang('words')</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="card border--primary mt-3">
                        <div class="card-header product-create-card-header d-flex justify-content-between">
                            <h5 class="mb-0">@lang('Product Specification')</h5>
                            <button type="button" class="btn btn-sm btn-outline-light addFeatureData"> <i class="la la-fw la-plus"></i>@lang('Add New')</button>
                        </div>
                        <div class="card-body">
                            <div class="row addedFeature">
                                @if (old('features'))
                                    @foreach (old('features') as $freature)
                                        @php $featureIndex = $loop->index; @endphp
                                        <div class="col-md-12 service-data">
                                            <div class="row gy-3 ">
                                                <div class="col-md-6">
                                                    <input name="features[{{ $loop->index }}][title]" class="form-control" type="text" value="{{ $freature['title'] }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="input-group mb-3">
                                                        <input type="text" class="form-control" name="features[{{ $loop->index }}][description]" value="{{ $freature['description'] }}">
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

                {{-- SEO for Clothing Product --}}
                <div class="card mb-3 border--success section-card" id="section-seo" data-section="section-seo">
                    <div class="card-header bg-success bg-opacity-10 d-flex align-items-center justify-content-between">
                        <span><i class="las la-search me-2 text-success"></i> <strong>@lang('Product SEO')</strong></span>
                        <button type="button" class="btn btn-sm btn-outline-success check-section-btn" data-section="section-seo" data-optional="1" title="@lang('View section info')"><i class="las la-info-circle me-1"></i> @lang('Check section')</button>
                    </div>
                    <div class="card-body">
                        <div class="section-fill-hint alert alert-info py-2 px-3 mb-3 d-none" role="alert" id="hint-seo-optional">
                            <i class="las la-check-circle me-2"></i> @lang('SEO fields are optional. Fill for better search ranking.')
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">@lang('URL Slug')</label>
                                <input type="text" name="slug" id="productSlug" class="form-control" value="{{ old('slug') }}" placeholder="@lang('Auto from product name')" maxlength="255">
                                <small class="text-muted d-block mt-1"><span id="slugCharCount">0</span>/255 — @lang('Leave blank to auto-generate from name')</small>
                            </div>
                            <div class="col-12">
                                <label class="form-label">@lang('Meta Title') <span class="text-muted small">(SEO title, 50-60 chars ideal)</span></label>
                                <input type="text" name="meta_title" id="metaTitle" class="form-control" value="{{ old('meta_title') }}" placeholder="@lang('Product name + brand for SEO')" maxlength="255">
                                <small class="text-muted"><span id="metaTitleCount">0</span>/255</small>
                            </div>
                            <div class="col-12">
                                <label class="form-label">@lang('Meta Description') <span class="text-muted small">(150-160 chars ideal)</span></label>
                                <textarea name="meta_description" id="metaDescription" class="form-control" rows="2" placeholder="@lang('Short description for search results')" maxlength="500">{{ old('meta_description') }}</textarea>
                                <small class="text-muted"><span id="metaDescCount">0</span>/500</small>
                            </div>
                            <div class="col-12">
                                <label class="form-label">@lang('SEO Keywords')</label>
                                <input type="text" name="meta_keywords" id="metaKeywords" class="form-control" value="{{ is_array(old('meta_keywords')) ? implode(', ', old('meta_keywords')) : old('meta_keywords') }}" placeholder="keyword1, keyword2, keyword3">
                                <small class="text-muted">@lang('Comma separated – helps search ranking')</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-3 border--primary section-card" id="section-media" data-section="section-media">
                    <div class="card-header product-create-card-header d-flex align-items-center justify-content-between">
                        <span><i class="las la-images me-2"></i> @lang('Image & Video')</span>
                        <button type="button" class="btn btn-sm btn-outline-danger check-section-btn" data-section="section-media" title="@lang('Show which fields to fill')"><i class="las la-exclamation-circle me-1"></i> @lang('Check required')</button>
                    </div>
                    <div class="card-body">
                        <div class="section-fill-hint alert alert-danger py-2 px-3 mb-3 d-none" role="alert">
                            <i class="las la-hand-point-right me-2"></i> <strong>@lang('Fill these fields')</strong> — @lang('Main Image and at least one Gallery image are required. Red marked areas must be filled.')
                        </div>
                        <div class="alert alert-light border mb-3 advanced-upload-hint">
                            <strong><i class="las la-cloud-upload-alt me-1"></i> @lang('Advanced upload')</strong><br>
                            <span class="small">@lang('Drag & drop or click to upload.')</span> @lang('Recommended: 800×800 or 1000×1000 px (square).') @lang('Formats: PNG, JPG, WebP, SVG.') <strong>@lang('Max 50MB per image.')</strong><br>
                            <span class="text-muted small">@lang('Main image')</span> <span class="text-danger">*</span> + <span class="text-muted small">@lang('Gallery')</span> <span class="text-danger">*</span> (1–6). @lang('Images are auto-optimized (WebP).')
                        </div>
                        <div class="image-uploader-wrapper">
                            <div class="profile-uploader">
                                <label class="form-group">@lang('Main Image') <span class="text-danger">*</span></label>
                                <div class="payment-method-item">
                                    <div class="payment-method-header d-flex flex-wrap">
                                        <div class="thumb">
                                            <div class="avatar-preview">
                                                <div class="profilePicPreview" style="background-image: url('{{ getImage(getFilePath('product'), getFileSize('product')) }}')"></div>
                                            </div>
                                            <div class="avatar-edit">
                                                <input type="file" name="image" class="profilePicUpload" id="image" accept=".png,.jpg,.jpeg,.webp,.svg,image/png,image/jpeg,image/webp,image/svg+xml" required>
                                                <label for="image" class="bg--primary"><i class="la la-pencil"></i></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <small class="form-text text-muted">@lang('Formats: PNG, JPG, WebP, SVG')</small>
                            </div>
                            <div class="gallery-uploader mt-3">
                                <label class="form-label required">@lang('Gallery Images') (max 6)</label>
                                <div class="input-field">
                                    <div class="input-images"></div>
                                    <small class="form-text text-muted">
                                        <i class="las la-info-circle"></i> @lang('PNG, JPG, WebP, SVG. Max 6 images.')
                                    </small>
                                </div>
                            </div>
                            <div class="form-group mt-3">
                                <label class="form-label">@lang('Product Video') (@lang('max 30 seconds'), @lang('optional'))</label>
                                <input type="file" name="video" id="productVideoInput" class="form-control" accept="video/mp4,video/webm,video/quicktime,video/x-msvideo">
                                <small class="form-text text-muted">@lang('MP4, WebM, MOV, AVI. Max 30 seconds. Shown on product page if uploaded.')</small>
                                <div id="productVideoDurationError" class="invalid-feedback d-none">@lang('Video must be maximum 30 seconds.')</div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn--primary w-100 h-45 mt-4"><i class="las la-save me-1"></i> @lang('Save Product')</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('script-lib')
    <script src="{{ asset('assets/admin/js/image-uploader.min.js') }}"></script>
@endpush

@push('style')

{{-- inline style moved to critical-admin.css --}}

@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            // বাধ্যতামূলক ঘর ভ্যালিডেশন – খালি থাকলে লাল, পূরণ করলে লাল সরে যাবে
            var $form = $('#productCreateForm');
            // Refresh CSRF token every 30 min to avoid 419 on long-lived create form
            setInterval(function() {
                $.get('{{ route("admin.csrf.token") }}').done(function(r) {
                    if (r.token) $form.find('input[name="_token"]').val(r.token);
                });
            }, 30 * 60 * 1000);
            $('#createDeliveryType').on('change', function() {
                $('#createDeliveryChargeWrap').toggle($(this).val() === 'paid');
            });
            function updateCreateStockWarning() {
                var qty = parseInt($('#quantityInput').val(), 10) || 0;
                var alertVal = parseInt($('#createLowStockAlert').val(), 10);
                if (isNaN(alertVal) || alertVal <= 0) { $('#createStockWarning').addClass('d-none'); return; }
                if (qty <= alertVal) $('#createStockWarning').removeClass('d-none'); else $('#createStockWarning').addClass('d-none');
            }
            $('#quantityInput, #createLowStockAlert').on('input', updateCreateStockWarning);
            updateCreateStockWarning();
            function clearFieldError(el) {
                var $el = $(el);
                $el.removeClass('product-field-error');
                var $grp = $el.closest('.form-group');
                $grp.removeClass('product-group-error');
                $grp.find('.select2-container').removeClass('product-field-error');
                $el.next('.select2-container').removeClass('product-field-error');
            }
            $form.on('input change', 'input, select, textarea', function() {
                clearFieldError(this);
            });
            $form.on('input', '[name^="size_qty["]', function() {
                $('#sizeStockWrap').removeClass('product-group-error');
            });
            $form.on('change', '.gallery-uploader input[type=file], .image-uploader-wrapper input[type=file][name=image]', function() {
                $(this).removeClass('product-field-error');
                $(this).closest('.profile-uploader').removeClass('product-group-error');
                $('.gallery-uploader').removeClass('product-group-error');
            });
            function markError(el) {
                if (!el || !$(el).length) return;
                var $el = $(el);
                $el.addClass('product-field-error');
                var $grp = $el.closest('.form-group');
                if ($grp.length) $grp.addClass('product-group-error');
                $grp.find('.select2-container').addClass('product-field-error');
                $el.next('.select2-container').addClass('product-field-error');
            }
            function isBlank(val) {
                return val === undefined || val === null || (typeof val === 'string' && val.trim() === '');
            }
            function validateProductRequired() {
                $form.find('.product-field-error').removeClass('product-field-error');
                $form.find('.product-group-error').removeClass('product-group-error');
                var firstError = null;
                // nicEdit: layout gives textarea id nicEditor0, nicEditor1... – save content to textarea before validation
                try {
                    var $desc = $('textarea[name=description]');
                    if ($desc.length) {
                        var descId = $desc.attr('id');
                        if (descId && typeof nicEditors !== 'undefined') {
                            var ed = nicEditors.findEditor(descId);
                            if (ed && ed.saveContent) ed.saveContent();
                        }
                    }
                } catch (e) {}

                if (isBlank($('[name=name]').val())) { markError($('[name=name]')[0]); if (!firstError) firstError = $('[name=name]')[0]; }
                if (!$('[name=brand_id]').val()) { markError($('[name=brand_id]')[0]); if (!firstError) firstError = $('[name=brand_id]')[0]; }
                if (!$('[name=category_id]').val()) { markError($('[name=category_id]')[0]); if (!firstError) firstError = $('[name=category_id]')[0]; }
                if (!$('[name=subcategory_id]').val()) { markError($('[name=subcategory_id]')[0]); if (!firstError) firstError = $('[name=subcategory_id]')[0]; }
                if (isBlank($('[name=product_sku]').val())) { markError($('[name=product_sku]')[0]); if (!firstError) firstError = $('[name=product_sku]')[0]; }
                var hasSizes = $('#hasSizes').is(':checked');
                if (!hasSizes) {
                    var qtyVal = $('[name=quantity]').val();
                    var qty = parseInt(qtyVal, 10);
                    if (qtyVal === '' || isNaN(qty) || qty < 1) { markError($('[name=quantity]')[0]); if (!firstError) firstError = $('[name=quantity]')[0]; }
                } else {
                    var sizeSum = 0;
                    $('[name^="size_qty["]').each(function() { sizeSum += parseInt($(this).val(), 10) || 0; });
                    if (sizeSum < 1) {
                        $('#sizeStockWrap').addClass('product-group-error');
                        if (!firstError) firstError = $('#sizeStockWrap').find('input').get(0);
                    }
                }
                if (isBlank($('[name=price]').val()) || parseFloat($('[name=price]').val()) <= 0) { markError($('[name=price]')[0]); if (!firstError) firstError = $('[name=price]')[0]; }
                if (isBlank($('[name=digital_item]').val())) { markError($('[name=digital_item]')[0]); if (!firstError) firstError = $('[name=digital_item]')[0]; }
                var digital = $('[name=digital_item]').val();
                if (digital === '1') {
                    if (isBlank($('[name=file_type]').val())) {
                        $('[name=file_type]').closest('.form-group').addClass('product-group-error');
                        if (!firstError) firstError = $('[name=file_type]')[0];
                    }
                    var ft = $('[name=file_type]').val();
                    if (ft === '2') {
                        if (isBlank($('[name=link]').val())) { markError($('[name=link]')[0]); if (!firstError) firstError = $('[name=link]')[0]; }
                    } else if (ft === '1') {
                        var f = $('[name=file]');
                        if (!f.length || !f.get(0).files || !f.get(0).files.length) {
                            f.closest('.form-group').addClass('product-group-error');
                            if (f.length && !firstError) firstError = f[0];
                        }
                    }
                }
                if (isBlank($('[name=summary]').val())) { markError($('[name=summary]')[0]); if (!firstError) firstError = $('[name=summary]')[0]; }
                var descVal = ($('textarea[name=description]').val() || '').replace(/<[^>]*>/g, '').trim();
                if (isBlank(descVal)) { markError($('textarea[name=description]')[0]); if (!firstError) firstError = $('textarea[name=description]')[0]; }
                var imgInput = $('[name=image]').get(0);
                if (!imgInput || !imgInput.files || !imgInput.files.length) {
                    $('[name=image]').closest('.profile-uploader').addClass('product-group-error');
                    $('[name=image]').addClass('product-field-error');
                    if (!firstError) firstError = imgInput;
                }
                var galleryHasFile = false;
                $form.find('input[type="file"][name*="gallery"]').each(function() {
                    if (this.files && this.files.length) galleryHasFile = true;
                });
                if (!galleryHasFile && $('.gallery-uploader .uploaded-image').length === 0) {
                    $('.gallery-uploader').addClass('product-group-error');
                    if ($('.gallery-uploader .input-images').length && !firstError) firstError = $('.gallery-uploader').get(0);
                }
                if (firstError) {
                    var $first = $(firstError);
                    if ($first.length && $first.offset()) {
                        $('html, body').animate({ scrollTop: $first.offset().top - 120 }, 300);
                    }
                    notify('error', '@lang('Please fill all required fields. Red fields are mandatory.')');
                    return false;
                }
                return true;
            }
            function checkVideoDuration(file, maxSeconds) {
                return new Promise(function(resolve) {
                    var v = document.createElement('video');
                    v.preload = 'metadata';
                    v.onloadedmetadata = function() {
                        URL.revokeObjectURL(v.src);
                        resolve(v.duration <= maxSeconds);
                    };
                    v.onerror = function() { URL.revokeObjectURL(v.src); resolve(true); };
                    v.src = URL.createObjectURL(file);
                });
            }
            $form.on('submit', function(e) {
                // খালি সাইজ কোয়ান্টিটি ০ করে দিন – যাতে "must be an integer" এরর না আসে
                $('[name^="size_qty["]').each(function() {
                    var v = $(this).val();
                    if (v === '' || v === null || isNaN(parseInt(v, 10))) $(this).val(0);
                });
                var valid = validateProductRequired();
                if (!valid) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    return false;
                }
                var videoInput = document.getElementById('productVideoInput');
                if (videoInput && videoInput.files && videoInput.files.length > 0) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    $('#productVideoDurationError').addClass('d-none');
                    videoInput.classList.remove('is-invalid');
                    checkVideoDuration(videoInput.files[0], 30).then(function(ok) {
                        if (ok) {
                            $form.off('submit').submit();
                        } else {
                            $('#productVideoDurationError').removeClass('d-none');
                            videoInput.classList.add('is-invalid');
                            notify('error', '@lang('Video must be maximum 30 seconds.')');
                        }
                    }).catch(function() { $form.off('submit').submit(); });
                    return false;
                }
                $form.find('button[type=submit]').prop('disabled', true).each(function() {
                    $(this).data('orig-html', $(this).html()).html('<i class="las la-spinner las la-spin me-1"></i> @lang('Saving...')');
                });
            });

            let featured = 1;
            let gallery = 1;

            @if (@old('features'))
                let extra = "{{ $featureIndex }}";
                featured = parseInt(featured) + parseInt(extra);
            @endif


            let preloaded = [];

            $('.input-images').imageUploader({
                preloaded: preloaded,
                imagesInputName: 'gallery',
                preloadedInputName: 'old',
                maxFiles: 6
            });

            $(document).on('input', 'input[name="gallery[]"]', function() {
                var fileUpload = $("input[type='file']");
                if (parseInt(fileUpload.get(0).files.length) > 6) {
                    $('#errorModal').modal('show');
                }
            });

            // Category/Subcategory from single map (no heavy data on each option)
            window.__categorySubcategoriesMap = @json($categorySubcategoriesMap ?? []);
            $('#productCategory').on('change', function() {
                var catId = $(this).val();
                var subs = (window.__categorySubcategoriesMap && window.__categorySubcategoriesMap[catId]) || [];
                var html = '<option value="" disabled selected>@lang('Select Subcategory')</option>';
                subs.forEach(function(s) { html += '<option value="' + s.id + '">' + (s.name || '') + '</option>'; });
                if (subs.length === 0) html += '<option value="" disabled>@lang('No subcategories available')</option>';
                $('#productSubcategory').html(html).val('').trigger('change');
                if (subs.length === 0) notify('info', '@lang('This category has no subcategories. Please select a different category.')');
            });
            
            // Initialize subcategories on page load if category is preselected
            $('#hasSizes').on('change', function() {
                var show = $(this).is(':checked');
                $('#sizeStockWrap').toggle(show);
                $('#quantityInput').prop('required', !show);
                if (show) $('#quantityInput').val(0);
            });
            @if(old('has_sizes')) $('#quantityInput').prop('required', false); @endif

            @if(old('category_id'))
                $('#productCategory').trigger('change');
                @if(old('subcategory_id'))
                    setTimeout(function() {
                        $('#productSubcategory').val('{{ old('subcategory_id') }}').trigger('change');
                    }, 100);
                @endif
            @endif

            /* প্রাইস ও ডিসকাউন্ট: ছাড় প্রাইসের বেশি হতে পারবে না – শুধু ছাড় ফিল্ড ক্লিয়ার, প্রাইস কখনো ক্লিয়ার না */
            $('[name=price]').on('input change', function() { clearFieldError(this); });
            $('[name=discount]').on('input change', function() { clearFieldError(this); });

            $('[name=price]').on('focusout', function () {
                var discountVal = $('[name=discount]').val();
                if (!discountVal || parseFloat(discountVal) === 0) return;
                var discountType  = $('[name=discount_type]').find(':selected').val();
                var priceValue    = parseFloat($(this).val());
                var discountValue = parseFloat(discountVal);
                if (isNaN(priceValue) || priceValue <= 0) return;
                checkDiscountValue(discountType, priceValue, discountValue);
            });

            $('[name=discount]').on('focusout', function () {
                var discountValue = parseFloat($(this).val());
                if (!discountValue || isNaN(discountValue)) return;
                var discountType = $('[name=discount_type]').find(':selected').val();
                var priceValue   = parseFloat($('[name=price]').val());
                if (!priceValue || isNaN(priceValue)) return;
                checkDiscountValue(discountType, priceValue, discountValue);
            });

            function checkDiscountValue(discountType, priceValue, discountValue) {
                var invalid = false;
                if (discountType == 1 || discountType === '1') {
                    if (discountValue >= priceValue || (priceValue - discountValue) <= 0) invalid = true;
                } else {
                    var afterDiscount = priceValue - (priceValue * discountValue / 100);
                    if (afterDiscount <= 0) invalid = true;
                }
                if (invalid) {
                    notify('error', 'Discount cannot be greater than or equal to price. Enter a valid discount.');
                    $('[name=discount]').val('');
                }
            }

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

            // ——— Advanced: Auto slug from product name (server will fix with Str::slug if blank) ———
            var slugLock = false;
            function slugifyText(t) {
                return (t || '').toString().toLowerCase()
                    .replace(/\s+/g, '-').replace(/[^a-z0-9\-]/g, '').replace(/-+/g, '-').replace(/^-|-$/g, '') || '';
            }
            $('[name=name]').on('input', function() {
                if (slugLock) return;
                var s = $('#productSlug');
                if (s.length) {
                    var v = slugifyText($(this).val());
                    if (v) s.val(v);
                }
            });
            $('#productSlug').on('input', function() { slugLock = $(this).val().length > 0; });

            // ——— SEO character counters (fast, no layout thrash) ———
            function updateCount(id, val) {
                var el = document.getElementById(id);
                if (el) el.textContent = (val || '').length;
            }
            $('#productSlug').on('input', function() { updateCount('slugCharCount', this.value); });
            $('#metaTitle').on('input', function() { updateCount('metaTitleCount', this.value); });
            $('#metaDescription').on('input', function() { updateCount('metaDescCount', this.value); });
            updateCount('slugCharCount', $('#productSlug').val());
            updateCount('metaTitleCount', $('#metaTitle').val());
            updateCount('metaDescCount', $('#metaDescription').val());

            // ——— Summary & Description counters (advanced details) ———
            function updateSummaryCount() { updateCount('summaryCharCount', $('#summaryField').val()); }
            function updateDescWordCount() {
                var t = '';
                try {
                    var $d = $form.find('textarea[name=description]');
                    if ($d.length && typeof nicEditors !== 'undefined') {
                        var id = $d.attr('id');
                        if (id) { var ed = nicEditors.findEditor(id); if (ed && ed.getContent) t = (ed.getContent() || '').replace(/<[^>]*>/g, ' '); }
                    }
                    if (!t) t = $d.val() || '';
                } catch (e) { t = $form.find('textarea[name=description]').val() || ''; }
                var words = (t || '').trim() ? (t || '').trim().split(/\s+/).length : 0;
                var el = document.getElementById('descriptionWordCount');
                if (el) el.textContent = words;
            }
            $('#summaryField').on('input', updateSummaryCount);
            setInterval(updateDescWordCount, 1000);
            updateSummaryCount();
            updateDescWordCount();

            // ——— Section → required fields (for red highlight on shortcut click) ———
            var sectionRequiredMap = {
                'section-basic': ['name', 'brand_id', 'category_id', 'subcategory_id', 'product_sku', 'quantity', 'price'],
                'section-size': ['size_qty'],
                'section-digital': ['digital_item'],
                'section-details': ['summary', 'description'],
                'section-media': ['image', 'gallery']
            };

            function clearAllSectionHighlights() {
                $form.find('.product-group-error').removeClass('product-group-error');
                $form.find('.product-field-error').removeClass('product-field-error');
                $form.find('.section-fill-hint').addClass('d-none');
                $('#sizeStockWrap').removeClass('product-group-error');
            }

            function highlightSectionRequired(sectionId) {
                clearAllSectionHighlights();
                var $section = $('#' + sectionId);
                if (!$section.length) return;
                var requiredNames = sectionRequiredMap[sectionId];
                var hasError = false;

                // Optional sections: only show hint, no red
                if ($section.find('.check-section-btn[data-optional="1"]').length) {
                    $section.find('.section-fill-hint').first().removeClass('d-none');
                    notify('info', sectionId === 'section-clothing' ? '@lang('Clothing: all fields optional.')' : '@lang('SEO: all fields optional.')');
                    return;
                }

                if (sectionId === 'section-basic' && requiredNames) {
                    requiredNames.forEach(function(n) {
                        if (n === 'quantity' && $('#hasSizes').is(':checked')) return;
                        var $el = $section.find('[name="' + n + '"]').first();
                        if (!$el.length) $el = $form.find('[name="' + n + '"]').first();
                        if ($el.length) {
                            var v = $el.val();
                            var empty = v === undefined || v === null || (typeof v === 'string' && v.trim() === '');
                            if (n === 'price') empty = empty || parseFloat(v) <= 0;
                            if (empty) { markError($el[0]); hasError = true; }
                        }
                    });
                    if (hasError) $section.find('.section-fill-hint').first().removeClass('d-none');
                } else if (sectionId === 'section-digital') {
                    var dig = $form.find('[name=digital_item]').val();
                    if (isBlank(dig)) {
                        markError($form.find('[name=digital_item]')[0]);
                        hasError = true;
                    } else if (dig === '1' || dig === 1) {
                        var ft = $form.find('[name=file_type]').val();
                        if (isBlank(ft)) {
                            $form.find('[name=file_type]').closest('.form-group').addClass('product-group-error');
                            hasError = true;
                        } else if (ft === '2') {
                            if (isBlank($form.find('[name=link]').val())) { markError($form.find('[name=link]')[0]); hasError = true; }
                        } else {
                            var f = $form.find('[name=file]');
                            if (!f.length || !f.get(0).files || !f.get(0).files.length) {
                                (f.closest('.form-group').length ? f.closest('.form-group') : $section.find('.fileLinkDiv')).addClass('product-group-error');
                                hasError = true;
                            }
                        }
                    }
                    if (hasError) $section.find('.section-fill-hint').first().removeClass('d-none');
                } else if (sectionId === 'section-size') {
                    var hasSizes = $('#hasSizes').is(':checked');
                    if (hasSizes) {
                        var sum = 0;
                        $('[name^="size_qty["]').each(function() { sum += parseInt($(this).val(), 10) || 0; });
                        if (sum < 1) {
                            $('#sizeStockWrap').addClass('product-group-error');
                            $section.find('.section-fill-hint').first().removeClass('d-none');
                            hasError = true;
                        }
                    } else {
                        var q = $('[name=quantity]').val();
                        if (!q || parseInt(q, 10) < 1) {
                            markError($('[name=quantity]')[0]);
                            $section.find('.section-fill-hint').first().removeClass('d-none');
                            hasError = true;
                        }
                    }
                } else if (sectionId === 'section-details' && requiredNames) {
                    requiredNames.forEach(function(n) {
                        var $el = $section.find('[name="' + n + '"]').first();
                        if (!$el.length) $el = $form.find('[name="' + n + '"]').first();
                        if ($el.length) {
                            var v = $el.val();
                            if (n === 'description' && typeof nicEditors !== 'undefined') {
                                try {
                                    var ed = nicEditors.findEditor($el.attr('id'));
                                    if (ed && ed.getContent) v = (ed.getContent() || '').replace(/<[^>]*>/g, '').trim();
                                } catch (e) {}
                            }
                            var empty = v === undefined || v === null || (typeof v === 'string' && v.trim() === '');
                            if (empty) { markError($el[0]); hasError = true; }
                        }
                    });
                    if (hasError) $section.find('.section-fill-hint').first().removeClass('d-none');
                } else if (sectionId === 'section-media') {
                    var imgInput = $form.find('[name=image]').get(0);
                    if (!imgInput || !imgInput.files || !imgInput.files.length) {
                        $form.find('[name=image]').closest('.profile-uploader').addClass('product-group-error');
                        $form.find('[name=image]').addClass('product-field-error');
                        hasError = true;
                    }
                    var galleryOk = $('.gallery-uploader .uploaded-image').length > 0;
                    if (!galleryOk) {
                        $form.find('input[type="file"][name*="gallery"]').each(function() {
                            if (this.files && this.files.length) galleryOk = true;
                        });
                    }
                    if (!galleryOk) {
                        $('.gallery-uploader').addClass('product-group-error');
                        hasError = true;
                    }
                    if (hasError) $section.find('.section-fill-hint').first().removeClass('d-none');
                }

                if (hasError) {
                    notify('warning', '@lang('Red marked fields in this section must be filled.')');
                }
            }

            // ——— Quick nav: scroll + highlight required fields in that section ———
            $(document).on('click', '#clothingQuickNav .quick-nav-btn', function(e) {
                e.preventDefault();
                var sectionId = $(this).data('section');
                var $target = $('#' + sectionId);
                if ($target.length) {
                    $('.section-card').removeClass('scroll-highlight');
                    $target.addClass('scroll-highlight');
                    setTimeout(function() { $target.removeClass('scroll-highlight'); }, 1500);
                    $target[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
                    setTimeout(function() { highlightSectionRequired(sectionId); }, 450);
                }
            });

            // ——— "Check required" button in each section header ———
            $(document).on('click', '.check-section-btn', function() {
                var sectionId = $(this).data('section');
                highlightSectionRequired(sectionId);
                var $s = $('#' + sectionId);
                if ($s.length) $s[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        })(jQuery);
    </script>
@endpush
