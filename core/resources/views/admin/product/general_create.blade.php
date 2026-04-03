@extends('admin.layouts.app')
@section('panel')
<div class="row">
    <div class="col-12">
        <form id="generalProductForm" action="{{ route('admin.product.store2') }}" method="post" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="product_type" value="general">

            <div class="card mb-3 border--primary product-create-toolbar">
                <div class="card-body py-3 d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div class="d-flex align-items-center flex-wrap gap-2">
                        <a href="{{ route('admin.product.index') }}" class="btn btn-outline--secondary btn-sm">
                            <i class="las la-arrow-left me-1"></i> @lang('Back to Product List')
                        </a>
                        <a href="{{ route('admin.product.create') }}" class="btn btn-outline--info btn-sm">
                            <i class="las la-tshirt me-1"></i> @lang('Add Clothing Product')
                        </a>
                        <span class="text-muted small d-none d-md-inline align-self-center">@lang('Universal upload') — @lang('Any product, any website.')</span>
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <nav class="create2-scroll-nav d-flex flex-wrap gap-1" aria-label="@lang('Jump to section')">
                            <a href="#section-basic" class="btn btn-sm btn-outline-dark rounded-pill px-2 py-1 create2-scroll-link">@lang('Basic')</a>
                            <a href="#section-pricing" class="btn btn-sm btn-outline-dark rounded-pill px-2 py-1 create2-scroll-link">@lang('Pricing')</a>
                            <a href="#section-media" class="btn btn-sm btn-outline-dark rounded-pill px-2 py-1 create2-scroll-link">@lang('Media')</a>
                            <a href="#section-details" class="btn btn-sm btn-outline-dark rounded-pill px-2 py-1 create2-scroll-link">@lang('Details')</a>
                            <a href="#section-variants" class="btn btn-sm btn-outline-dark rounded-pill px-2 py-1 create2-scroll-link">@lang('Variants')</a>
                            <a href="#section-seo" class="btn btn-sm btn-outline-dark rounded-pill px-2 py-1 create2-scroll-link">@lang('SEO')</a>
                        </nav>
                        <button type="submit" class="btn btn--primary btn-lg px-4" id="generalProductSubmitBtn">
                            <i class="las la-save me-1"></i> @lang('Save Product')
                        </button>
                    </div>
                </div>
            </div>

            {{-- Resell from any website – one page, all steps --}}
            <div class="card mb-3 border-info shadow-sm" id="import-from-website">
                <div class="card-header bg-info bg-opacity-10 d-flex align-items-center">
                    <i class="las la-globe me-2 text-info"></i>
                    <strong>@lang('Resell from any website')</strong>
                    <span class="badge bg-info ms-2">@lang('Optional')</span>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">@lang('Sell products from Amazon, AliExpress, Daraz, or any site. Paste the source URL below, then fill all sections on this page (name, description, images, your price) and save.')</p>
                    <div class="row g-2 align-items-center mb-3">
                        <div class="col-md-8">
                            <label class="form-label small mb-0">@lang('Source product URL')</label>
                            <input type="url" name="source_url" class="form-control form-control-lg" value="{{ old('source_url') }}" placeholder="https://www.amazon.com/... or https://www.aliexpress.com/...">
                        </div>
                    </div>
                    <div class="small text-muted border-top pt-2">
                        <strong>@lang('Steps')</strong>: 1) @lang('Paste source URL above') → 2) @lang('Copy title & description from that page') → 3) @lang('Fill Basic & Details below') → 4) @lang('Upload image in Media') → 5) @lang('Set price in Pricing') → 6) @lang('Save Product')
                    </div>
                </div>
            </div>

            {{-- Single scrollable form: no duplicate tabs, one Save button --}}
            <div class="create2-sections">
                {{-- A. Basic --}}
                <div class="card mb-3 create2-section" id="section-basic">
                    <div class="card-header"><strong>@lang('Basic Product Information')</strong></div>
                    <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6 @error('name') has-error @enderror">
                                    <label class="form-label">@lang('Product Name') <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="@lang('Product name')">
                                    @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">@lang('URL Slug')</label>
                                    <input type="text" name="slug" class="form-control" value="{{ old('slug') }}" placeholder="@lang('Auto-generated if empty')">
                                    <small class="text-muted">@lang('Leave blank to auto-generate from name')</small>
                                </div>
                                <div class="col-md-4 @error('category_id') has-error @enderror">
                                    <label class="form-label">@lang('Category') <span class="text-danger">*</span></label>
                                    <select name="category_id" class="form-control select2-category" id="genProductCategory" required>
                                        <option value="" disabled selected>@lang('Select Category')</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}">{{ __($cat->name) }}</option>
                                        @endforeach
                                    </select>
                                    @error('category_id') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-4 @error('subcategory_id') has-error @enderror">
                                    <label class="form-label">@lang('Subcategory') <span class="text-danger">*</span></label>
                                    <select name="subcategory_id" class="form-control" id="genProductSubcategory" required>
                                        <option value="" disabled selected>@lang('Select Subcategory')</option>
                                    </select>
                                    @error('subcategory_id') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-4 @error('brand_id') has-error @enderror">
                                    <label class="form-label">@lang('Brand') <span class="text-danger">*</span></label>
                                    <select name="brand_id" class="form-control" required>
                                        <option value="" disabled selected>@lang('Select Brand')</option>
                                        @foreach($brands as $brand)
                                            <option value="{{ $brand->id }}" @selected(old('brand_id') == $brand->id)>{{ __($brand->name) }}</option>
                                        @endforeach
                                    </select>
                                    @error('brand_id') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6 @error('product_sku') has-error @enderror">
                                    <label class="form-label">@lang('SKU') <span class="text-danger">*</span></label>
                                    <input type="text" name="product_sku" class="form-control" id="genProductSku" value="{{ old('product_sku') }}" placeholder="@lang('Auto-generated if empty')">
                                    <small class="text-muted">@lang('Leave blank to auto-generate')</small>
                                    @error('product_sku') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                {{-- B. Price & Stock --}}
                <div class="card mb-3 create2-section" id="section-pricing">
                    <div class="card-header"><strong>@lang('Price and Stock')</strong></div>
                    <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">@lang('Original Price')</label>
                                    <div class="input-group">
                                        <input type="number" step="any" name="original_price" class="form-control" value="{{ old('original_price') }}" min="0" placeholder="0">
                                        <span class="input-group-text">{{ $general->cur_text ?? 'BDT' }}</span>
                                    </div>
                                    <small class="text-muted">@lang('Cost/source price for profit calculation')</small>
                                </div>
                                <div class="col-md-4 @error('price') has-error @enderror">
                                    <label class="form-label">@lang('Selling Price') <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" step="any" name="price" class="form-control" value="{{ old('price') }}" min="0" required placeholder="0">
                                        <span class="input-group-text">{{ $general->cur_text ?? 'BDT' }}</span>
                                    </div>
                                    @error('price') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">@lang('Discount Price')</label>
                                    <div class="input-group">
                                        <input type="number" step="any" name="discount" id="genDiscount" class="form-control" value="{{ old('discount') }}" min="0" placeholder="0">
                                        <select name="discount_type" id="genDiscountType" class="input-group-text" style="max-width: 80px;">
                                            <option value="1">{{ $general->cur_text ?? 'BDT' }}</option>
                                            <option value="2" @selected(old('discount_type') == 2)>%</option>
                                        </select>
                                    </div>
                                    <small class="text-muted" id="genDiscountHint"></small>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">@lang('Profit Margin') (%)</label>
                                    <input type="number" step="any" name="profit_margin" class="form-control" value="{{ old('profit_margin') }}" min="0" placeholder="0">
                                </div>
                                <div class="col-12"><hr class="my-2"></div>
                                <div class="col-md-4 @error('quantity') has-error @enderror">
                                    <label class="form-label">@lang('Stock Quantity') <span class="text-danger">*</span></label>
                                    <input type="number" name="quantity" id="genQuantity" class="form-control" value="{{ old('quantity', 1) }}" min="0" required>
                                    @error('quantity') <span class="text-danger small">{{ $message }}</span> @enderror
                                    <div class="alert alert-warning py-2 small mt-2 d-none" id="stockWarningGen" role="alert">
                                        <i class="las la-exclamation-triangle me-1"></i> @lang('Low stock: quantity is at or below alert threshold.')
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">@lang('Low Stock Alert')</label>
                                    <input type="number" name="low_stock_alert" id="genLowStockAlert" class="form-control" value="{{ old('low_stock_alert') }}" min="0" placeholder="e.g. 5">
                                    <small class="text-muted">@lang('Notify when stock below this')</small>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">@lang('Warehouse Location')</label>
                                    <input type="text" name="warehouse_location" class="form-control" value="{{ old('warehouse_location') }}" placeholder="@lang('Optional')">
                                </div>
                                @if(\Schema::hasColumn('products', 'delivery_type'))
                                <div class="col-12"><hr class="my-2"></div>
                                <div class="col-md-4">
                                    <label class="form-label">@lang('Delivery Type')</label>
                                    <select name="delivery_type" class="form-control" id="deliveryTypeSelect">
                                        <option value="free" @selected(old('delivery_type', 'free') === 'free')>@lang('Free Delivery')</option>
                                        <option value="paid" @selected(old('delivery_type') === 'paid')>@lang('Paid Delivery')</option>
                                    </select>
                                </div>
                                <div class="col-md-4" id="deliveryChargeWrap" style="{{ old('delivery_type', 'free') === 'paid' ? '' : 'display:none;' }}">
                                    <label class="form-label">@lang('Delivery Charge')</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" name="delivery_charge" class="form-control" value="{{ old('delivery_charge', 0) }}" min="0" placeholder="0">
                                        <span class="input-group-text">{{ $general->cur_sym ?? '৳' }}</span>
                                    </div>
                                    <small class="text-muted">@lang('Shown when delivery is paid')</small>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                {{-- C. Media --}}
                <div class="card mb-3 create2-section" id="section-media">
                    <div class="card-header"><strong>@lang('Media')</strong> <span class="text-muted small">JPG, PNG, WebP, GIF, MP4, MOV</span></div>
                    <div class="card-body">
                            <div class="alert alert-light border mb-3 small">
                                <strong><i class="las la-cloud-upload-alt me-1"></i> @lang('Advanced upload')</strong> — @lang('Drag & drop or click. Recommended 800×800 or 1000×1000 px. Main image') <span class="text-danger">*</span> + @lang('Gallery (1–6).') @lang('For resell: save image from source site and upload here.')
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">@lang('Main Image') <span class="text-danger">*</span></label>
                                    <div class="profile-uploader">
                                        <div class="payment-method-item">
                                            <div class="payment-method-header d-flex flex-wrap">
                                                <div class="thumb">
                                                    <div class="avatar-preview">
                                                        <div class="profilePicPreview gen-main-preview" style="background-image: url('{{ getImage(getFilePath('product'), getFileSize('product')) }}')"></div>
                                                    </div>
                                                    <div class="avatar-edit">
                                                        <input type="file" name="image" class="profilePicUpload" id="genMainImage" accept=".png,.jpg,.jpeg,.webp" required>
                                                        <label for="genMainImage" class="bg--primary"><i class="la la-pencil"></i></label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">@lang('Gallery Images') (max 6)</label>
                                    <div class="input-images-gen"></div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <label class="form-label">@lang('Product Video') <span class="text-muted small">(@lang('optional'), max 30 sec)</span></label>
                                <input type="file" name="video" class="form-control" accept="video/mp4,video/webm,video/quicktime,video/x-msvideo">
                                <small class="text-muted">MP4, WebM, MOV, AVI.</small>
                            </div>
                            <div class="row g-3 mt-2">
                                <div class="col-md-4">
                                    <label class="form-label">@lang('Is Digital Product?')</label>
                                    <select name="digital_item" class="form-control" id="genDigitalItem">
                                        <option value="0" @selected(old('digital_item', 0) == 0)>@lang('No')</option>
                                        <option value="1" @selected(old('digital_item') == 1)>@lang('Yes')</option>
                                    </select>
                                </div>
                                <div class="col-md-4" id="genFileTypeWrap" style="display: {{ old('digital_item') == 1 ? 'block' : 'none' }};">
                                    <label class="form-label">@lang('Delivery')</label>
                                    <select name="file_type" class="form-control">
                                        <option value="1" @selected(old('file_type', 1) == 1)>@lang('File')</option>
                                        <option value="2" @selected(old('file_type') == 2)>@lang('Link')</option>
                                    </select>
                                </div>
                                <div class="col-md-8" id="genLinkWrap" style="display: {{ (old('digital_item') == 1 && old('file_type') == 2) ? 'block' : 'none' }};">
                                    <label class="form-label">@lang('Product URL')</label>
                                    <input type="url" name="link" class="form-control" value="{{ old('link') }}" placeholder="https://...">
                                </div>
                                <div class="col-md-4"><label class="form-label">@lang('Fabric')</label><input type="text" name="fabric_type" class="form-control" value="{{ old('fabric_type') }}" placeholder="@lang('Optional')"></div>
                                <div class="col-md-4"><label class="form-label">@lang('Material')</label><input type="text" name="material" class="form-control" value="{{ old('material') }}" placeholder="@lang('Optional')"></div>
                                <div class="col-md-4"><label class="form-label">@lang('Season')</label>
                                    <select name="season" class="form-control"><option value="">—</option>@foreach($seasons ?? [] as $key => $label)<option value="{{ $key }}" @selected(old('season') == $key)>{{ __($label) }}</option>@endforeach</select>
                                </div>
                                <div class="col-12"><label class="form-label">@lang('Color Variants')</label><input type="text" name="color_variants" class="form-control" value="{{ is_array(old('color_variants')) ? implode(', ', old('color_variants')) : old('color_variants') }}" placeholder="Red, Blue (comma-separated)"></div>
                            </div>
                        </div>
                    </div>

                {{-- D. Details --}}
                <div class="card mb-3 create2-section" id="section-details">
                    <div class="card-header"><strong>@lang('Short & Full Description')</strong></div>
                    <div class="card-body">
                            <p class="text-muted small mb-3">@lang('Paste or write product details. Include specs, features, material — from any source.')</p>
                            <div class="mb-3">
                                <label class="form-label">@lang('Short Description') <span class="text-danger">*</span></label>
                                <textarea name="summary" id="genSummary" class="form-control" rows="3" required placeholder="@lang('Brief summary for listing')" maxlength="1000">{{ old('summary') }}</textarea>
                                <small class="text-muted"><span id="genSummaryCount">0</span>/1000</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">@lang('Full Description') <span class="text-danger">*</span></label>
                                <textarea name="description" class="form-control nicEdit" rows="6" required placeholder="@lang('Full description – paste from source site if importing')">{{ old('description') }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">@lang('Key Features')</label>
                                <textarea name="key_features" class="form-control" rows="2" placeholder="@lang('Bullet points or short list')">{{ old('key_features') }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">@lang('Specifications')</label>
                                <div class="addedFeatureGen">
                                    @if(old('features'))
                                        @foreach(old('features') as $idx => $f)
                                            <div class="row g-2 mb-2 service-data-gen">
                                                <div class="col-5"><input type="text" name="features[{{ $idx }}][title]" class="form-control" value="{{ $f['title'] ?? '' }}" placeholder="@lang('Title')"></div>
                                                <div class="col-5"><input type="text" name="features[{{ $idx }}][description]" class="form-control" value="{{ $f['description'] ?? '' }}" placeholder="@lang('Value')"></div>
                                                <div class="col-2"><button type="button" class="btn btn-sm btn-danger removeFeatureGen"><i class="las la-times"></i></button></div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                                <button type="button" class="btn btn-sm btn-outline--primary addFeatureGen"><i class="las la-plus"></i> @lang('Add Row')</button>
                            </div>
                        </div>
                    </div>

                {{-- E. Variants --}}
                <div class="card mb-3 create2-section" id="section-variants">
                    <div class="card-header"><strong>@lang('Variants')</strong> <span class="text-muted small">Color, Size, Material</span></div>
                    <div class="card-body">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="has_variants" id="genHasVariants" value="1" {{ old('has_variants') ? 'checked' : '' }}>
                                <label class="form-check-label" for="genHasVariants">@lang('This product has variants (e.g. Color, Size, Storage)')</label>
                            </div>
                            <div id="genVariantsWrap" style="display: {{ old('has_variants') ? 'block' : 'none' }};">
                                <p class="text-muted small mb-2">@lang('Add one row per variant. Example: Color=Red, Size=L, Price=500, Qty=10')</p>
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="genVariantsTable">
                                        <thead>
                                            <tr>
                                                <th>@lang('Attribute (e.g. color)')</th>
                                                <th>@lang('Value (e.g. Red)')</th>
                                                <th>@lang('Price')</th>
                                                <th>@lang('Qty')</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody id="genVariantsBody">
                                            @foreach(old('variant_rows', []) as $vr)
                                            <tr class="variant-row">
                                                <td><input type="text" name="variant_rows[][attr]" class="form-control form-control-sm" value="{{ $vr['attr'] ?? '' }}" placeholder="color"></td>
                                                <td><input type="text" name="variant_rows[][value]" class="form-control form-control-sm" value="{{ $vr['value'] ?? '' }}" placeholder="Red"></td>
                                                <td><input type="number" step="any" name="variant_rows[][price]" class="form-control form-control-sm" value="{{ $vr['price'] ?? '' }}" placeholder="0"></td>
                                                <td><input type="number" name="variant_rows[][qty]" class="form-control form-control-sm" value="{{ $vr['qty'] ?? '0' }}" min="0"></td>
                                                <td><button type="button" class="btn btn-sm btn-outline-danger removeVariantRow"><i class="las la-times"></i></button></td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline--primary" id="addVariantRow">@lang('Add Variant Row')</button>
                            </div>
                        </div>
                    </div>

                {{-- Visibility & Homepage badges --}}
                <div class="card mb-3 create2-section border-info">
                    <div class="card-header bg-info bg-opacity-10"><i class="las la-eye me-1"></i> @lang('Visibility & Homepage Badges')</div>
                    <div class="card-body">
                        <p class="text-muted small mb-3">@lang('Show this product in homepage sections. One spotlight per product (Featured / Hot Deal / Today Deal); Trending Now can be combined.')</p>
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
                                <small class="text-muted d-block mt-1">@lang('This lets you control which homepage line shows the product.')</small>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-medium">@lang('Priority / Rank')</label>
                                <input type="number" name="home_section_rank" class="form-control" value="{{ old('home_section_rank', 0) }}" min="0" max="1000000">
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="home_exclude_from_auto" value="0">
                                    <input class="form-check-input" type="checkbox" name="home_exclude_from_auto" value="1" id="gen_exclude_auto" @checked(old('home_exclude_from_auto'))>
                                    <label class="form-check-label" for="gen_exclude_auto">@lang('Hide from other auto rows')</label>
                                </div>
                            </div>
                        </div>
                        @endif
                        <div class="row g-3">
                            <div class="col-md-6 col-lg-3">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="featured_product" value="0">
                                    <input class="form-check-input" type="checkbox" name="featured_product" value="1" id="gen_featured" {{ old('featured_product') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="gen_featured"><i class="las la-star text-info me-1"></i> @lang('Featured')</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="hot_deals" value="0">
                                    <input class="form-check-input" type="checkbox" name="hot_deals" value="1" id="gen_hot_deals" {{ old('hot_deals') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="gen_hot_deals"><i class="las la-fire text-warning me-1"></i> @lang('Hot Deal')</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="today_deals" value="0">
                                    <input class="form-check-input" type="checkbox" name="today_deals" value="1" id="gen_today_deals" {{ old('today_deals') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="gen_today_deals"><i class="las la-clock text-danger me-1"></i> @lang('Today Deal')</label>
                                </div>
                            </div>
                            @if(\Illuminate\Support\Facades\Schema::hasColumn('products', 'trending_now'))
                            <div class="col-md-6 col-lg-3">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="trending_now" value="0">
                                    <input class="form-check-input" type="checkbox" name="trending_now" value="1" id="gen_trending_now" {{ old('trending_now') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="gen_trending_now"><i class="las la-fire-alt text-success me-1"></i> @lang('Trending Now')</label>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Shipping (compact) --}}
                <div class="card mb-3 create2-section">
                    <div class="card-header">@lang('Shipping')</div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4"><label class="form-label">@lang('Weight') (kg)</label><input type="number" step="any" name="shipping_weight" class="form-control" value="{{ old('shipping_weight') }}" min="0" placeholder="0"></div>
                            <div class="col-md-4"><label class="form-label">@lang('Shipping Class')</label><input type="text" name="shipping_class" class="form-control" value="{{ old('shipping_class') }}" placeholder="e.g. standard"></div>
                            <div class="col-md-4"><label class="form-label">@lang('Delivery Time')</label><input type="text" name="delivery_time" class="form-control" value="{{ old('delivery_time') }}" placeholder="e.g. 2-5 days"></div>
                        </div>
                    </div>
                </div>

                {{-- F. SEO --}}
                <div class="card mb-3 create2-section" id="section-seo">
                    <div class="card-header"><strong>@lang('SEO')</strong></div>
                    <div class="card-body">
                            <p class="text-muted small mb-3">@lang('Improve search ranking. Meta title 50–60 chars, description 150–160 chars ideal.')</p>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">@lang('Meta Title')</label>
                                    <input type="text" name="meta_title" id="genMetaTitle" class="form-control" value="{{ old('meta_title') }}" placeholder="@lang('SEO title')" maxlength="255">
                                    <small class="text-muted"><span id="genMetaTitleCount">0</span>/255</small>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">@lang('Meta Description')</label>
                                    <textarea name="meta_description" id="genMetaDesc" class="form-control" rows="2" placeholder="@lang('SEO description')" maxlength="500">{{ old('meta_description') }}</textarea>
                                    <small class="text-muted"><span id="genMetaDescCount">0</span>/500</small>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">@lang('SEO Keywords')</label>
                                    <input type="text" name="meta_keywords" class="form-control" value="{{ is_array(old('meta_keywords')) ? implode(', ', old('meta_keywords')) : old('meta_keywords') }}" placeholder="keyword1, keyword2">
                                </div>
                            </div>
                        </div>
                    </div>
            </div>

        </form>
    </div>
</div>
@endsection

@push('script-lib')
<script src="{{ asset('assets/admin/js/image-uploader.min.js') }}" defer></script>
@endpush
@push('style-lib')
<link rel="stylesheet" href="{{ asset('assets/admin/css/image-uploader.min.css') }}">
@endpush
@push('style')
<style>
    #import-from-website .card-header { border-left: 4px solid #0dcaf0; }
    #create2QuickNav .btn { font-size: 0.8rem; }
    #create2QuickNav .btn:hover { background: var(--primary); color: #fff; border-color: var(--primary); }
    .nav-tabs-custom .nav-link { font-weight: 500; }
    .nav-tabs-custom .nav-link.active { border-bottom: 2px solid var(--primary); }
</style>
@endpush

@push('script')
<script>
(function($) {
    "use strict";
    var featureIndex = {{ count(old('features', [])) }};
    window.__categorySubcategoriesMap = @json($categorySubcategoriesMap ?? []);
    $('#genProductCategory').on('change', function() {
        var catId = $(this).val();
        var subs = (window.__categorySubcategoriesMap && window.__categorySubcategoriesMap[catId]) || [];
        var html = '<option value="" disabled selected>@lang('Select Subcategory')</option>';
        subs.forEach(function(s) { html += '<option value="' + s.id + '">' + (s.name || '') + '</option>'; });
        $('#genProductSubcategory').html(html).val('');
    });
    @if(old('category_id'))
    $('#genProductCategory').trigger('change');
    setTimeout(function() { $('#genProductSubcategory').val('{{ old('subcategory_id') }}'); }, 100);
    @endif

    $('#genDigitalItem').on('change', function() {
        var isDigital = $(this).val() == '1';
        $('#genFileTypeWrap').toggle(isDigital);
        if (!isDigital) $('#genLinkWrap').hide();
        else $('#genLinkWrap').toggle($('select[name=file_type]').val() == '2');
    });
    $('select[name=file_type]').on('change', function() { $('#genLinkWrap').toggle($(this).val() == '2'); });
    @if(old('digital_item') == 1)
    $('#genFileTypeWrap').show();
    if ($('select[name=file_type]').val() == '2') $('#genLinkWrap').show();
    @endif

    $('#genHasVariants').on('change', function() { $('#genVariantsWrap').toggle($(this).is(':checked')); });
    $('#addVariantRow').on('click', function() {
        var row = '<tr class="variant-row"><td><input type="text" name="variant_rows[][attr]" class="form-control form-control-sm" placeholder="color"></td><td><input type="text" name="variant_rows[][value]" class="form-control form-control-sm" placeholder="Red"></td><td><input type="number" step="any" name="variant_rows[][price]" class="form-control form-control-sm" placeholder="0"></td><td><input type="number" name="variant_rows[][qty]" class="form-control form-control-sm" value="0" min="0"></td><td><button type="button" class="btn btn-sm btn-outline-danger removeVariantRow"><i class="las la-times"></i></button></td></tr>';
        $('#genVariantsBody').append(row);
    });
    $(document).on('click', '.removeVariantRow', function() { $(this).closest('tr').remove(); });

    $('.addFeatureGen').on('click', function() {
        var html = '<div class="row g-2 mb-2 service-data-gen"><div class="col-5"><input type="text" name="features[' + featureIndex + '][title]" class="form-control" placeholder="@lang('Title')"></div><div class="col-5"><input type="text" name="features[' + featureIndex + '][description]" class="form-control" placeholder="@lang('Value')"></div><div class="col-2"><button type="button" class="btn btn-sm btn-danger removeFeatureGen"><i class="las la-times"></i></button></div></div>';
        $('.addedFeatureGen').append(html);
        featureIndex++;
    });
    $(document).on('click', '.removeFeatureGen', function() { $(this).closest('.service-data-gen').remove(); });

    $('.input-images-gen').imageUploader({ imagesInputName: 'gallery', maxFiles: 6 });
    $('#genMainImage').on('change', function() {
        var f = this.files[0];
        if (f && f.type.indexOf('image') !== -1) {
            var r = new FileReader();
            r.onload = function() { $('.gen-main-preview').css('background-image', 'url(' + r.result + ')'); };
            r.readAsDataURL(f);
        }
    });

    // ——— Stock warning: show when quantity <= low_stock_alert ———
    function updateStockWarning() {
        var qty = parseInt($('#genQuantity').val(), 10) || 0;
        var alertVal = parseInt($('#genLowStockAlert').val(), 10);
        if (isNaN(alertVal) || alertVal <= 0) { $('#stockWarningGen').addClass('d-none'); return; }
        if (qty <= alertVal) $('#stockWarningGen').removeClass('d-none'); else $('#stockWarningGen').addClass('d-none');
    }
    $('#genQuantity, #genLowStockAlert').on('input', updateStockWarning);
    updateStockWarning();

    // ——— Auto discount % from original price and selling price ———
    function updateDiscountHint() {
        var orig = parseFloat($('input[name=original_price]').val()) || 0;
        var price = parseFloat($('input[name=price]').val()) || 0;
        var $hint = $('#genDiscountHint');
        if (orig > 0 && price > 0 && orig > price) {
            var pct = Math.round((orig - price) / orig * 100);
            $hint.text('@lang("Suggested discount") ' + pct + '%').css('color', '');
        } else {
            $hint.text('');
        }
    }
    $('input[name=original_price], input[name=price]').on('input', function() {
        updateDiscountHint();
        var orig = parseFloat($('input[name=original_price]').val()) || 0;
        var price = parseFloat($('input[name=price]').val()) || 0;
        if (orig > 0 && price > 0 && orig > price && !$('#genDiscount').data('touched')) {
            var pct = Math.round((orig - price) / orig * 100);
            $('#genDiscountType').val('2');
            $('#genDiscount').val(pct);
        }
    });
    $('#genDiscount').on('input', function() { $(this).data('touched', true); });

    $('#generalProductForm').on('submit', function(e) {
        var $form = $(this);
        var $btn = $('#generalProductSubmitBtn');
        $btn.prop('disabled', true).html('<i class="las la-spinner las la-spin me-1"></i> @lang('Saving...')');
        if ($form.data('ajaxSubmit') !== false) {
            e.preventDefault();
            var formData = new FormData(this);
            var url = $form.attr('action');
            fetch(url, { method: 'POST', body: formData, headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
                .then(function(r) { return r.json().then(function(data) { return { ok: r.ok, status: r.status, data: data }; }).catch(function() { return { ok: r.ok, status: r.status, data: {} }; }); })
                .then(function(res) {
                    if (res.ok && res.data && res.data.redirect) {
                        window.location.href = res.data.redirect;
                    } else {
                        $btn.prop('disabled', false).html('<i class="las la-save me-1"></i> @lang('Save Product')');
                        var msg = (res.data && res.data.message) ? res.data.message : (res.data && res.data.errors) ? Object.values(res.data.errors).flat().join('\n') : '@lang("Something went wrong.")';
                        alert(msg);
                    }
                })
                .catch(function() {
                    $btn.prop('disabled', false).html('<i class="las la-save me-1"></i> @lang('Save Product')');
                    alert('@lang("Request failed. Please try again.")');
                });
        }
    });
    $('#generalProductForm').data('ajaxSubmit', true);

    // ——— Smooth scroll to section ———
    $(document).on('click', '.create2-scroll-link', function(e) {
        var href = $(this).attr('href');
        if (href && href.indexOf('#') === 0) {
            var el = document.querySelector(href);
            if (el) { e.preventDefault(); el.scrollIntoView({ behavior: 'smooth', block: 'start' }); }
        }
    });

    // ——— Auto SKU: generate from category + random if empty ———
    function generateSku() {
        var name = $('input[name=name]').val() || '';
        var cat = $('#genProductCategory option:selected').text() || '';
        if (!name) return '';
        var pre = cat ? cat.replace(/\s+/g, '').substring(0, 4).toUpperCase() : 'PRD';
        var slug = name.toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9\-]/g, '').substring(0, 8);
        return (pre + '-' + (slug || '') + '-' + Date.now().toString(36)).toUpperCase();
    }
    $('#genProductSku').on('focus', function() { if (!$(this).val()) $(this).val(generateSku()); });
    $('input[name=name]').on('blur', function() { var sku = $('#genProductSku'); if (!sku.data('touched') && !sku.val()) sku.val(generateSku()); });
    $('#genProductSku').on('input', function() { $(this).data('touched', true); });

    // ——— Auto slug from name ———
    $('input[name=name]').on('input', function() {
        var s = $('input[name=slug]');
        if (s.length && !s.data('touched')) {
            var v = (this.value || '').toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9\-]/g, '').replace(/-+/g, '-').replace(/^-|-$/g, '');
            if (v) s.val(v);
        }
    });
    $('input[name=slug]').on('input', function() { $(this).data('touched', true); });

    // ——— Delivery type: show/hide delivery charge ———
    $('#deliveryTypeSelect').on('change', function() {
        $('#deliveryChargeWrap').toggle($(this).val() === 'paid');
    });

    // ——— Character counters (SEO & summary) ———
    function genCount(id, val) { var el = document.getElementById(id); if (el) el.textContent = (val || '').length; }
    $('#genMetaTitle').on('input', function() { genCount('genMetaTitleCount', this.value); });
    $('#genMetaDesc').on('input', function() { genCount('genMetaDescCount', this.value); });
    $('#genSummary').on('input', function() { genCount('genSummaryCount', this.value); });
    genCount('genMetaTitleCount', $('#genMetaTitle').val());
    genCount('genMetaDescCount', $('#genMetaDesc').val());
    genCount('genSummaryCount', $('#genSummary').val());
})(jQuery);
</script>
@endpush
