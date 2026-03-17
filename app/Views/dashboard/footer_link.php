<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="<?= base_url(env('ImagePath').'assets/vendors/js/vendor.bundle.base.js'); ?>"></script>
<script src="<?= base_url(env('ImagePath').'assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js'); ?>"></script>
<script src="<?= base_url(env('ImagePath').'assets/vendors/chart.js/chart.umd.js'); ?>"></script>
<script src="<?= base_url(env('ImagePath').'assets/vendors/progressbar.js/progressbar.min.js'); ?>"></script>
<script src="<?= base_url(env('ImagePath').'assets/js/off-canvas.js'); ?>"></script>
<script src="<?= base_url(env('ImagePath').'assets/vendors/datatables.net/jquery.dataTables.js'); ?>"></script>
<script src="<?= base_url(env('ImagePath').'assets/vendors/datatables.net-bs4/dataTables.bootstrap4.js'); ?>"></script>
<script src="<?= base_url(env('ImagePath').'assets/js/template.js'); ?>"></script>
<script src="<?= base_url(env('ImagePath').'assets/js/settings.js'); ?>"></script>
<script src="<?= base_url(env('ImagePath').'assets/js/hoverable-collapse.js'); ?>"></script>
<script src="<?= base_url(env('ImagePath').'assets/js/todolist.js'); ?>"></script>
<script src="<?= base_url(env('ImagePath').'assets/js/jquery.cookie.js'); ?>" type="text/javascript"></script>
<script src="<?= base_url(env('ImagePath').'assets/js/dashboard.js'); ?>"></script>
<script src="<?= base_url(env('ImagePath').'assets/js/datatable.js'); ?>"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/progressbar.js/1.0.1/progressbar.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js"></script>
<script src="<?= base_url(env('ImagePath').'assets/js/script.js'); ?>"></script>
<script src="<?= base_url(env('ImagePath').'assets/js/mobile-table.js'); ?>"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.1/nouislider.min.js"></script>

<!-- 404 Detection and Redirect Script -->
<script>
(function() {
    'use strict';
    
    // Function to check if current page is a 404 error
    function is404Page() {
        // Check multiple indicators of 404 page
        const pageTitle = document.title.toLowerCase();
        const bodyText = document.body ? document.body.innerText.toLowerCase() : '';
        const url = window.location.href;
        
        // Common 404 indicators (including CodeIgniter's specific message)
        const indicators = [
            '404',
            'page not found',
            'not found',
            'error 404',
            'file not found',
            'sorry! cannot seem to find',  // CodeIgniter's 404 message
            'cannot seem to find the page',
            'sorry cannot find'
        ];
        
        // Check title
        for (let indicator of indicators) {
            if (pageTitle.includes(indicator)) {
                return true;
            }
        }
        
        // Check body content (first 1000 chars to catch the message)
        const bodyPreview = bodyText.substring(0, 1000);
        for (let indicator of indicators) {
            if (bodyPreview.includes(indicator)) {
                return true;
            }
        }
        
        // Check for the specific 404 page structure (h1 with "404" and specific message)
        const h1Elements = document.querySelectorAll('h1');
        for (let h1 of h1Elements) {
            if (h1.textContent.trim() === '404') {
                // Found 404 heading, check if it's the error page
                const wrapDiv = h1.closest('.wrap');
                if (wrapDiv) {
                    const pElement = wrapDiv.querySelector('p');
                    if (pElement && pElement.textContent.toLowerCase().includes('cannot seem to find')) {
                        return true;
                    }
                }
                // If we see a standalone 404 heading, it's likely a 404 page
                return true;
            }
        }
        
        // Check if page has very minimal content (common for 404 pages)
        if (document.body && document.body.children.length < 3 && bodyText.length < 200) {
            // Check if it contains "404" anywhere
            if (bodyText.includes('404')) {
                return true;
            }
        }
        
        return false;
    }
    
    // Function to redirect to dashboard
    function redirectToDashboard() {
        const dashboardUrl = '<?= base_url("/dashboard") ?>';
        console.log('🔄 404 detected, redirecting to dashboard:', dashboardUrl);
        
        // Notify service worker if available
        if ('serviceWorker' in navigator && navigator.serviceWorker.controller) {
            navigator.serviceWorker.controller.postMessage({
                type: 'PAGE_404',
                url: window.location.href
            }).catch(() => {
                // Service worker message failed, continue with redirect
            });
        }
        
        // Redirect to dashboard
        window.location.href = dashboardUrl;
    }
    
    // Check for 404 when page loads (immediate check)
    function checkAndRedirect() {
        if (is404Page()) {
            redirectToDashboard();
            return true;
        }
        return false;
    }
    
    // Immediate check (for pages already loaded)
    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        // Check immediately
        if (!checkAndRedirect()) {
            // Also check after a short delay in case content loads asynchronously
            setTimeout(checkAndRedirect, 100);
            setTimeout(checkAndRedirect, 500);
            setTimeout(checkAndRedirect, 1000);
        }
    } else {
        // Wait for DOM to load
        document.addEventListener('DOMContentLoaded', function() {
            if (!checkAndRedirect()) {
                // Multiple checks to catch async-loaded 404 pages
                setTimeout(checkAndRedirect, 100);
                setTimeout(checkAndRedirect, 500);
                setTimeout(checkAndRedirect, 1000);
            }
        });
    }
    
    // Listen for messages from service worker
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.addEventListener('message', function(event) {
            if (event.data && event.data.type === 'CHECK_404') {
                // Service worker is asking us to check for 404
                setTimeout(function() {
                    if (is404Page()) {
                        redirectToDashboard();
                    }
                }, 500);
            } else if (event.data && event.data.type === 'REDIRECT') {
                // Service worker is telling us to redirect
                if (event.data.url) {
                    window.location.href = event.data.url;
                }
            }
        });
    }
    
    // Also check on navigation (for SPA-like behavior)
    let lastUrl = location.href;
    new MutationObserver(function() {
        const currentUrl = location.href;
        if (currentUrl !== lastUrl) {
            lastUrl = currentUrl;
            setTimeout(function() {
                if (is404Page()) {
                    redirectToDashboard();
                }
            }, 500);
        }
    }).observe(document, { subtree: true, childList: true });
})();
</script>

<!-- <script src="https://cdn.jsdelivr.net/npm/progressbar.js/dist/progressbar.min.js"></script> -->