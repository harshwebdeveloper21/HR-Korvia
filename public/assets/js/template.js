(function ($) {
  'use strict';
  $(function () {
    var body = $('body');
    var contentWrapper = $('.content-wrapper');
    var scroller = $('.container-scroller');
    var footer = $('.footer');
    var sidebar = $('.sidebar');

    // Function to add active class based on the current URL
    // function addActiveClass(element) {
    //   var currentPath = location.pathname; // Get the current full path
    //   var href = element.attr('href');
      
    //   // Check if the current href matches exactly or contains the full path
    //   if (currentPath === href || currentPath.indexOf(href) !== -1) {
    //     element.parents('.nav-item').last().addClass('active');
    //     if (element.parents('.sub-menu').length) {
    //       element.closest('.collapse').addClass('show');
    //       element.addClass('active');
    //     }
    //   } else {
    //     // Remove the active class from non-matching elements
    //     // element.parents('.nav-item').last().removeClass('active');
    //   }
    // }
$(document).ready(function () {
  var currentPath = window.location.pathname.replace(/\/$/, '');

  $('.sidebar .nav-link').each(function () {
    var $this = $(this);
    var href = $this.attr('href');

    if (href && href.charAt(0) !== '#') {
      var hrefPath = new URL(href, window.location.origin).pathname.replace(/\/$/, '');

      var isMatch = (currentPath === hrefPath);

      // Smart UX active route matching for sub-pages / actions
      if (!isMatch) {
        if (hrefPath === '/departmentview' && (currentPath === '/department' || currentPath.indexOf('/department/') === 0)) {
          isMatch = true;
        } else if (hrefPath === '/branches' && (currentPath === '/branches/create' || currentPath.indexOf('/branches/edit') === 0 || currentPath.indexOf('/branches/assign') === 0)) {
          isMatch = true;
        }
      }

      if (isMatch) {
        $this.addClass('active');
        $this.parents('.collapse').addClass('show');

        if ($this.closest('.sub-menu').length) {
          $this.closest('.collapse').prev('.nav-link').addClass('active');
        } else if (!$this.closest('.erp-group-list').length) {
          $this.closest('.nav-item').addClass('active');
        }
      }
    }
  });

  // Keep only the module that holds the current page open, then sync toggle states with what is visible.
  var $activeModule = $('.sidebar .erp-module').not('.erp-dashboard-wrap').has('.nav-link.active').first();
  if ($activeModule.length) {
    $('.sidebar .erp-module').not($activeModule).children('.collapse.show').removeClass('show');
  }
  $('.sidebar [data-bs-toggle="collapse"]').each(function () {
    var target = $(this).attr('href') || $(this).attr('data-bs-target');
    if (!target || target.charAt(0) !== '#') {
      return;
    }
    var isOpen = $(target).hasClass('show');
    $(this).attr('aria-expanded', isOpen ? 'true' : 'false').toggleClass('collapsed', !isOpen);
  });
});





    // Get the current path of the URL
    var current = location.pathname.split("/").slice(-1)[0].replace(/^\/|\/$/g, '');

    // Apply active class to sidebar and horizontal menu items
    // $('.nav li a', sidebar).each(function () {
    //   var $this = $(this);
    //   addActiveClass($this);
    // });

    // $('.horizontal-menu .nav li a').each(function () {
    //   var $this = $(this);
    //   addActiveClass($this);
    // });

    // Close sibling menus, and keep the group that contains the menu being opened.
    sidebar.on('show.bs.collapse', '.collapse', function (e) {
      if (e.target !== this) {
        return;
      }
      var current = this;
      sidebar.find('.collapse.show').each(function () {
        if (this === current || $.contains(this, current) || $.contains(current, this)) {
          return;
        }
        $(this).collapse('hide');
      });
    });

    // Change sidebar and content-wrapper height
    applyStyles();

    function applyStyles() {
      // Applying perfect scrollbar
      if (!body.hasClass("rtl")) {
        if ($('.settings-panel .tab-content .tab-pane.scroll-wrapper').length) {
          const settingsPanelScroll = new PerfectScrollbar('.settings-panel .tab-content .tab-pane.scroll-wrapper');
        }
        if ($('.chats').length) {
          const chatsScroll = new PerfectScrollbar('.chats');
        }
        if (body.hasClass("sidebar-fixed")) {
          if ($('#sidebar').length) {
            var fixedSidebarScroll = new PerfectScrollbar('#sidebar .nav');
          }
        }
      }
    }

    // Prevent scroll chaining from sidebar to window when reaching top/bottom
    sidebar.on('wheel', function (e) {
      var delta = e.originalEvent.deltaY;
      var scrollTop = this.scrollTop;
      var scrollHeight = this.scrollHeight;
      var height = $(this).outerHeight();

      if ((delta < 0 && scrollTop <= 0) || (delta > 0 && scrollTop + height >= scrollHeight - 1)) {
        e.preventDefault();
      }
    });

    // Minimize sidebar toggle
    $('[data-bs-toggle="minimize"]').on("click", function () {
      if ((body.hasClass('sidebar-toggle-display')) || (body.hasClass('sidebar-absolute'))) {
        body.toggleClass('sidebar-hidden');
      } else {
        body.toggleClass('sidebar-icon-only');
      }
    });

    // Checkbox and radio buttons styling
    $(".form-check label,.form-radio label").append('<i class="input-helper"></i>');

    // Horizontal menu in mobile
    $('[data-toggle="horizontal-menu-toggle"]').on("click", function () {
      $(".horizontal-menu .bottom-navbar").toggleClass("header-toggled");
    });

    // Horizontal menu navigation in mobile
    var navItemClicked = $('.horizontal-menu .page-navigation >.nav-item');
    navItemClicked.on("click", function (event) {
      if (window.matchMedia('(max-width: 991px)').matches) {
        if (!($(this).hasClass('show-submenu'))) {
          navItemClicked.removeClass('show-submenu');
        }
        $(this).toggleClass('show-submenu');
      }
    });

    // Scroll effect for horizontal menu
    $(window).scroll(function () {
      if (window.matchMedia('(min-width: 992px)').matches) {
        var header = $('.horizontal-menu');
        if ($(window).scrollTop() >= 70) {
          $(header).addClass('fixed-on-scroll');
        } else {
          $(header).removeClass('fixed-on-scroll');
        }
      }
    });

    // Datepicker
    if ($("#datepicker-popup").length) {
      $('#datepicker-popup').datepicker({
        enableOnReadonly: true,
        todayHighlight: true,
      });
      $("#datepicker-popup").datepicker("setDate", "0");
    }

  });

  // Check all checkboxes in order status
  $("#check-all").click(function () {
    $(".form-check-input").prop('checked', $(this).prop('checked'));
  });

  // Focus input when clicking on search icon
  $('#navbar-search-icon').click(function () {
    $("#navbar-search-input").focus();
  });

  // Scroll effect for header (Desktop only: smooth auto-adjust on scroll)
  function updateHeaderOnScroll() {
    if (window.innerWidth < 992) {
      $(".fixed-top").removeClass("headerLight");
      $("body").removeClass("has-scrolled");
      return;
    }
    var scroll = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop || $(window).scrollTop() || 0;
    if (scroll >= 20) {
      $(".fixed-top").addClass("headerLight");
      $("body").addClass("has-scrolled");
    } else {
      $(".fixed-top").removeClass("headerLight");
      $("body").removeClass("has-scrolled");
    }
  }

  $(window).on('scroll resize touchmove', updateHeaderOnScroll);
  $(document).on('scroll', updateHeaderOnScroll);
  $(document).ready(updateHeaderOnScroll);
})(jQuery);
