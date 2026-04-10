(function ($) {
  "user strict";
  $(window).on("load", function () {
    $(".preloader").fadeOut(1500);
    $("body").removeClass("overflow-hidden");
    var img = $(".bg_img");
    img.css("background-image", function () {
      var bg = "url(" + $(this).data("background") + ")";
      return bg;
    });
    sliderFunc();
  });
  $(".menu>li>.submenu").parent("li").addClass("menu-item-has-children");
  // drop down menu width overflow problem fix
  $("ul")
    .parent("li")
    .hover(function () {
      var menu = $(this).find("ul");
      var menupos = $(menu).offset();
      if (menupos.left + menu.width() > $(window).width()) {
        var newpos = -$(menu).width();
        menu.css({
          left: newpos,
        });
      }
    });

  $(".showFilterBtn").on("click", function () {
    $(".responsive-filter-card").slideToggle();
  });

  $(".menu li a, .category-link li a").on("click", function (e) {
    var element = $(this).parent("li");
    if (element.hasClass("open")) {
      element.removeClass("open");
      element.find("li").removeClass("open");
      element.find("ul").slideUp(300, "swing");
    } else {
      element.addClass("open");
      element.children("ul").slideDown(300, "swing");
      element.siblings("li").children("ul").slideUp(300, "swing");
      element.siblings("li").removeClass("open");
      element.siblings("li").find("li").removeClass("open");
      element.siblings("li").find("ul").slideUp(300, "swing");
    }
  });



  function cateMenuFunction() {
    $(".categoryButton").on("click", function () {
      if ($(window).width() < 992) {
        if (!$(this).hasClass("active")) {
          $(".menuButton").removeClass("active");
          $(this).addClass("active");
          $(".menu").hide(50);
          $(".category-link").show(50);
        } else {
          $(".categoryButton").removeClass("active");
          $(".menuButton").addClass("active");
          $(".category-link").hide(50);
          $(".menu").show(50);
        }
      }
    });
    $(".menuButton").on("click", function () {
      if ($(window).width() < 992) {
        $(".categoryButton").removeClass("active");
        $(this).addClass("active");
        $(".category-link").hide(50);
        $(".menu").show(50);
      }
    });
  }
  cateMenuFunction();

  // Scroll To Top
  var scrollTop = $(".scrollToTop");
  $(window).on("scroll", function () {
    if ($(this).scrollTop() < 500) {
      scrollTop.removeClass("active");
    } else {
      scrollTop.addClass("active");
    }
  });


  $('.testimonial-slider').slick({
    slidesToShow: 2,
    slidesToScroll: 1,
    speed: 700,
    dots: true,
    arrows: false,
    responsive: [
      {
        breakpoint: 768,
        settings: {
          slidesToShow: 1
        }
      }
    ]
  });
  //header
  var header = $(".header-bottom");
  $(window).on("scroll", function () {
    if ($(this).scrollTop() < 1) {
      header.removeClass("fadeInDown animated");
      $(".header-bottom").removeClass("active");
    } else {
      header.addClass("fadeInDown animated");
      $(".header-bottom").addClass("active");
    }
  });

  //Click event to scroll to top
  $(".scrollToTop").on("click", function () {
    $("html, body").animate(
      {
        scrollTop: 0,
      },
      500
    );
    return false;
  });
  //Header Bar
  $(".header-bar, .close-sidebar").on("click", function () {
    $(".header-bar, .close-sidebar").toggleClass("active");
    $(".overlay").toggleClass("active");
    $(".menu-area").toggleClass("active");
  });

  function toggleDashboardSidebar() {
    var isOpen = $(".dashboard__sidebar").toggleClass("active").hasClass("active");
    $(".overlay").toggleClass("active", isOpen);
    if (window.innerWidth < 992) {
      document.body.classList.toggle("dashboard-sidebar-open", isOpen);
    }
  }
  function closeDashboardSidebar() {
    $(".dashboard__sidebar").removeClass("active");
    $(".overlay").removeClass("active");
    document.body.classList.remove("dashboard-sidebar-open");
  }
  var dashboardTogglerTouchHandled = false;
  $(document).on("click", ".dashboard-sidebar-toggler", function (e) {
    if (dashboardTogglerTouchHandled) {
      dashboardTogglerTouchHandled = false;
      return;
    }
    e.preventDefault();
    e.stopPropagation();
    toggleDashboardSidebar();
  });
  $(document).on("touchend", ".dashboard-sidebar-toggler", function (e) {
    var el = e.target.closest && e.target.closest(".dashboard-sidebar-toggler");
    if (el) {
      if (e.cancelable) e.preventDefault();
      dashboardTogglerTouchHandled = true;
      setTimeout(function () { dashboardTogglerTouchHandled = false; }, 400);
      toggleDashboardSidebar();
    }
  });
  $(".close-dashboard-sidebar").on("click", closeDashboardSidebar);
  $(document).on("click", ".close-dashboard-sidebar", closeDashboardSidebar);
  $(".menu-close").on("click", function () {
    $(".overlay, .menu-area, .header-bar").removeClass("active");
    document.body.classList.remove("dashboard-sidebar-open");
  });
  $(".overlay, .close-searchbar").on("click", function () {
    $(
      ".overlay, .dashboard-menu, .header-bar, .dashboard__sidebar, .menu-area, .filter--sidebar"
    ).removeClass("active");
    document.body.classList.remove("dashboard-sidebar-open");
  });
  $(".close--sidebar").on("click", function () {
    $(".filter--sidebar, .overlay").removeClass("active");
  });
  $(".filter--bar").on("click", function () {
    $(".filter--sidebar, .overlay").addClass("active");
  });
  $(".faq__item .faq__title").on("click", function (e) {
    var element = $(this).parent(".faq__item");
    if (element.hasClass("open")) {
      element.removeClass("open");
      element.find(".faq__content").removeClass("open");
      element.find(".faq__content").slideUp(200, "swing");
    } else {
      element.addClass("open");
      element.children(".faq__content").slideDown(200, "swing");
      element
        .siblings(".faq__item")
        .children(".faq__content")
        .slideUp(200, "swing");
      element.siblings(".faq__item").removeClass("open");
      element.siblings(".faq__item").find(".faq__title").removeClass("open");
      element
        .siblings(".faq__item")
        .find(".faq__content")
        .slideUp(200, "swing");
    }
  });
  function copyBtn() {
    var copyText = document.getElementById("referral-link");
    copyText.select();
    document.execCommand("copy");
  }
  $(".copyBtn, #referral-link").on("click", copyBtn);

  /* .banner-slider is initialized on home page only with PHP-driven interval (see home.blade.php @push script). Skip .home-banner on home so admin slider settings apply. */
  	new WOW().init();

  if (!$(".banner-slider").length) {
    $(".home-banner").owlCarousel({
      loop: true,
      nav: false,
      dots: false,
      items: 1,
      autoplay: true,
      animateOut: "slideOutLeft",
      animateIn: "slideInRight",
      autoplayTimeout: 2500,
      dots: true,
    });
  }

  $(".related-products-slider").owlCarousel({
    items: 1,
    dots: false,
    autoplay: true,
    margin: 20,
    loop: true,
    nav: false,
    responsive: {
      460: {
        items: 2,
      },
      768: {
        items: 3,
      },
      1200: {
        items: 4,
      },
    },
  });
  $(".recent__logins").owlCarousel({
    loop: false,
    responsiveClass: true,
    nav: false,
    dots: true,
    autoplay: true,
    autoplayTimeout: 2500,
    autoplayHoverPause: true,
    responsive: {
      0: {
        items: 2,
      },
      500: {
        items: 2,
      },
      768: {
        items: 3,
      },
      992: {
        items: 2,
      },
      1200: {
        items: 3,
      },
    },
  });
  $(".type-change").on("click", function (e) {
    if ($(this).hasClass("active")) {
      $(this).removeClass("active");
      $(this).siblings("input").attr({ type: "password" });
      $(this)
        .closest(".position-relative")
        .find(".type-change")
        .html('<i class="las la-eye"></i>');
    } else {
      $(this).addClass("active");
      $(this).siblings("input").attr({ type: "text" });
      $(this)
        .closest(".position-relative")
        .find(".type-change")
        .html('<i class="las la-eye-slash"></i>');
    }
  });
  $("ul>li>.category-sublink").parent("li").addClass("cate-icon");
  function sliderFunc() {
    if ($(window).width() < 1200) {
      $(".product-max-xl-slider").addClass("owl-theme owl-carousel");
      $(".product-max-xl-slider").owlCarousel({
        loop: true,
        nav: false,
        dots: false,
        items: 1,
        margin: 15,
        autoplay: true,
        autoplayTimeout: 1000,
        mouseDrag: false,
        responsive: {
          450: {
            items: 2,
          },
          768: {
            items: 3,
          },
          992: {
            items: 4,
          },
        },
      });
    }
  }
  $(window).on("resize", sliderFunc);
  var sync1 = $(".sync1");
  var sync2 = $(".sync2");
  var thumbnailItemClass = ".owl-item";
  var itemCount = sync1.find("> .thumbs").length || sync1.children().length || 1;
  var slides = sync1
    .owlCarousel({
      items: 1,
      loop: itemCount > 1,
      margin: 0,
      mouseDrag: true,
      touchDrag: true,
      pullDrag: false,
      scrollPerPage: true,
      nav: true,
      navText: ['<i class="las la-chevron-left"></i>','<i class="las la-chevron-right"></i>'],
      dots: true,
    })
    .on("changed.owl.carousel", syncPosition);

  function syncPosition(el) {
    $owl_slider = $(this).data("owl.carousel");
    var loop = $owl_slider.options.loop;

    if (loop) {
      var count = el.item.count - 1;
      var current = Math.round(el.item.index - el.item.count / 2 - 0.5);
      if (current < 0) {
        current = count;
      }
      if (current > count) {
        current = 0;
      }
    } else {
      var current = el.item.index;
    }

    var owl_thumbnail = sync2.data("owl.carousel");
    var itemClass = "." + owl_thumbnail.options.itemClass;

    var thumbnailCurrentItem = sync2
      .find(itemClass)
      .removeClass("synced")
      .eq(current);
    thumbnailCurrentItem.addClass("synced");

    if (!thumbnailCurrentItem.hasClass("active")) {
      var duration = 500;
      sync2.trigger("to.owl.carousel", [current, duration, true]);
    }
  }
  var thumbs = sync2
    .owlCarousel({
      items: 3,
      loop: itemCount > 1,
      margin: 10,
      nav: false,
      dots: false,
      responsive: {
        500: {
          items: 4,
        },
        768: {
          items: 5,
        },
        992: {
          items: 4,
        },
        1200: {
          items: 5,
        },
      },
      onInitialized: function (e) {
        var thumbnailCurrentItem = $(e.target)
          .find(thumbnailItemClass)
          .eq(this._current);
        thumbnailCurrentItem.addClass("synced");
      },
    })
    .on("click", thumbnailItemClass, function (e) {
      e.preventDefault();
      var duration = 500;
      var itemIndex = $(e.target).parents(thumbnailItemClass).index();
      sync1.trigger("to.owl.carousel", [itemIndex, duration, true]);
    })
    .on("changed.owl.carousel", function (el) {
      var number = el.item.index;
      $owl_slider = sync1.data("owl.carousel");
      $owl_slider.to(number, 500, true);
    });
  sync1.owlCarousel();
  var inStock = parseFloat($(".quantity--amount .amount").text());
  $(".qtybutton").on("click", function () {
    var $button = $(this);
    $button.parent().find(".qtybutton").removeClass("active");
    $button.addClass("active");
    var oldValue = $button.parent().find("input").val();

    var showInStock = $(".quantity--amount .amount");
    if ($button.hasClass("inc")) {
      var newVal = parseFloat(oldValue) + 1;
      showInStock.html(inStock - newVal);
    } else {
      if (oldValue > 1) {
        var newVal = parseFloat(oldValue) - 1;
        showInStock.html(inStock - newVal);
      } else {
        newVal = 0;
      }
    }
    $button.parent().find("input").val(newVal);
  });
  $(".productQuantity").on("input", function () {
    var inputVal = $(this).val();
    $(".quantity--amount .amount").text(inStock - inputVal);
  });

  var gridWrapper = $("#products");
  $("#box-item").on("click", function () {
    gridWrapper.children().hide();
    gridWrapper.children().attr("class", "col-sm-6");
    $(this).siblings().removeClass("active");
    $(this).addClass("active");
    gridWrapper.children().show();
  });
  $("#grid-item").on("click", function () {
    gridWrapper.children().hide();
    gridWrapper.children().attr("class", "col-md-4 col-sm-6");
    $(this).siblings().removeClass("active");
    $(this).addClass("active");
    gridWrapper.children().show();
  });
  $("#grid-4-item").on("click", function () {
    gridWrapper.children().hide();
    gridWrapper.children().attr("class", "col-xl-3 col-lg-4 col-md-4 col-sm-6");
    $(this).siblings().removeClass("active");
    $(this).addClass("active");
    gridWrapper.children().show();
  });
  $("#list-item").on("click", function () {
    gridWrapper.children().hide();
    gridWrapper.children().attr("class", "col-12 products-list");
    $(this).siblings().removeClass("active");
    $(this).addClass("active");
    gridWrapper.children().show();
  });

  var fadeTime = 300;
  $(".delete-btn").on("click", function () {
    removeItem(this);
  });
  function removeItem(removeButton) {
    var productRow = $(removeButton).parent().parent();
    productRow.slideUp(fadeTime, function () {
      productRow.remove();
    });
  }

  var fadeTime = 300;
  $(".remove-wishlist").on("click", function () {
    removeItem(this);
  });
  function removeItem(removeButton) {
    var productRow = $(removeButton).parent().parent();
    productRow.hide(fadeTime, function () {
      productRow.remove();
    });
  }

  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
  var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
  })
})(jQuery);
