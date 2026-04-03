@forelse($logs as $log)
<tr class="{{ in_array($log->action_type, ['login_failed', 'payment_failure']) ? 'table-warning' : '' }}">
    <td><span class="badge badge--primary">{{ $log->action_type }}</span></td>
    <td><span class="text-break">{{ \Illuminate\Support\Str::limit($log->description ?? '—', 50) }}</span></td>
    <td>
        @if($log->user_id)
        <a href="{{ route('admin.users.detail', $log->user_id) }}">{{ $log->user->fullname ?? '—' }}</a>
        @else
        <span class="text-muted">{{ __('Guest') }}</span>
        @endif
    </td>
    <td><span class="font-monospace small">{{ $log->ip_address ?? '—' }}</span></td>
    <td>{{ $log->device ?? '—' }}</td>
    <td>
        <span class="d-block">{{ showDateTime($log->created_at) }}</span>
        <span class="small text-muted">{{ diffForHumans($log->created_at) }}</span>
    </td>
</tr>
@empty
<tr>
    <td class="text-muted text-center" colspan="6">{{ __('No activity yet.') }}</td>
</tr>
@endforelse
