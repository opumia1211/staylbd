<div class="d-flex mb-2 flex-wrap gap-2 justify-content-between align-items-center admin-page-header">
    <div class="d-flex flex-column gap-1 min-w-0">
        @if(!empty($breadcrumb) && is_array($breadcrumb))
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 flex-wrap">
                    @foreach($breadcrumb as $i => $item)
                        @php
                            $label = is_array($item) ? ($item['label'] ?? '') : $item;
                            $url = is_array($item) ? ($item['url'] ?? null) : null;
                            $active = $i === count($breadcrumb) - 1;
                        @endphp
                        <li class="breadcrumb-item {{ $active ? 'active' : '' }}">
                            @if($active || !$url)
                                <span class="{{ $active ? 'fw-semibold' : 'text-muted' }}">{{ __($label) }}</span>
                            @else
                                <a href="{{ $url }}" class="text-decoration-none text-muted">{{ __($label) }}</a>
                            @endif
                        </li>
                    @endforeach
                </ol>
            </nav>
            <h5 class="fw-bold mb-0 mt-1">{{ __($pageTitle ?? 'Admin') }}</h5>
        @else
            <h5 class="fw-bold mb-0">{{ __($pageTitle ?? 'Admin') }}</h5>
        @endif
    </div>
    <div class="d-flex flex-wrap justify-content-end gap-2 align-items-center breadcrumb-plugins">
        @stack('breadcrumb-plugins')
    </div>
</div>
