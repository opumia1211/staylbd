@extends('admin.layouts.app')
@section('panel')
    <div id="app">
        <div class="row mb-none-30">
            <div class="col-12">
                {{-- Header with stats and actions --}}
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row align-items-center justify-content-between">
                            <div class="col-md-6">
                                <h5 class="mb-1">@lang('Language Keywords of') {{ __($lang->name) }}</h5>
                                <p class="mb-0 text-muted small">
                                    <span class="badge bg-info">{{ $keyCount ?? 0 }} @lang('keywords')</span>
                                    <code class="ms-2">{{ $lang->code }}.json</code>
                                </p>
                            </div>
                            <div class="col-md-6 mt-3 mt-md-0 text-md-end">
                                <form action="{{ route('admin.language.set.locale') }}" method="post" class="d-inline-block me-2">
                                    @csrf
                                    <input type="hidden" name="code" value="{{ $lang->code }}">
                                    <button type="submit" class="btn btn-sm btn--primary"><i class="las la-check-circle"></i> @lang('Use this language in admin panel')</button>
                                </form>
                                <a href="{{ route('admin.language.manage') }}" class="btn btn-sm btn-outline--dark me-2"><i class="las la-arrow-left"></i> @lang('Back to Languages')</a>
                                <button type="button" data-bs-toggle="modal" data-bs-target="#addModal" class="btn btn-sm btn-outline--primary"><i class="fa fa-plus"></i> @lang('Add New Key')</button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Search --}}
                <div class="card mb-4">
                    <div class="card-body py-3">
                        <div class="row">
                            <div class="col-md-6">
                                <input type="text" class="form-control lang-search" placeholder="@lang('Search by key or value...')" id="langSearch">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive--sm table-responsive">
                            <table class="table table--light tabstyle--two custom-data-table white-space-wrap" id="myTable">
                                <thead>
                                    <tr>
                                        <th>@lang('Key')</th>
                                        <th>{{ __($lang->name) }}</th>
                                        <th class="w-85">@lang('Action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($json as $k => $language)
                                        <tr class="lang-row">
                                            <td class="white-space-wrap"><code>{{ $k }}</code></td>
                                            <td class="text-left white-space-wrap">{{ $language }}</td>
                                            <td>
                                                <a href="javascript:void(0)" data-title="{{ $k }}" data-key="{{ $k }}" data-value="{{ $language }}" class="editModal btn btn-sm btn-outline--primary"><i class="la la-pencil"></i> @lang('Edit')</a>
                                                <a href="javascript:void(0)" data-key="{{ $k }}" data-value="{{ $language }}" class="btn btn-sm btn-outline--danger deleteKey"><i class="la la-trash"></i> @lang('Remove')</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-5">{{ __($emptyMessage ?? 'No keywords yet') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Modals --}}
                <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="addModalLabel"> @lang('Add New')</h4>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="las la-times"></i>
                        </button>
                    </div>

                    <form action="{{route('admin.language.store.key',$lang->id)}}" method="post">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="key">@lang('Key')</label>
                                <input type="text" class="form-control" id="key" name="key" value="{{old('key')}}" required>

                            </div>
                            <div class="form-group">
                                <label for="value">@lang('Value')</label>
                                <input type="text" class="form-control" id="value" name="value" value="{{old('value')}}" required>

                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn--primary w-100 h-45"> @lang('Submit')</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>


        <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="editModalLabel">@lang('Edit')</h4>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><i class="las la-times"></i></button>
                    </div>

                    <form action="{{route('admin.language.update.key',$lang->id)}}" method="post">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group ">
                                <label for="inputName" class="form-title"></label>
                                <input type="text" class="form-control" name="value" required>
                            </div>
                            <input type="hidden" name="key">
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>


        <!-- Modal for DELETE -->
        <div class="modal fade" id="DelModal" tabindex="-1" role="dialog" aria-labelledby="DelModalLabel"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="DelModalLabel"> @lang('Confirmation Alert!')</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><i class="las la-times"></i></button>
                    </div>
                    <div class="modal-body">
                        <p>@lang('Are you sure to delete this key from this language?')</p>
                    </div>
                    <form action="{{route('admin.language.delete.key',$lang->id)}}" method="post">
                        @csrf
                        <input type="hidden" name="key">
                        <input type="hidden" name="value">
                        <div class="modal-footer">
                            <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('No')</button>
                            <button type="submit" class="btn btn--primary">@lang('Yes')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Import Modal --}}
    <div class="modal fade" id="importModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('Keywords')</h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><i class="las la-times"></i></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>@lang('Import From')</label>
                        <select class="form-control select_lang"  required>
                            <option value="">@lang('Select One')</option>
                            <option value="999">@lang('System')</option>

                            @foreach($list_lang as $data)
                                <option value="{{$data->id}}" @if($data->id == $lang->id) class="d-none" @endif >{{__($data->name)}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Close')</button>
                    <button type="button" class="btn btn--primary import_lang"> @lang('Import Now')</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('admin.language.manage') }}" class="btn btn-sm btn-outline--dark"><i class="las la-arrow-left"></i> @lang('Back')</a>
    <button type="button" class="btn btn-sm btn--primary box--shadow1 importBtn"><i class="la la-download"></i>@lang('Import Keywords')</button>
@endpush

@push('script')
    <script>
        (function($){
            "use strict";
            $(document).on('click','.deleteKey',function (e) {
                e.preventDefault();
                var modal = $('#DelModal');
                modal.find('input[name=key]').val($(this).data('key'));
                modal.find('input[name=value]').val($(this).data('value'));
                var el = document.getElementById('DelModal');
                if (typeof bootstrap !== 'undefined' && el) bootstrap.Modal.getOrCreateInstance(el).show();
                else modal.modal('show');
            });
            $(document).on('click','.editModal',function (e) {
                e.preventDefault();
                var modal = $('#editModal');
                modal.find('.form-title').text($(this).data('title'));
                modal.find('input[name=key]').val($(this).data('key'));
                modal.find('input[name=value]').val($(this).data('value'));
                var el = document.getElementById('editModal');
                if (typeof bootstrap !== 'undefined' && el) bootstrap.Modal.getOrCreateInstance(el).show();
                else modal.modal('show');
            });
            $(document).on('click','.importBtn',function () {
                var el = document.getElementById('importModal');
                if (typeof bootstrap !== 'undefined' && el) bootstrap.Modal.getOrCreateInstance(el).show();
                else $('#importModal').modal('show');
            });
            $(document).on('click','.import_lang',function(e){
                var id = $('.select_lang').val();
                if (id == '') {
                    notify('error', 'Invalid selection');
                    return;
                }
                $.ajax({
                    type: "post",
                    url: "{{ route('admin.language.import.lang') }}",
                    data: { id: id, toLangid: "{{ $lang->id }}", _token: "{{ csrf_token() }}" },
                    success: function(data) {
                        if (data == 'success') {
                            notify('success', 'Import Data Successfully');
                            window.location.href = "{{ url()->current() }}";
                        }
                    }
                });
            });
            var searchTimer;
            $('#langSearch').on('input', function() {
                var q = $(this).val().toLowerCase().trim();
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function() {
                    $('.lang-row').each(function() {
                        var text = $(this).text().toLowerCase();
                        $(this).toggle(!q || text.indexOf(q) !== -1);
                    });
                }, 150);
            });
        })(jQuery);
    </script>
@endpush
