@extends('admin.layouts.app')

@section('panel')
<div class="container-xxl flex-grow-1 container-p-y p-0">
    {{-- ── Tactical IDE Header ── --}}
    <div class="row mb-3 g-3 align-items-center">
        <div class="col-md-6">
            <div class="d-flex align-items-center gap-2">
                <div class="bg-label-primary p-2 rounded">
                    <i class="las la-terminal fs-4 text-primary"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold">@lang('Custom CSS IDE')</h5>
                    <div class="d-flex align-items-center gap-2 small text-muted">
                        <span class="badge bg-label-info rounded-pill tiny">@lang('v3.0 Stable')</span>
                        <span class="mx-1">|</span>
                        <code class="tiny text-primary">{{ $displayPath }}</code>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 text-md-end">
            <div class="btn-group shadow-sm">
                <button type="button" class="btn btn-primary" id="saveBtn">
                    <i class="las la-save me-1"></i> @lang('Push to Production')
                </button>
                <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown"></button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="javascript:void(0)" id="beautifyBtn"><i class="las la-magic me-2"></i>@lang('Beautify Code')</a></li>
                    <li><a class="dropdown-item" href="javascript:void(0)" id="searchBtn"><i class="las la-search me-2"></i>@lang('Search & Replace')</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#resetCssModal"><i class="las la-trash-alt me-2"></i>@lang('Wipe Infrastructure')</a></li>
                </ul>
            </div>
            <a href="{{ route('admin.system.optimize') }}" class="btn btn-outline-secondary ms-2 shadow-sm">
                <i class="las la-broom"></i>
            </a>
        </div>
    </div>

    <div class="row g-0 border rounded shadow-sm overflow-hidden bg-white" style="min-height: 700px;">
        {{-- ── Sidebar: Strategic Intelligence ── --}}
        <div class="col-xl-3 col-lg-4 border-end bg-lighter d-none d-lg-block overflow-auto" style="max-height: 700px;">
            <div class="p-4">
                <div class="mb-4">
                    <label class="form-label fw-bold small text-uppercase text-muted ls-1 mb-3">@lang('Advanced Snippets')</label>
                    <div class="accordion accordion-flush custom-snippets" id="snippetAccordion">
                        <div class="accordion-item bg-transparent">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed py-2 px-0 bg-transparent fw-bold small" type="button" data-bs-toggle="collapse" data-bs-target="#accGlass">
                                    <i class="las la-glass-martini me-2 text-primary"></i>@lang('Glassmorphism')
                                </button>
                            </h2>
                            <div id="accGlass" class="accordion-collapse collapse" data-bs-parent="#snippetAccordion">
                                <div class="accordion-body px-0 py-2">
                                    <button class="btn btn-sm btn-outline-primary w-100 mb-2 snippet-btn text-start" data-snippet=".glass-node { background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 15px; }">
                                        <i class="las la-plus me-1"></i> @lang('Frosty Card')
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item bg-transparent">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed py-2 px-0 bg-transparent fw-bold small" type="button" data-bs-toggle="collapse" data-bs-target="#accAnim">
                                    <i class="las la-play-circle me-2 text-primary"></i>@lang('Micro-Animations')
                                </button>
                            </h2>
                            <div id="accAnim" class="accordion-collapse collapse" data-bs-parent="#snippetAccordion">
                                <div class="accordion-body px-0 py-2">
                                    <button class="btn btn-sm btn-outline-primary w-100 mb-2 snippet-btn text-start" data-snippet="@keyframes pulse { 0% { transform: scale(1); } 50% { transform: scale(1.05); } 100% { transform: scale(1); } } .pulse { animation: pulse 2s infinite; }">
                                        <i class="las la-plus me-1"></i> @lang('Pulse Engine')
                                    </button>
                                    <button class="btn btn-sm btn-outline-primary w-100 mb-2 snippet-btn text-start" data-snippet=".hover-lift { transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); } .hover-lift:hover { transform: translateY(-5px); }">
                                        <i class="las la-plus me-1"></i> @lang('Smooth Lift')
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item bg-transparent">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed py-2 px-0 bg-transparent fw-bold small" type="button" data-bs-toggle="collapse" data-bs-target="#accLayout">
                                    <i class="las la-th-large me-2 text-primary"></i>@lang('Layout Clusters')
                                </button>
                            </h2>
                            <div id="accLayout" class="accordion-collapse collapse" data-bs-parent="#snippetAccordion">
                                <div class="accordion-body px-0 py-2">
                                    <button class="btn btn-sm btn-outline-primary w-100 mb-2 snippet-btn text-start" data-snippet=".grid-center { display: grid; place-items: center; min-height: 200px; }">
                                        <i class="las la-plus me-1"></i> @lang('Perfect Center')
                                    </button>
                                    <button class="btn btn-sm btn-outline-primary w-100 mb-2 snippet-btn text-start" data-snippet=".flex-between { display: flex; align-items: center; justify-content: space-between; gap: 15px; }">
                                        <i class="las la-plus me-1"></i> @lang('Strategic Flex')
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold small text-uppercase text-muted ls-1 mb-3">@lang('Color Intelligence')</label>
                    <div class="p-3 bg-white border rounded shadow-sm">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="small fw-bold">@lang('Live Picker')</span>
                            <input type="color" class="form-control form-control-sm p-0 border-0" id="colorPicker" value="#696cff" style="width: 30px; height: 30px; cursor: pointer;">
                        </div>
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control bg-lighter border-0" id="colorHex" value="#696cff" readonly>
                            <button class="btn btn-outline-primary" type="button" id="copyColor"><i class="las la-copy"></i></button>
                        </div>
                    </div>
                </div>

                <div class="bg-label-info p-3 rounded small border-start border-4 border-info">
                    <i class="las la-info-circle me-1"></i> @lang('Use CTRL+F for search, CTRL+S to save. Editor supports Sublime key-mapping.')
                </div>
            </div>
        </div>

        {{-- ── Main: The Code Matrix ── --}}
        <div class="col-xl-9 col-lg-8 position-relative">
            <form action="{{ route('admin.setting.custom.css') }}" method="post" id="cssForm">
                @csrf
                <div class="editor-wrapper">
                    <textarea class="form-control customCss" name="css">{{ $fileContent }}</textarea>
                </div>
                {{-- Status Bar --}}
                <div class="bg-dark text-white px-4 py-2 d-flex align-items-center justify-content-between small opacity-75">
                    <div class="d-flex gap-4">
                        <span><i class="las la-stream me-1 text-primary"></i> <span id="lineCount">0</span> @lang('Lines')</span>
                        <span><i class="las la-font me-1 text-primary"></i> <span id="charCount">0</span> @lang('Chars')</span>
                    </div>
                    <div class="d-flex gap-3 align-items-center">
                        <span id="saveStatus" class="tiny text-success d-none"><i class="las la-check-circle me-1"></i>@lang('Synced')</span>
                        <span class="tiny text-muted">@lang('Mode'): CSS</span>
                        <span class="tiny text-muted">@lang('Theme'): Monokai</span>
                    </div>
                </div>
            </form>
            
            {{-- Search Panel (Dynamic) --}}
            <div id="customSearchBox" class="position-absolute top-0 end-0 m-3 p-3 bg-white border rounded shadow-lg d-none" style="z-index: 100; width: 300px;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0 small fw-bold">@lang('Advanced Search')</h6>
                    <button type="button" class="btn-close small" id="closeSearch"></button>
                </div>
                <div class="mb-2">
                    <input type="text" class="form-control form-control-sm mb-2" id="searchInput" placeholder="@lang('Search query...')">
                    <input type="text" class="form-control form-control-sm" id="replaceInput" placeholder="@lang('Replace with...')">
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-primary btn-sm w-100" id="findNext">@lang('Find')</button>
                    <button type="button" class="btn btn-outline-primary btn-sm w-100" id="replaceAll">@lang('Replace All')</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Reset Modal --}}
<div class="modal fade" id="resetCssModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white border-0">
                <h5 class="modal-title fw-bold text-white"><i class="las la-skull-crossbones me-2"></i>@lang('Core Infrastructure Purge')</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <p class="mb-0 text-muted">@lang('Warning: This action will permanently erase all custom architectural overrides and restore the factory theme styling. There is no recovery after synchronization.')</p>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">@lang('Abort')</button>
                <form action="{{ route('admin.setting.custom.css.reset') }}" method="post" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger px-4">@lang('Confirm Wipe')</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('style')
<style>
    .bg-lighter { background-color: #f9fafb !important; }
    .ls-1 { letter-spacing: 1px; }
    .tiny { font-size: 0.7rem !important; }
    
    .CodeMirror {
        height: calc(700px - 36px) !important;
        font-family: 'JetBrains Mono', 'Fira Code', monospace;
        font-size: 14px;
        line-height: 1.6;
    }
    .custom-snippets .btn { border-color: rgba(105, 108, 255, 0.1); border-style: dashed; }
    .custom-snippets .btn:hover { background: rgba(105, 108, 255, 0.05); border-style: solid; color: #696cff; }
    
    .accordion-button::after { background-size: 1rem; width: 1rem; height: 1rem; }
    .accordion-button:not(.collapsed) { color: #696cff; }
    
    .CodeMirror-selected { background-color: rgba(105, 108, 255, 0.3) !important; }
    .CodeMirror-focused .CodeMirror-selected { background-color: rgba(105, 108, 255, 0.4) !important; }
</style>
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
        tabSize: 4,
        indentWithTabs: false,
        lineWrapping: true,
        extraKeys: {
            "Ctrl-S": function(cm) { document.getElementById('cssForm').submit(); },
            "Ctrl-F": function(cm) { toggleSearch(); },
            "Ctrl-Space": "autocomplete"
        }
    });

    function updateStats() {
        var doc = editor.getDoc();
        document.getElementById('lineCount').textContent = doc.lineCount();
        document.getElementById('charCount').textContent = doc.getValue().length;
    }
    editor.on('change', updateStats);
    updateStats();

    // Strategic Actions
    document.getElementById('saveBtn').onclick = () => document.getElementById('cssForm').submit();
    
    document.getElementById('beautifyBtn').onclick = () => {
        let content = editor.getValue();
        // Simple beautifier logic: Add newlines after semi-colons and braces if they don't exist
        let beautified = content
            .replace(/\{/g, " {\n    ")
            .replace(/\}/g, "\n}\n")
            .replace(/;/g, ";\n    ")
            .replace(/\n\s*\n/g, "\n")
            .replace(/^\s+/gm, (m) => m.replace(/    /g, "    "));
        editor.setValue(beautified);
        notify('success', '@lang("CSS structural normalization complete.")');
    };

    // Search Logic
    function toggleSearch() {
        document.getElementById('customSearchBox').classList.toggle('d-none');
        if(!document.getElementById('customSearchBox').classList.contains('d-none')) {
            document.getElementById('searchInput').focus();
        }
    }
    document.getElementById('searchBtn').onclick = toggleSearch;
    document.getElementById('closeSearch').onclick = toggleSearch;

    document.getElementById('findNext').onclick = () => {
        let query = document.getElementById('searchInput').value;
        if(query) {
            let cursor = editor.getSearchCursor(query);
            if(cursor.findNext()) {
                editor.setSelection(cursor.from(), cursor.to());
                editor.scrollIntoView({from: cursor.from(), to: cursor.to()}, 20);
            }
        }
    };

    document.getElementById('replaceAll').onclick = () => {
        let query = document.getElementById('searchInput').value;
        let replace = document.getElementById('replaceInput').value;
        if(query) {
            let content = editor.getValue();
            let newContent = content.split(query).join(replace);
            editor.setValue(newContent);
            notify('success', '@lang("Global replacement complete.")');
        }
    };

    // Color Intelligence
    const picker = document.getElementById('colorPicker');
    const hexInput = document.getElementById('colorHex');
    picker.oninput = (e) => hexInput.value = e.target.value;
    document.getElementById('copyColor').onclick = () => {
        navigator.clipboard.writeText(hexInput.value);
        notify('success', '@lang("Color token copied to matrix.")');
    };

    // Snippets
    document.querySelectorAll('.snippet-btn').forEach(btn => {
        btn.onclick = function() {
            let snippet = this.getAttribute('data-snippet');
            editor.replaceSelection(snippet);
            editor.focus();
        };
    });
</script>
@endpush

