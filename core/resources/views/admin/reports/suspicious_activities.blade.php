@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-12">
            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table table--light style--two table-hover">
                            <thead>
                                <tr>
                                    <th>@lang('Reason')</th>
                                    <th>@lang('User')</th>
                                    <th>@lang('IP')</th>
                                    <th>@lang('Activity')</th>
                                    <th>@lang('At')</th>
                                    <th>@lang('Resolved')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($list as $item)
                                <tr>
                                    <td><span class="badge badge--danger">{{ $item->reason }}</span></td>
                                    <td>
                                        @if($item->user_id)
                                        <a href="{{ route('admin.users.detail', $item->user_id) }}">{{ $item->user->username ?? $item->user_id }}</a>
                                        @else
                                        —
                                        @endif
                                    </td>
                                    <td><span class="font-monospace">{{ $item->ip_address ?? '—' }}</span></td>
                                    <td>{{ $item->activityLog->action_type ?? '—' }} {{ $item->activityLog ? \Illuminate\Support\Str::limit($item->activityLog->description, 30) : '' }}</td>
                                    <td>{{ $item->created_at ? showDateTime($item->created_at) : '—' }}</td>
                                    <td>{{ $item->resolved ? __('Yes') : __('No') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-muted text-center">@lang('No suspicious activities flagged.')</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($list->hasPages())
                <div class="card-footer py-4">
                    {{ paginateLinks($list) }}
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection
