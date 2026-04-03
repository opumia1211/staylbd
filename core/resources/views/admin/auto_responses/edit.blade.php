@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-10 col-xl-8">
            <div class="card b-radius--10">
                <div class="card-body">
                    <h5 class="mb-4"><i class="las la-pen me-2 text--primary"></i>@lang('Edit Auto AI Reply Rule')</h5>
                    <form action="{{ route('admin.autoai.update', $item->id) }}" method="post" class="admin-auto-response-update-form">
                        @csrf
                        <div class="form-group mb-4">
                            <label class="form-label">@lang('Rule name') <span class="text-muted">(@lang('optional'))</span></label>
                            <input type="text" class="form-control form--control" name="name" value="{{ old('name', $item->name) }}" placeholder="@lang('e.g. Refund inquiry')">
                        </div>
                        <div class="form-group mb-4">
                            <label class="form-label">@lang('Keywords') <span class="text-danger">*</span></label>
                            @php
                                $keywordsDisplay = $item->getKeywordsList();
                                $keywordsText = old('keywords_input', implode("\n", $keywordsDisplay));
                            @endphp
                            <textarea class="form-control form--control" name="keywords_input" rows="5" required placeholder="refund, return, রিফান্ড">{{ $keywordsText }}</textarea>
                            <small class="text-muted d-block mt-1">
                                @lang('One per line or comma-separated. Any language; case-insensitive match.')
                            </small>
                            @error('keywords_input')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group mb-4">
                            <label class="form-label">@lang('Auto Reply Message') <span class="text-danger">*</span></label>
                            <textarea class="form-control form--control" name="message" rows="5" required>{{ old('message', $item->message) }}</textarea>
                        </div>
                        <div class="form-group mb-4">
                            <label class="form-label">@lang('Visibility')</label>
                            <div class="d-flex gap-4 flex-wrap">
                                @php $isPublic = old('is_public') !== null ? (old('is_public') == 1 || old('is_public') === '1') : ($item->is_public ?? true); @endphp
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="is_public" id="visibility_public" value="1" {{ $isPublic ? 'checked' : '' }}>
                                    <label class="form-check-label" for="visibility_public"><i class="las la-globe text-success me-1"></i>@lang('Public') — @lang('Send this reply to users when keyword matches')</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="is_public" id="visibility_private" value="0" {{ !$isPublic ? 'checked' : '' }}>
                                    <label class="form-check-label" for="visibility_private"><i class="las la-lock text-secondary me-1"></i>@lang('Private') — @lang('Do not send to users; store for reference only')</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-4">
                            <div class="form-check form-switch">
                                <input type="hidden" name="is_active" value="0">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $item->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">@lang('Active') — @lang('Inactive rules are not used for auto-reply')</label>
                            </div>
                        </div>
                        <hr>
                        <button type="submit" class="btn btn--primary">@lang('Update Rule')</button>
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
                        <li class="mb-2">• @lang('You can add many keywords; user message matching any of them will trigger this reply.')</li>
                        <li class="mb-2">• @lang('Use different languages (e.g. English + Bengali) for the same intent.')</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
