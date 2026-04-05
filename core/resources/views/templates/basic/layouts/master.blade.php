@extends($activeTemplate . 'layouts.app')

@push('style')
{{-- Dashboard/user page CSS এখন tailwind-storefront bundle-এ import করা — duplicate stylesheet link সরানো --}}
<style>
/* Dashboard: সাইডবার–কন্টেন্ট ও মেনুবার–প্রোডাক্ট লিস্ট গ্যাপ শুধু ৫মিমি */
:root {
    --dash-gap: 5px;   /* মেনুবার ও সাইডবার মাঝে গ্যাপ ৫মিমি */
    --dash-gap-content: 0;
    --dash-sidebar-w: 220px;
    --dash-radius: 8px;
    --dash-border: 1px solid rgba(0, 0, 0, 0.08);
    --dash-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
    --dash-transition: 0.2s ease;
}
@media (max-width: 1199.98px) {
    .dashboard-section { --dash-sidebar-w: 210px; }
}
/* সাইডবার একই ডিজাইন – সব মেজারমেন্ট dashboard-sidebar.css এ */
.glass-header { z-index: 100100 !important; }
/* Auth iframe overlay (same as storefront) above dashboard chrome */
#pageAuthOverlay {
    z-index: 100120 !important;
}

.dashboard-section {
    padding-top: 2px !important;
    padding-bottom: 0.5rem !important;
    min-height: 0;
    position: relative;
    z-index: 1;
}
.dashboard-two-panels-container {
    width: 100%;
    max-width: 100%;
    margin-left: auto;
    margin-right: auto;
    padding-left: 0;
    padding-right: 0;
}
@media (min-width: 992px) {
    .dashboard-two-panels-container {
        width: 100%;
        max-width: 100%;
        padding-left: 0;
        padding-right: 0;
    }
}

@media (min-width: 992px) {
    .dashboard-section .row.dashboard-row-equal-no-gap {
        display: flex;
        flex-wrap: nowrap;
        align-items: stretch;
        gap: var(--dash-gap) !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
    }
    .dashboard-section .row.dashboard-row-equal-no-gap > .dashboard-aside-col {
        flex: 0 0 var(--dash-sidebar-w) !important;
        width: var(--dash-sidebar-w) !important;
        max-width: var(--dash-sidebar-w) !important;
        min-width: var(--dash-sidebar-w) !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
        position: relative;
        z-index: 1;
        align-self: stretch;
    }
    .dashboard-section .row.dashboard-row-equal-no-gap > .dashboard-content-col {
        flex: 1 1 0 !important;
        max-width: none !important;
        min-width: 0;
        padding-left: 0 !important;
        padding-right: 0 !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
    }
    .dashboard__sidebar {
        position: relative;
        z-index: 1;
        overflow-y: auto;
        overflow-x: hidden;
        width: 100%;
        max-width: var(--dash-sidebar-w);
        min-height: 300px;
        max-height: none;
        border-radius: var(--dash-radius) 0 0 var(--dash-radius);
        border: var(--dash-border);
        box-shadow: var(--dash-shadow);
        transition: box-shadow var(--dash-transition);
    }
    .dashboard-content-col .dashboard-wrapper {
        border-radius: 0 var(--dash-radius) var(--dash-radius) 0;
        border: var(--dash-border);
        box-shadow: var(--dash-shadow);
        transition: box-shadow var(--dash-transition), opacity var(--dash-transition);
    }
    .dashboard-wrapper {
        min-height: 0;
    }
    .dashboard-wrapper.is-loading { opacity: 0.7; pointer-events: none; }
}

@media (max-width: 991.98px) {
    .user-quick-actions { display: none !important; }
    .dashboard-section { padding-top: 2px !important; padding-bottom: 0.5rem !important; min-height: 0; }
    .dashboard-two-panels-container { width: 100%; max-width: 100%; padding-left: 0.25rem; padding-right: 0.25rem; }
    .dashboard-aside-col { margin-bottom: 0; width: 100%; max-width: 100%; overflow: visible; }
    .dashboard__responsive__header { display: flex !important; position: relative; z-index: 100; }
    .dashboard__responsive__header .dashboard-hamburger-btn { touch-action: manipulation; -webkit-tap-highlight-color: transparent; position: relative; z-index: 10; }
    .dashboard__sidebar {
        width: var(--dash-sidebar-w) !important;
        max-width: min(220px, 92vw) !important;
        min-height: 100vh;
        height: 100vh;
        transition: transform 0.25s ease, visibility 0.25s ease;
        z-index: 99950;
    }
    .dashboard-wrapper { padding: 5px !important; min-height: 0; }
}

