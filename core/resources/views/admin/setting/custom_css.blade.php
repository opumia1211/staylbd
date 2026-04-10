@extends('admin.layouts.app')
@section('panel')
<div class="row mb-none-30">
    <div class="col-12">
        {{-- Quick Actions & Info --}}
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between mb-3">
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('admin.system.optimize') }}" class="btn btn-outline--primary btn-sm"><i class="las la-broom me-1"></i>@lang('Clear Cache')</a>
                        <button type="button" class="btn btn-outline--danger btn-sm" data-bs-toggle="modal" data-bs-target="#resetCssModal"><i class="las la-undo me-1"></i>@lang('Reset to Empty')</button>
                        <button type="button" class="btn btn-outline--info btn-sm" data-bs-toggle="collapse" data-bs-target="#snippetsPanel"><i class="las la-puzzle-piece me-1"></i>@lang('CSS Snippets')</button>
                    </div>
                    @if($lastModified)
                        <small class="text-muted"><i class="las la-clock me-1"></i>@lang('Last modified'): {{ $lastModified }}</small>
                    @endif
                </div>
                <div class="small">
                    <strong>@lang('Template'):</strong> <span class="badge bg-info">{{ $templateName }}</span>
                    <span class="mx-2">|</span>
                    <strong>@lang('File'):</strong> <code class="small">{{ $displayPath }}</code>
                </div>
            </div>
        </div>

        {{-- Info Alert --}}
        <div class="card mb-4 bl--5-primary">
            <div class="card-body">
                <h6 class="text--primary"><i class="las la-info-circle me-1"></i>@lang('About This Page')</h6>
                <p class="mb-1 small">@lang('From this page, you can add/update CSS for the user interface. Changing content on this page required programming knowledge.')</p>
                <p class="mb-0 small text--warning">@lang('Please do not change/edit/add anything without having proper knowledge of it. The website may misbehave due to any mistake you have made.')</p>
                <p class="mb-0 mt-2 small text-muted">@lang('When you deploy to server: the custom.css file deploys with your project. You can edit it from this panel on the server—no extra setup needed.')</p>
            </div>
        </div>

        {{-- CSS Snippets (Collapsible) --}}
        <div class="collapse mb-4" id="snippetsPanel">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="las la-puzzle-piece me-2"></i>@lang('Quick Insert Snippets')</h6>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-6 col-lg-4">
                            <button type="button" class="btn btn-outline--primary btn-sm w-100 snippet-btn" data-snippet="/* Custom utility class */
.my-custom-class {
    color: var(--main);
    font-weight: 600;
}">
                                <i class="las la-tag me-1"></i>@lang('Utility Class')
                            </button>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <button type="button" class="btn btn-outline--primary btn-sm w-100 snippet-btn" data-snippet="/* Button override */
.btn-custom {
    background: var(--main);
    border-radius: 8px;
    padding: 10px 20px;
}">
                                <i class="las la-mouse-pointer me-1"></i>@lang('Button Style')
                            </button>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <button type="button" class="btn btn-outline--primary btn-sm w-100 snippet-btn" data-snippet="/* Responsive breakpoint */
@media (max-width: 768px) {
    .target-element {
        /* mobile styles */
    }
}">
                                <i class="las la-mobile-alt me-1"></i>@lang('Mobile Breakpoint')
                            </button>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <button type="button" class="btn btn-outline--primary btn-sm w-100 snippet-btn" data-snippet="/* Hide element */
.hide-this {
    display: none !important;
}">
                                <i class="las la-eye-slash me-1"></i>@lang('Hide Element')
                            </button>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <button type="button" class="btn btn-outline--primary btn-sm w-100 snippet-btn" data-snippet="/* Spacing utility */
.mt-custom { margin-top: 20px; }
.mb-custom { margin-bottom: 20px; }
.pt-custom { padding-top: 20px; }
.pb-custom { padding-bottom: 20px; }">
                                <i class="las la-arrows-alt me-1"></i>@lang('Spacing')
                            </button>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <button type="button" class="btn btn-outline--primary btn-sm w-100 snippet-btn" data-snippet="/* Card/Box style */
