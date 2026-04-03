'use strict';

/* Sidebar menu: native scroll only, do NOT auto-scroll on load or navigation */
$(function(){
  var $wrapper = $('#sidebar__menuWrapper');
  if ($wrapper.length) {
    $wrapper.css({ height: 'calc(100vh - 86.75px)', overflowY: 'auto', overflowX: 'hidden' });
  }
});

$(function(){
  $('.dropdown-menu__body').slimScroll({
    height: '270px'
  });
});

// modal-dialog-scrollable
$(function(){
  $('.modal-dialog-scrollable .modal-body').slimScroll({
    height: '100%'
  });
});

// activity-list 
$(function(){
  $('.activity-list').slimScroll({
    height: '385px'
  });
});

// recent ticket list 
$(function(){
  $('.recent-ticket-list__body').slimScroll({
    height: '295px'
  });
});

// Enhanced Sidebar Search - Comprehensive Search with AdminSearchController
var searchTimeout;
$('.navbar-search-field').on('input', function () {
    var searchInput = $(this);
    var search = searchInput.val().trim();
    var search_result_pane = $('.search-list');
    
    // Clear previous timeout
    clearTimeout(searchTimeout);
    
    // Clear results immediately
    $(search_result_pane).html('');
    
    if (search.length == 0) {
        $('.search-list').addClass('d-none');
        return;
    }
    
    $('.search-list').removeClass('d-none');
    
    // Show loading state
    $(search_result_pane).html('<li class="text-muted text-center py-3"><i class="las la-spinner la-spin"></i> Searching...</li>');
    
    // Debounce search - wait 300ms after user stops typing
    searchTimeout = setTimeout(function() {
        // First, search menu items locally (instant)
        var menuMatch = $('.sidebar__menu-wrapper .nav-link').filter(function (idx, elem) {
            var text = $(elem).text().trim().toLowerCase();
            return text.indexOf(search.toLowerCase()) >= 0 ? elem : null;
        });
        
        var hasMenuResults = menuMatch.length > 0;
        var allResults = [];
        
        // Add menu results
        if (hasMenuResults) {
            menuMatch.each(function (idx, elem) {
                var parent = $(elem).parents('.sidebar-menu-item.sidebar-dropdown').find('.menu-title').first().text();
                if (!parent) {
                    parent = 'Main Menu';
                }
                var item_url = $(elem).attr('href') || $(elem).data('default-url');
                var item_text = $(elem).text().replace(/(\d+)/g, '').trim();
                
                allResults.push({
                    category: parent,
                    title: item_text,
                    url: item_url,
                    type: 'menu'
                });
            });
        }
        
        // Then search comprehensive via API (1+ char for faster find e.g. "offer" -> Offer Timers)
        if (search.length >= 1) {
            var searchUrl = (typeof window.adminSearchUrl !== 'undefined' && window.adminSearchUrl) ? window.adminSearchUrl : (window.location.origin + '/admin/search');
            $.ajax({
                url: searchUrl,
                method: 'GET',
                data: { q: search },
                success: function(response) {
                    // Handle AdminSearchController response format
                    var apiResults = [];
                    
                    if (response && response.success && response.results) {
                        // Response format: { success: true, results: {category: [items]}, total: N }
                        // Results are grouped by category, need to flatten
                        if (typeof response.results === 'object' && !Array.isArray(response.results)) {
                            // Grouped format - flatten it
                            Object.keys(response.results).forEach(function(category) {
                                var categoryItems = response.results[category];
                                if (Array.isArray(categoryItems)) {
                                    categoryItems.forEach(function(item) {
                                        if (item && item.url) {
                                            apiResults.push({
                                                category: category,
                                                title: item.title || 'Untitled',
                                                url: item.url,
                                                type: item.type || 'other',
                                                icon: item.icon || 'las la-circle'
                                            });
                                        }
                                    });
                                }
                            });
                        } else if (Array.isArray(response.results)) {
                            // Flat array format
                            apiResults = response.results;
                        }
                    } else if (response && Array.isArray(response)) {
                        // Direct array format
                        apiResults = response;
                    } else if (response && response.data) {
                        // Data wrapper format
                        apiResults = Array.isArray(response.data) ? response.data : [];
                    }
                    
                    // Remove duplicates (check by URL)
                    var existingUrls = {};
                    allResults.forEach(function(item) {
                        if (item.url) {
                            existingUrls[item.url] = true;
                        }
                    });
                    
                    // Add API results
                    apiResults.forEach(function(item) {
                        if (item && item.url && !existingUrls[item.url]) {
                            allResults.push({
                                category: item.category || 'Other',
                                title: item.title || 'Untitled',
                                url: item.url,
                                type: item.type || 'other',
                                icon: item.icon || 'las la-circle'
                            });
                            existingUrls[item.url] = true;
                        }
                    });
                    
                    displaySearchResults(allResults, search_result_pane);
                },
                error: function() {
                    // If API fails, show menu results only
                    displaySearchResults(allResults, search_result_pane);
                }
            });
        } else {
            // For single character, show menu results only
            displaySearchResults(allResults, search_result_pane);
        }
    }, 300);
});