.dashboard-hamburger-btn {
    background: none;
    border: none;
    padding: 0.4rem 0.5rem;
    cursor: pointer;
    font-size: 1.5rem;
    line-height: 1;
    color: inherit;
}
.dashboard-row-equal-no-gap { margin-left: 0 !important; margin-right: 0 !important; gap: 0 !important; }
.dashboard-aside-col { padding: 0 !important; margin: 0 !important; }
.dashboard-content-col {
    padding: 0 !important;
    margin: 0 !important;
    overflow-anchor: auto;
    min-width: 0;
}
#user-dashboard-root .dashboard-two-panels-container.container-fluid { padding-left: 0 !important; padding-right: 0 !important; }
/* কন্টেন্ট কাটা না যাওয়ার জন্য: ওভারফ্লো স্ক্রল, কার্ট টেবিল ও বাটন সম্পূর্ণ দেখা */
#dashboard-ajax-content,
.dashboard-wrapper {
    overflow-x: auto;
    overflow-y: visible;
    -webkit-overflow-scrolling: touch;
}
.dashboard-wrapper { min-width: 0; }
#dashboard-ajax-content { display: flex; flex-direction: column; align-items: stretch; transition: opacity 0.18s ease; min-height: 0; }
/* Centered pages (Track Order, Compare): do NOT set max-width here – dashboard.css sets per-page max-width so block stays in middle */
.dashboard-content--center { align-self: center; margin-left: auto; margin-right: auto; width: 100%; }
.dashboard__author .thumb img {
    width: 40px;
    height: 40px;
    border-radius: 999px;
    object-fit: cover;
}
.dashboard__author .content .title { font-size: 0.85rem !important; line-height: 1.25; }
.dashboard__author .content .fz--14 { font-size: 0.75rem !important; }
/* কন্টেন্ট এরিয়া গ্যাপ ৫মিমি – লাল মার্ক রিমুভ */
.dashboard-wrapper { padding: 5px !important; min-height: 0; }
#user-dashboard-root.pt-30 { padding-top: 2px !important; }
#user-dashboard-root.pb-30 { padding-bottom: 0.5rem !important; }
#user-dashboard-root { margin-bottom: 0 !important; isolation: isolate; }
#user-dashboard-root .dashboard-wrapper { overflow: visible; }
@media (min-width: 576px) { .dashboard-wrapper { padding: 5px !important; } }
@media (min-width: 992px) { .dashboard-wrapper { padding: 5px !important; } }
/* মেনুবার ও প্রোডাক্ট লিস্ট মাঝে গ্যাপ ৫মিমি – টাইটেল/ব্যাক থেকে টেবিল পর্যন্ত */
#dashboard-ajax-content .dashboard-top-row { margin-bottom: 5px !important; }
#dashboard-ajax-content .dashboard-page-header__title { font-size: 1rem !important; text-align: center !important; font-weight: 600 !important; margin: 0 0 0.15rem !important; }
#dashboard-ajax-content .dashboard-title-center .dashboard-page-header__title { font-size: 1rem !important; text-align: center !important; }
#dashboard-ajax-content .dashboard-page-header__subtitle,
#dashboard-ajax-content .dashboard-page-header p { text-align: center !important; margin-left: auto !important; margin-right: auto !important; }
#dashboard-ajax-content .dashboard-page-header__actions { text-align: center !important; margin-top: 5px !important; }
#dashboard-ajax-content .dashboard-back-link-wrap { margin-bottom: 5px !important; }
#dashboard-ajax-content:has(.wishlist-page) .dashboard-top-row { margin-bottom: 5px !important; }
@media (min-width: 576px) {
    #dashboard-ajax-content .dashboard-page-header__title { font-size: 1.1rem !important; margin: 0 0 0.25rem !important; }
    #dashboard-ajax-content .dashboard-title-center .dashboard-page-header__title { font-size: 1.1rem !important; }
}
/* Track Order: ফর্ম ব্লক পেজের মাঝখানে (অবশ্যই সেন্টার) */
#dashboard-ajax-content .track-order-outer-wrap {
    display: block !important;
    width: 100% !important;
    text-align: center !important;
    padding-top: 2rem !important;
}
#dashboard-ajax-content .track-order-outer-wrap .track-order-page {
    display: inline-block !important;
    text-align: left;
    max-width: 560px !important;
    width: 100% !important;
    margin-left: auto !important;
    margin-right: auto !important;
}
</style>
@endpush

@section('app')
    @include($activeTemplate . 'partials.header')
    <div class="dashboard-section pt-30 pb-30 bg-white" id="user-dashboard-root" data-user-dashboard="1">
        <div class="container-fluid dashboard-two-panels-container">
            <div class="row g-0 align-items-stretch dashboard-row-equal-no-gap">
                <div class="col-xxl-3 col-lg-3 dashboard-aside-col">
                    @include($activeTemplate . 'partials.dashboard_aside')
                    @php $__inlineSidebar = get_inline_ads_for_display(get_offer_timer_page_from_route(), 'sidebar_right'); @endphp
                    @include($activeTemplate . 'partials.inline_ad', ['inlineAds' => $__inlineSidebar, 'placement' => 'sidebar_right'])
                </div>
                <div class="col-xxl-9 col-lg-9 dashboard-content-col">
                    {{-- মোবাইল/ট্যাবে অ্যাভাটার+ইউজারনেম+হ্যামবার্গার বার রিমুভ – শুধু হেডারের হ্যামবার্গার দিয়ে প্রোফাইল মেনু খুলবে --}}
                    @include($activeTemplate . 'partials.user_quick_actions')
                    <div class="dashboard-wrapper" id="dashboard-ajax-content">
                        <div class="dashboard-top-row">
                            @include($activeTemplate . 'partials.dashboard_back_link')
                            <div class="dashboard-title-center">@yield('dashboard_page_title')</div>
                        </div>
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include($activeTemplate . 'partials.footer')

    @php $__popupAds = get_popup_ads_for_display(get_offer_timer_page_from_route()); @endphp
    @if($__popupAds->isNotEmpty())
    @include($activeTemplate . 'partials.popup_ad', ['popupAds' => $__popupAds])
    @endif

    {{-- Floating live chat available on all user pages --}}
    @include($activeTemplate . 'partials.contact_panel')

    @guest
    {{-- Quick Order modal for guest (cart and any dashboard page showing Quick Order button) --}}
    @include($activeTemplate . 'partials.guest_checkout_modal')
    @endguest
@endsection

@push('script')
    @include($activeTemplate . 'partials.contact_panel_script')
