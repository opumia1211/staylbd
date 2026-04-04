/**
 * Glass Header JavaScript
 * Handles mobile menu, scroll effects, and interactions
 * On /user/*: Hamburger opens/closes USER PROFILE MENU (OVERVIEW, SHOPPING, ACCOUNT).
 * On other pages: Hamburger opens/closes glass site menu (Home, Products, Categories).
 */

(function() {
    'use strict';

    function iconSvg(name, extraClass) {
        var cls = 'ui-icon ui-icon--runtime' + (extraClass ? (' ' + extraClass) : '');
        var icons = {
            spinner: '<svg class="' + cls + ' ui-icon--spin" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2v4m0 12v4m8-8h-4M6 12H2m15.07-7.07l-2.83 2.83M7.76 16.24l-2.83 2.83m0-11.31l2.83 2.83m7.07 7.07l2.83 2.83"></path></svg>',
            search: '<svg class="' + cls + '" viewBox="0 0 24 24" fill="currentColor"><path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"></path></svg>',
            image: '<svg class="' + cls + '" viewBox="0 0 24 24" fill="currentColor"><path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"></path></svg>',
            scan: '<svg class="' + cls + '" viewBox="0 0 24 24" fill="currentColor"><path d="M12 8a4 4 0 1 0 0 8 4 4 0 0 0 0-8zm0 6a2 2 0 1 1 0-4 2 2 0 0 1 0 4z"></path><path d="M7 2L5.17 4H2a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h20a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-3.17L17 2H7zM5.83 6h12.34l1.83 2H22v10H2V8h4.17l-.34-2z"></path></svg>',
            folder: '<svg class="' + cls + '" viewBox="0 0 24 24" fill="currentColor"><path d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"></path></svg>',
            folderOpen: '<svg class="' + cls + '" viewBox="0 0 24 24" fill="currentColor"><path d="M20 6h-8l-2-2H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2z"></path></svg>',
            tag: '<svg class="' + cls + '" viewBox="0 0 24 24" fill="currentColor"><path d="M21.41 11.58l-9-9C12.05 2.22 11.55 2 11 2H4c-1.1 0-2 .9-2 2v7c0 .55.22 1.05.59 1.42l9 9c.36.36.86.58 1.41.58.55 0 1.05-.22 1.41-.59l7-7c.37-.36.59-.86.59-1.41 0-.55-.23-1.06-.59-1.42zM5.5 7C4.67 7 4 6.33 4 5.5S4.67 4 5.5 4 7 4.67 7 5.5 6.33 7 5.5 7z"></path></svg>',
            link: '<svg class="' + cls + '" viewBox="0 0 24 24" fill="currentColor"><path d="M3.9 12c0-1.71 1.39-3.1 3.1-3.1h4V7H7c-2.76 0-5 2.24-5 5s2.24 5 5 5h4v-1.9H7c-1.71 0-3.1-1.39-3.1-3.1zM8 13h8v-2H8v2zm9-6h-4v1.9h4c1.71 0 3.1 1.39 3.1 3.1s-1.39 3.1-3.1 3.1h-4V17h4c2.76 0 5-2.24 5-5s-2.24-5-5-5z"></path></svg>',
            file: '<svg class="' + cls + '" viewBox="0 0 24 24" fill="currentColor"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"></path></svg>'
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
                    window.location.href = `{{ route('products') }}?search=${encodeURIComponent(query)}`;
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
            window.location.href = `{{ route('products') }}?${params.toString()}`;
        });
    }

    // Header Scroll Effect
    const header = document.querySelector('.glass-header');
    let lastScroll = 0;

    if (header) {
        window.addEventListener('scroll', function() {
            const currentScroll = window.pageYOffset;

            if (currentScroll > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }

            // Hide/show header on scroll (optional - can be removed if not needed)
            if (currentScroll > lastScroll && currentScroll > 100) {
                // Scrolling down - hide header
                // header.style.transform = 'translateY(-100%)';
            } else {
                // Scrolling up - show header
                header.style.transform = 'translateY(0)';
            }

            lastScroll = currentScroll;
        });
    }

    // Mobile search remains permanently integrated in header.
    const searchInputField = document.querySelector('#universalSearchInput');
    const searchWrapper = document.querySelector('.glass-search-wrapper');
    const searchCenter = document.querySelector('.glass-header-center');
    const glassHeader = document.querySelector('.glass-header');
    const glassHeaderNav = document.querySelector('.glass-header-nav');
    const glassHeaderRight = document.querySelector('.glass-header-right');
    function setupMobileSearch() {
        if (!searchInputField || !searchWrapper) return;
        searchInputField.addEventListener('focus', function() {
            searchWrapper.style.transform = 'scale(1.01)';
        });
        searchInputField.addEventListener('blur', function() {
            setTimeout(function() {
                searchWrapper.style.transform = 'scale(1)';
            }, 120);
        });
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', setupMobileSearch);
    else setupMobileSearch();

    // Professional Voice Search
    const voiceBtn = document.querySelector('#voiceSearchBtn');
    const searchResultsEl = document.querySelector('#searchResults');
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
                    voiceBtn.classList.add('listening');
                    if (searchInputField) searchInputField.placeholder = listeningPlaceholder;
                    showVoiceSearchAlert('Microphone active. You can speak now.', 'success');
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
                        isListening = false;
                        voiceBtn.classList.remove('listening');
                        restoreSearchPlaceholder();
                        voiceBtn.setAttribute('aria-pressed', 'false');
                        return;
                    }
                    
                    if (voiceSearchNeedsHttps()) {
                        voiceBtn.setAttribute('aria-pressed', 'false');
                        showVoiceSearchAlert('Voice search needs a secure site (HTTPS) or localhost.', 'error');
                        return;
                    }

                    voiceBtn.setAttribute('aria-pressed', 'true');
                    try {
                        recognition.start();
                    } catch (err) {
                        console.error('Failed to start recognition', err);
                        isListening = false;
                        voiceBtn.classList.remove('listening');
                        restoreSearchPlaceholder();
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

    // Image search via camera / gallery (button outside search card)
    const cameraBtn = document.querySelector('#cameraSearchBtn');
    const imageInput = document.querySelector('#imageSearchInput');

    if (cameraBtn && imageInput) {
        cameraBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            try {
                imageInput.click();
            } catch (err) {
                console.error('Error opening file picker:', err);
                alert('Unable to open image picker. Please try again.');
            }
        });

        imageInput.addEventListener('change', function(e) {
            var file = e.target.files && e.target.files[0];
            if (file) {
                var validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                if (validTypes.indexOf(file.type) === -1) {
                    alert('Please select a valid image file (JPEG, PNG, GIF, or WebP).');
                    this.value = '';
                    return;
                }
                var maxSize = 5 * 1024 * 1024;
                if (file.size > maxSize) {
                    alert('Image size should be less than 5MB.');
                    this.value = '';
                    return;
                }
                cameraBtn.classList.add('uploading');
                cameraBtn.innerHTML = iconSvg('spinner');
                performImageSearch(file);
            }
        });
    }

    // Universal Search - AbortController to cancel previous request (light optimization)
    let searchAbortController = null;
    let searchTimeout = null;
    let searchResultsFocusedIndex = -1;
    const searchForm = document.querySelector('#universalSearchForm');
    const SEARCH_CACHE_KEY = 'staylbd_recent_search_terms_v1';
    const SEARCH_CACHE_LIMIT = 8;

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

    function showRecentSearchSuggestions(query) {
        var resultsContainer = document.querySelector('#searchResults');
        if (!resultsContainer || !searchForm) return false;
        var matches = getRecentMatches(query);
        if (!matches.length) return false;
        var productsUrl = searchForm.getAttribute('action') || (window.location.origin + '/products');
        var html = '<div class="glass-search-recent-head px-2 py-1 small text-muted">Recent Searches</div>';
        matches.forEach(function(term) {
            html += '<a href="' + productsUrl + '?search=' + encodeURIComponent(term) + '" class="glass-search-result-item glass-search-result-item--recent text-decoration-none d-block">' +
                '<div class="glass-search-result-item-content">' +
                '<div class="glass-search-result-item-title">' + iconSvg('search', 'me-2') + escapeHtml(term) + '</div>' +
                '<div class="glass-search-result-item-meta"><span class="glass-search-result-item-type">Recent</span></div>' +
                '</div></a>';
        });
        resultsContainer.innerHTML = html;
        resultsContainer.classList.add('active');
        return true;
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
            if (!q) { showRecentSearchSuggestions(''); return; }
            showRecentSearchSuggestions(q);
            searchTimeout = setTimeout(function() { performUniversalSearch(q); }, 120);
        });

        searchInputField.addEventListener('focus', function() {
            var q = this.value.trim();
            if (!q) { showRecentSearchSuggestions(''); return; }
            showRecentSearchSuggestions(q);
            searchTimeout = setTimeout(function() { performUniversalSearch(q); }, 60);
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
        var url = (searchInputField && searchInputField.getAttribute('data-search-url')) || '/search/universal';
        var resEl = document.querySelector('#searchResults');
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
                if (query && query.trim()) saveRecentSearchTerm(query);
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
        if (window.innerWidth <= 991 && searchWrapper) searchWrapper.style.transform = 'scale(1.01)';

        var hasAny = results && (
            (results.products && results.products.length > 0) ||
            (results.categories && results.categories.length > 0) ||
            (results.subcategories && results.subcategories.length > 0) ||
            (results.brands && results.brands.length > 0) ||
            (results.pages && results.pages.length > 0)
        );

        if (!hasAny && (!results || results.total === 0)) {
            var msg = (query && query.length > 0) ? 'No results for "' + escapeHtml(query) + '". Try different keywords or check spelling.' : 'Type to search products, categories, brands, pages...';
            var didYouMean = (results && results.did_you_mean && results.did_you_mean.length > 0);
            if (didYouMean) {
                var productsUrl = (searchForm && searchForm.action) ? searchForm.action : (window.location.origin + '/products');
                if (productsUrl.indexOf('?') !== -1) productsUrl = productsUrl.split('?')[0];
                msg += '<div class="glass-search-did-you-mean mt-2 pt-2" style="border-top:1px solid rgba(0,0,0,0.06);"><strong>Did you mean?</strong> ';
                results.did_you_mean.forEach(function(s) {
                    msg += '<a href="' + productsUrl + '?search=' + encodeURIComponent(s) + '" class="d-inline-block me-2 mt-1" style="color:var(--theme-color,#4fc4f7);">' + escapeHtml(s) + '</a>';
                });
                msg += '</div>';
            }
            resultsContainer.innerHTML = '<div class="glass-search-no-results">' + msg + '</div>';
            resultsContainer.classList.add('active');
            return;
        }

        var html = '';

        if (results.products && results.products.length > 0) {
            results.products.forEach(function(item) {
                var priceText = '';
                if (typeof item.price_text === 'string' && item.price_text.trim() !== '') {
                    priceText = item.price_text.trim();
                } else if (typeof item.price_formatted === 'string' && item.price_formatted.trim() !== '') {
                    priceText = item.price_formatted.trim();
                } else if (typeof item.price !== 'undefined' && item.price !== null && String(item.price).trim() !== '') {
                    priceText = String(item.price).trim();
                }
                html += '<a href="' + item.url + '" class="glass-search-result-item text-decoration-none d-block">' +
                    '<img src="' + (item.image || '/assets/images/default.png') + '" alt="" onerror="this.src=\'/assets/images/default.png\'">' +
                    '<div class="glass-search-result-item-content">' +
                    '<div class="glass-search-result-item-title">' + escapeHtml(item.name) + '</div>' +
                    '<div class="glass-search-result-item-meta">' + (item.category ? escapeHtml(item.category) + ' • ' : '') + (item.brand ? escapeHtml(item.brand) + ' • ' : '') + '<span class="glass-search-result-item-type">Product</span></div>' +
                    (priceText ? '<div class="glass-search-result-item-price" style="font-weight:700;color:#0f766e;margin-top:2px;">' + escapeHtml(priceText) + '</div>' : '') +
                    '</div></a>';
            });
        }
        if (results.categories && results.categories.length > 0) {
            results.categories.forEach(function(item) {
                html += '<a href="' + item.url + '" class="glass-search-result-item text-decoration-none d-block"><div class="glass-search-result-item-content">' +
                    '<div class="glass-search-result-item-title">' + iconSvg('folder', 'me-2') + escapeHtml(item.name) + ' <span class="glass-search-result-item-type">Category</span></div></div></a>';
            });
        }
        if (results.subcategories && results.subcategories.length > 0) {
            results.subcategories.forEach(function(item) {
                html += '<a href="' + item.url + '" class="glass-search-result-item text-decoration-none d-block"><div class="glass-search-result-item-content">' +
                    '<div class="glass-search-result-item-title">' + iconSvg('folderOpen', 'me-2') + escapeHtml(item.name) + (item.category ? ' <span class="glass-search-result-item-meta">(' + escapeHtml(item.category) + ')</span>' : '') + ' <span class="glass-search-result-item-type">Subcategory</span></div></div></a>';
            });
        }
        if (results.brands && results.brands.length > 0) {
            results.brands.forEach(function(item) {
                html += '<a href="' + item.url + '" class="glass-search-result-item text-decoration-none d-block"><div class="glass-search-result-item-content">' +
                    '<div class="glass-search-result-item-title">' + iconSvg('tag', 'me-2') + escapeHtml(item.name) + ' <span class="glass-search-result-item-type">Brand</span></div></div></a>';
            });
        }
        if (results.pages && results.pages.length > 0) {
            results.pages.forEach(function(item) {
                var icon = item.type === 'route' ? 'link' : 'file';
                var typeLabel = item.type === 'route' ? 'Page' : (item.type || 'Page');
                var desc = item.description ? '<div class="glass-search-result-item-meta" style="font-size:11px;color:#888;">' + escapeHtml(item.description) + '</div>' : '';
                html += '<a href="' + item.url + '" class="glass-search-result-item text-decoration-none d-block"><div class="glass-search-result-item-content">' +
                    '<div class="glass-search-result-item-title">' + iconSvg(icon, 'me-2') + escapeHtml(item.name) + ' <span class="glass-search-result-item-type">' + typeLabel + '</span></div>' + desc + '</div></a>';
            });
        }

        resultsContainer.innerHTML = html;
        resultsContainer.classList.add('active');
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    // Hide Search Results
    function hideSearchResults() {
        const resultsContainer = document.querySelector('#searchResults');
        if (resultsContainer) {
            resultsContainer.classList.remove('active');
        }
    }

    // Perform Image Search - Enhanced & Fixed
    function performImageSearch(file) {
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
            if (imageInput) {
                imageInput.value = '';
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
            if (imageInput) {
                imageInput.value = '';
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
                    const headerHeight = header ? header.offsetHeight : 60;
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
        // Initialization (no console.log in production to avoid noise/errors)
        
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