.card-custom {
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
}">
                                <i class="las la-square me-1"></i>@lang('Card Style')
                            </button>
                        </div>
                    </div>
                    <p class="mb-0 mt-2 small text-muted">@lang('Click a snippet to insert at cursor. Position cursor in editor first.')</p>
                </div>
            </div>
        </div>

        {{-- Editor --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                <h6 class="mb-0">@lang('Write Custom CSS')</h6>
                <div class="d-flex gap-2 align-items-center">
                    <span class="badge bg-secondary" id="lineCount">0 @lang('lines')</span>
                    <span class="badge bg-secondary" id="charCount">0 @lang('chars')</span>
                    <button type="button" class="btn btn-sm btn-outline--primary" id="fullscreenBtn" title="@lang('Fullscreen')"><i class="las la-expand"></i></button>
                </div>
            </div>
            <form action="{{ route('admin.setting.custom.css') }}" method="post">
                @csrf
                <div class="card-body">
                    <div class="form-group custom-css">
                        <textarea class="form-control customCss" rows="10" name="css">{{ $fileContent }}</textarea>
                    </div>
                </div>
                <div class="card-footer d-flex flex-wrap gap-2 justify-content-between align-items-center">
                    <div class="small text-muted">
                        <i class="las la-save me-1"></i>@lang('Save to'): <code>assets/templates/{{ $templateName }}/css/custom.css</code>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn--primary h-45"><i class="las la-save me-1"></i>@lang('Save CSS')</button>
                    </div>
                </div>
            </form>
        </div>

        {{-- After Save Note --}}
        <div class="alert alert-info mt-4 mb-0">
            <i class="las la-lightbulb me-2"></i>@lang('After saving, clear cache if changes do not appear:') <a href="{{ route('admin.system.optimize') }}" class="alert-link">@lang('Clear Cache')</a>
        </div>
    </div>
</div>

{{-- Reset Confirmation Modal --}}
<div class="modal fade" id="resetCssModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Reset Custom CSS')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">@lang('Are you sure you want to clear all custom CSS? This cannot be undone.')</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('Cancel')</button>
                <form action="{{ route('admin.setting.custom.css.reset') }}" method="post" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn--danger">@lang('Reset to Empty')</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('style')

{{-- inline style moved to critical-admin.css --}}

@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/codemirror.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/css.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/sublime.min.js') }}"></script>
@endpush

@push('script')
<script>
    "use strict";
    var editor = CodeMirror.fromTextArea(document.getElementsByClassName("customCss")[0], {
        lineNumbers: true,
        mode: "text/css",
        theme: "monokai",
        keyMap: "sublime",
        autoCloseBrackets: true,
        matchBrackets: true,
        showCursorWhenSelecting: true,
        indentUnit: 4,
        indentWithTabs: false
    });

    function updateStats() {
        var doc = editor.getDoc();
        var lines = doc.lineCount();
        var chars = doc.getValue().length;
        document.getElementById('lineCount').textContent = lines + ' {{ __("lines") }}';
        document.getElementById('charCount').textContent = chars + ' {{ __("chars") }}';
    }
    editor.on('change', updateStats);
    updateStats();

    document.getElementById('fullscreenBtn').addEventListener('click', function() {
        var cm = document.querySelector('.CodeMirror');
        cm.classList.toggle('CodeMirror-fullscreen');
        this.querySelector('i').classList.toggle('la-expand');
        this.querySelector('i').classList.toggle('la-compress');
    });

    document.querySelectorAll('.snippet-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var snippet = this.getAttribute('data-snippet');
            var doc = editor.getDoc();
            var cursor = doc.getCursor();
            doc.replaceRange(snippet, cursor);
        });
    });
</script>
@endpush
