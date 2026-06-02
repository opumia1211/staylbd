@extends('admin.layouts.app')

@section('panel')
    <div class="row mb-3">
        <div class="col-12">
            <a href="{{ route('admin.orders.channels.index') }}" class="btn btn-sm btn-outline-secondary"><i class="las la-arrow-left"></i> @lang('Channels')</a>
            <h5 class="mb-0 text-dark fw-bold mt-2">{{ $pageTitle }}</h5>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border shadow-sm">
                <div class="card-body">
                    <form action="{{ $channel->exists ? route('admin.orders.channels.update', $channel->id) : route('admin.orders.channels.store') }}" method="post">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">@lang('Channel name')</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $channel->name) }}" required maxlength="120">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">@lang('Platform')</label>
                                <select name="platform" class="form-select" required>
                                    @foreach($platforms as $key => $label)
                                        <option value="{{ $key }}" @selected(old('platform', $channel->platform) === $key)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">@lang('Direction')</label>
                                <select name="direction" class="form-select" required>
                                    @foreach($directions as $key => $label)
                                        <option value="{{ $key }}" @selected(old('direction', $channel->direction) === $key)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">@lang('Store API URL')</label>
                            <input type="url" name="api_url" class="form-control" value="{{ old('api_url', $channel->api_url) }}" placeholder="https://your-store.com/wp-json/">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">@lang('API Key / Token')</label>
                            <input type="text" name="api_key" class="form-control" value="{{ old('api_key', $channel->api_key) }}" autocomplete="off">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">@lang('Webhook secret header') <small class="text-muted">(X-Channel-Secret)</small></label>
                            <input type="text" name="webhook_secret" class="form-control" value="{{ old('webhook_secret', $channel->settings['webhook_secret'] ?? '') }}">
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="chActive" @checked(old('is_active', $channel->is_active))>
                            <label class="form-check-label" for="chActive">@lang('Active')</label>
                        </div>

                        <button type="submit" class="btn btn-primary">@lang('Save')</button>
                    </form>
                </div>
            </div>
        </div>

        @if($channel->exists)
        <div class="col-lg-4">
            <div class="card border shadow-sm mb-3">
                <div class="card-header"><h6 class="mb-0">@lang('Webhook URL')</h6></div>
                <div class="card-body">
                    <code class="small d-block text-break user-select-all">{{ $channel->webhookUrl() }}</code>
                    <p class="text-muted small mt-2 mb-2">@lang('POST JSON: external_id, customer_name, phone, email, address, total')</p>
                    <form action="{{ route('admin.orders.channels.regenerate-token', $channel->id) }}" method="post" onsubmit="return confirm('@lang('Regenerate webhook URL? Old URL will stop working.')');">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger">@lang('Regenerate URL')</button>
                    </form>
                </div>
            </div>
            <div class="card border bg-light">
                <div class="card-body small text-secondary">
                    <strong>@lang('Example payload')</strong>
                    <pre class="mt-2 mb-0 p-2 bg-white border rounded small">{
  "external_id": "WC-1001",
  "customer_name": "John",
  "phone": "01700000000",
  "total": 1500
}</pre>
                </div>
            </div>
        </div>
        @endif
    </div>
@endsection