@endpush
@guest
@push('script')
{{-- Guest Quick Order modal: open on button click or URL param, load locations, submit via AJAX --}}
<script>
(function() {
    function openGuestCheckoutModal() {
        var el = document.getElementById('guestCheckoutModal');
        if (el && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            var inst = bootstrap.Modal.getInstance(el);
            if (!inst) inst = new bootstrap.Modal(el);
            inst.show();
        }
    }
    document.addEventListener('click', function(e) {
        var t = e.target.closest('a[data-bs-target="#guestCheckoutModal"], a#openGuestCheckoutBtn, a#openGuestCheckoutBtnInline, button[data-bs-target="#guestCheckoutModal"], button#openGuestCheckoutBtn, button#openGuestCheckoutBtnInline');
        if (t) {
            var href = t.getAttribute('href') || t.href || '';
            if (href && (href.indexOf('open_guest_checkout=1') !== -1 || href.indexOf('cart/quickorder') !== -1) && typeof history !== 'undefined' && history.pushState) {
                history.pushState({}, '', href);
            }
            e.preventDefault();
            e.stopPropagation();
            openGuestCheckoutModal();
            return false;
        }
    }, true);
    window.addEventListener('popstate', function() {
        var el = document.getElementById('guestCheckoutModal');
        if (el && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            var inst = bootstrap.Modal.getInstance(el);
            if (inst) {
                inst.hide();
            }
        }
    });
    function initGuestCheckoutScript() {
        if (typeof jQuery === 'undefined') return;
        var $ = jQuery;
        var guestCheckoutLocationLoaded = false;
        function loadGuestCheckoutLocations() {
            if (guestCheckoutLocationLoaded) return;
            if (!$('#guestCheckoutForm').length) return;
            $.get('{{ route("guest.checkout.location.data") }}').done(function(res) {
                if (!res || !res.success) return;
                guestCheckoutLocationLoaded = true;
                var divisions = res.divisions || [];
                var districtsByDiv = res.districts_by_division || {};
                var thanasByDistrict = res.thanas_by_district || {};
                var $div = $('#guest_division'), $dist = $('#guest_district'), $thana = $('#guest_thana');
                if (!$div.length) return;
                $div.find('option:not(:first)').remove();
                divisions.forEach(function(d) {
                    var id = d.id || d[0];
                    var name = (d.name_en || d[1] || d).toString();
                    $div.append($('<option>').attr('value', name).text(name));
                });
                $div.off('change.guestLoc').on('change.guestLoc', function() {
                    var divName = $(this).val();
                    $dist.find('option:not(:first)').remove();
                    $thana.find('option:not(:first)').remove();
                    if (!divName) return;
                    var divId = divisions.find(function(x) { return (x.name_en || x[1]) === divName; });
                    divId = divId ? (divId.id || divId[0]) : divName;
                    var dists = districtsByDiv[divId] || [];
                    dists.forEach(function(d) {
                        var en = (d.en || d.name_en || d).toString();
                        $dist.append($('<option>').attr('value', en).text(en));
                    });
                });
                $dist.off('change.guestLoc').on('change.guestLoc', function() {
                    var distName = $(this).val();
                    $thana.find('option:not(:first)').remove();
                    if (!distName) return;
                    var thanas = thanasByDistrict[distName] || [];
                    thanas.forEach(function(t) {
                        var en = (t.en || t.name_en || (typeof t === 'object' ? t.en : t)).toString();
                        $thana.append($('<option>').attr('value', en).text(en));
                    });
                });
                if (res.countries && Array.isArray(res.countries)) {
                    var $country = $('#guest_country');
                    $country.find('option:not(:first)').remove();
                    res.countries.forEach(function(c) {
                        var name = (c.name || c.country || c).toString();
                        $country.append($('<option>').attr('value', name).text(name));
                    });
                    if ($country.find('option[value="Bangladesh"]').length) $country.val('Bangladesh');
                }
            });
        }
        $(document).on('show.bs.modal', '#guestCheckoutModal', function() { loadGuestCheckoutLocations(); });
        $(function() {
            if (document.getElementById('guestOrderPage')) {
                loadGuestCheckoutLocations();
            }
        });
        $(document).on('submit', '#guestCheckoutForm', function(e) {
            e.preventDefault();
            var $form = $(this), $btn = $('#guestCheckoutSubmitBtn'), $success = $('#guestCheckoutSuccess'), $err = $('#guestCheckoutError');
            $success.addClass('d-none'); $err.addClass('d-none');
            $form.find('.is-invalid').removeClass('is-invalid');
            var areaCity = ($('#guest_area_city').val() || '').trim();
            $('#guest_district_hidden').val(areaCity);
            $('#guest_city').val(areaCity);
            $btn.prop('disabled', true);
            $btn.find('.btn-text').addClass('d-none');
            $btn.find('.spinner-border').removeClass('d-none');
            $.ajax({
                url: '{{ route("guest.checkout.order") }}',
                method: 'POST',
                data: $form.serialize(),
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'), 'Accept': 'application/json' },
                dataType: 'json'
            }).done(function(data) {
                if (data.success) {
                    $form.addClass('d-none');
                    $('#guestCheckoutSuccessMessage').text(data.message || '{{ __("Your order has been successfully placed. Our team will contact you shortly.") }}');
                    $success.removeClass('d-none');
                    if (typeof getCartCount === 'function') getCartCount();
                    setTimeout(function() {
                        var el = document.getElementById('guestCheckoutModal');
                        if (el && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                            var inst = bootstrap.Modal.getInstance(el);
                            if (inst) inst.hide();
                        }
                        window.location.href = '{{ route("user.cart") }}';
                    }, 3000);
                } else {
                    $('#guestCheckoutErrorMessage').text(data.message || '{{ __("Something went wrong.") }}');
                    $err.removeClass('d-none');
                }
            }).fail(function(xhr) {
                var msg = '{{ __("Something went wrong. Please try again.") }}';
                if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    var first = Object.values(xhr.responseJSON.errors)[0];
                    msg = Array.isArray(first) ? first[0] : first;
                }
                $('#guestCheckoutErrorMessage').text(msg);
                $err.removeClass('d-none');
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    $.each(xhr.responseJSON.errors, function(field, messages) {
                        var $el = $form.find('[name="' + field + '"]');
                        if ($el.length) { $el.addClass('is-invalid'); $el.next('.invalid-feedback').text(Array.isArray(messages) ? messages[0] : messages); }
                    });
                }
            }).always(function() {
                $btn.prop('disabled', false);
                $btn.find('.btn-text').removeClass('d-none');
                $btn.find('.spinner-border').addClass('d-none');
            });
            return false;
        });
        $(document).on('hidden.bs.modal', '#guestCheckoutModal', function() {
            var $form = $('#guestCheckoutForm');
            if ($form.length) { $form.removeClass('d-none')[0].reset(); }
            $('#guestCheckoutSuccess, #guestCheckoutError').addClass('d-none');
        });
        if (window.location.search.indexOf('open_guest_checkout=1') !== -1) {
            $(function() { openGuestCheckoutModal(); });
        }
        window.addEventListener('dashboard-content-updated', function() {
            if (window.location.search.indexOf('open_guest_checkout=1') !== -1) openGuestCheckoutModal();
        });
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initGuestCheckoutScript);
    } else {
        initGuestCheckoutScript();
    }
})();
</script>
@endpush
@endguest
@push('script')
    @include($activeTemplate . 'partials.auth_iframe_overlay_script')
