{{--
    Standard dashboard page header: centered title + subtitle.
    Use on every dashboard feature page for consistent UI.
    Back button is rendered by dashboard_back_link in master.
    Pass: title, subtitle (optional), actions (optional HTML string).
--}}
<div class="dashboard-page-header">
    <h1 class="dashboard-page-header__title">{{ $title ?? '' }}</h1>
    @if(!empty($subtitle))
        <p class="dashboard-page-header__subtitle">{{ $subtitle }}</p>
    @endif
    @if(!empty($actions))
        <div class="dashboard-page-header__actions">{!! $actions !!}</div>
    @endif
</div>