// Function to display search results
function displaySearchResults(results, container) {
    container.html('');
    
    if (results.length == 0) {
        container.append('<li class="text-muted text-center py-4"><i class="las la-search"></i> No results found. Try different keywords.</li>');
        return;
    }
    
    // Group by category
    var grouped = {};
    results.forEach(function(item) {
        var cat = item.category || 'Other';
        if (!grouped[cat]) {
            grouped[cat] = [];
        }
        grouped[cat].push(item);
    });
    
    // Display grouped results - Professional styling
    var categoryCount = 0;
    Object.keys(grouped).forEach(function(category) {
        var categoryItems = grouped[category];
        var isFirstInCategory = categoryCount === 0;
        
        categoryItems.forEach(function(item, index) {
            var icon = item.icon || 'las la-circle';
            var showCategory = isFirstInCategory && index === 0;
            
            container.append(`
                <li class="search-result-item" style="padding: 12px 20px; border-bottom: 1px solid rgba(0, 0, 0, 0.05);">
                    ${showCategory ? `<small class="d-block text-uppercase" style="color: #666666 !important; font-size: 11px !important; font-weight: 700 !important; margin-bottom: 6px !important; letter-spacing: 0.5px;">${category}</small>` : ''}
                    <a href="${item.url}" class="search-result-link d-block" style="color: #333333 !important; font-size: 14px !important; font-weight: 600 !important; text-decoration: none !important;">
                        <i class="${icon}" style="margin-right: 10px; color: #4634ff !important; font-size: 16px !important; vertical-align: middle;"></i>${item.title}
                    </a>
                </li>
            `);
        });
        
        categoryCount++;
    });
    
    // Limit to 20 results for performance
    if (container.find('li').length > 20) {
        container.find('li').slice(20).remove();
        container.append('<li class="text-muted text-center py-2"><small>Showing first 20 results. Refine your search for more.</small></li>');
    }
}


  $(function () {
    $('[data-bs-toggle="tooltip"]').tooltip()
  })

  // responsive sidebar expand js - Enhanced with proper event handling
  $(document).on('click', '.res-sidebar-open-btn', function(e){
    e.preventDefault();
    e.stopPropagation();
    $('.sidebar').addClass('open');
    $('body').addClass('sidebar-open');
  }); 

  $(document).on('click', '.res-sidebar-close-btn', function(e){
    e.preventDefault();
    e.stopPropagation();
    $('.sidebar').removeClass('open');
    $('body').removeClass('sidebar-open');
  }); 

/* Get the documentElement (<html>) to display the page in fullscreen */
let elem = document.documentElement;

$('.sidebar-dropdown > a').on('click', function () {
  if ($(this).parent().find('.sidebar-submenu').length) {
    if ($(this).parent().find('.sidebar-submenu').first().is(':visible')) {
      $(this).find('.side-menu__sub-icon').removeClass('transform rotate-180');
      $(this).removeClass('side-menu--open');
      // Remove slideUp animation - instant hide
      $(this).parent().find('.sidebar-submenu').first().hide().removeClass('sidebar-submenu__open');
    } else {
      $(this).find('.side-menu__sub-icon').addClass('transform rotate-180');
      $(this).addClass('side-menu--open');
      // Remove slideDown animation - instant show
      $(this).parent().find('.sidebar-submenu').first().show().addClass('sidebar-submenu__open');
    }
  }
});