@endpush
@push('script')
    <script>
    'use strict';
    (function() {
        var root = document.getElementById('user-dashboard-root');
        var contentEl = document.getElementById('dashboard-ajax-content');
        if (!root || !contentEl) return;
        var dashSidebar = root.querySelector('.dashboard__sidebar');
        var overlay = document.querySelector('.overlay');
        var hamburger = document.getElementById('glassMenuToggle');
        /* Hamburger on user pages is handled by glass-header.js (single handler for all devices) */
        var basePath = '{{ url("/") }}'.replace(/^https?:\/\/[^/]+/, '');
        function normPath(path) {
            if (!path) return '';
            path = path.replace(/^https?:\/\/[^/]+/, '') || '/';
            return path.replace(/\/+$/, '') || '/';
        }
        function isDashboardLink(a) {
            if (!a || !a.href) return false;
            if (a.target === '_blank' || a.getAttribute('data-no-ajax')) return false;
            var href = a.getAttribute('href');
            if (!href || href === '#' || href.indexOf('javascript') === 0) return false;
            if (a.getAttribute('data-dashboard-link') === '1') return true;
            try {
                var path = normPath(a.pathname || (a.href ? new URL(a.href).pathname : ''));
                if (path.indexOf('/user/logout') !== -1) return false;
                if (path.indexOf('/user/') !== -1 || path.indexOf('track-order') !== -1) return true;
                if (a.getAttribute('data-dashboard-link') === '1') return true;
            } catch (e) {}
            return false;
        }
        function setLoading(on) {
            if (contentEl) contentEl.classList.toggle('is-loading', !!on);
        }
        var pendingRequest = null;
        function setSidebarActive(urlOrPath) {
            var path;
            try {
                path = normPath(urlOrPath.indexOf('/') === 0 ? urlOrPath : new URL(urlOrPath, window.location.origin).pathname);
            } catch (e) {
                path = normPath(urlOrPath);
            }
            var links = root.querySelectorAll('.dashboard__sidebar a[href]');
            var run = function() {
                links.forEach(function(a) {
                    var p;
                    try {
                        p = normPath(a.pathname || new URL(a.href).pathname);
                    } catch (e) {
                        p = normPath(a.getAttribute('href') || '');
                    }
                    a.classList.toggle('active', p === path || (path.indexOf(p) === 0 && p.length > 1));
                });
            };
            if (typeof requestIdleCallback !== 'undefined') requestIdleCallback(run, { timeout: 50 });
            else setTimeout(run, 0);
        }
        function runScripts(container) {
            if (!container) return;
            var scripts = container.querySelectorAll('script');
            scripts.forEach(function(oldScript) {
                var s = document.createElement('script');
                if (oldScript.src) {
                    s.src = oldScript.src;
                } else {
                    s.textContent = oldScript.textContent;
                }
                s.async = false;
                oldScript.parentNode.removeChild(oldScript);
                document.body.appendChild(s);
            });
        }
        function loadPage(url, push) {
            if (normPath(new URL(url, window.location.origin).pathname) === normPath(window.location.pathname)) return;
            if (pendingRequest) pendingRequest.abort();
            var ac = new AbortController();
            pendingRequest = ac;
            setLoading(true);
            fetch(url, {
                signal: ac.signal,
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' },
                credentials: 'same-origin'
            }).then(function(r) {
                if (!r.ok) throw new Error('Load failed');
                return r.text();
            }).then(function(html) {
                if (pendingRequest !== ac) return;
                var parser = new DOMParser();
                var doc = parser.parseFromString(html, 'text/html');
                var newContent = doc.getElementById('dashboard-ajax-content');
                if (newContent) {
                    contentEl.innerHTML = newContent.innerHTML;
                    runScripts(contentEl);
                    setSidebarActive(url);
                    if (push !== false) history.pushState({ dashboard: true }, '', url);
                    if (window.innerWidth < 992) {
                        var sb = root.querySelector('.dashboard__sidebar');
                        var ov = document.querySelector('.overlay');
                        if (sb) sb.classList.remove('active');
                        if (ov) ov.classList.remove('active');
                        document.body.classList.remove('dashboard-sidebar-open');
                    }
                    try { window.dispatchEvent(new CustomEvent('dashboard-content-updated')); } catch (e) {}
                } else {
                    window.location.href = url;
                }
            }).catch(function(err) {
                if (err && err.name === 'AbortError') return;
                window.location.href = url;
            }).finally(function() {
                if (pendingRequest === ac) pendingRequest = null;
                setLoading(false);
            });
        }
        root.addEventListener('click', function(e) {
            var a = e.target.closest('a');
            if (!a || !isDashboardLink(a)) return;
            e.preventDefault();
            loadPage(a.href);
        }, true);
        window.addEventListener('popstate', function(e) {
            if (e.state && e.state.dashboard && contentEl) {
                loadPage(window.location.href, false);
            }
        });
        var GUEST_CART_KEY = 'staylbd_guest_cart';
        var GUEST_WISHLIST_KEY = 'staylbd_guest_wishlist';
        function tryRestoreGuestData(done) {
            var cartData = null, wishlistData = null;
            try {
                var c = localStorage.getItem(GUEST_CART_KEY);
                if (c) { var p = JSON.parse(c); if (Array.isArray(p) && p.length > 0) cartData = p; }
            } catch (e) {}
            try {
                var w = localStorage.getItem(GUEST_WISHLIST_KEY);
                if (w) { var p = JSON.parse(w); if (Array.isArray(p) && p.length > 0) wishlistData = p; }
            } catch (e) {}
            if (!cartData && !wishlistData) { if (typeof done === 'function') done(); return; }
            var pending = (cartData ? 1 : 0) + (wishlistData ? 1 : 0);
            function finish() { if (--pending <= 0 && typeof done === 'function') done(); }
            if (cartData) {
                $.ajax({ type: 'POST', url: "{{ route('cart.list.restore.guest') }}", data: JSON.stringify({ items: cartData }), contentType: 'application/json', headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}", 'Accept': 'application/json' } }).done(finish).fail(finish);
            }
            if (wishlistData) {
                var productIds = wishlistData.map(function(x) { return x && (x.product_id != null) ? x.product_id : x; }).filter(Boolean);
                if (productIds.length > 0) {
                    $.ajax({ type: 'POST', url: "{{ route('wish.list.restore.guest') }}", data: JSON.stringify({ product_ids: productIds }), contentType: 'application/json', headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}", 'Accept': 'application/json' } }).done(finish).fail(finish);
                } else { finish(); }
            }
        }
        var defer = function(fn) {
            if (typeof requestIdleCallback !== 'undefined') requestIdleCallback(fn, { timeout: 400 });
            else setTimeout(fn, 0);
        };
        defer(function() {
            tryRestoreGuestData(function() {
                if (typeof getCartCount === 'function') getCartCount();
                if (typeof getWishlistCount === 'function') getWishlistCount();
                if (typeof getCompareCount === 'function') getCompareCount();
            });
        });
    })();
    </script>
@endpush
@push('script')
    <script>
        'use strict';
        (function($) {
            function getCartCount() {
                $.ajax({
                    type: "GET",
                    url: "{{ route('cart.list.count') }}",
                    dataType: 'json',
                    success: function(response) {
                        var c = (response && response.count != null) ? response.count : response;
                        $('.show-cart-count').text(c);
                        try {
                            if (response && response.items && response.items.length > 0) localStorage.setItem(GUEST_CART_KEY, JSON.stringify(response.items));
                            else localStorage.removeItem(GUEST_CART_KEY);
                        } catch (e) {}
                        var items = (response && Array.isArray(response.items)) ? response.items : [];
                        var pidInCart = {};
                        items.forEach(function(it) {
                            var p = it && (it.product_id != null) ? parseInt(it.product_id, 10) : 0;
                            if (p > 0) pidInCart[p] = true;
                        });
                        $('.cart-btn, .add-to-cart').each(function() {
                            var $b = $(this);
                            var pid = parseInt($b.attr('data-product_id') || $b.data('product_id'), 10);
                            $b.toggleClass('in-cart', pid > 0 && !!pidInCart[pid]);
                        });
                    }
                });
            }
            function getWishlistCount() {
                $.ajax({
                    type: "GET",
                    url: "{{ route('wish.list.count') }}",
                    dataType: 'json',
                    success: function(response) {
                        var items = Array.isArray(response) ? response : (response && response.items ? response.items : []);
                        var total = items.length;
                        $.each(items || [], function(i, value) {
                            var pid = value && (value.product_id != null) ? value.product_id : value;
                            if (pid != null) $(document).find('.add-wishlist[data-product_id="' + pid + '"]').addClass('active added');
                        });
                        $('.show-wishlist-count').text(total);
                        try {
                            if (items.length > 0) localStorage.setItem(GUEST_WISHLIST_KEY, JSON.stringify(items));
                            else localStorage.removeItem(GUEST_WISHLIST_KEY);
                        } catch (e) {}
                    }
                });
            }
            function getCompareCount() {
                $.ajax({
                    type: "GET",
                    url: "{{ route('compare.count') }}",
                    dataType: 'json',
                    cache: false,
                    success: function(response) {
                        var c = (response && response.count != null) ? response.count : 0;
                        c = Math.min(Math.max(0, parseInt(c, 10)), 200);
                        $('.show-compare-count').text(c);
                        var ids = (response && response.product_ids && Array.isArray(response.product_ids)) ? response.product_ids : [];
                        $('.add-to-compare, .btn-compare').each(function() {
                            var pid = parseInt($(this).attr('data-product_id') || $(this).data('product_id'), 10);
                            $(this).toggleClass('in-compare', pid && ids.indexOf(pid) >= 0);
                        });
                    },
                    error: function() {
                        $('.show-compare-count').text('0');
                        $('.add-to-compare, .btn-compare').removeClass('in-compare');
                    }
                });
            }
            window.getCartCount = getCartCount;
            window.getWishlistCount = getWishlistCount;
            window.getCompareCount = getCompareCount;

            $(document).on('click', '.wishlist-delete-btn', function(e) {
                e.preventDefault();
                var btn = $(this);
                var product_id = parseInt(btn.data('product_id'), 10);
                if (!product_id) return;
                $.get("{{ route('wish.list.remove') }}", { product_id: product_id }, 'json').done(function(r) {
                    if (r && r.success) {
                        if (typeof notify === 'function') notify('success', r.success);
                        var row = btn.closest('.wishlist-row');
                        if (row.length) row.fadeOut(280, function() {
                            $(this).remove();
                            var page = document.querySelector('.wishlist-page');
                            var left = document.querySelectorAll('.wishlist-page .wishlist-row').length;
                            var badge = document.querySelector('.wishlist-page .wishlist-count-badge');
                            if (badge && page) {
                                var max = parseInt(page.getAttribute('data-wishlist-max'), 10) || 200;
                                badge.textContent = left + ' / ' + max;
                            }
                            if (left === 0) {
                                var contentEl = document.getElementById('dashboard-ajax-content');
                                if (contentEl) {
                                    fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' }, credentials: 'same-origin' })
                                        .then(function(res) { return res.text(); })
                                        .then(function(html) {
                                            var doc = new DOMParser().parseFromString(html, 'text/html');
                                            var newContent = doc.getElementById('dashboard-ajax-content');
                                            if (newContent) contentEl.innerHTML = newContent.innerHTML;
                                        });
                                } else window.location.reload();
                            }
                        });
                        if (typeof getWishlistCount === 'function') getWishlistCount();
                    } else { if (typeof notify === 'function') notify('error', (r && r.error) || 'Could not remove.'); }
                }).fail(function() { if (typeof notify === 'function') notify('error', 'Could not remove from wishlist.'); });
            });
            $(document).on('click', '#user-dashboard-root .add-wishlist', function(e) {
                e.preventDefault();
                var btn = $(this);
                var product_id = parseInt(btn.data('product_id'), 10);
                if (!product_id) return;
                var isInWishlist = btn.hasClass('active') || btn.hasClass('added');
                if (isInWishlist) {
                    $.get("{{ route('wish.list.remove') }}", { product_id: product_id }, 'json').done(function(r) {
                        if (r && r.success) {
                            if (typeof notify === 'function') notify('success', r.success);
                            btn.closest('tr').fadeOut(280, function() { $(this).remove(); });
                            btn.closest('.wishlist-card-col').fadeOut(280, function() { $(this).remove(); });
                            btn.closest('.wishlist-mobile-card').fadeOut(280, function() { $(this).remove(); });
                            getWishlistCount();
                        } else { if (typeof notify === 'function') notify('error', (r && r.error) || 'Could not remove.'); }
                    }).fail(function() { if (typeof notify === 'function') notify('error', 'Could not remove from wishlist.'); });
                } else {
                    $.post("{{ route('wish.list.add') }}", { product_id: product_id, _token: "{{ csrf_token() }}" }, null, 'json').done(function(r) {
                        if (r && r.success) { btn.addClass('active added'); if (typeof notify === 'function') notify('success', r.success); getWishlistCount(); }
                        else { if (typeof notify === 'function') notify('error', (r && r.error) || 'Could not add.'); }
                    }).fail(function() { if (typeof notify === 'function') notify('error', 'Could not add to wishlist.'); });
                }
            });
            $(document).on('click', '#user-dashboard-root .add-to-cart', function(e) {
                e.preventDefault();
                var btn = $(this);
                var product_id = parseInt(btn.data('product_id'), 10);
                if (!product_id) { if (typeof notify === 'function') notify('error', 'Product not found.'); return; }
                $.ajax({
                    method: 'POST',
                    url: "{{ route('cart.list.add') }}",
                    data: { product_id: product_id, quantity: 1, _token: "{{ csrf_token() }}" },
                    headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}", 'X-Requested-With': 'XMLHttpRequest' },
                    dataType: 'json'
                }).done(function(r) {
                    if (r && r.success) { if (typeof notify === 'function') notify('success', r.success); getCartCount(); }
                    else { if (typeof notify === 'function') notify('error', (r && r.error) || 'Could not add.'); }
                }).fail(function(xhr) {
                    var msg = xhr.status === 419 ? 'Session expired. Please refresh.' : ((xhr.responseJSON && xhr.responseJSON.error) || 'Could not add to cart.');
                    if (typeof notify === 'function') notify('error', msg);
                });
            });
            /* Compare: add/remove from dashboard or any page – real-time badge + button state */
            $(document).on('click', '.add-to-compare, .btn-compare', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var btn = $(this);
                var product_id = parseInt(btn.attr('data-product_id') || btn.data('product_id'), 10);
                if (!product_id) return;
                var isInCompare = btn.hasClass('in-compare');
                var url = isInCompare ? '{{ route("compare.remove") }}' : '{{ route("compare.add") }}';
                $.ajax({
                    type: 'POST',
                    url: url,
                    data: { product_id: product_id, _token: '{{ csrf_token() }}' },
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    dataType: 'json',
                    success: function(r) {
                        if (r && r.success) {
                            if (typeof notify === 'function') notify('success', r.message || (isInCompare ? '{{ __("Removed from comparison.") }}' : '{{ __("Added to comparison.") }}'));
                            if (typeof getCompareCount === 'function') getCompareCount();
                        } else {
                            if (typeof notify === 'function') notify('error', (r && r.message) || '');
                            if (r && r.count != null && typeof getCompareCount === 'function') getCompareCount();
                        }
                    },
                    error: function() {
                        if (typeof notify === 'function') notify('error', '{{ __("Could not update comparison.") }}');
                    }
                });
                return false;
            });
            /* Wishlist: Add all to cart */
            $(document).on('click', '.wishlist-add-all-cart', function() {
                var btns = $('.wishlist-page .add-to-cart');
                if (btns.length === 0) { if (typeof notify === 'function') notify('info', '{{ __("No products to add.") }}'); return; }
                var $main = $(this);
                $main.prop('disabled', true);
                var idx = 0;
                function addNext() {
                    if (idx >= btns.length) {
                        $main.prop('disabled', false);
                        if (typeof notify === 'function') notify('success', '{{ __("All products added to cart.") }}');
                        if (typeof getCartCount === 'function') getCartCount();
                        return;
                    }
                    var pid = parseInt(btns.eq(idx).data('product_id'), 10);
                    if (!pid) { idx++; addNext(); return; }
                    $.ajax({
                        method: 'POST',
                        url: "{{ route('cart.list.add') }}",
                        data: { product_id: pid, quantity: 1, _token: "{{ csrf_token() }}" },
                        headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}", 'X-Requested-With': 'XMLHttpRequest' },
                        dataType: 'json'
                    }).done(function(r) {
                        if (r && r.success && typeof getCartCount === 'function') getCartCount();
                    }).always(function() { idx++; addNext(); });
                }
                addNext();
            });

            /* Compare page: remove, clear, view toggle, tick-mark bulk delete – always loaded for AJAX */
            $(document).on('click', '.remove-compare-btn', function() {
                var productId = $(this).data('product_id');
                $.ajax({
                    type: 'POST',
                    url: '{{ route("compare.remove") }}',
                    data: { product_id: productId, _token: '{{ csrf_token() }}' },
                    success: function(r) {
                        if (r && r.success) {
                            if (typeof notify === 'function') notify('success', r.message);
                            var contentEl = document.getElementById('dashboard-ajax-content');
                            if (contentEl) {
                                fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' }, credentials: 'same-origin' })
                                    .then(function(res) { return res.text(); })
                                    .then(function(html) {
                                        var doc = new DOMParser().parseFromString(html, 'text/html');
                                        var newContent = doc.getElementById('dashboard-ajax-content');
                                        if (newContent) contentEl.innerHTML = newContent.innerHTML;
                                    });
                            } else window.location.reload();
                            if (typeof getCompareCount === 'function') getCompareCount();
                        }
                    }
                });
            });
            $(document).on('click', '.clear-compare-btn', function() {
                if (!confirm('{{ __("Clear all products from comparison?") }}')) return;
                $.ajax({
                    type: 'POST',
                    url: '{{ route("compare.clear") }}',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(r) {
                        if (r && r.success) {
                            if (typeof notify === 'function') notify('success', r.message);
                            var contentEl = document.getElementById('dashboard-ajax-content');
                            if (contentEl) {
                                fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' }, credentials: 'same-origin' })
                                    .then(function(res) { return res.text(); })
                                    .then(function(html) {
                                        var doc = new DOMParser().parseFromString(html, 'text/html');
                                        var newContent = doc.getElementById('dashboard-ajax-content');
                                        if (newContent) contentEl.innerHTML = newContent.innerHTML;
                                    });
                            } else window.location.reload();
                            if (typeof getCompareCount === 'function') getCompareCount();
                        }
                    }
                });
            });
            $(document).on('click', '.compare-page .btn-print', function() { window.print(); });
            $(document).on('click', '.wishlist-page .wishlist-btn-print', function() { window.print(); });
            $(document).on('click', '.clear-wishlist-btn', function() {
                if (!confirm('{{ __("Clear all products from wishlist?") }}')) return;
                var $btn = $(this);
                $btn.prop('disabled', true);
                $.ajax({
                    type: 'POST',
                    url: '{{ route("wish.list.clear") }}',
                    data: { _token: '{{ csrf_token() }}' },
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    dataType: 'json'
                }).done(function(r) {
                    if (r && r.success) {
                        if (typeof notify === 'function') notify('success', r.success);
                        var contentEl = document.getElementById('dashboard-ajax-content');
                        if (contentEl) {
                            fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' }, credentials: 'same-origin' })
                                .then(function(res) { return res.text(); })
                                .then(function(html) {
                                    var doc = new DOMParser().parseFromString(html, 'text/html');
                                    var newContent = doc.getElementById('dashboard-ajax-content');
                                    if (newContent) contentEl.innerHTML = newContent.innerHTML;
                                });
                        } else window.location.reload();
                        if (typeof getWishlistCount === 'function') getWishlistCount();
                    }
                }).always(function() { $btn.prop('disabled', false); });
            });
            $(document).on('click', '.compare-page .dashboard-view-btn', function() {
                var page = document.querySelector('.compare-page.dashboard-list-page');
                if (!page) return;
                var view = $(this).data('view');
                if (!view) return;
                page.setAttribute('data-view-mode', view);
                try { localStorage.setItem('compare_view', view); } catch (e) {}
                var compact = page.querySelector('.compare-compact-view');
                var comfortable = page.querySelector('.compare-comfortable-view');
                if (compact) compact.classList.toggle('d-none', view !== 'compact');
                if (comfortable) comfortable.classList.toggle('d-none', view !== 'comfortable');
                page.querySelectorAll('.dashboard-view-btn').forEach(function(btn) { btn.classList.toggle('active', btn.getAttribute('data-view') === view); });
            });
            function updateCompareDeleteBtn() {
                var page = document.querySelector('.compare-page');
                if (!page) return;
                var checked = page.querySelectorAll('.compare-item-cb:checked');
                var btn = page.querySelector('.compare-delete-selected');
                if (btn) {
                    var label = btn.querySelector('.compare-delete-selected-label');
                    if (checked.length > 0) {
                        btn.classList.remove('d-none');
                        if (label) label.textContent = '{{ __("Delete selected") }} (' + checked.length + ')';
                    } else {
                        btn.classList.add('d-none');
                        if (label) label.textContent = '{{ __("Delete selected") }}';
                    }
                }
                var selectAll = page.querySelector('.compare-select-all');
                if (selectAll) {
                    var all = page.querySelectorAll('.compare-item-cb');
                    selectAll.checked = all.length > 0 && all.length === page.querySelectorAll('.compare-item-cb:checked').length;
                    selectAll.indeterminate = page.querySelectorAll('.compare-item-cb:checked').length > 0 && page.querySelectorAll('.compare-item-cb:checked').length < all.length;
                }
            }
            $(document).on('change', '.compare-page .compare-item-cb', updateCompareDeleteBtn);
            $(document).on('change', '.compare-page .compare-select-all', function() {
                var page = document.querySelector('.compare-page');
                if (!page) return;
                var check = this.checked;
                page.querySelectorAll('.compare-item-cb').forEach(function(cb) { cb.checked = check; });
                updateCompareDeleteBtn();
            });
            $(document).on('click', '.compare-page .compare-delete-selected', function() {
                var page = document.querySelector('.compare-page');
                if (!page) return;
                var ids = [];
                page.querySelectorAll('.compare-item-cb:checked').forEach(function(cb) { ids.push(parseInt(cb.value, 10)); });
                ids = ids.filter(function(id, i, a) { return a.indexOf(id) === i; });
                if (ids.length === 0) return;
                if (!confirm('{{ __("Remove selected products from comparison?") }}')) return;
                var $btn = $(this);
                $btn.prop('disabled', true);
                $.ajax({
                    type: 'POST',
                    url: '{{ route("compare.remove.bulk") }}',
                    data: { product_ids: ids, _token: '{{ csrf_token() }}' },
                    dataType: 'json'
                }).done(function(r) {
                    if (r && r.success) {
                        if (typeof notify === 'function') notify('success', r.message);
                        var contentEl = document.getElementById('dashboard-ajax-content');
                        if (contentEl) {
                            fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' }, credentials: 'same-origin' })
                                .then(function(res) { return res.text(); })
                                .then(function(html) {
                                    var doc = new DOMParser().parseFromString(html, 'text/html');
                                    var newContent = doc.getElementById('dashboard-ajax-content');
                                    if (newContent) contentEl.innerHTML = newContent.innerHTML;
                                });
                        } else window.location.reload();
                        if (typeof getCompareCount === 'function') getCompareCount();
                    }
                }).fail(function() {
                    if (typeof notify === 'function') notify('error', '{{ __("Could not remove selected.") }}');
                }).always(function() { $btn.prop('disabled', false); });
            });
            function initComparePage() {
                var page = document.querySelector('.compare-page');
                if (page) {
                    try {
                        var saved = localStorage.getItem('compare_view');
                        if (saved === 'compact' || saved === 'comfortable') {
                            page.setAttribute('data-view-mode', saved);
                            var compact = page.querySelector('.compare-compact-view');
                            var comfortable = page.querySelector('.compare-comfortable-view');
                            if (compact) compact.classList.toggle('d-none', saved !== 'compact');
                            if (comfortable) comfortable.classList.toggle('d-none', saved !== 'comfortable');
                            page.querySelectorAll('.dashboard-view-btn').forEach(function(btn) { btn.classList.toggle('active', btn.getAttribute('data-view') === saved); });
                        }
                    } catch (e) {}
                    updateCompareDeleteBtn();
                }
            }
            window.addEventListener('dashboard-content-updated', initComparePage);

            var defer = typeof requestIdleCallback !== 'undefined' ? function(fn) { requestIdleCallback(fn, { timeout: 300 }); } : function(fn) { setTimeout(fn, 0); };
            defer(function() {
                getCartCount();
                getWishlistCount();
                getCompareCount();
                initComparePage();
            });

            defer(function() {
                Array.from(document.querySelectorAll('#dashboard-ajax-content table')).forEach(table => {
                    let heading = table.querySelectorAll('thead tr th');
                    Array.from(table.querySelectorAll('tbody tr')).forEach(row => {
                        Array.from(row.querySelectorAll('td')).forEach((column, i) => {
                            if (heading[i]) (column.colSpan == 100) || column.setAttribute('data-label', heading[i].innerText);
                        });
                    });
                });
            });
            window.addEventListener('dashboard-content-updated', function() {
                Array.from(document.querySelectorAll('#dashboard-ajax-content table')).forEach(table => {
                    let heading = table.querySelectorAll('thead tr th');
                    Array.from(table.querySelectorAll('tbody tr')).forEach(row => {
                        Array.from(row.querySelectorAll('td')).forEach((column, i) => {
                            if (heading[i]) (column.colSpan == 100) || column.setAttribute('data-label', heading[i].innerText);
                        });
                    });
                });
                if (typeof getCompareCount === 'function') getCompareCount();
            });

            function updateOfferTimerBars() {
                document.querySelectorAll('.offer-timer-bar[data-end-ts]').forEach(function(bar) {
                    var endTs = parseInt(bar.getAttribute('data-end-ts'), 10), d = endTs - Date.now();
                    var wrap = bar.querySelector('.offer-timer-bar__countdown');
                    if (!wrap) return;
                    var hEl = wrap.querySelector('.countdown-hours'), mEl = wrap.querySelector('.countdown-mins'), sEl = wrap.querySelector('.countdown-secs');
                    if (d <= 0) { if (hEl) hEl.textContent = '00'; if (mEl) mEl.textContent = '00'; if (sEl) sEl.textContent = '00'; return; }
                    var h = Math.floor(d/3600000), m = Math.floor((d%3600000)/60000), s = Math.floor((d%60000)/1000);
                    if (hEl) hEl.textContent = ('0'+h).slice(-2); if (mEl) mEl.textContent = ('0'+m).slice(-2); if (sEl) sEl.textContent = ('0'+s).slice(-2);
                });
            }
            updateOfferTimerBars();
            setInterval(updateOfferTimerBars, 1000);

            /* Cart Qty +/- and Totals: works for full page and AJAX-loaded cart (user/cart) */
            var cartSym = '{{ $general->cur_sym ?? "" }}';
            function cartSubTotal() {
                var subtotal = 0;
                $('#dashboard-ajax-content .cart-row, #dashboard-ajax-content .cart-row-mobile, .cart-page .cart-row, .cart-page .cart-row-mobile').each(function() {
                    var $row = $(this);
                    var $cb = $row.find('input.cart-select-item:checked');
                    if ($cb.length) {
                        var val = parseFloat($cb.attr('data-row-subtotal')) || parseFloat($row.attr('data-row-subtotal')) || 0;
                        subtotal += val;
                    }
                });
                $('#dashboard-ajax-content .subtotal-price, #dashboard-ajax-content .total-price, .cart-page .subtotal-price, .cart-page .total-price').text(cartSym + subtotal.toFixed(2));
            }
            window.cartSubTotal = cartSubTotal;
            function cartCalculation($row) {
                var qty = parseInt($row.find('input[name="quantity"]').val(), 10) || 1;
                var maxQtyAttr = parseInt($row.attr('data-max-qty'), 10);
                if (!isNaN(maxQtyAttr) && maxQtyAttr > 0 && qty > maxQtyAttr) {
                    qty = maxQtyAttr;
                    $row.find('input[name="quantity"]').val(maxQtyAttr);
                    if (typeof notify === 'function') notify('error', '{{ __("Maximum available quantity reached for this product.") }}');
                }
                var priceText = $row.find('.price').text();
                var price = parseFloat(String(priceText).split(cartSym)[1] || 0) || 0;
                var newSub = (qty * price).toFixed(2);
                $row.find('.subtotal').text(cartSym + newSub);
                $row.attr('data-row-subtotal', newSub);
                $row.find('.cart-select-item').attr('data-row-subtotal', newSub).data('row-subtotal', newSub);
                var data = { product_id: $row.find('.productName').data('product_id'), quantity: qty, _token: '{{ csrf_token() }}' };
                var v = $row.find('.productName').data('variant_id'); if (v) data.variant_id = v;
                var vd = $row.attr('data-variant-details'); if (vd) data.variant_details = vd;
                $.post('{{ route("cart.list.update") }}', data);
                cartSubTotal();
            }
            $(document).on('click', '.cart-decrease', function(e) {
                e.preventDefault();
                var $row = $(this).closest('tr').length ? $(this).closest('tr') : $(this).closest('.cart-row-mobile');
                var $inp = $row.find('input[name="quantity"]');
                var q = parseInt($inp.val(), 10) || 1;
                if (q > 1) {
                    $inp.val(q - 1);
                    cartCalculation($row);
                } else {
                    $inp.val(1);
                    if (typeof notify === 'function') notify('error', '{{ __("Minimum quantity is 1.") }}');
                }
            });
            $(document).on('click', '.cart-increase', function(e) {
                e.preventDefault();
                var $row = $(this).closest('tr').length ? $(this).closest('tr') : $(this).closest('.cart-row-mobile');
                var $inp = $row.find('input[name="quantity"]');
                var current = parseInt($inp.val(), 10) || 1;
                var maxQtyAttr = parseInt($row.attr('data-max-qty'), 10);
                if (!isNaN(maxQtyAttr) && maxQtyAttr > 0 && current >= maxQtyAttr) {
                    $inp.val(maxQtyAttr);
                    if (typeof notify === 'function') notify('error', '{{ __("Maximum available quantity reached for this product.") }}');
                    cartCalculation($row);
                    return;
                }
                $inp.val(current + 1);
                cartCalculation($row);
            });
            $(document).on('change', 'input[name="quantity"]', function() {
                var $row = $(this).closest('tr').length ? $(this).closest('tr') : $(this).closest('.cart-row-mobile');
                var q = parseInt($(this).val(), 10);
                if (q < 1) { $(this).val(1); q = 1; }
                cartCalculation($row);
            });
            $(document).on('change', '.cart-select-item, #cartSelectAll', function() {
                if (this.id === 'cartSelectAll') $('.cart-select-item').prop('checked', $(this).prop('checked'));
                cartSubTotal();
            });
            $(document).on('submit', '#checkoutSelectionForm', function(e) {
                var $checked = $('#dashboard-ajax-content .cart-page__table input.cart-select-item:checked[name="cart_ids[]"], #dashboard-ajax-content .cart-row-mobile input.cart-select-item:checked[name="cart_ids[]"], .cart-page__table input.cart-select-item:checked[name="cart_ids[]"], .cart-row-mobile input.cart-select-item:checked[name="cart_ids[]"]');
                if (!$checked.length) { e.preventDefault(); if (typeof notify === 'function') notify('error', 'Please select at least one item to checkout.'); return false; }
                $('#checkout-cart-ids-container').empty();
                var hasValid = false;
                $checked.each(function() {
                    var v = $(this).val();
                    if (v && /^\d+$/.test(String(v))) { $('#checkout-cart-ids-container').append($('<input type="hidden" name="cart_ids[]">').val(v)); hasValid = true; }
                });
                if (!hasValid) { e.preventDefault(); if (typeof notify === 'function') notify('error', 'Please select at least one item to checkout.'); return false; }
            });
            $(function() { cartSubTotal(); });
            window.addEventListener('dashboard-content-updated', function() { $(function() { cartSubTotal(); }); });

            /* Cart Remove: works for full page and AJAX-loaded cart (user/cart) */
            var cartRemoveModalItem = null;
            $(document).on('click', '.remove-btn, .cart-remove-btn-user', function() {
                cartRemoveModalItem = $(this).closest('tr').length ? $(this).closest('tr') : $(this).closest('.cart-row-mobile');
                var modal = document.getElementById('removeCartModal');
                if (modal && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    (new bootstrap.Modal(modal)).show();
                } else if (modal && typeof jQuery !== 'undefined' && jQuery(modal).modal) {
                    jQuery(modal).modal('show');
                }
            });
            $(document).on('click', '.remove-product', function() {
                var row = cartRemoveModalItem;
                if (!row || !row.length) return;
                var product_id = row.find('.productName').data('product_id') || row.find('.cart-remove-btn-user').data('product_id');
                var variant_id = row.find('.productName').data('variant_id') || row.find('.cart-remove-btn-user').data('variant_id') || null;
                var variant_details = row.attr('data-variant-details') || row.find('.cart-remove-btn-user').data('variant_details') || '';
                var data = { product_id: product_id, _token: '{{ csrf_token() }}' };
                if (variant_id) data.variant_id = variant_id;
                if (variant_details) data.variant_details = variant_details;
                $.post('{{ route("cart.list.remove") }}', data).done(function(r) {
                    if (r && r.success) {
                        row.fadeOut(200, function() {
                            $(this).remove();
                            if (typeof window.cartSubTotal === 'function') window.cartSubTotal();
                        });
                        if (typeof getCartCount === 'function') getCartCount();
                        if (typeof notify === 'function') notify('success', r.success);
                    } else if (typeof notify === 'function') notify('error', r && r.error);
                }).fail(function() {
                    if (typeof notify === 'function') notify('error', '{{ __("Could not remove item.") }}');
                });
                var modal = document.getElementById('removeCartModal');
                if (modal && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    bootstrap.Modal.getInstance(modal).hide();
                } else if (modal && typeof jQuery !== 'undefined') {
                    jQuery(modal).modal('hide');
                }
                cartRemoveModalItem = null;
            });
        })(jQuery);
    </script>
@endpush
