@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-12">
            {{-- Clear, visible header --}}
            <div class="alert alert-warning border-2 border-warning mb-4 shadow-sm" role="alert">
                <div class="d-flex align-items-start gap-3">
                    <i class="las la-database display-4 text-warning flex-shrink-0"></i>
                    <div class="flex-grow-1">
                        <h4 class="alert-heading mb-2">@lang('Database setup required')</h4>
                        <p class="mb-0 fs-6">
                            @lang('The table') <strong><code class="bg-white px-2 py-1 rounded">contact_channel_integrations</code></strong>
                            @lang('does not exist. Create it using one of the options below, then refresh this page.')
                        </p>
                    </div>
                </div>
            </div>

            {{-- Option 1: One-click create (high-advanced) --}}
            <div class="card border-success shadow-sm mb-4">
                <div class="card-header bg-success bg-opacity-10 py-3">
                    <h5 class="mb-0 text-success">
                        <i class="las la-bolt me-2"></i>
                        @lang('Option 1: Create table automatically (recommended)')
                    </h5>
                </div>
                <div class="card-body">
                    <p class="mb-3 text-muted">
                        @lang('Click the button below to run the migration and create the table. No need to copy SQL or use terminal.')
                    </p>
                    <form method="POST" action="{{ route('admin.contact.channels.run.migration') }}" class="d-inline" onsubmit="return confirm('{{ __('Create contact_channel_integrations table now?') }}');">
                        @csrf
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="las la-magic me-2"></i> @lang('Create table now')
                        </button>
                    </form>
                </div>
            </div>

            {{-- Option 2: Artisan command - large and copyable --}}
            <div class="card border-primary shadow-sm mb-4">
                <div class="card-header bg-primary bg-opacity-10 py-3">
                    <h5 class="mb-0 text-primary">
                        <i class="las la-terminal me-2"></i>
                        @lang('Option 2: Run migration from terminal')
                    </h5>
                </div>
                <div class="card-body">
                    <p class="mb-2 text-muted">@lang('From your project root (folder containing artisan), run:')</p>
                    <div class="position-relative bg-dark rounded p-3 mb-2">
                        <pre class="text-light mb-0 fs-6 font-monospace" style="word-break: break-all; white-space: pre-wrap;" id="migrationCmd">php artisan migrate --path=database/migrations/2026_02_11_000001_create_contact_channel_integrations_table.php --force</pre>
                        <button type="button" class="btn btn-sm btn-outline-light position-absolute top-0 end-0 m-2" onclick="copyText('migrationCmd'); this.textContent = '{{ __('Copied!') }}';" title="@lang('Copy')">
                            <i class="las la-copy"></i> @lang('Copy')
                        </button>
                    </div>
                </div>
            </div>

            {{-- Option 3: SQL in phpMyAdmin - full width, large font, copy --}}
            <div class="card border-info shadow-sm mb-4">
                <div class="card-header bg-info bg-opacity-10 py-3">
                    <h5 class="mb-0 text-info">
                        <i class="las la-database me-2"></i>
                        @lang('Option 3: Run SQL in phpMyAdmin')
                    </h5>
                </div>
                <div class="card-body">
                    <ol class="mb-3 ps-3 lh-lg">
                        <li>@lang('Open phpMyAdmin and select your database') <strong>(e.g. staylbd_wintersm)</strong>.</li>
                        <li>@lang('Click the') <strong>@lang('SQL')</strong> @lang('tab.')</li>
                        <li>@lang('Copy the entire code below, paste into the SQL box, then click') <strong>@lang('Go')</strong>.</li>
                    </ol>
                    <div class="position-relative">
                        <pre class="bg-light border-2 border-info border rounded p-4 mb-0 font-monospace fs-6" style="max-height: none; overflow: visible; white-space: pre-wrap; word-break: break-word;" id="sqlBlock">CREATE TABLE IF NOT EXISTS `contact_channel_integrations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `channel` varchar(32) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `settings` json DEFAULT NULL,
  `auth_meta` json DEFAULT NULL,
  `last_synced_at` timestamp NULL DEFAULT NULL,
  `last_error_at` timestamp NULL DEFAULT NULL,
  `last_error_message` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `contact_channel_integrations_channel_index` (`channel`),
  KEY `contact_channel_integrations_is_active_index` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;</pre>
                        <button type="button" class="btn btn-info position-absolute top-0 end-0 m-2" onclick="copyText('sqlBlock'); this.innerHTML = '<i class=\'las la-check\'></i> {{ __('Copied!') }}';" title="@lang('Copy SQL')">
                            <i class="las la-copy me-1"></i> @lang('Copy SQL')
                        </button>
                    </div>
                </div>
            </div>

            {{-- Refresh button - prominent --}}
            <div class="text-center py-3">
                <a href="{{ route('admin.contact.channels.index') }}" class="btn btn-primary btn-lg">
                    <i class="las la-sync-alt me-2"></i> @lang('Refresh page after creating table')
                </a>
            </div>
        </div>
    </div>

    <script>
        function copyText(elementId) {
            var el = document.getElementById(elementId);
            if (!el) return;
            var text = el.innerText || el.textContent;
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text);
            } else {
                var ta = document.createElement('textarea');
                ta.value = text;
                document.body.appendChild(ta);
                ta.select();
                document.execCommand('copy');
                document.body.removeChild(ta);
            }
        }
    </script>
@endsection
