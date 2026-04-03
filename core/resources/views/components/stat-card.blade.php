@props([
    'title' => '',
    'value' => '',
    'icon' => 'las la-chart-bar',
    'link' => null,
    'linkText' => __('View All'),
])

@if ($link)
<a href="{{ $link }}" class="dashboard-stat-card dashboard-stat-card--link text-decoration-none text-dark">
@else
<div class="dashboard-stat-card">
@endif
    @if ($icon)
        <div class="dashboard-stat-card__icon">
            <i class="{{ $icon }}"></i>
        </div>
    @endif
    <div class="dashboard-stat-card__body">
        <span class="dashboard-stat-card__value" data-stat-value>{{ $value }}</span>
        <span class="dashboard-stat-card__title">{{ __($title) }}</span>
    </div>
    @if ($link)
        <span class="dashboard-stat-card__link">{{ $linkText }}</span>
    @endif
@if ($link)
</a>
@else
</div>
@endif