// select-2 init
$('.select2-basic').select2();
$('.select2-multi-select').select2();
$(".select2-auto-tokenize").select2({
  tags: true,
  tokenSeparators: [',']
});


function proPicURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            var preview = $(input).parents('.thumb').find('.profilePicPreview');
            $(preview).css('background-image', 'url(' + e.target.result + ')');
            $(preview).addClass('has-image');
            $(preview).hide();
            $(preview).show(); // Remove fadeIn animation - instant display
        }
        reader.readAsDataURL(input.files[0]);
    }
}
$(".profilePicUpload").on('change', function () {
    proPicURL(this);
});

$(".remove-image").on('click', function () {
    $(this).parents(".profilePicPreview").css('background-image', 'none');
    $(this).parents(".profilePicPreview").removeClass('has-image');
    $(this).parents(".thumb").find('input[type=file]').val('');
});

$("form").on("change", ".file-upload-field", function(){
  $(this).parent(".file-upload-wrapper").attr("data-text",$(this).val().replace(/.*(\/|\\)/, '') );
});



var inputElements = $('input,select,textarea');

$.each(inputElements, function (index, element) {
    element = $(element);
    if (!element.hasClass('profilePicUpload') && (!element.attr('id')) && element.attr('type') != 'hidden') {
      element.closest('.form-group').find('label').attr('for',element.attr('name'));
      element.attr('id',element.attr('name'))
    }
});



var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title], [data-title], [data-bs-title]'))
tooltipTriggerList.map(function (tooltipTriggerEl) {
  return new bootstrap.Tooltip(tooltipTriggerEl)
});

$.each($('input, select, textarea'), function (i, element) {
  
  if (element.hasAttribute('required')) {
    $(element).closest('.form-group').find('label').first().addClass('required');
  }

});


//Custom Data Table
$('.custom-data-table').closest('.card').find('.card-body').attr('style','padding-top:0px');
var tr_elements = $('.custom-data-table tbody tr');
$(document).on('input','input[name=search_table]',function(){
  var search = $(this).val().toUpperCase();
  var match = tr_elements.filter(function (idx, elem) {
    return $(elem).text().trim().toUpperCase().indexOf(search) >= 0 ? elem : null;
  }).sort();
  var table_content = $('.custom-data-table tbody');
  if (match.length == 0) {
    table_content.html('<tr><td colspan="100%" class="text-center">Data Not Found</td></tr>');
  }else{
    table_content.html(match);
  }
});

$('.pagination').closest('nav').addClass('d-flex justify-content-end');

$('.showFilterBtn').on('click',function(){
  $('.responsive-filter-card').slideToggle();
});

$(document).on('click','.short-codes',function () {
  var text = $(this).text();
  var vInput = document.createElement("input");
  vInput.value = text;
  document.body.appendChild(vInput);
  vInput.select();
  document.execCommand("copy");
  document.body.removeChild(vInput);
  $(this).addClass('copied');
  setTimeout(() => {
      $(this).removeClass('copied');
  }, 1000);
});

Array.from(document.querySelectorAll('table')).forEach(table => {
  let heading = table.querySelectorAll('thead tr th');
  Array.from(table.querySelectorAll('tbody tr')).forEach((row) => {
      Array.from(row.querySelectorAll('td')).forEach((colum, i) => {
          colum.setAttribute('data-label', heading[i].innerText)
      });
  });
});

