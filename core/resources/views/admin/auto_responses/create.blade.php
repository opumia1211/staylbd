@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-10 col-xl-8">
            <div class="card b-radius--10">
                <div class="card-body">
                    <h5 class="mb-4"><i class="las la-plus-circle me-2 text--primary"></i>@lang('Add Auto AI Reply Rule')</h5>
                    <form action="{{ route('admin.autoai.store') }}" method="post">
                        @csrf
                        <div class="form-group mb-4">
                            <label class="form-label">@lang('Rule name') <span class="text-muted">(@lang('optional'))</span></label>
                            <input type="text" class="form-control form--control" name="name" value="{{ old('name') }}" placeholder="@lang('e.g. Refund inquiry, Order status')">
                            <small class="text-muted">@lang('A short name to identify this rule in the list.')</small>
                        </div>
                        <div class="form-group mb-4">
                            <label class="form-label">@lang('Keywords') <span class="text-danger">*</span></label>
                            <textarea class="form-control form--control" name="keywords_input" rows="5" required placeholder="refund&#10;return&#10;money back&#10;রিফান্ড&#10;ফেরত">{{ old('keywords_input') }}</textarea>
                            <small class="text-muted d-block mt-1">
                                @lang('One keyword per line, or separate by commas. User message in any language containing any of these words will trigger this reply. Case-insensitive.')
                            </small>
                            @error('keywords_input')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group mb-4">
                            <label class="form-label">@lang('Auto Reply Message') <span class="text-danger">*</span></label>
                            <textarea class="form-control form--control" name="message" rows="5" required placeholder="@lang('Type the reply that will be sent automatically...')">{{ old('message') }}</textarea>
                        </div>
                        <div class="form-group mb-4">
                            <label class="form-label">@lang('Visibility')</label>
                            <div class="d-flex gap-4 flex-wrap">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="is_public" id="visibility_public" value="1" {{ old('is_public', '1') == '1' || old('is_public') === null ? 'checked' : '' }}>
                                    <label class="form-check-label" for="visibility_public"><i class="las la-globe text-success me-1"></i>@lang('Public') — @lang('Send this reply to users when keyword matches')</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="is_public" id="visibility_private" value="0" {{ old('is_public') === '0' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="visibility_private"><i class="las la-lock text-secondary me-1"></i>@lang('Private') — @lang('Do not send to users; store for reference only')</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-4">
                            <div class="form-check form-switch">
                                <input type="hidden" name="is_active" value="0">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">@lang('Active') — @lang('Inactive rules are not used for auto-reply')</label>
                            </div>
                        </div>
                        <hr>
                        <button type="submit" class="btn btn--primary">@lang('Save Rule')</button>
                        <a href="{{ route('admin.autoai.index') }}" class="btn btn--dark">@lang('Cancel')</a>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-xl-4">
            <div class="card b-radius--10">
                <div class="card-body">
                    <h6 class="mb-3"><i class="las la-lightbulb text--warning me-2"></i>@lang('Tips')</h6>
                    <ul class="list-unstyled small text-muted mb-0">
                        <li class="mb-2">• @lang('Add keywords in multiple languages (e.g. refund, রিফান্ড) to cover more users.')</li>
                        <li class="mb-2">• @lang('First matching rule wins; order rules by priority in the list if needed.')</li>
                        <li class="mb-2">• @lang('Keep keywords short and relevant to avoid false matches.')</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
