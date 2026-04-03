/**
 * Site-Wide Filter Sidebar JavaScript
 * Professional Filter Functionality - Only Shows on Product Pages
 */

(function() {
    'use strict';

    const sidebar = document.getElementById('siteFilterSidebar');
    const toggleBtn = document.getElementById('siteFilterToggle');
    const closeBtn = document.getElementById('siteFilterSidebarClose');
    const overlay = document.getElementById('siteFilterOverlay');
    const applyBtn = document.getElementById('siteFilterApplyBtn');
    const resetBtn = document.getElementById('siteFilterResetBtn');
    const categoryCheckboxes = document.querySelectorAll('.site-filter-category');
    const brandCheckboxes = document.querySelectorAll('.site-filter-brand');
    const minPriceInput = document.getElementById('siteFilterMinPrice');
    const maxPriceInput = document.getElementById('siteFilterMaxPrice');
    const priceSlider = document.getElementById('siteFilterPriceSlider');

    // Initialize: Show sidebar only on product pages (desktop)
    if (sidebar) {
        // Check if we're on a product page
        const isProductPage = window.location.pathname.includes('/products') ||
                             window.location.pathname.includes('/category/') ||
                             window.location.pathname.includes('/brand/') ||
                             window.location.pathname.includes('/subcategory/') ||
                             window.location.search.includes('search') ||
                             window.location.search.includes('categories') ||
                             window.location.search.includes('brands') ||
                             window.location.search.includes('min_price') ||
                             window.location.search.includes('max_price');
        
        if (isProductPage && window.innerWidth > 1199) {
            // Show sidebar on desktop product pages
            sidebar.style.display = 'block';
            sidebar.classList.add('active');
            sidebar.style.opacity = '1';
            sidebar.style.transform = 'translateX(0)';
            // Adjust main content margin
            const mainContent = document.querySelector('.main-content-with-filter');
            if (mainContent) {
                mainContent.style.marginLeft = '280px'; // Updated for smaller sidebar
            }
        } else if (isProductPage && window.innerWidth <= 1199) {
            // On mobile/tablet, sidebar exists but hidden (transform)
            sidebar.style.display = 'block';
            sidebar.classList.remove('active');
        } else {
            // Not a product page - hide sidebar completely
            sidebar.style.display = 'none';
            if (toggleBtn) toggleBtn.style.display = 'none';
            // Ensure no margin on main content
            const mainContent = document.querySelector('.main-content-with-filter');
            if (mainContent) {
                mainContent.style.marginLeft = '0';
            }
        }
    }

    // Toggle Sidebar (Mobile/Tablet)
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            if (sidebar && sidebar.classList.contains('active')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });
    }

    // Close Sidebar - FIXED: Properly hide sidebar
    function closeSidebar() {
        if (sidebar) {
            // Remove active class
            sidebar.classList.remove('active');
            
            // Hide sidebar completely
            sidebar.style.display = 'none';
            sidebar.style.opacity = '0';
            sidebar.style.transform = 'translateX(-100%)';
            
            // Remove overlay
            if (overlay) {
                overlay.classList.remove('active');
                overlay.style.display = 'none';
            }
            
            // Restore body scroll
            document.body.style.overflow = '';
            
            // On desktop, remove margin from main content for full screen
            const mainContent = document.querySelector('.main-content-with-filter');
            if (mainContent && window.innerWidth > 1199) {
                mainContent.style.marginLeft = '0';
            }
            
            // Dispatch custom event for other scripts
            window.dispatchEvent(new CustomEvent('filterSidebarClosed'));
        }
    }
    
    // Open Sidebar (for toggle button or when needed)
    function openSidebar() {
        if (sidebar) {
            // Show sidebar
            sidebar.style.display = 'block';
            sidebar.classList.add('active');
            sidebar.style.opacity = '1';
            sidebar.style.transform = 'translateX(0)';
            
            // Show overlay
            if (overlay) {
                overlay.classList.add('active');
                overlay.style.display = 'block';
            }
            
            // Lock body scroll
            document.body.style.overflow = 'hidden';
            
            // On desktop, add margin to main content
            const mainContent = document.querySelector('.main-content-with-filter');
            if (mainContent && window.innerWidth > 1199) {
                mainContent.style.marginLeft = '280px'; // Updated for smaller sidebar
            }
            
            // Dispatch custom event
            window.dispatchEvent(new CustomEvent('filterSidebarOpened'));
        }
    }

    // Close button - FIXED: Ensure it works properly
    if (closeBtn) {
        closeBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            closeSidebar();
        });
        
        // Also handle touch events for mobile
        closeBtn.addEventListener('touchend', function(e) {
            e.preventDefault();
            e.stopPropagation();
            closeSidebar();
        });
    }

    if (overlay) {
        overlay.addEventListener('click', closeSidebar);
    }

    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && sidebar.classList.contains('active')) {
            closeSidebar();
        }
    });

    // Category Checkbox Logic - Enhanced
    const categoryAll = document.getElementById('site-filter-category-all');
    if (categoryAll) {
        categoryAll.addEventListener('change', function() {
            if (this.checked) {
                categoryCheckboxes.forEach(cb => {
                    if (cb !== categoryAll) {
                        cb.checked = false;
                    }
                });
                // Also uncheck all subcategories
                document.querySelectorAll('.site-filter-subcategory').forEach(scb => {
                    scb.checked = false;
                });
            }
        });
    }

    categoryCheckboxes.forEach(cb => {
        if (cb !== categoryAll) {
            cb.addEventListener('change', function() {
                if (this.checked) {
                    categoryAll.checked = false;
                }
            });
        }
    });
    
    // Subcategory Toggle (Expand/Collapse)
    document.querySelectorAll('.site-filter-category-toggle').forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const categoryId = this.getAttribute('data-category');
            const subcategoriesDiv = document.getElementById('subcategories-' + categoryId);
            if (subcategoriesDiv) {
                if (subcategoriesDiv.style.display === 'none') {
                    subcategoriesDiv.style.display = 'block';
                    this.classList.remove('la-chevron-down');
                    this.classList.add('la-chevron-up');
                } else {
                    subcategoriesDiv.style.display = 'none';
                    this.classList.remove('la-chevron-up');
                    this.classList.add('la-chevron-down');
                }
            }
        });
    });

    // Brand Checkbox Logic
    const brandAll = document.getElementById('site-filter-brand-all');
    if (brandAll) {
        brandAll.addEventListener('change', function() {
            if (this.checked) {
                brandCheckboxes.forEach(cb => {
                    if (cb !== brandAll) {
                        cb.checked = false;
                    }
                });
            }
        });
    }

    brandCheckboxes.forEach(cb => {
        if (cb !== brandAll) {
            cb.addEventListener('change', function() {
                if (this.checked) {
                    brandAll.checked = false;
                }
            });
        }
    });

    // Price Slider Update Inputs - Enhanced with Display
    const priceDisplay = document.getElementById('siteFilterPriceDisplay');
    
    function updatePriceDisplay() {
        if (priceDisplay && minPriceInput && maxPriceInput) {
            const minVal = parseFloat(minPriceInput.value) || parseFloat(minPriceInput.min) || 0;
            const maxVal = parseFloat(maxPriceInput.value) || parseFloat(maxPriceInput.max) || 10000;
            // Format numbers with 2 decimal places
            const formattedMin = minVal.toFixed(2);
            const formattedMax = maxVal.toFixed(2);
            priceDisplay.textContent = formattedMin + ' - ' + formattedMax;
        }
    }
    
    if (priceSlider && minPriceInput && maxPriceInput) {
        // Initialize display
        updatePriceDisplay();
        
        priceSlider.addEventListener('input', function() {
            const maxVal = this.value;
            maxPriceInput.value = maxVal;
            updatePriceDisplay();
        });

        minPriceInput.addEventListener('input', function() {
            const minVal = parseFloat(this.value) || 0;
            const maxVal = parseFloat(maxPriceInput.value) || parseFloat(priceSlider.max);
            if (minVal > maxVal) {
                this.value = maxVal;
            }
            updatePriceDisplay();
        });

        maxPriceInput.addEventListener('input', function() {
            const maxVal = parseFloat(this.value) || parseFloat(priceSlider.max);
            const minVal = parseFloat(minPriceInput.value) || 0;
            if (maxVal < minVal) {
                this.value = minVal;
            }
            if (priceSlider) {
                priceSlider.value = maxVal;
            }
            updatePriceDisplay();
        });
    }

    // Apply Filters - Enhanced with Subcategories - 100% Functional
    if (applyBtn) {
        applyBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const params = new URLSearchParams();
            
            // Categories - Ensure it works 100%
            const selectedCategories = Array.from(categoryCheckboxes)
                .filter(cb => cb.checked && cb.value && cb.value !== '')
                .map(cb => cb.value);
            if (selectedCategories.length > 0) {
                selectedCategories.forEach(cat => {
                    params.append('categories[]', cat);
                });
            }
            
            // Subcategories - Ensure it works 100%
            const selectedSubcategories = Array.from(document.querySelectorAll('.site-filter-subcategory'))
                .filter(cb => cb.checked && cb.value && cb.value !== '')
                .map(cb => cb.value);
            if (selectedSubcategories.length > 0) {
                selectedSubcategories.forEach(subcat => {
                    params.append('subcategories[]', subcat);
                });
            }
            
            // Brands - Ensure it works 100%
            const selectedBrands = Array.from(brandCheckboxes)
                .filter(cb => cb.checked && cb.value && cb.value !== '')
                .map(cb => cb.value);
            if (selectedBrands.length > 0) {
                selectedBrands.forEach(brand => {
                    params.append('brands[]', brand);
                });
            }
            
            // Price - Ensure it works 100%
            if (minPriceInput && minPriceInput.value && parseFloat(minPriceInput.value) > 0) {
                params.append('min_price', parseFloat(minPriceInput.value));
            }
            if (maxPriceInput && maxPriceInput.value && parseFloat(maxPriceInput.value) > 0) {
                params.append('max_price', parseFloat(maxPriceInput.value));
            }
            
            // Navigate to products page with filters
            const filterUrl = `{{ route('products') }}${params.toString() ? '?' + params.toString() : ''}`;
            window.location.href = filterUrl;
        });
    }

    // Reset Filters - Enhanced
    if (resetBtn) {
        resetBtn.addEventListener('click', function() {
            // Reset checkboxes
            if (categoryAll) {
                categoryAll.checked = true;
                categoryCheckboxes.forEach(cb => {
                    if (cb !== categoryAll) {
                        cb.checked = false;
                    }
                });
            }
            
            // Reset subcategories
            document.querySelectorAll('.site-filter-subcategory').forEach(scb => {
                scb.checked = false;
            });
            
            // Collapse all subcategory groups
            document.querySelectorAll('.site-filter-subcategories').forEach(div => {
                div.style.display = 'none';
            });
            document.querySelectorAll('.site-filter-category-toggle').forEach(toggle => {
                toggle.classList.remove('la-chevron-up');
                toggle.classList.add('la-chevron-down');
            });
            
            if (brandAll) {
                brandAll.checked = true;
                brandCheckboxes.forEach(cb => {
                    if (cb !== brandAll) {
                        cb.checked = false;
                    }
                });
            }
            
            // Reset price inputs
            if (minPriceInput) {
                minPriceInput.value = '';
            }
            if (maxPriceInput) {
                maxPriceInput.value = '';
            }
            if (priceSlider) {
                priceSlider.value = priceSlider.max;
            }
            updatePriceDisplay();
            
            // Navigate to products page without filters
            window.location.href = `{{ route('products') }}`;
        });
    }

    // Auto-close sidebar on desktop when clicking outside (if needed)
    if (window.innerWidth > 1199) {
        document.addEventListener('click', function(e) {
            if (sidebar && !sidebar.contains(e.target) && !toggleBtn.contains(e.target)) {
                // Sidebar stays open on desktop
            }
        });
    }

    // Handle window resize
    window.addEventListener('resize', function() {
        if (!sidebar) return;
        
        const isProductPage = window.location.pathname.includes('/products') ||
                             window.location.pathname.includes('/category/') ||
                             window.location.pathname.includes('/brand/') ||
                             window.location.pathname.includes('/subcategory/') ||
                             window.location.search.includes('search') ||
                             window.location.search.includes('categories') ||
                             window.location.search.includes('brands') ||
                             window.location.search.includes('min_price') ||
                             window.location.search.includes('max_price');
        
        if (window.innerWidth > 1199 && isProductPage) {
            // Desktop: Show sidebar if on product page
            sidebar.style.display = 'block';
            if (!sidebar.classList.contains('active')) {
                sidebar.classList.add('active');
            }
            sidebar.style.opacity = '1';
            sidebar.style.transform = 'translateX(0)';
            overlay.classList.remove('active');
            // Adjust main content margin
            const mainContent = document.querySelector('.main-content-with-filter');
            if (mainContent && sidebar.classList.contains('active')) {
                mainContent.style.marginLeft = '280px'; // Updated for smaller sidebar
            }
        } else if (window.innerWidth <= 1199 && isProductPage) {
            // Mobile/Tablet: Sidebar exists but hidden by transform
            sidebar.style.display = 'block';
            if (!sidebar.classList.contains('active')) {
                sidebar.classList.remove('active');
            }
            overlay.classList.remove('active');
        } else {
            // Not a product page - hide completely
            sidebar.style.display = 'none';
            if (toggleBtn) toggleBtn.style.display = 'none';
            // Ensure no margin on main content
            const mainContent = document.querySelector('.main-content-with-filter');
            if (mainContent) {
                mainContent.style.marginLeft = '0';
            }
        }
    });

    // Show sidebar when search/filter is triggered
    document.addEventListener('DOMContentLoaded', function() {
        if (!sidebar) return;
        
        // Check URL parameters on page load
        const urlParams = new URLSearchParams(window.location.search);
        const hasFilters = urlParams.has('search') || 
                          urlParams.has('categories') || 
                          urlParams.has('brands') || 
                          urlParams.has('min_price') || 
                          urlParams.has('max_price');
        
        const isProductPage = window.location.pathname.includes('/products') ||
                             window.location.pathname.includes('/category/') ||
                             window.location.pathname.includes('/brand/') ||
                             window.location.pathname.includes('/subcategory/');
        
        if ((hasFilters || isProductPage) && window.innerWidth > 1199) {
            sidebar.style.display = 'block';
            sidebar.classList.add('active');
            sidebar.style.opacity = '1';
            sidebar.style.transform = 'translateX(0)';
            // Adjust main content margin
            const mainContent = document.querySelector('.main-content-with-filter');
            if (mainContent) {
                mainContent.style.marginLeft = '280px'; // Updated for smaller sidebar
            }
        } else if ((hasFilters || isProductPage) && window.innerWidth <= 1199) {
            sidebar.style.display = 'block';
            // Keep hidden on mobile until toggle clicked
        } else {
            // Not a product page - hide completely
            sidebar.style.display = 'none';
            if (toggleBtn) toggleBtn.style.display = 'none';
        }
    });

})();