var len = 0;
var clickLink = 0;
var search = null;
var process = false;
$('#searchInput').on('keydown', function(e){
  var length = $('.search-list li').length;
  if(search != $(this).val() && process){
      len = 0;
      clickLink = 0;
      $(`.search-list li:eq(${len}) a`).focus();
      $(`#searchInput`).focus();
  }
  //Down
  if(e.keyCode == 40 && length){
      process = true;
      var contra = false;
      if(len < clickLink && clickLink < length){
          len += 2;
      }
      $(`.search-list li[class="bg--dark"]`).removeClass('bg--dark');
      $(`.search-list li a[class="text--white"]`).removeClass('text--white');
      $(`.search-list li:eq(${len}) a`).focus().addClass('text--white');
      $(`.search-list li:eq(${len})`).addClass('bg--dark');
      $(`#searchInput`).focus();
      clickLink = len;
      if(!$(`.search-list li:eq(${clickLink}) a`).length){
          $(`.search-list li:eq(${len})`).addClass('text--white');
      }
      len += 1;
      if(length == Math.abs(clickLink)){
          len = 0;
      }
  }
  //Up
  else if(e.keyCode == 38 && length){
      process = true;
      if(len > clickLink && len != 0){
          len -= 2;
      }
      $(`.search-list li[class="bg--dark"]`).removeClass('bg--dark');
      $(`.search-list li a[class="text--white"]`).removeClass('text--white');
      $(`.search-list li:eq(${len}) a`).focus().addClass('text--white');
      $(`.search-list li:eq(${len})`).addClass('bg--dark');
      $(`#searchInput`).focus();
      clickLink = len;
      if(!$(`.search-list li:eq(${clickLink}) a`).length){
          $(`.search-list li:eq(${len})`).addClass('text--white');
      }
      len -= 1;
      if(length == Math.abs(clickLink)){
          len = 0;
      }
  }
  //Enter
  else if(e.keyCode == 13){
      e.preventDefault();
      if($(`.search-list li:eq(${clickLink}) a`).length && process){
          $(`.search-list li:eq(${clickLink}) a`)[0].click();
      }
  }
  //Retry
  else if(e.keyCode == 8){
      len = 0;
      clickLink = 0;
      $(`.search-list li:eq(${len}) a`).focus();
      $(`#searchInput`).focus();
  }
  search = $(this).val();
});

