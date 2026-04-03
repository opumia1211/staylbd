<div class="dashboard__responsive__header d-flex align-items-center justify-content-between d-lg-none">
    <div class="thumb__wrapper d-flex align-items-center">
        <div class="thumb me-2">
            <img src="{{ getImage(getFilePath('userProfile') . '/' . auth()->user()->image, getFileSize('userProfile')) }}" alt="{{ auth()->user()->username }}" loading="lazy" width="40" height="40">
        </div>
        <span class="username">@lang('@'){{ auth()->user()->username }}</span>
    </div>
    <button type="button" class="dashboard-sidebar-toggler dashboard-hamburger-btn" aria-label="@lang('Open menu')">
        @include($activeTemplate . 'partials.icon', ['name' => 'bars'])
    </button>
</div>
