@extends('admin.layouts.app')
@section('panel')
<div class="row mb-none-30">
    <div class="col-12">
        {{-- Quick Info --}}
        <div class="alert alert-info mb-4">
            <h6 class="alert-heading"><i class="las la-info-circle me-1"></i>@lang('About This Page')</h6>
            <p class="mb-0 small">@lang('Submit bug reports or feature requests. Reports are stored locally in your database. When you deploy to server, existing reports move with the database—no extra setup needed.')</p>
        </div>

        {{-- Stats Cards --}}
        @if(isset($stats))
        <div class="row mb-4">
            <div class="col-sm-6 col-lg-3">
                <div class="card bg--primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="text-white mb-0">{{ $stats['total'] ?? 0 }}</h4>
                                <small class="text-white opacity-75">@lang('Total Reports')</small>
                            </div>
                            <i class="las la-list-alt me-2" style="font-size: 2rem; opacity: 0.6;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card bg--danger">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="text-white mb-0">{{ $stats['bugs'] ?? 0 }}</h4>
                                <small class="text-white opacity-75">@lang('Bug Reports')</small>
                            </div>
                            <i class="las la-bug me-2" style="font-size: 2rem; opacity: 0.6;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card bg--primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="text-white mb-0">{{ $stats['features'] ?? 0 }}</h4>
                                <small class="text-white opacity-75">@lang('Feature Requests')</small>
                            </div>
                            <i class="las la-lightbulb me-2" style="font-size: 2rem; opacity: 0.6;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card bg--warning">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="text-dark mb-0">{{ $stats['pending'] ?? 0 }}</h4>
                                <small class="text-dark opacity-75">@lang('Pending')</small>
                            </div>
                            <i class="las la-clock me-2" style="font-size: 2rem; opacity: 0.6;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Filters --}}
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.request.report') }}" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">@lang('Type')</label>
                        <select class="form-control" name="type">
                            <option value="">@lang('All')</option>
                            <option value="bug" {{ request('type') == 'bug' ? 'selected' : '' }}>@lang('Bug Report')</option>
                            <option value="feature" {{ request('type') == 'feature' ? 'selected' : '' }}>@lang('Feature Request')</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">@lang('Status')</label>
                        <select class="form-control" name="status">
                            <option value="">@lang('All')</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>@lang('Pending')</option>
                            <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>@lang('Read')</option>
                            <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>@lang('Resolved')</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn--primary w-100"><i class="las la-filter me-1"></i>@lang('Filter')</button>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <a href="{{ route('admin.request.report') }}" class="btn btn-outline--primary w-100">@lang('Clear')</a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Reports Table --}}
        <div class="card b-radius--10">
            <div class="card-body p-0">
                <div class="table-responsive--md table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th>@lang('Date')</th>
                                <th>@lang('Type')</th>
                                <th>@lang('Message')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Submitted By')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reports as $report)
                                <tr>
                                    <td>{{ $report->created_at->format('M d, Y H:i') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $report->type_badge }}">
                                            {{ $report->type === 'bug' ? __('Report Bug') : __('Feature Request') }}
                                        </span>
                                    </td>
                                    <td class="text-break" style="max-width: 300px;">{{ Str::limit($report->message, 100) }}</td>
                                    <td>
                                        <form action="{{ route('admin.request.report.status', $report->id) }}" method="post" class="d-inline">
                                            @csrf
                                            <select class="form-select form-select-sm status-select" name="status" style="width: auto;">
                                                <option value="pending" {{ $report->status == 'pending' ? 'selected' : '' }}>@lang('Pending')</option>
                                                <option value="read" {{ $report->status == 'read' ? 'selected' : '' }}>@lang('Read')</option>
                                                <option value="resolved" {{ $report->status == 'resolved' ? 'selected' : '' }}>@lang('Resolved')</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td>{{ $report->admin_name ?? '-' }}</td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <button type="button" class="btn btn-sm btn-outline--info view-report" data-message="{{ e($report->message) }}" data-type="{{ $report->type }}" data-date="{{ $report->created_at->format('M d, Y H:i') }}" title="@lang('View')"><i class="las la-eye"></i></button>
                                            <form action="{{ route('admin.request.report.delete', $report->id) }}" method="post" class="d-inline" onsubmit="return confirm('@lang('Are you sure to delete this report?')');">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline--danger" title="@lang('Delete')"><i class="las la-trash"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">{{ __($emptyMessage ?? 'No reports yet') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if(isset($reports) && $reports->hasPages())
            <div class="card-footer">
                {{ $reports->links() }}
            </div>
            @endif
        </div>

        {{-- View Modal --}}
        <div class="modal fade" id="viewReportModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">@lang('Report Details')</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-2"><strong>@lang('Type'):</strong> <span id="viewType"></span></p>
                        <p class="mb-2"><strong>@lang('Date'):</strong> <span id="viewDate"></span></p>
                        <p class="mb-0"><strong>@lang('Message'):</strong></p>
                        <div id="viewMessage" class="border rounded p-3 mt-2 bg-light"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Submit Modal --}}
        <div class="modal fade" id="bugModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">@lang('Report & Request')</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.request.report') }}" method="post">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group mb-3">
                                <label>@lang('Type')</label>
                                <select class="form-control" name="type" required>
                                    <option value="bug" {{ old('type') == 'bug' ? 'selected' : '' }}>@lang('Report Bug')</option>
                                    <option value="feature" {{ old('type') == 'feature' ? 'selected' : '' }}>@lang('Feature Request')</option>
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label>@lang('Message')</label>
                                <textarea class="form-control" name="message" rows="5" required placeholder="@lang('Describe the bug or feature request in detail...')" maxlength="5000">{{ old('message') }}</textarea>
                                <small class="text-muted">@lang('Max 5000 characters')</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn--primary w-100 h-45"><i class="las la-paper-plane me-1"></i>@lang('Submit')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('breadcrumb-plugins')
    <button class="btn btn-sm btn-outline--primary" data-bs-toggle="modal" data-bs-target="#bugModal"><i class="las la-bug"></i> @lang('Report a bug')</button>
    <a href="https://viserlab.com/support" target="_blank" class="btn btn-sm btn-outline--primary"><i class="las la-headset"></i> @lang('Request for Support')</a>
@endpush

@push('script')
<script>
(function() {
    document.querySelectorAll('.status-select').forEach(function(sel) {
        sel.addEventListener('change', function() {
            this.closest('form').submit();
        });
    });

    document.querySelectorAll('.view-report').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.getElementById('viewType').textContent = this.dataset.type === 'bug' ? '{{ __("Report Bug") }}' : '{{ __("Feature Request") }}';
            document.getElementById('viewDate').textContent = this.dataset.date;
            document.getElementById('viewMessage').textContent = this.dataset.message;
            new bootstrap.Modal(document.getElementById('viewReportModal')).show();
        });
    });
})();
</script>
@endpush