// Admin Header Search – lightweight, debounced, abort previous request
$(document).ready(function() {
    var searchTimeout;
    var currentResults = [];
    var selectedIndex = -1;
    var searchXhr = null;
    var searchInput = $('#adminHeaderSearchInput');
    var searchResults = $('#adminHeaderSearchResults');
    var searchLoader = $('.admin-header-search-loader');
    var searchIcon = $('.admin-header-search-icon');

    if (!searchInput.length) return;

    function performSearch(query) {
        if (query.length < 1) {
            searchResults.removeClass('show').html('');
            return;
        }
        if (searchXhr && searchXhr.abort) searchXhr.abort();
        searchIcon.addClass('d-none');
        searchLoader.removeClass('d-none');
        searchResults.addClass('show');

        var searchUrl = (typeof window.adminSearchUrl !== 'undefined' && window.adminSearchUrl) ? window.adminSearchUrl : '/sajaladminopu/search';
        searchXhr = $.ajax({
            url: searchUrl,
            method: 'GET',
            data: { q: query },
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function(response) {
                searchLoader.addClass('d-none');
                searchIcon.removeClass('d-none');
                searchXhr = null;
                if (response.success && response.results && Object.keys(response.results).length > 0) {
                    currentResults = [];
                    var html = '';
                    Object.keys(response.results).forEach(function(category) {
                        var categoryResults = response.results[category];
                        html += '<div class="search-results-category">' + category + '</div>';
                        categoryResults.forEach(function(item) {
                            currentResults.push(item);
                            var idx = currentResults.length - 1;
                            var title = (item.title || '').toString();
                            var desc = (item.description || '').toString();
                            var url = (item.url || '#').toString().replace(/"/g, '&quot;');
                            var icon = (item.icon || 'las la-circle').toString().replace(/"/g, '&quot;');
                            html += '<div class="search-result-item" data-index="' + idx + '" data-url="' + url + '">' +
                                '<div class="search-result-icon"><i class="' + icon + '"></i></div>' +
                                '<div class="search-result-content">' +
                                '<div class="search-result-title">' + highlightText(title, query) + '</div>' +
                                '<p class="search-result-description">' + desc + '</p>' +
                                '<span class="search-result-url">' + url + '</span></div></div>';
                        });
                    });
                    html += '<div class="search-results-footer">' + (response.total || currentResults.length) + ' result(s)</div>';
                    searchResults.html(html);
                    selectedIndex = -1;
                } else {
                    searchResults.html('<div class="search-no-results">No exact match. Try: <strong>orders</strong>, <strong>products</strong>, <strong>settings</strong>, <strong>report</strong> — or check Suggested results above.</div>');
                    currentResults = [];
                }
            },
            error: function(xhr, status, error) {
                if (status === 'abort') return;
                searchLoader.addClass('d-none');
                searchIcon.removeClass('d-none');
                searchXhr = null;
                searchResults.html('<div class="search-no-results">Search unavailable. Try again in a moment.</div>');
            }
        });
    }

    // Highlight search text
    function highlightText(text, query) {
        if (!query) return text;
        const regex = new RegExp(`(${query})`, 'gi');
        return text.replace(regex, '<span class="highlight">$1</span>');
    }

    // Handle input - Now supports single character search
    searchInput.on('input', function() {
        const query = $(this).val().trim();
        clearTimeout(searchTimeout);
        
        if (query.length < 1) {
            searchResults.removeClass('show').html('');
            currentResults = [];
            selectedIndex = -1;
            return;
        }

        // Reduced debounce time for single character search (faster response)
        searchTimeout = setTimeout(function() {
            performSearch(query);
        }, query.length === 1 ? 200 : 300);
    });

    // Handle keyboard navigation
    searchInput.on('keydown', function(e) {
        const items = searchResults.find('.search-result-item');
        
        if (e.keyCode === 40) { // Down arrow
            e.preventDefault();
            selectedIndex = Math.min(selectedIndex + 1, items.length - 1);
            updateSelection(items);
        } else if (e.keyCode === 38) { // Up arrow
            e.preventDefault();
            selectedIndex = Math.max(selectedIndex - 1, -1);
            updateSelection(items);
        } else if (e.keyCode === 13) { // Enter
            e.preventDefault();
            if (selectedIndex >= 0 && currentResults[selectedIndex]) {
                window.location.href = currentResults[selectedIndex].url;
            }
        } else if (e.keyCode === 27) { // Escape
            searchResults.removeClass('show');
            searchInput.blur();
        }
    });

    function updateSelection(items) {
        items.removeClass('active');
        if (selectedIndex >= 0 && selectedIndex < items.length) {
            const selectedItem = items.eq(selectedIndex);
            selectedItem.addClass('active');
            const scrollTop = selectedItem.offset().top - searchResults.offset().top + searchResults.scrollTop() - 100;
            searchResults.scrollTop(scrollTop); // Remove animate - instant scroll
        }
    }

    // Handle click on result items
    $(document).on('click', '.search-result-item', function() {
        const url = $(this).data('url');
        if (url) {
            window.location.href = url;
        }
    });

    // Close search results when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.admin-header-search-wrapper').length) {
            searchResults.removeClass('show');
            selectedIndex = -1;
        }
    });

    // Focus search on Ctrl+K or Cmd+K
    $(document).on('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.keyCode === 75) {
            e.preventDefault();
            searchInput.focus();
        }
    });
});

// Handle sidebar collapse/expand - Adjust header position
$(document).ready(function() {
    function updateHeaderPosition() {
        const bodyWrapper = $('.body-wrapper');
        const navbarWrapper = $('.navbar-wrapper');
        
        if (bodyWrapper.hasClass('active')) {
            // Sidebar is collapsed (80px width)
            navbarWrapper.css('left', '80px');
        } else {
            // Sidebar is expanded (250px width)
            navbarWrapper.css('left', '250px');
        }
    }
    
    // Check on page load
    updateHeaderPosition();
    
    // Watch for sidebar collapse/expand (if there's a toggle button)
    $(document).on('click', '.navbar__expand', function() {
        setTimeout(updateHeaderPosition, 50); // Small delay to allow class toggle
    });
    
    // Watch for body-wrapper active class changes
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                updateHeaderPosition();
            }
        });
    });
    
    const bodyWrapper = document.querySelector('.body-wrapper');
    if (bodyWrapper) {
        observer.observe(bodyWrapper, {
            attributes: true,
            attributeFilter: ['class']
        });
    }
});