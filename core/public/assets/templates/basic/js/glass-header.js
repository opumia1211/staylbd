/**
 * Glass Header JavaScript
 * Handles mobile menu, scroll effects, and interactions
 * On /user/*: Hamburger opens/closes USER PROFILE MENU (OVERVIEW, SHOPPING, ACCOUNT).
 * On other pages: Hamburger opens/closes glass site menu (Home, Products, Categories).
 */

(function() {
    'use strict';

    // Core Elements & State
    const headerMaster = document.querySelector('.stayl-fixed-master');
    const searchWrapper = document.querySelector('.stayl-search-pill-wrapper');
    const searchInputField = document.querySelector('#universalSearchInput');
    const searchForm = document.querySelector('#universalSearchForm');
    const resultsContainer = document.querySelector('#searchResults');
    const cameraBtn = document.querySelector('#cameraSearchBtn');
    const voiceBtn = document.querySelector('#voiceSearchBtn');
    const mobileSearchToggle = document.querySelector('#staylMobileSearchToggle');
    const themeToggle = document.querySelector('#staylThemeToggle');

    function iconSvg(name, extraClass) {
        var cls = 'ui-icon ui-icon--runtime' + (extraClass ? (' ' + extraClass) : '');
        var icons = {
            spinner: '<svg class="animate-spin" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-6.219-8.56"></path></svg>',
            search: '<svg class="' + cls + '" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="7"></circle><path d="M20 20l-3.5-3.5"></path></svg>',
            image: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>',
            scan: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7V5a2 2 0 0 1 2-2h2"/><path d="M17 3h2a2 2 0 0 1 2 2v2"/><path d="M21 17v2a2 2 0 0 1-2 2h-2"/><path d="M7 21H5a2 2 0 0 1-2-2v-2"/><circle cx="12" cy="12" r="3"/><path d="M12 8v8"/><path d="M8 12h8"/></svg>',
            folder: '<svg class="' + cls + '" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 7a2 2 0 0 1 2-2h5l2 2h7a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg>',
            folderOpen: '<svg class="' + cls + '" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 8a2 2 0 0 1 2-2h5l2 2h7a2 2 0 0 1 2 2v1H3z"></path><path d="M3 11h18l-2 8H5z"></path></svg>',
            tag: '<svg class="' + cls + '" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20 13l-7 7-9-9V4h7z"></path><circle cx="7.5" cy="7.5" r="1.2"></circle></svg>',
            link: '<svg class="' + cls + '" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M10 13a5 5 0 0 1 0-7l1.5-1.5a5 5 0 0 1 7 7L17 13"></path><path d="M14 11a5 5 0 0 1 0 7L12.5 19.5a5 5 0 0 1-7-7L7 11"></path></svg>',
            file: '<svg class="' + cls + '" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M14 2H7a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7z"></path><path d="M14 2v5h5"></path></svg>',
            mic: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/><line x1="12" y1="19" x2="12" y2="22"/></svg>'
        };
        return icons[name] || icons.file;
    }

    // Professional Glass Sidebar Toggle - Hamburger opens/closes menu from left
    const menuToggle = document.getElementById('glassMenuToggle');
    const sidebar = document.getElementById('glassSidebar');
    const sidebarClose = document.getElementById('glassSidebarClose');
    const sidebarEdgeToggle = document.getElementById('glassSidebarEdgeToggleLauncher') || document.getElementById('glassSidebarEdgeToggle');
    const sidebarOverlay = sidebar ? (sidebar.querySelector('.glass-sidebar-overlay') || sidebar.querySelector('.glass-mobile-menu-overlay')) : null;
    const sidebarSearchForm = document.getElementById('glassSidebarSearchForm');
    const sidebarSearchInput = document.getElementById('glassSidebarSearchInput');
    const sidebarSearchResults = document.getElementById('glassSidebarSearchResults');
    const sidebarApplyFilter = document.getElementById('glassSidebarApplyFilter');

    /** True ONLY when on user profile section – hamburger opens profile menu (Dashboard, Track Order, etc.). False on public pages. */
    function isOnDashboardPage() {
        try {
            var path = (typeof window.location.pathname === 'string') ? window.location.pathname : '';
            if (path.indexOf('/user') !== -1) return true;
            if (path.indexOf('/user/') !== -1) return true;
            if (document.getElementById('user-dashboard-root')) return true;
            if (document.querySelector('[data-user-dashboard="1"]')) return true;
        } catch (e) {}
        return false;
    }
    function pathIsUserPage() {
        try {
            var p = (typeof window.location.pathname === 'string') ? window.location.pathname : '';
            return p.indexOf('/user') !== -1;
        } catch (e) { return false; }
    }
    /** Hamburger visible: mobile, tablet, small laptop (Bootstrap d-xl-none = max-width 1199.98px). Use matchMedia for reliable detection. */
    function isHamburgerVisibleWidth() {
        if (typeof window.matchMedia !== 'undefined' && window.matchMedia('(max-width: 1199.98px)').matches) return true;
        return window.innerWidth < 1200;
    }
    /** Close user profile sidebar (used by overlay and close button) */
    function closeDashboardSidebar() {
        var dashSidebar = document.querySelector('#user-dashboard-root .dashboard__sidebar') || document.querySelector('.dashboard__sidebar');
        var overlay = document.querySelector('.overlay');
        if (dashSidebar) dashSidebar.classList.remove('active');
        if (overlay) overlay.classList.remove('active');
        document.body.classList.remove('dashboard-sidebar-open');
    }
    /** Toggle user profile sidebar (OVERVIEW, SHOPPING, ACCOUNT – full menu: Dashboard, Track Order, Notifications, etc.) */
    function toggleDashboardSidebarFromHeader() {
        var dashSidebar = document.querySelector('#user-dashboard-root .dashboard__sidebar') || document.querySelector('.dashboard__sidebar');
        var overlay = document.querySelector('.overlay');
        if (dashSidebar) {
            if (sidebar && sidebar.classList.contains('active')) closeSidebar();
            var isOpen = dashSidebar.classList.toggle('active');
            if (overlay) overlay.classList.toggle('active', isOpen);
            document.body.classList.toggle('dashboard-sidebar-open', isOpen);
        }
    }

    // Single handler for hamburger: on user page open dashboard sidebar; on other pages open glass menu. Use capture so we run first on all devices.
    var headerHamburgerTouchHandled = false;
    function bindHamburger() {
        var btn = document.getElementById('glassMenuToggle');
        if (!btn) return;
        function handleUserPageOpen(e) {
            if (!pathIsUserPage() && !isOnDashboardPage()) return false;
            e.preventDefault();
            e.stopPropagation();
            if (e.stopImmediatePropagation) e.stopImmediatePropagation();
            if (e.type === 'touchend') {
                headerHamburgerTouchHandled = true;
                /* Prevent synthetic click so overlay does not receive it and close sidebar */
                e.preventDefault();
            }
            if (e.type === 'click' && headerHamburgerTouchHandled) { headerHamburgerTouchHandled = false; return true; }
            toggleDashboardSidebarFromHeader();
            var dashSidebar = document.querySelector('#user-dashboard-root .dashboard__sidebar') || document.querySelector('.dashboard__sidebar');
            if (dashSidebar && dashSidebar.classList.contains('active')) window._dashboardOverlayOpenTime = Date.now();
            return true;
        }
        btn.addEventListener('click', function(e) {
            if (handleUserPageOpen(e)) return;
            e.preventDefault();
            e.stopPropagation();
            if (sidebar) {
                if (sidebar.classList.contains('active')) closeSidebar();
                else openSidebar();
            }
        }, true);
        btn.addEventListener('touchend', function(e) {
            if (handleUserPageOpen(e)) {
                e.preventDefault();
                e.stopPropagation();
                if (e.stopImmediatePropagation) e.stopImmediatePropagation();
                setTimeout(function() { headerHamburgerTouchHandled = false; }, 400);
                return;
            }
        }, { capture: true, passive: false });
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bindHamburger);
    } else {
        bindHamburger();
    }

    // Close Sidebar
    function closeSidebar() {
        if (sidebar) {
            sidebar.classList.remove('active');
            document.body.style.overflow = '';
            document.body.classList.remove('glass-sidebar-open');
            // Reset search results
            if (sidebarSearchResults) {
                sidebarSearchResults.classList.remove('active');
                sidebarSearchResults.innerHTML = '';
            }
        }
    }

    function openSidebar() {
        if (sidebar) {
            sidebar.classList.add('active');
            document.body.style.overflow = 'hidden';
            document.body.classList.add('glass-sidebar-open');
        }
    }

    if (sidebarClose) {
        sidebarClose.addEventListener('click', closeSidebar);
    }

    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', closeSidebar);
    }
    if (sidebarEdgeToggle) {
        sidebarEdgeToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (sidebar && sidebar.classList.contains('active')) closeSidebar();
            else openSidebar();
        });
        sidebarEdgeToggle.addEventListener('touchend', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (sidebar && sidebar.classList.contains('active')) closeSidebar();
            else openSidebar();
        }, { passive: false });
    }

    // Close sidebar on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (sidebar && sidebar.classList.contains('active')) closeSidebar();
            if (isOnDashboardPage()) closeDashboardSidebar();
        }
    });

    // On user profile: overlay click and close-dashboard-sidebar button close the profile menu
    function bindDashboardSidebarClose() {
        var overlay = document.querySelector('.overlay');
        if (overlay && !overlay._dashboardCloseBound) {
            overlay._dashboardCloseBound = true;
            overlay.addEventListener('click', function() {
                if (!isOnDashboardPage() || !document.querySelector('.dashboard__sidebar.active')) return;
                /* Guard: ignore click within 350ms of opening (avoids synthetic tap closing menu on mobile) */
                var openTime = window._dashboardOverlayOpenTime || 0;
                if (Date.now() - openTime < 350) return;
                closeDashboardSidebar();
            });
        }
        document.querySelectorAll('.close-dashboard-sidebar').forEach(function(btn) {
            if (btn._dashboardCloseBound) return;
            btn._dashboardCloseBound = true;
            btn.addEventListener('click', closeDashboardSidebar);
        });
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bindDashboardSidebarClose);
    } else {
        bindDashboardSidebarClose();
    }

    // Language dropdown: vanilla toggle (no Bootstrap dropdown dependency).
    (function() {
        var langWrap = document.querySelector('.glass-lang-dropdown-wrap');
        var glassHeader = document.querySelector('.glass-header');
        if (!langWrap) return;
        var toggleBtn = langWrap.querySelector('.js-lang-dropdown-toggle');
        var menu = langWrap.querySelector('.dropdown-menu');
        if (!toggleBtn || !menu) return;

        function isMobileOrTablet() { return window.innerWidth <= 991; }
        function setOpenState(isOpen) {
            langWrap.classList.toggle('show', isOpen);
            menu.classList.toggle('show', isOpen);
            toggleBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            if (glassHeader) {
                glassHeader.classList.toggle('glass-header-lang-open', isOpen);
            }
            if (isOpen && isMobileOrTablet()) {
                langWrap.classList.remove('dropup');
            }
        }

        toggleBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            setOpenState(!langWrap.classList.contains('show'));
        });

        document.addEventListener('click', function(e) {
            if (!langWrap.contains(e.target)) {
                setOpenState(false);
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                setOpenState(false);
            }
        });
    })();

    // Language switch: force full navigation so session locale applies immediately.
    document.addEventListener('click', function(e) {
        var langLink = e.target.closest('.js-lang-switch');
        if (!langLink) return;
        var href = langLink.getAttribute('href');
        if (!href) return;
        e.preventDefault();
        e.stopPropagation();
        window.location.assign(href);
    }, true);

    // Sidebar category accordion - expand button toggles subcategories (link navigates)
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.sidebar-cat-expand');
        if (!btn) return;
        const sidebar = document.getElementById('glassSidebar');
        if (!sidebar || !sidebar.contains(btn)) return;
        e.preventDefault();
        e.stopPropagation();
        const li = btn.closest('li');
        if (li) li.classList.toggle('expanded');
    });

    // Sidebar Search - Real-time Search
    if (sidebarSearchInput) {
        let searchTimeout;
        sidebarSearchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();
            
            if (query.length < 1) {
                sidebarSearchResults.classList.remove('active');
                sidebarSearchResults.innerHTML = '';
                return;
            }

            searchTimeout = setTimeout(() => {
                performSidebarSearch(query);
            }, 300);
        });

        // Search on form submit
        if (sidebarSearchForm) {
            sidebarSearchForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const query = sidebarSearchInput.value.trim();
                if (query) {
                    const productsUrl = (sidebarSearchForm && sidebarSearchForm.getAttribute('action')) || '/products';
                    window.location.href = `${productsUrl}?search=${encodeURIComponent(query)}`;
                }
            });
        }
    }

    // Perform Sidebar Search (GET with q= for live suggestions)
    function performSidebarSearch(query) {
        if (!sidebarSearchResults) return;

        var sidebarSearchUrl = (document.querySelector('#universalSearchForm') && document.querySelector('#universalSearchForm').getAttribute('data-search-url')) || (document.querySelector('#universalSearchInput') && document.querySelector('#universalSearchInput').getAttribute('data-search-url')) || '/search/universal';

        sidebarSearchResults.classList.add('active');
        sidebarSearchResults.innerHTML = '<div style="padding: 20px; text-align: center; color: #999;">Searching...</div>';

        fetch(sidebarSearchUrl + '?q=' + encodeURIComponent(query), {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.results && data.results.length > 0) {
                let html = '<div style="padding: 10px; background: rgba(255,255,255,0.8); border-radius: 8px;">';
                data.results.slice(0, 5).forEach(item => {
                    const url = item.url || '#';
                    const title = item.title || item.name || 'Unknown';
                    const type = item.type || 'product';
                    html += `
                        <a href="${url}" style="display: block; padding: 10px; margin-bottom: 5px; background: rgba(255,255,255,0.9); border-radius: 6px; text-decoration: none; color: #333; transition: all 0.3s; border: 1px solid transparent;" 
                           onmouseover="this.style.background='rgba(79,196,247,0.1)'; this.style.borderColor='rgba(79,196,247,0.3)';"
                           onmouseout="this.style.background='rgba(255,255,255,0.9)'; this.style.borderColor='transparent';">
                            <div style="font-weight: 600; font-size: 14px; color: #1a1a1a;">${title}</div>
                            <div style="font-size: 12px; color: #999; margin-top: 2px;">${type}</div>
                        </a>
                    `;
                });
                html += '</div>';
                sidebarSearchResults.innerHTML = html;
            } else {
                sidebarSearchResults.innerHTML = '<div style="padding: 20px; text-align: center; color: #999;">No results found</div>';
            }
        })
        .catch(error => {
            console.error('Search error:', error);
            sidebarSearchResults.innerHTML = '<div style="padding: 20px; text-align: center; color: #ff6b6b;">Search error. Please try again.</div>';
        });
    }

    // Apply Filters
    if (sidebarApplyFilter) {
        sidebarApplyFilter.addEventListener('click', function() {
            const categories = Array.from(document.querySelectorAll('.glass-filter-checkbox[name="filter_category"]:checked'))
                .map(cb => cb.value).filter(v => v);
            const brands = Array.from(document.querySelectorAll('.glass-filter-checkbox[name="filter_brand"]:checked'))
                .map(cb => cb.value);
            const minPrice = document.getElementById('glassSidebarMinPrice')?.value || '';
            const maxPrice = document.getElementById('glassSidebarMaxPrice')?.value || '';

            // Build URL
            const params = new URLSearchParams();
            if (categories.length > 0) {
                categories.forEach(cat => params.append('categories[]', cat));
            }
            if (brands.length > 0) {
                brands.forEach(brand => params.append('brands[]', brand));
            }
            if (minPrice) params.append('min_price', minPrice);
            if (maxPrice) params.append('max_price', maxPrice);

            // Navigate to products page with filters
            const productsUrl = (document.querySelector('#universalSearchForm') && document.querySelector('#universalSearchForm').getAttribute('action')) || '/products';
            window.location.href = `${productsUrl}?${params.toString()}`;
        });
    }

    function setupHeaderScroll() {
        const header = document.querySelector('.glass-header');
        const headerMaster = document.querySelector('.stayl-fixed-master');
        if (!header && !headerMaster) return;

        let lastScroll = window.pageYOffset;
        const scrollThreshold = 40;

        window.addEventListener('scroll', function() {
            const currentScroll = window.pageYOffset;
            
            // Basic sticky/scrolled classes
            if (header) {
                if (currentScroll > 50) {
                    header.classList.add('scrolled');
                } else {
                    header.classList.remove('scrolled');
                }

                if (currentScroll > 140) {
                    header.classList.add('is-sticky');
                } else {
                    header.classList.remove('is-sticky');
                }
            }

            // Advanced "Stayl Fixed Master" collapse logic
            if (headerMaster) {
                if (currentScroll > scrollThreshold) {
                    headerMaster.classList.add('is-scrolled-down');
                } else {
                    headerMaster.classList.remove('is-scrolled-down');
                }
            }

            lastScroll = currentScroll;
        }, { passive: true });
    }

    function setupThemeToggle() {
        const themeBtn = document.getElementById('staylThemeToggle');
        const sunIcon = document.getElementById('themeIconSun');
        const moonIcon = document.getElementById('themeIconMoon');
        const body = document.body;
        const html = document.documentElement;
        if (!themeBtn) return;

        themeBtn.addEventListener('click', () => {
            // Lock transitions globally for an instant switch
            html.classList.add('theme-switching-fast');
            
            body.classList.toggle('dark-mode');
            html.classList.toggle('dark'); // Sync with Tailwind
            const isDark = body.classList.contains('dark-mode');
            localStorage.setItem('stayl-theme', isDark ? 'dark' : 'light');
            
            if (sunIcon) sunIcon.classList.toggle('hidden', !isDark);
            if (moonIcon) moonIcon.classList.toggle('hidden', isDark);
            
            // Unlock transitions immediately after render
            setTimeout(() => {
                html.classList.remove('theme-switching-fast');
            }, 50);
        });
    }

    function setupThemeToggle() {
        // ... (unchanged content handled automatically if I just replace after)
    }
    
    // =========================================================================
    // Elite Live Environment System (Location & Weather)
    // =========================================================================
    async function initLiveEnvironment() {
        const locationContainer = document.getElementById('stayl-live-location');
        const weatherContainer = document.getElementById('stayl-live-weather');
        
        if (!locationContainer || !weatherContainer) return;
        
        const locText = locationContainer.querySelector('.stayl-location-text');
        const weatherSvg = document.getElementById('stayl-weather-svg');
        const weatherText = weatherContainer.querySelector('.stayl-weather-text');
        
        // Cache data for 30 minutes to ensure instant load on navigation
        const cachedEnv = sessionStorage.getItem('stayl_live_env_v2');
        const now = new Date().getTime();
        
        if (cachedEnv) {
            const data = JSON.parse(cachedEnv);
            if (now - data.timestamp < 1800000) { // 30 mins
                renderEnvironment(data.city, data.temp, data.weatherCode);
                return;
            }
        }
        
        try {
            // Step 1: Fast IP Geolocation
            const geoRes = await fetch('https://ipapi.co/json/');
            if (!geoRes.ok) throw new Error('Geo failed');
            const geoData = await geoRes.json();
            
            const city = geoData.city || geoData.region || 'Unknown Location';
            const lat = geoData.latitude;
            const lon = geoData.longitude;
            
            // Step 2: Open-Meteo Weather (No API Key Required, Fast, Accurate)
            const weatherRes = await fetch(`https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}&current_weather=true`);
            if (!weatherRes.ok) throw new Error('Weather failed');
            const weatherData = await weatherRes.json();
            
            const temp = Math.round(weatherData.current_weather.temperature);
            const weatherCode = weatherData.current_weather.weathercode;
            
            const finalData = {
                city, temp, weatherCode, timestamp: now
            };
            
            sessionStorage.setItem('stayl_live_env_v2', JSON.stringify(finalData));
            renderEnvironment(city, temp, weatherCode);
            
        } catch (error) {
            console.error('Stayl Live Env Error:', error);
            if (locText) locText.textContent = 'Location Unavailable';
            if (weatherText) weatherText.textContent = '--°C';
        }
        
        function renderEnvironment(city, temp, code) {
            if (locText) {
                locText.textContent = city;
                locText.parentElement.title = `Current location: ${city}`;
            }
            if (weatherText) {
                // WMO Weather interpretation codes (WW)
                // 0: Clear sky
                // 1, 2, 3: Mainly clear, partly cloudy, and overcast
                // 45, 48: Fog
                // 51-67: Drizzle / Rain
                // 71-77: Snow
                // 95-99: Thunderstorm
                
                let desc = 'Clear';
                let icon = `<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-amber-400"><circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/></svg>`;
                
                if (code >= 1 && code <= 3) {
                    desc = 'Cloudy';
                    icon = `<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-slate-300"><path d="M17.5 19H9a7 7 0 1 1 6.71-9h1.79a4.5 4.5 0 1 1 0 9Z"/></svg>`;
                } else if (code >= 51 && code <= 67) {
                    desc = 'Rainy';
                    icon = `<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-sky-400"><path d="M17.5 19H9a7 7 0 1 1 6.71-9h1.79a4.5 4.5 0 1 1 0 9Z"/><path d="M16 23v-2"/><path d="M8 23v-2"/><path d="M12 23v-2"/></svg>`;
                } else if (code >= 71 && code <= 77) {
                    desc = 'Snowy';
                    icon = `<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white"><path d="M17.5 19H9a7 7 0 1 1 6.71-9h1.79a4.5 4.5 0 1 1 0 9Z"/><path d="m8 15 4 4 4-4"/><path d="M12 15v8"/></svg>`;
                } else if (code >= 95) {
                    desc = 'Stormy';
                    icon = `<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-indigo-400"><path d="M17.5 19H9a7 7 0 1 1 6.71-9h1.79a4.5 4.5 0 1 1 0 9Z"/><path d="m13 13-3 5h4l-3 5"/></svg>`;
                }
                
                weatherText.innerHTML = `<span class="font-bold text-white text-[13px] mr-1">${temp}°C</span> <span class="opacity-80">${desc}</span>`;
                if (weatherSvg) weatherSvg.innerHTML = icon;
            }
        }
    }

    function setupDropdownFallbacks() {
        const langBtn = document.getElementById('langBtn');
        const langMenu = document.getElementById('langMenu');
        const currencyBtn = document.getElementById('currencyBtn');
        const currencyMenu = document.getElementById('currencyMenu');

        if (langBtn && langMenu) {
            langBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                if (currencyMenu) currencyMenu.classList.add('hidden');
                langMenu.classList.toggle('hidden');
            });
        }

        if (currencyBtn && currencyMenu) {
            currencyBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                if (langMenu) langMenu.classList.add('hidden');
                currencyMenu.classList.toggle('hidden');
            });
        }

        document.addEventListener('click', function () {
            if (langMenu) langMenu.classList.add('hidden');
            if (currencyMenu) currencyMenu.classList.add('hidden');
        });
    }

    // Mobile search remains permanently integrated in header.
    const searchCenter = document.querySelector('.glass-header-center');
    const glassHeader = document.querySelector('.glass-header');
    const glassHeaderNav = document.querySelector('.glass-header-nav');
    const glassHeaderRight = document.querySelector('.glass-header-right');
    function setupMobileSearch() {
        if (!searchInputField || !searchWrapper) return;
        searchInputField.addEventListener('focus', function() {
            searchWrapper.style.transform = 'scale(1.01)';
            if (window.innerWidth < 1200 && glassHeader) {
                glassHeader.classList.add('mobile-search-focused');
            }
        });
        searchInputField.addEventListener('blur', function() {
            if (glassHeader) glassHeader.classList.remove('mobile-search-focused');
            setTimeout(function() {
                searchWrapper.style.transform = 'scale(1)';
            }, 120);
        });
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', setupMobileSearch);
    else setupMobileSearch();

    // Professional Voice Search
    let searchResultsEl = resultsContainer;
    let recognition = null;
    let isListening = false;
    var defaultSearchPlaceholder = (searchInputField && searchInputField.getAttribute('placeholder')) || '';
    var listeningPlaceholder = (searchInputField && searchInputField.dataset && searchInputField.dataset.placeholderListening) || 'Listening... Please speak.';

    function restoreSearchPlaceholder() {
        if (searchInputField) searchInputField.placeholder = defaultSearchPlaceholder;
    }

    function showVoiceSearchAlert(msg, type = 'error') {
        if (searchResultsEl) {
            searchResultsEl.innerHTML = `<div class="glass-search-no-results" style="color: ${type === 'error' ? '#ff6b6b' : '#22c55e'}; font-weight: 500; padding: 12px; text-align: center;">
                <div style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                    ${type === 'error' ? 
                        '<svg style="width: 20px; height: 20px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>' : 
                        '<svg style="width: 20px; height: 20px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>'
                    }
                    <span>${msg}</span>
                </div>
            </div>`;
            searchResultsEl.classList.add('active');
            setTimeout(function() {
                if (searchResultsEl) searchResultsEl.classList.remove('active');
            }, 6000);
        } else {
            alert(msg);
        }
    }

    /** Mic + speech APIs need HTTPS (localhost is exempt). */
    function voiceSearchNeedsHttps() {
        if (typeof window.isSecureContext === 'boolean' && window.isSecureContext) return false;
        var h = (location && location.hostname) ? String(location.hostname).toLowerCase() : '';
        return h !== 'localhost' && h !== '127.0.0.1' && h !== '[::1]';
    }

    if (voiceBtn) {
        try {
            voiceBtn.style.touchAction = 'manipulation';
            voiceBtn.setAttribute('type', 'button');
        } catch (ignore) {}
        
        var SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;

        if (SpeechRecognition) {
            try {
                recognition = new SpeechRecognition();
                recognition.continuous = false;
                recognition.interimResults = true; // Show interim text live
                var docLang = (document.documentElement && document.documentElement.lang) ? document.documentElement.lang.trim() : '';
                // Defaults to Bengali if set, otherwise English
                recognition.lang = (docLang.toLowerCase().includes('bn')) ? 'bn-BD' : (docLang || 'en-US');

                recognition.onstart = function() {
                    isListening = true;
                    if (voiceBtn) voiceBtn.classList.add('listening');
                    if (searchInputField) searchInputField.placeholder = listeningPlaceholder;
                };

                recognition.onresult = function(event) {
                    var transcript = '';
                    for (var i = event.resultIndex; i < event.results.length; ++i) {
                        transcript += event.results[i][0].transcript;
                    }
                    
                    if (searchInputField) {
                        searchInputField.value = transcript.trim();
                        
                        // Give it that live feeling
                        if (event.results[0].isFinal) {
                            restoreSearchPlaceholder();
                            voiceBtn.classList.remove('listening');
                            isListening = false;
                            
                            // Automatically search when speech finishes completely
                            if (typeof performUniversalSearch === 'function') {
                                performUniversalSearch(transcript.trim());
                            }
                            
                            // Optionally submit the form immediately
                            // const searchForm = document.querySelector('#universalSearchForm');
                            // if (searchForm) searchForm.submit();
                        }
                    }
                };

                recognition.onerror = function(event) {
                    console.error('Speech recognition error:', event.error);
                    voiceBtn.classList.remove('listening');
                    isListening = false;
                    restoreSearchPlaceholder();

                    var errorMsg = 'Voice search failed. Please try again.';
                    switch (event.error) {
                        case 'no-speech':
                            errorMsg = 'No speech detected. Please try again.';
                            break;
                        case 'audio-capture':
                            errorMsg = 'Microphone not available. Check your device settings.';
                            break;
                        case 'not-allowed':
                            errorMsg = 'Microphone permission denied. Allow access in the browser address bar.';
                            break;
                        case 'network':
                            errorMsg = 'Voice search needs a network connection (browser requirement).';
                            break;
                    }

                    showVoiceSearchAlert(errorMsg, 'error');
                };

                recognition.onend = function() {
                    voiceBtn.classList.remove('listening');
                    isListening = false;
                    restoreSearchPlaceholder();
                };

                var voiceTouchHandled = false;

                function handleVoiceActivate(e) {
                    if (e) {
                        e.preventDefault();
                        e.stopPropagation();
                    }
                    
                    if (isListening) {
                        try { recognition.stop(); } catch (err) {}
                        return;
                    }
                    
                    if (voiceSearchNeedsHttps()) {
                        showVoiceSearchAlert('Voice search needs HTTPS or localhost.', 'error');
                        return;
                    }

                    try {
                        recognition.start();
                    } catch (err) {
                        console.error('Failed to start recognition', err);
                        // If it's already started, just ignore or stop it
                        if (err.name === 'InvalidStateError') {
                            try { recognition.stop(); } catch(stopErr) {}
                        }
                        isListening = false;
                        voiceBtn.classList.remove('listening');
                    }
                }

                voiceBtn.addEventListener('touchend', function(e) {
                    voiceTouchHandled = true;
                    handleVoiceActivate(e);
                    setTimeout(function() {
                        voiceTouchHandled = false;
                    }, 450);
                }, { capture: true, passive: false });

                voiceBtn.addEventListener('click', function(e) {
                    if (voiceTouchHandled) {
                        e.preventDefault();
                        e.stopPropagation();
                        return;
                    }
                    handleVoiceActivate(e);
                }, true);
            } catch (err) {
                console.error('Error initializing speech recognition:', err);
                voiceBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    alert('Voice search is not available in this browser.');
                });
            }
        } else {
            voiceBtn.addEventListener('click', function(e) {
                e.preventDefault();
                alert('Voice search is not supported in this browser. Try Chrome or Edge.');
            });
        }
    }

    if (cameraBtn) {
        const lensCard = document.querySelector('#staylLensCard');
        const cameraOpt = document.querySelector('#lensOptionCamera');
        const fileOpt = document.querySelector('#lensOptionFile');
        const cameraInput = document.querySelector('#staylCameraFileDirect');
        const galleryInput = document.querySelector('#staylGalleryFileDirect');

        // Camera Modal Elements
        const cameraModal = document.querySelector('#staylCameraModal');
        const cameraVideo = document.querySelector('#staylCameraVideo');
        const cameraCanvas = document.querySelector('#staylCameraCanvas');
        const captureBtn = document.querySelector('#staylCameraCaptureBtn');
        const closeCameraBtn = document.querySelector('#staylCameraCloseBtn');
        let cameraStream = null;

        cameraBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const currentLensCard = document.querySelector('#staylLensCard');
            if (currentLensCard) {
                currentLensCard.classList.toggle('is-active');
            } else {
                console.error("Lens card not found in DOM.");
            }
        });

        document.addEventListener('click', function(e) {
            const currentLensCard = document.querySelector('#staylLensCard');
            if (currentLensCard && currentLensCard.classList.contains('is-active') && !cameraBtn.contains(e.target) && !currentLensCard.contains(e.target)) {
                currentLensCard.classList.remove('is-active');
            }
        });

        async function startCamera() {
            try {
                cameraStream = await navigator.mediaDevices.getUserMedia({ 
                    video: { facingMode: 'environment' }, 
                    audio: false 
                });
                if (cameraVideo) {
                    cameraVideo.srcObject = cameraStream;
                    if (cameraModal) cameraModal.classList.add('is-active');
                }
            } catch (err) {
                console.error("Camera access failed:", err);
                alert("Camera access denied or not available.");
            }
        }

        function stopCamera() {
            if (cameraStream) {
                cameraStream.getTracks().forEach(track => track.stop());
                cameraStream = null;
            }
            if (cameraModal) cameraModal.classList.remove('is-active');
        }

        if (cameraOpt) {
            cameraOpt.addEventListener('click', function() {
                startCamera();
                if (lensCard) lensCard.classList.remove('is-active');
            });
        }

        if (closeCameraBtn) closeCameraBtn.addEventListener('click', stopCamera);
        if (cameraModal) {
            cameraModal.querySelector('.stayl-camera-modal__backdrop')?.addEventListener('click', stopCamera);
        }

        if (captureBtn && cameraVideo && cameraCanvas) {
            captureBtn.addEventListener('click', function() {
                const context = cameraCanvas.getContext('2d');
                cameraCanvas.width = cameraVideo.videoWidth;
                cameraCanvas.height = cameraVideo.videoHeight;
                context.drawImage(cameraVideo, 0, 0, cameraCanvas.width, cameraCanvas.height);
                
                cameraCanvas.toBlob(function(blob) {
                    const file = new File([blob], "capture.jpg", { type: "image/jpeg" });
                    stopCamera();
                    cameraBtn.classList.add('uploading');
                    cameraBtn.innerHTML = iconSvg('spinner');
                    performImageSearch(file, null);
                }, 'image/jpeg', 0.85);
            });
        }

        if (fileOpt && galleryInput) {
            fileOpt.addEventListener('click', function() {
                galleryInput.click();
                if (lensCard) lensCard.classList.remove('is-active');
            });
        }

        const handleImageSelect = function(e) {
            if (e.target.files && e.target.files[0]) {
                const file = e.target.files[0];
                cameraBtn.classList.add('uploading');
                cameraBtn.innerHTML = iconSvg('spinner');
                performImageSearch(file, e.target);
            }
        };

        if (cameraInput) cameraInput.addEventListener('change', handleImageSelect);
        if (galleryInput) galleryInput.addEventListener('change', handleImageSelect);
    }

    // Universal Search State
    let searchAbortController = null;
    let searchTimeout = null;
    let searchResultsFocusedIndex = -1;
    const SEARCH_CACHE_KEY = 'staylbd_recent_search_terms_v1';
    const SEARCH_CACHE_LIMIT = 8;
    const SEARCH_DEBOUNCE_MS = 150;
    var trendingKeywordsCache = null;
    var trendingLoadPromise = null;

    function readRecentSearchTerms() {
        try {
            var raw = localStorage.getItem(SEARCH_CACHE_KEY);
            var parsed = raw ? JSON.parse(raw) : [];
            return Array.isArray(parsed) ? parsed.filter(Boolean) : [];
        } catch (e) { return []; }
    }

    function writeRecentSearchTerms(terms) {
        try { localStorage.setItem(SEARCH_CACHE_KEY, JSON.stringify(terms.slice(0, SEARCH_CACHE_LIMIT))); } catch (e) {}
    }

    function saveRecentSearchTerm(term) {
        var t = String(term || '').trim();
        if (!t) return;
        var list = readRecentSearchTerms().filter(function(x) { return String(x).toLowerCase() !== t.toLowerCase(); });
        list.unshift(t);
        writeRecentSearchTerms(list);
    }

    function getRecentMatches(query) {
        var q = String(query || '').trim().toLowerCase();
        var list = readRecentSearchTerms();
        if (!q) return list.slice(0, 5);
        return list.filter(function(t) { return String(t).toLowerCase().indexOf(q) !== -1; }).slice(0, 5);
    }

    function productsSearchBaseUrl() {
        var productsUrl = (searchForm && searchForm.getAttribute('action')) ? searchForm.getAttribute('action') : (window.location.origin + '/products');
        if (productsUrl.indexOf('?') !== -1) productsUrl = productsUrl.split('?')[0];
        return productsUrl;
    }

    function fetchTrendingKeywords(done) {
        if (trendingKeywordsCache !== null) {
            done(trendingKeywordsCache);
            return;
        }
        if (trendingLoadPromise) {
            trendingLoadPromise.then(function() { done(trendingKeywordsCache); });
            return;
        }
        var url = searchForm && searchForm.getAttribute('data-trending-url');
        if (!url) {
            trendingKeywordsCache = [];
            done(trendingKeywordsCache);
            return;
        }
        trendingLoadPromise = fetch(url, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin'
        })
            .then(function(r) { return r.ok ? r.json() : { success: false, keywords: [] }; })
            .then(function(data) {
                trendingKeywordsCache = (data && data.success && Array.isArray(data.keywords)) ? data.keywords : [];
            })
            .catch(function() { trendingKeywordsCache = []; })
            .finally(function() { trendingLoadPromise = null; });
        trendingLoadPromise.then(function() { done(trendingKeywordsCache); });
    }

    function renderTrendingBlock(keywords) {
        if (!keywords || !keywords.length) return '';
        var productsUrl = productsSearchBaseUrl();
        var part = '<div class="glass-search-trending-block mt-2 pt-2" style="border-top:1px solid rgba(15,23,42,0.08);"><div class="glass-search-recent-head px-2 py-1 small text-muted">Trending</div><div class="d-flex flex-wrap gap-1 px-1 pb-1">';
        keywords.forEach(function(kw) {
            part += '<a href="' + productsUrl + '?search=' + encodeURIComponent(kw) + '" class="glass-search-trending-chip text-decoration-none" style="font-size:12px;padding:4px 10px;border-radius:999px;background:rgba(16,185,129,0.12);color:#047857;font-weight:600;">' + escapeHtml(kw) + '</a>';
        });
        part += '</div></div>';
        return part;
    }

    function finalizeSearchPanel(html) {
        var resultsContainer = document.querySelector('#searchResults');
        if (!resultsContainer) return;
        fetchTrendingKeywords(function(keywords) {
            resultsContainer.innerHTML = html + renderTrendingBlock(keywords);
            resultsContainer.classList.add('active');
        });
    }

    function hideSearchResults() {
        var resultsContainer = document.querySelector('#searchResults');
        if (resultsContainer) resultsContainer.classList.remove('active');
        searchResultsFocusedIndex = -1;
    }

    function showDiscoveryPanel(query) {
        var resultsContainer = document.querySelector('#searchResults');
        if (!resultsContainer || !searchForm) return;
        var matches = getRecentMatches(query);
        var productsUrl = searchForm.getAttribute('action') || (window.location.origin + '/products');
        if (productsUrl.indexOf('?') !== -1) productsUrl = productsUrl.split('?')[0];
        var html = '';
        if (matches.length) {
            html += '<div class="glass-search-recent-head px-2 py-1 small text-muted">Recent searches</div>';
            matches.forEach(function(term) {
                html += '<a href="' + productsUrl + '?search=' + encodeURIComponent(term) + '" class="glass-search-result-item glass-search-result-item--recent text-decoration-none d-block">' +
                    '<div class="glass-search-result-item-content">' +
                    '<div class="glass-search-result-item-title">' + iconSvg('search', 'me-2') + escapeHtml(term) + '</div>' +
                    '<div class="glass-search-result-item-meta"><span class="glass-search-result-item-type">Recent</span></div>' +
                    '</div></a>';
            });
        }
        fetchTrendingKeywords(function(keywords) {
            html += renderTrendingBlock(keywords);
            if (!html.trim()) {
                html = '<div class="glass-search-no-results small py-2">Type to search products, categories, brands…</div>';
            }
            resultsContainer.innerHTML = html;
            resultsContainer.classList.add('active');
        });
    }

    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            if (!searchInputField) return;
            saveRecentSearchTerm(searchInputField.value);
            hideSearchResults();
        });
    }

    if (searchInputField) {
        searchInputField.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                hideSearchResults();
                searchResultsFocusedIndex = -1;
                return;
            }
            var resultsContainer = document.querySelector('#searchResults');
            var items = resultsContainer && resultsContainer.classList.contains('active') ? resultsContainer.querySelectorAll('.glass-search-result-item') : [];
            if (e.key === 'Enter' && items.length > 0 && searchResultsFocusedIndex >= 0) {
                var link = items[searchResultsFocusedIndex];
                if (link.href) { e.preventDefault(); window.location.href = link.href; }
                return;
            }
            if (e.key === 'ArrowDown' && items.length > 0) {
                e.preventDefault();
                searchResultsFocusedIndex = searchResultsFocusedIndex < items.length - 1 ? searchResultsFocusedIndex + 1 : 0;
                items.forEach(function(el, i) { el.classList.toggle('glass-search-result-focused', i === searchResultsFocusedIndex); });
            }
            if (e.key === 'ArrowUp' && items.length > 0) {
                e.preventDefault();
                searchResultsFocusedIndex = searchResultsFocusedIndex <= 0 ? items.length - 1 : searchResultsFocusedIndex - 1;
                items.forEach(function(el, i) { el.classList.toggle('glass-search-result-focused', i === searchResultsFocusedIndex); });
            }
        });

        searchInputField.addEventListener('input', function() {
            var q = this.value.trim();
            clearTimeout(searchTimeout);
            if (!q) {
                hideSearchResults();
                return;
            }
            searchTimeout = setTimeout(function() { performUniversalSearch(q); }, SEARCH_DEBOUNCE_MS);
        });

        searchInputField.addEventListener('focus', function() {
            var q = this.value.trim();
            if (q.length > 0) {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() { performUniversalSearch(q); }, SEARCH_DEBOUNCE_MS);
            }
        });

        var resultsEl = document.querySelector('#searchResults');
        if (resultsEl) {
            resultsEl.addEventListener('click', function() {
                if (!searchInputField) return;
                saveRecentSearchTerm(searchInputField.value);
            });
        }

        document.addEventListener('click', function(e) {
            var r = document.querySelector('#searchResults');
            var zone = document.querySelector('.glass-search-zone');
            if (r && zone && !r.contains(e.target) && e.target !== searchInputField && !zone.contains(e.target)) hideSearchResults();
        });
    }

    function performUniversalSearch(query) {
        if (searchAbortController) searchAbortController.abort();
        searchAbortController = new AbortController();
        var url = (searchForm && searchForm.getAttribute('data-universal-url')) || (window.location.origin + '/search/universal');
        var resEl = document.querySelector('#searchResults');
        if (!resEl) return;
        
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ search: query }),
            signal: searchAbortController.signal
        })
        .then(function(r) { return r.ok ? r.json() : Promise.reject(r.status); })
        .then(function(data) {
            if (data.success) {
                displaySearchResults(data.results, query);
            }
            else if (resEl) { resEl.innerHTML = '<div class="glass-search-no-results">' + (data.message || 'No results') + '</div>'; resEl.classList.add('active'); }
        })
        .catch(function(err) {
            if (err && err.name === 'AbortError') return;
            if (resEl) { resEl.innerHTML = '<div class="glass-search-no-results">Search failed. Try again.</div>'; resEl.classList.add('active'); }
        });
    }

    // Display Search Results - products, categories, subcategories, brands, pages, did you mean
    function displaySearchResults(results, query) {
        const resultsContainer = document.querySelector('#searchResults');
        if (!resultsContainer) return;
        searchResultsFocusedIndex = -1;
        
        var hasAny = results && (
            (results.products && results.products.length > 0) ||
            (results.categories && results.categories.length > 0) ||
            (results.subcategories && results.subcategories.length > 0) ||
            (results.brands && results.brands.length > 0) ||
            (results.pages && results.pages.length > 0)
        );

        if (!hasAny && (!results || results.total === 0)) {
            var msg = (query && query.length > 0) ? 'No results for "' + escapeHtml(query) + '".' : 'Type to search everything...';
            finalizeSearchPanel('<div class="glass-search-no-results">' + msg + '</div>');
            return;
        }

        var html = '';

        // Products Section (Cards)
        if (results.products && results.products.length > 0) {
            html += '<div class="glass-search-result-group-title">Products</div>';
            results.products.forEach(function(item) {
                var priceText = item.price_text || item.price || '';
                html += '<a href="' + item.url + '" class="glass-search-card glass-search-result-item text-decoration-none">' +
                    '<img src="' + (item.image || '/assets/images/default.png') + '" class="glass-search-card-img" alt="" onerror="this.src=\'/assets/images/default.png\'">' +
                    '<div class="glass-search-card-info">' +
                    '<div class="glass-search-card-name">' + escapeHtml(item.name) + '</div>' +
                    '<div class="glass-search-card-meta">' + (item.brand ? '<span>' + escapeHtml(item.brand) + '</span> • ' : '') + '<span>' + (item.category || 'Product') + '</span></div>' +
                    (priceText ? '<div class="glass-search-card-price">' + escapeHtml(priceText) + '</div>' : '') +
                    '</div><div class="glass-search-tag">View</div></a>';
            });
        }

        // Categories & Brands (Simplified)
        if (results.categories && results.categories.length > 0) {
            html += '<div class="glass-search-result-group-title">Categories</div>';
            results.categories.forEach(function(item) {
                html += '<a href="' + item.url + '" class="glass-search-card glass-search-result-item text-decoration-none">' +
                    '<div class="glass-search-card-info">' +
                    '<div class="glass-search-card-name">' + escapeHtml(item.name) + '</div>' +
                    '</div><div class="glass-search-tag">Category</div></a>';
            });
        }

        // Brands Section
        if (results.brands && results.brands.length > 0) {
            html += '<div class="glass-search-result-group-title">Brands</div>';
            results.brands.forEach(function(item) {
                html += '<a href="' + item.url + '" class="glass-search-card glass-search-result-item text-decoration-none">' +
                    '<div class="glass-search-card-info">' +
                    '<div class="glass-search-card-name">' + escapeHtml(item.name) + '</div>' +
                    '</div><div class="glass-search-tag">Brand</div></a>';
            });
        }

        // Pages Section
        if (results.pages && results.pages.length > 0) {
            html += '<div class="glass-search-result-group-title">Pages</div>';
            results.pages.forEach(function(item) {
                var icon = item.type === 'route' ? 'link' : 'file';
                html += '<a href="' + item.url + '" class="glass-search-card glass-search-result-item text-decoration-none">' +
                    '<div class="glass-search-card-info">' +
                    '<div class="glass-search-card-name">' + escapeHtml(item.name) + '</div>' +
                    '</div><div class="glass-search-tag">Page</div></a>';
            });
        }

        finalizeSearchPanel(html);
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    // Perform Image Search - Enhanced & Fixed
    function performImageSearch(file, inputEl) {
        const formData = new FormData();
        formData.append('image', file);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.content || '');
        
        const resultsContainer = document.querySelector('#searchResults');
        const cameraBtnReset = document.querySelector('#cameraSearchBtn');
        const searchForm = document.querySelector('#universalSearchForm');
        const imageSearchUrl = (searchForm && searchForm.getAttribute('data-image-search-url')) || '/search/image';
        
        // Show loading message
        if (resultsContainer) {
            resultsContainer.innerHTML = '<div class="glass-search-no-results">' + iconSvg('spinner', 'me-2') + 'Processing image...</div>';
            resultsContainer.classList.add('active');
        }

        fetch(imageSearchUrl, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            if (cameraBtnReset) {
                cameraBtnReset.classList.remove('uploading');
                cameraBtnReset.innerHTML = iconSvg('scan');
            }
            
            if (data.success) {
                // If image search returns results, display them
                if (data.results && data.results.total > 0) {
                    displaySearchResults(data.results, '');
                } else {
                    // Show message about image search
                    if (resultsContainer) {
                        resultsContainer.innerHTML = '<div class="glass-search-no-results">Image uploaded successfully. Image recognition feature will be enhanced with API integration. For now, please use text search.</div>';
                        resultsContainer.classList.add('active');
                    }
                }
            } else {
                // Show error message
                if (resultsContainer) {
                    resultsContainer.innerHTML = '<div class="glass-search-no-results" style="color: #ff6b6b;">' + (data.message || 'Image search failed. Please try again.') + '</div>';
                    resultsContainer.classList.add('active');
                }
            }
            
            // Clear file input
            if (inputEl) {
                inputEl.value = '';
            }
        })
        .catch(error => {
            console.error('Image search error:', error);
            
            if (cameraBtnReset) {
                cameraBtnReset.classList.remove('uploading');
                cameraBtnReset.innerHTML = iconSvg('scan');
            }
            
            // Show error message
            if (resultsContainer) {
                resultsContainer.innerHTML = '<div class="glass-search-no-results" style="color: #ff6b6b;">Image upload failed. Please check your connection and try again.</div>';
                resultsContainer.classList.add('active');
            }
            
            // Clear file input
            if (inputEl) {
                inputEl.value = '';
            }
        });
    }

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href !== '#' && href.length > 1) {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    const headerHeight = glassHeader ? glassHeader.offsetHeight : 72;
                    const targetPosition = target.offsetTop - headerHeight;
                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
                }
            }
        });
    });

    // Close mobile menu on window resize (when switching to xl desktop)
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 1200) {
            closeSidebar();
        }
    });

    // Initialize on DOM ready
    document.addEventListener('DOMContentLoaded', function() {
        // Final Initialization
        setupHeaderScroll();
        setupThemeToggle();
        setupDropdownFallbacks();
        if (typeof initLiveEnvironment === 'function') initLiveEnvironment();
        
        // Show filter sidebar when search is performed
        const searchForm = document.querySelector('#universalSearchForm');
        if (searchForm) {
            searchForm.addEventListener('submit', function() {
                // Sidebar will be shown on products page after navigation
                // This is handled by the route check in frontend.blade.php
            });
        }
    });

})();
