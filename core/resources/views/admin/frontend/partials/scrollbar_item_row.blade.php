@php
    $item = is_array($item ?? null) ? $item : (array) ($item ?? []);
    $type = $item['type'] ?? 'text';
    $rawContent = $type === 'image' ? '' : ($item['content_text'] ?? $item['content'] ?? '');
    $contentValue = old("items.$idx.content_text", is_string($rawContent) ? $rawContent : (string) $rawContent);
    $imageValue = $type === 'image' ? ($item['content'] ?? $item['content_image'] ?? '') : ($item['content_image'] ?? '');
    $color = old("items.$idx.color", $item['color'] ?? '#333333');
    $fontFamily = old("items.$idx.font_family", $item['font_family'] ?? 'inherit');
    $fontStyle = old("items.$idx.font_style", $item['font_style'] ?? 'normal');
    $fontSize = old("items.$idx.font_size", $item['font_size'] ?? '');
    $fontWeight = old("items.$idx.font_weight", $item['font_weight'] ?? '400');
    $letterSpacing = old("items.$idx.letter_spacing", $item['letter_spacing'] ?? '');
    $textTransform = old("items.$idx.text_transform", $item['text_transform'] ?? 'none');
    $isActive = (int) old("items.$idx.is_active", $item['is_active'] ?? 1);
    $charCount = mb_strlen($contentValue ?? '', 'UTF-8');
@endphp

<div class="scrollbar-item-row border rounded p-3 mb-3" data-idx="{{ $idx }}">
    <div class="row g-2 align-items-end">
        <div class="col-md-2">
            <label class="form-label small">@lang('Type')</label>
            <select class="form-select form-select-sm item-type" name="items[{{ $idx }}][type]">
                <option value="text" {{ $type === 'text' ? 'selected' : '' }}>@lang('Text')</option>
                <option value="emoji" {{ $type === 'emoji' ? 'selected' : '' }}>@lang('Emoji')</option>
                <option value="image" {{ $type === 'image' ? 'selected' : '' }}>@lang('Image')</option>
            </select>
        </div>
        <div class="col-md-2 item-text-wrap {{ $type === 'image' ? 'd-none' : '' }}">
            <label class="form-label small">@lang('Color')</label>
            <div class="d-flex align-items-center gap-1">
                @php
                    $colorHex = preg_match('/^#[0-9A-Fa-f]{6}$/', $color) ? $color : '#333333';
                @endphp
                <input type="color" class="form-control form-control-color border item-color-picker" value="{{ $colorHex }}" title="@lang('Pick color')" style="width:2.25rem;height:2rem;min-width:2.25rem;padding:2px;cursor:pointer;">
                <input type="text" class="form-control form-control-sm item-color flex-grow-1" name="items[{{ $idx }}][color]" value="{{ $color }}" placeholder="#333" maxlength="20">
            </div>
        </div>
        <div class="col-md-2">
            <label class="form-label small">@lang('Font')</label>
            <input type="text" class="form-control form-control-sm" name="items[{{ $idx }}][font_family]" value="{{ $fontFamily }}">
        </div>
        <div class="col-md-2">
            <label class="form-label small">@lang('Style')</label>
            <select class="form-select form-select-sm" name="items[{{ $idx }}][font_style]">
                <option value="normal" {{ $fontStyle === 'normal' ? 'selected' : '' }}>@lang('Normal')</option>
                <option value="bold" {{ $fontStyle === 'bold' ? 'selected' : '' }}>@lang('Bold')</option>
                <option value="italic" {{ $fontStyle === 'italic' ? 'selected' : '' }}>@lang('Italic')</option>
            </select>
        </div>
        <div class="col-md-1">
            <label class="form-label small">@lang('Size')</label>
            <input type="text" class="form-control form-control-sm item-font-size" name="items[{{ $idx }}][font_size]" value="{{ $fontSize }}">
        </div>
        <div class="col-md-1">
            <label class="form-label small">@lang('Weight')</label>
            <input type="text" class="form-control form-control-sm" name="items[{{ $idx }}][font_weight]" value="{{ $fontWeight }}">
        </div>
        <div class="col-md-1">
            <label class="form-label small">@lang('Spacing')</label>
            <input type="text" class="form-control form-control-sm" name="items[{{ $idx }}][letter_spacing]" value="{{ $letterSpacing }}">
        </div>
        <div class="col-md-1">
            <label class="form-label small">@lang('Transform')</label>
            <select class="form-select form-select-sm" name="items[{{ $idx }}][text_transform]">
                <option value="none" {{ $textTransform === 'none' ? 'selected' : '' }}>@lang('None')</option>
                <option value="uppercase" {{ $textTransform === 'uppercase' ? 'selected' : '' }}>@lang('Upper')</option>
                <option value="lowercase" {{ $textTransform === 'lowercase' ? 'selected' : '' }}>@lang('Lower')</option>
                <option value="capitalize" {{ $textTransform === 'capitalize' ? 'selected' : '' }}>@lang('Cap')</option>
            </select>
        </div>
        <div class="col-md-1">
            <label class="form-label small">@lang('Show')</label>
            <div class="form-check mt-1">
                <input type="checkbox" class="form-check-input item-is-active" name="items[{{ $idx }}][is_active]" value="1" {{ $isActive === 1 ? 'checked' : '' }}>
            </div>
        </div>
        <div class="col-md-2">
            <label class="form-label small">&nbsp;</label>
            <div class="btn-group btn-group-sm w-100">
                <button type="button" class="btn btn-outline-secondary move-item-up" title="@lang('Move up')"><i class="las la-arrow-up"></i></button>
                <button type="button" class="btn btn-outline-secondary move-item-down" title="@lang('Move down')"><i class="las la-arrow-down"></i></button>
                <button type="button" class="btn btn-danger remove-item" title="@lang('Remove')"><i class="las la-times"></i></button>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 mt-2 item-content-wrap {{ $type === 'image' ? 'd-none' : '' }}">
            <label class="form-label small">@lang('Content (text, max 2000 characters)')</label>
            <textarea class="form-control item-content scrollbar-content-field"
                      name="items[{{ $idx }}][content_text]"
                      rows="3"
                      maxlength="2000">{{ $type === 'image' ? '' : $contentValue }}</textarea>
            <small class="text-muted item-char-count">{{ $type === 'image' ? 0 : $charCount }} / 2000</small>
            @error("items.$idx.content_text")
                <span class="text-danger small d-block">{{ $message }}</span>
            @enderror
        </div>
        <div class="col-12 mt-2 item-image-wrap {{ $type === 'image' ? '' : 'd-none' }}">
            <label class="form-label small">@lang('Image')</label>
            <input type="hidden" name="items[{{ $idx }}][content_image]" class="item-image-content" value="{{ $imageValue }}">
            <input type="file" class="form-control form-control-sm item-image-file" name="items[{{ $idx }}][image_file]" accept="image/*">
            @if($type === 'image' && $imageValue)
                <small class="text-muted d-block mt-1">@lang('Current'): {{ $imageValue }}</small>
            @endif
        </div>
    </div>
</div>
