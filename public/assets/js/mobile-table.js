/* Mobile Expandable Table JavaScript */
$(document).ready(function() {
    
    // Check if mobile view
    function isMobileView() {
        return window.innerWidth < 768;
    }

    // Apply mobile visibility
    function applyMobileTableVisibility() {
        if (isMobileView()) {
            $('.mobile-expand-col').css('display', 'table-cell');
            $('.expand-toggle').css('display', 'flex');
            $('.desktop-only-col').css('display', 'none');
        } else {
            $('.mobile-expand-col').css('display', 'none');
            $('.expand-toggle').css('display', 'none');
            $('.desktop-only-col').css('display', '');
            $('.expanded-details').removeClass('show');
            $('.expand-toggle').removeClass('expanded');
        }
    }

    // Handle window resize
    $(window).on('resize', function() {
        applyMobileTableVisibility();
    });

    // Handle expand toggle click
    $(document).on('click', '.expand-toggle', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const targetId = $(this).data('target');
        const $details = $('#' + targetId);
        const $toggle = $(this);

        if ($details.hasClass('show')) {
            $details.removeClass('show');
            $toggle.removeClass('expanded');
        } else {
            // Close other expanded details
            $('.expanded-details').removeClass('show');
            $('.expand-toggle').removeClass('expanded');
            
            // Open this one
            $details.addClass('show');
            $toggle.addClass('expanded');
        }
    });

    // Initial visibility check
    applyMobileTableVisibility();

    // Make function globally available for use after DataTables init
    window.applyMobileTableVisibility = applyMobileTableVisibility;
});

