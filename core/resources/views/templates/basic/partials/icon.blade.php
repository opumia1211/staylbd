@php
    $name = $name ?? 'circle';
    $class = $class ?? '';
@endphp

@switch($name)
    @case('microphone')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="3" width="6" height="12" rx="3"></rect><path d="M5 11a7 7 0 0 0 14 0"></path><path d="M12 18v3"></path><path d="M8 21h8"></path></svg>
        @break
    @case('image')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="16" rx="2"></rect><circle cx="9" cy="10" r="1.6"></circle><path d="M21 16l-5-5-8 8"></path></svg>
        @break
    @case('scan')
    @case('camera-scan')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.85" stroke-linecap="round" stroke-linejoin="round"><path d="M4 10V6a2 2 0 0 1 2-2h3.5M16 4H18a2 2 0 0 1 2 2v3.5M20 14v4a2 2 0 0 1-2 2h-3.5M8 20H6a2 2 0 0 1-2-2v-3.5"></path><rect x="8.5" y="8.5" width="7" height="7" rx="1.2"></rect></svg>
        @break
    @case('search')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"></circle><path d="M20 20l-3.5-3.5"></path></svg>
        @break
    @case('box')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7l9-4 9 4-9 4-9-4z"></path><path d="M3 7v10l9 4 9-4V7"></path><path d="M12 11v10"></path></svg>
        @break
    @case('phone')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6 19.8 19.8 0 0 1-3.1-8.7A2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.3 1.8.6 2.6a2 2 0 0 1-.5 2.1L8 9.6a16 16 0 0 0 6.4 6.4l1.2-1.2a2 2 0 0 1 2.1-.5c.8.3 1.7.5 2.6.6A2 2 0 0 1 22 16.9z"></path></svg>
        @break
    @case('shipping-fast')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="1.5" y="6.5" width="12" height="10" rx="1.5"></rect><path d="M13.5 9h4l3 3v4.5h-1.8"></path><circle cx="6" cy="18" r="1.8"></circle><circle cx="17.5" cy="18" r="1.8"></circle></svg>
        @break
    @case('bars')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 7h16"></path><path d="M4 12h16"></path><path d="M4 17h16"></path></svg>
        @break
    @case('language')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M3 12h18"></path><path d="M12 3a15 15 0 0 1 0 18"></path><path d="M12 3a15 15 0 0 0 0 18"></path></svg>
        @break
    @case('bell')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18H5.5a1 1 0 0 1-.8-1.6l1.3-1.8V10a6 6 0 1 1 12 0v4.6l1.3 1.8a1 1 0 0 1-.8 1.6H15z"></path><path d="M9.5 18a2.5 2.5 0 0 0 5 0"></path></svg>
        @break
    @case('heart')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
        @break
    @case('exchange-alt')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M7 7h13"></path><path d="M17 3l4 4-4 4"></path><path d="M17 17H4"></path><path d="M7 13l-4 4 4 4"></path></svg>
        @break
    @case('shopping-cart')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="20" r="1.6"></circle><circle cx="18" cy="20" r="1.6"></circle><path d="M3 4h2l2.4 10.2a2 2 0 0 0 2 1.6h8.8a2 2 0 0 0 2-1.6L23 7H7"></path></svg>
        @break
    @case('cart-outline')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="20" r="1.7"></circle><circle cx="18" cy="20" r="1.7"></circle><path d="M2.5 3.5H5l2.1 10a2 2 0 0 0 2 1.6h9a2 2 0 0 0 1.9-1.4L22 7.5H7.2"></path><path d="M8.5 8.5h11"></path><path d="M10.5 11.5h8.5"></path></svg>
        @break
    @case('cart-plus')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="20" r="1.6"></circle><circle cx="18" cy="20" r="1.6"></circle><path d="M3 4h2l2.4 10.2a2 2 0 0 0 2 1.6h8.8a2 2 0 0 0 2-1.6L23 7H7"></path><path d="M14 4v5"></path><path d="M11.5 6.5h5"></path></svg>
        @break
    @case('cart-grid')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="8" cy="20" r="2"></circle>
            <circle cx="18" cy="20" r="2"></circle>
            <path d="M2.5 3H5l2.5 12h11.5L22 7H6"></path>
            <path d="M7 11h14"></path>
            <path d="M10 7v8"></path>
            <path d="M14 7v8"></path>
            <path d="M18 7v8"></path>
        </svg>
        @break
    @case('shopping-bag')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8h12l-1 12H7L6 8z"></path><path d="M9 8a3 3 0 0 1 6 0"></path></svg>
        @break
    @case('clipboard-list')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="4.5" width="14" height="17" rx="2"></rect><path d="M9 4.5h6"></path><path d="M9 9h6"></path><path d="M9 13h6"></path><path d="M9 17h4"></path></svg>
        @break
    @case('user')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"></circle><path d="M4 20a8 8 0 0 1 16 0"></path></svg>
        @break
    @case('times')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M6 6l12 12M18 6L6 18"></path></svg>
        @break
    @case('home')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11.5L12 4l9 7.5"></path><path d="M5.5 10.5V20h13V10.5"></path></svg>
        @break
    @case('sign-in-alt')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 4h5v16h-5"></path><path d="M10 12h9"></path><path d="M13 9l-3 3 3 3"></path><path d="M3 4h7v16H3"></path></svg>
        @break
    @case('user-plus')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="4"></circle><path d="M2 20a7 7 0 0 1 14 0"></path><path d="M19 8v6"></path><path d="M16 11h6"></path></svg>
        @break
    @case('money-bill-wave')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="6" width="20" height="12" rx="2"></rect><path d="M2 10c2 0 2-2 4-2s2 2 4 2 2-2 4-2 2 2 4 2 2-2 4-2"></path><circle cx="12" cy="12" r="2.2"></circle></svg>
        @break
    @case('comments')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 15a3 3 0 0 1-3 3H9l-5 3V6a3 3 0 0 1 3-3h10a3 3 0 0 1 3 3z"></path></svg>
        @break
    @case('haykal')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l1.5 3.5L17 8l-3.5 1.5L12 13l-1.5-3.5L7 8l3.5-1.5z"></path><path d="M5 21h14"></path></svg>
        @break
    @case('user-tie')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="7" r="4"></circle><path d="M8 14l4 2 4-2"></path><path d="M9 21l3-5 3 5"></path><path d="M4 21a8 8 0 0 1 16 0"></path></svg>
        @break
    @case('key')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="12" r="4"></circle><path d="M12 12h9"></path><path d="M18 12v3"></path><path d="M21 12v2"></path></svg>
        @break
    @case('sign-out-alt')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M10 4H5v16h5"></path><path d="M14 12H5"></path><path d="M11 9l3 3-3 3"></path><path d="M14 12h6"></path></svg>
        @break
    @case('eye')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6-10-6-10-6z"></path><circle cx="12" cy="12" r="3"></circle></svg>
        @break
    @case('paper-plane')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 2L11 13"></path><path d="M22 2l-7 20-4-9-9-4z"></path></svg>
        @break
    @case('envelope')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"></rect><path d="M3 7l9 6 9-6"></path></svg>
        @break
    @case('download')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v12"></path><path d="M7 10l5 5 5-5"></path><path d="M4 21h16"></path></svg>
        @break
    @case('android')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="currentColor"><path d="M7 8h10a3 3 0 0 1 3 3v6a3 3 0 0 1-3 3h-1v2a1 1 0 1 1-2 0v-2h-4v2a1 1 0 1 1-2 0v-2H7a3 3 0 0 1-3-3v-6a3 3 0 0 1 3-3zm2.1-3.7a.8.8 0 1 0-1.2 1L9 6.8a7.3 7.3 0 0 1 6 0l1.1-1.5a.8.8 0 1 0-1.2-1L13.7 5a8.9 8.9 0 0 0-3.4 0z"></path></svg>
        @break
    @case('apple')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="currentColor"><path d="M16.8 12.9c0-2.2 1.8-3.3 1.9-3.4-1-1.5-2.6-1.7-3.1-1.7-1.3-.1-2.5.8-3.1.8-.6 0-1.6-.8-2.6-.7-1.4 0-2.6.8-3.3 2-.7 1.3-.2 3.2.5 4.7.7 1.4 1.5 3 2.6 3 .9 0 1.3-.6 2.5-.6s1.5.6 2.5.6c1 0 1.7-1.5 2.4-2.9.8-1.7 1.1-3.3 1.1-3.4-.1 0-2.1-.8-2.1-3zm-2.1-6.5c.6-.7 1-1.6.9-2.5-.9 0-1.9.6-2.5 1.3-.6.6-1 1.6-.9 2.5.9.1 1.9-.5 2.5-1.3z"></path></svg>
        @break
    @case('windows')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="currentColor"><path d="M3 5.5l8-1.1v7H3v-5.9zm9-1.2L21 3v8.4h-9V4.3zM3 12.6h8v7L3 18.5v-5.9zm9 0h9V21l-9-1.3v-7.1z"></path></svg>
        @break
    @case('desktop')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="12" rx="2"></rect><path d="M8 20h8"></path><path d="M12 16v4"></path></svg>
        @break
    @case('mobile-alt')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="7" y="2.5" width="10" height="19" rx="2"></rect><circle cx="12" cy="18" r="0.8" fill="currentColor" stroke="none"></circle></svg>
        @break
    @case('map-marker-alt')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s7-7 7-12a7 7 0 1 0-14 0c0 5 7 12 7 12z"></path><circle cx="12" cy="10" r="2.5"></circle></svg>
        @break
    @case('angle-double-up')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 14l6-6 6 6"></path><path d="M6 20l6-6 6 6"></path></svg>
        @break
    @case('th-large')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="currentColor"><rect x="3" y="3" width="8" height="8" rx="1.5"></rect><rect x="13" y="3" width="8" height="8" rx="1.5"></rect><rect x="3" y="13" width="8" height="8" rx="1.5"></rect><rect x="13" y="13" width="8" height="8" rx="1.5"></rect></svg>
        @break
    @case('bolt')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="currentColor"><path d="M13 2L4 14h6l-1 8 9-12h-6l1-8z"></path></svg>
        @break
    @case('angle-up')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 14l6-6 6 6"></path></svg>
        @break
    @case('angle-down')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 10l6 6 6-6"></path></svg>
        @break
    @case('angle-left')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 6l-6 6 6 6"></path></svg>
        @break
    @case('angle-right')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 6l6 6-6 6"></path></svg>
        @break
    @case('check')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12l5 5 9-9"></path></svg>
        @break
    @case('minus')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M5 12h14"></path></svg>
        @break
    @case('plus')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M12 5v14"></path><path d="M5 12h14"></path></svg>
        @break
    @case('sync-alt')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 0 0-15.5-6.4"></path><path d="M3 4v6h6"></path><path d="M3 12a9 9 0 0 0 15.5 6.4"></path><path d="M21 20v-6h-6"></path></svg>
        @break
    @case('credit-card')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"></rect><path d="M2 10h20"></path></svg>
        @break
    @case('list-alt')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="16" rx="2"></rect><path d="M8 9h8"></path><path d="M8 13h8"></path><path d="M8 17h5"></path></svg>
        @break
    @case('print')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M7 8V4h10v4"></path><rect x="6" y="14" width="12" height="6"></rect><rect x="4" y="8" width="16" height="8" rx="2"></rect></svg>
        @break
    @case('link')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 1 0-7l1.5-1.5a5 5 0 0 1 7 7L17 13"></path><path d="M14 11a5 5 0 0 1 0 7L12.5 19.5a5 5 0 0 1-7-7L7 11"></path></svg>
        @break
    @case('tag')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 13l-7 7-9-9V4h7z"></path><circle cx="7.5" cy="7.5" r="1.2"></circle></svg>
        @break
    @case('store')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 10l1-5h16l1 5"></path><path d="M4 10h16v10H4z"></path><path d="M9 14h6"></path></svg>
        @break
    @case('facebook')
    @case('facebook-f')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="currentColor"><path d="M14 8h3V4h-3c-3 0-5 2-5 5v3H6v4h3v4h4v-4h3.2l.8-4H13V9c0-.6.4-1 1-1z"></path></svg>
        @break
    @case('instagram')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3.5" y="3.5" width="17" height="17" rx="5"></rect><circle cx="12" cy="12" r="4"></circle><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"></circle></svg>
        @break
    @case('youtube')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="currentColor"><path d="M21.6 7.2a2.8 2.8 0 0 0-2-2C17.8 4.7 12 4.7 12 4.7s-5.8 0-7.6.5a2.8 2.8 0 0 0-2 2A30 30 0 0 0 2 12a30 30 0 0 0 .4 4.8 2.8 2.8 0 0 0 2 2c1.8.5 7.6.5 7.6.5s5.8 0 7.6-.5a2.8 2.8 0 0 0 2-2A30 30 0 0 0 22 12a30 30 0 0 0-.4-4.8zM10 15.5v-7l6 3.5-6 3.5z"></path></svg>
        @break
    @case('linkedin')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="currentColor"><path d="M6.5 8.5a2 2 0 1 1 0-4 2 2 0 0 1 0 4zM4.8 9.8H8v9.5H4.8zM10 9.8h3v1.3h.1c.4-.8 1.5-1.6 3.1-1.6 3.3 0 3.9 2.2 3.9 5v4.8h-3.2v-4.2c0-1 0-2.3-1.4-2.3s-1.6 1.1-1.6 2.2v4.3H10z"></path></svg>
        @break
    @case('whatsapp')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="currentColor"><path d="M20 12a8 8 0 0 1-11.8 7l-3.2 1 1.1-3.1A8 8 0 1 1 20 12zm-4.7 1.8c-.2-.1-1.2-.6-1.4-.7-.2-.1-.3-.1-.5.1l-.4.6c-.1.2-.2.2-.4.1a5.9 5.9 0 0 1-1.7-1.1 6.5 6.5 0 0 1-1.2-1.5c-.1-.2 0-.3.1-.4l.3-.4.1-.2a.4.4 0 0 0 0-.3c0-.1-.5-1.2-.7-1.6-.2-.4-.3-.4-.5-.4h-.4a.8.8 0 0 0-.6.3 2.3 2.3 0 0 0-.7 1.7c0 1 .7 2 1 2.4.1.1 1.5 2.4 3.7 3.2 2.2.9 2.2.6 2.6.6.4-.1 1.2-.5 1.4-1 .2-.5.2-1 .1-1z"></path></svg>
        @break
    @case('telegram')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="currentColor"><path d="M21 4L3 11l5.7 2 2.2 6L14 14l4.6 3L21 4zM9.9 12.5l7.9-5.1-6.3 6.2-.3 2.3-1.3-3.4z"></path></svg>
        @break
    @case('pinterest')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="currentColor"><path d="M12 3a9 9 0 0 0-3.3 17.4l1.2-4.7c-.3-.6-.5-1.6-.5-2.5 0-2.1 1.2-3.7 2.8-3.7 1.3 0 1.9 1 1.9 2.2 0 1.4-.9 3.4-1.3 5.2-.4 1.5.8 2.7 2.3 2.7 2.7 0 4.8-2.8 4.8-6.8 0-3.5-2.5-6-6.2-6-4.2 0-6.7 3.2-6.7 6.5 0 1.3.5 2.6 1.1 3.3.1.1.1.2.1.4l-.4 1.4c-.1.2-.2.3-.5.2-1.8-.7-2.9-2.9-2.9-4.6C3.9 8 7.2 4.4 12 4.4c4.7 0 8.4 3.3 8.4 7.8 0 4.7-3 8.5-7.1 8.5-1.4 0-2.7-.7-3.2-1.6l-.9 3.2c-.3 1.1-1.1 2.5-1.6 3.3 1.2.4 2.4.6 3.7.6a9 9 0 1 0 0-18z"></path></svg>
        @break
    @case('x-twitter')
    @case('twitter')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="currentColor"><path d="M18.9 3H22l-6.8 7.7L23 21h-6.2l-4.8-6.3L6.5 21H3.4l7.3-8.3L1 3h6.3l4.3 5.7L18.9 3zm-1.1 16h1.7L6.3 4.9H4.5L17.8 19z"></path></svg>
        @break
    @case('tiktok')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="currentColor"><path d="M14 3v8.8a3.2 3.2 0 1 1-2.6-3.1V5.2a6.7 6.7 0 1 0 5.8 6.6V8.5a6.5 6.5 0 0 0 3.7 1.2V6.5A3.9 3.9 0 0 1 17 3h-3z"></path></svg>
        @break
    @case('github')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="currentColor"><path d="M12 .5C5.65.5.5 5.65.5 12c0 5.1 3.29 9.43 7.86 10.97.58.1.79-.25.79-.56 0-.28-.01-1.02-.01-2-3.2.7-3.88-1.54-3.88-1.54-.52-1.33-1.28-1.68-1.28-1.68-1.05-.72.08-.71.08-.71 1.16.08 1.77 1.2 1.77 1.2 1.03 1.77 2.72 1.26 3.38.96.1-.75.4-1.26.73-1.55-2.55-.29-5.23-1.28-5.23-5.68 0-1.26.45-2.28 1.18-3.09-.12-.29-.51-1.47.11-3.06 0 0 .97-.31 3.18 1.18a11 6.6 0 0 1 3.5-.47c1.19.01 2.39.16 3.5.47 2.21-1.49 3.17-1.18 3.17-1.18.63 1.59.24 2.77.12 3.06.74.81 1.17 1.83 1.17 3.09 0 4.42-2.69 5.39-5.25 5.68.42.36.8 1.08.8 2.18 0 1.57-.01 2.84-.01 3.23 0 .31.21.67.8.56A10.55 10.55 0 0 0 23.5 12C23.5 5.65 18.35.5 12 .5z"></path></svg>
        @break
    @case('discord')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="currentColor"><path d="M20.317 4.37a19.791 19.791 0 0 0-4.885-1.515.074.074 0 0 0-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 0 0-5.487 0 12.64 12.64 0 0 0-.617-1.25.077.077 0 0 0-.079-.037A19.736 19.736 0 0 0 3.677 4.37a.07.07 0 0 0-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 0 0 .031.057 19.9 19.9 0 0 0 5.993 3.03.078.078 0 0 0 .084-.028 14.09 14.09 0 0 0 1.226-1.994.076.076 0 0 0-.041-.106 13.107 13.107 0 0 1-1.872-.892.077.077 0 0 1-.008-.128 10.2 10.2 0 0 0 .372-.292.074.074 0 0 1 .077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 0 1 .078.01c.12.098.246.198.373.292a.074.074 0 0 1-.006.127 12.299 12.299 0 0 1-1.873.892.077.077 0 0 0-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 0 0 .084.028 19.839 19.839 0 0 0 6.002-3.03.077.077 0 0 0 .032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 0 0-.031-.03zM8.02 15.33c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.956-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.956 2.418-2.157 2.418zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.955-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.946 2.418-2.157 2.418z"></path></svg>
        @break
    @case('reddit')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="currentColor"><path d="M12 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0zm5.01 4.744c.688 0 1.25.561 1.25 1.249a1.25 1.25 0 0 1-2.498.056l-2.597-.547-.8 3.747 1.99.372a2.68 2.68 0 0 1 4.262 2.24 2.68 2.68 0 0 1-1.076 2.157c.034.164.056.331.056.5 0 2.61-3.004 4.73-6.708 4.73-3.704 0-6.708-2.12-6.708-4.73 0-.169.022-.336.056-.5A2.68 2.68 0 0 1 4.86 11.86a2.68 2.68 0 0 1 4.262-2.24l1.99-.372-.8-3.747-2.597.547a1.25 1.25 0 1 1-1.248-2.305l3.5-.735a1.25 1.25 0 0 1 1.498.747l1.332 6.25a2.68 2.68 0 0 1 2.01-.91 2.68 2.68 0 0 1 2.68 2.68 2.68 2.68 0 0 1-.91 2.01zM8.25 14.25a1.125 1.125 0 1 1 0 2.25 1.125 1.125 0 0 1 0-2.25zm7.5 0a1.125 1.125 0 1 1 0 2.25 1.125 1.125 0 0 1 0-2.25zm-3.75-3a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3z"></path></svg>
        @break
    @case('spotify')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="currentColor"><path d="M12 0C5.4 0 0 5.4 0 12s5.4 12 12 12 12-5.4 12-12S18.66 0 12 0zm5.521 17.34c-.24.359-.66.48-1.021.24-2.82-1.74-6.36-2.101-10.561-1.141-.418.122-.779-.179-.899-.539-.12-.421.18-.78.54-.9 4.56-1.021 8.52-.6 11.64 1.32.42.18.479.659.301 1.02zm1.44-3.3c-.301.42-.841.6-1.262.3-3.239-1.98-8.159-2.58-11.939-1.38-.479.12-1.02-.12-1.14-.6-.12-.48.12-1.021.6-1.141C9.6 9.9 15 10.561 18.72 12.84c.361.181.54.78.241 1.2zm.12-3.36C15.24 8.4 8.82 8.16 5.16 9.301c-.6.179-1.2-.181-1.38-.721-.18-.601.18-1.2.72-1.381 4.26-1.26 11.28-1.02 15.721 1.621.539.3.719 1.02.419 1.56-.299.421-1.02.599-1.559.3z"></path></svg>
        @break
    @case('arrow-left')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"></path><path d="M11 6l-6 6 6 6"></path></svg>
        @break
    @case('filter')
    @case('sliders-h')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M4 6h16"></path><circle cx="9" cy="6" r="2"></circle><path d="M4 12h16"></path><circle cx="15" cy="12" r="2"></circle><path d="M4 18h16"></path><circle cx="7" cy="18" r="2"></circle></svg>
        @break
    @case('list')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M8 6h12"></path><path d="M8 12h12"></path><path d="M8 18h12"></path><circle cx="4" cy="6" r="1"></circle><circle cx="4" cy="12" r="1"></circle><circle cx="4" cy="18" r="1"></circle></svg>
        @break
    @case('folder-open')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7a2 2 0 0 1 2-2h5l2 2h7a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg>
        @break
    @case('shield-alt')
    @case('user-shield')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l8 3v6c0 5-3.4 8.5-8 9-4.6-.5-8-4-8-9V6l8-3z"></path></svg>
        @break
    @case('lock')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="11" width="14" height="10" rx="2"></rect><path d="M8 11V8a4 4 0 0 1 8 0v3"></path></svg>
        @break
    @case('headset')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 13v-1a8 8 0 0 1 16 0v1"></path><rect x="3" y="13" width="4" height="6" rx="1.5"></rect><rect x="17" y="13" width="4" height="6" rx="1.5"></rect><path d="M17 19a5 5 0 0 1-5 5h-1"></path></svg>
        @break
    @case('info-circle')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 11v6"></path><circle cx="12" cy="8" r="1" fill="currentColor" stroke="none"></circle></svg>
        @break
    @case('ban')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M6 6l12 12"></path></svg>
        @break
    @case('sms')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="14" rx="2"></rect><path d="M3 8l9 5 9-5"></path><path d="M8 20h8"></path></svg>
        @break
    @case('map-marked-alt')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 4L3 6v14l6-2 6 2 6-2V4l-6 2-6-2z"></path><path d="M15 10a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"></path></svg>
        @break
    @case('external-link-alt')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3h7v7"></path><path d="M10 14L21 3"></path><path d="M21 14v7H3V3h7"></path></svg>
        @break
    @case('exclamation-circle')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v7"></path><circle cx="12" cy="17" r="1" fill="currentColor" stroke="none"></circle></svg>
        @break
    @case('check-circle')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M8 12l2.5 2.5L16 9"></path></svg>
        @break
    @case('arrow-right')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"></path><path d="M13 6l6 6-6 6"></path></svg>
        @break
    @case('trash-alt')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7h16"></path><path d="M10 3h4"></path><path d="M8 7l1 13h6l1-13"></path></svg>
        @break
    @case('user-circle')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><circle cx="12" cy="9.5" r="2.7"></circle><path d="M6.7 18.5a6.5 6.5 0 0 1 10.6 0"></path></svg>
        @break
    @case('cart-arrow-down')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="20" r="1.4"></circle><circle cx="18" cy="20" r="1.4"></circle><path d="M3 4h2l2.3 10h10.4L20 7H7"></path><path d="M14 4v5"></path><path d="M12 7l2 2 2-2"></path></svg>
        @break
    @case('layer-group')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l9 5-9 5-9-5 9-5z"></path><path d="M3 12l9 5 9-5"></path><path d="M3 16l9 5 9-5"></path></svg>
        @break
    @case('history')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 3-6.7"></path><path d="M3 4v5h5"></path><path d="M12 8v4l3 2"></path></svg>
        @break
    @case('dollar-sign')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M12 3v18"></path><path d="M16 7.5c0-1.7-1.8-3-4-3s-4 1.3-4 3 1.4 2.5 4 3 4 1.3 4 3-1.8 3-4 3-4-1.3-4-3"></path></svg>
        @break
    @case('tags')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10l-8 8-8-8V4h6z"></path><path d="M14 4h6v6"></path><circle cx="9" cy="8" r="1"></circle></svg>
        @break
    @case('percent')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M5 19L19 5"></path><circle cx="7" cy="7" r="2"></circle><circle cx="17" cy="17" r="2"></circle></svg>
        @break
    @case('ruler-combined')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20L20 4l2 2L6 22l-2-2z"></path><path d="M14 6l4 4"></path><path d="M11 9l4 4"></path><path d="M8 12l4 4"></path></svg>
        @break
    @case('palette')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a9 9 0 0 0 0 18h1a2 2 0 0 0 0-4h-1a2 2 0 0 1 0-4h3a4 4 0 0 0 0-8h-3z"></path><circle cx="7.5" cy="9" r="1"></circle><circle cx="10" cy="6.8" r="1"></circle><circle cx="14" cy="6.8" r="1"></circle></svg>
        @break
    @case('inbox')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 5h18v12H3z"></path><path d="M3 13h5l2 3h4l2-3h5"></path></svg>
        @break
    @case('bell-slash')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M10 18h4"></path><path d="M5 8a7 7 0 0 1 9.3-6.6"></path><path d="M18 10v4l2 2H8"></path><path d="M3 3l18 18"></path></svg>
        @break
    @case('check-double')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 13l4 4 5-5"></path><path d="M10 13l4 4 7-7"></path></svg>
        @break
    @case('paperclip')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11l-8.5 8.5a5 5 0 0 1-7.1-7.1L14 3.8a3.5 3.5 0 1 1 5 5L9.6 18.2a2 2 0 0 1-2.8-2.8L15 7.2"></path></svg>
        @break
    @case('file')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H7a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7z"></path><path d="M14 2v5h5"></path></svg>
        @break
    @case('spinner')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 2a10 10 0 1 0 10 10"></path></svg>
        @break
    @case('chart-line')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 20h18"></path><path d="M5 15l4-4 3 3 6-7"></path></svg>
        @break
    @case('fire')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="currentColor"><path d="M13 2s1 3-1.5 5.5S8 10 8 13a4 4 0 1 0 8 0c0-2-1.1-3.7-1.1-3.7S17 11 17 14a6 6 0 1 1-12 0c0-4.6 3.2-6.7 5.3-8.8C12.2 3.4 13 2 13 2z"></path></svg>
        @break
    @case('comment-slash')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a8 8 0 0 1-8 8H7l-4 2 1.5-4.5A8 8 0 1 1 21 12z"></path><path d="M4 4l16 16"></path></svg>
        @break
    @case('clock')
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v6l4 2"></path></svg>
        @break
    @default
        <svg class="ui-icon {{ $class }}" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"></circle></svg>
@endswitch
