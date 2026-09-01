<?php

use App\Services\AuthService;

$request = \Config\Services::request();
$authService = new AuthService($request);
$user = $authService->check();

$role = $user ? $user->role : 'employee';
$uri = uri_string();
$currentUrl = current_url();

// Helper to check active state
function isNavActive($keywords, $uri, $currentUrl) {
    if (!is_array($keywords)) {
        $keywords = [$keywords];
    }
    foreach ($keywords as $kw) {
        if ($kw === 'dashboard' && ($uri === '' || $uri === '/' || $uri === 'dashboard' || strpos($uri, 'dashboard') !== false)) {
            return true;
        }
        if ($kw !== 'dashboard' && (strpos($uri, $kw) !== false || strpos($currentUrl, $kw) !== false)) {
            return true;
        }
    }
    return false;
}
?>

<!-- Mobile Bottom Navigation Bar -->
<div class="mobile-bottom-nav d-lg-none" id="mobileBottomNav">
    <div class="mobile-bottom-nav-inner">
        <?php if ($role === 'admin'): ?>
            <!-- Admin Role Menu -->
            <!-- 1. Home / Dashboard -->
            <a href="<?= base_url('/dashboard') ?>" class="nav-tab <?= isNavActive(['dashboard'], $uri, $currentUrl) ? 'active' : '' ?>">
                <div class="nav-tab-icon">
                    <i class="mdi mdi-home"></i>
                </div>
                <span class="nav-tab-label">Home</span>
            </a>

            <!-- 2. Employees -->
            <a href="<?= base_url('/empview') ?>" class="nav-tab <?= isNavActive(['empview', 'employee'], $uri, $currentUrl) ? 'active' : '' ?>">
                <div class="nav-tab-icon">
                    <i class="mdi mdi-account-group"></i>
                </div>
                <span class="nav-tab-label">Employee</span>
            </a>

            <!-- 3. Attendance -->
            <a href="<?= base_url('/attendence') ?>" class="nav-tab <?= isNavActive(['attendence', 'view-calendar', 'attendance'], $uri, $currentUrl) ? 'active' : '' ?>">
                <div class="nav-tab-icon">
                    <i class="mdi mdi-calendar-check"></i>
                </div>
                <span class="nav-tab-label">Attendance</span>
            </a>

            <!-- 4. Leaves / Payroll -->
            <a href="<?= base_url('/leaveview') ?>" class="nav-tab <?= isNavActive(['leaveview', 'addleave', 'employee-live-request', 'leave'], $uri, $currentUrl) ? 'active' : '' ?>">
                <div class="nav-tab-icon">
                    <i class="mdi mdi-calendar-clock"></i>
                </div>
                <span class="nav-tab-label">Leaves</span>
            </a>

            <!-- 5. More / Menu Toggle -->
            <a href="javascript:void(0)" class="nav-tab" data-bs-toggle="offcanvas" title="Open Full Menu">
                <div class="nav-tab-icon">
                    <i class="mdi mdi-menu"></i>
                </div>
                <span class="nav-tab-label">Menu</span>
            </a>

        <?php elseif ($role === 'hr'): ?>
            <!-- HR Role Menu -->
            <!-- 1. Home / Dashboard -->
            <a href="<?= base_url('/dashboard') ?>" class="nav-tab <?= isNavActive(['dashboard'], $uri, $currentUrl) ? 'active' : '' ?>">
                <div class="nav-tab-icon">
                    <i class="mdi mdi-home"></i>
                </div>
                <span class="nav-tab-label">Home</span>
            </a>

            <!-- 2. Employees -->
            <a href="<?= base_url('/empview') ?>" class="nav-tab <?= isNavActive(['empview', 'employee'], $uri, $currentUrl) ? 'active' : '' ?>">
                <div class="nav-tab-icon">
                    <i class="mdi mdi-account-group"></i>
                </div>
                <span class="nav-tab-label">Employee</span>
            </a>

            <!-- 3. Attendance -->
            <a href="<?= base_url('/attendence') ?>" class="nav-tab <?= isNavActive(['attendence', 'view-calendar', 'attendance'], $uri, $currentUrl) ? 'active' : '' ?>">
                <div class="nav-tab-icon">
                    <i class="mdi mdi-calendar-check"></i>
                </div>
                <span class="nav-tab-label">Attendance</span>
            </a>

            <!-- 4. Leaves -->
            <a href="<?= base_url('/leaveview') ?>" class="nav-tab <?= isNavActive(['leaveview', 'addleave', 'employee-live-request', 'leave'], $uri, $currentUrl) ? 'active' : '' ?>">
                <div class="nav-tab-icon">
                    <i class="mdi mdi-calendar-clock"></i>
                </div>
                <span class="nav-tab-label">Leaves</span>
            </a>

            <!-- 5. Recruitment or Menu -->
            <a href="<?= base_url('/candidateview') ?>" class="nav-tab <?= isNavActive(['candidateview', 'jobview', 'onboardingview', 'interview'], $uri, $currentUrl) ? 'active' : '' ?>">
                <div class="nav-tab-icon">
                    <i class="mdi mdi-account-search"></i>
                </div>
                <span class="nav-tab-label">Recruit</span>
            </a>

        <?php else: ?>
            <!-- Employee Role Menu -->
            <!-- 1. Home / Dashboard -->
            <a href="<?= base_url('/dashboard') ?>" class="nav-tab <?= isNavActive(['dashboard'], $uri, $currentUrl) ? 'active' : '' ?>">
                <div class="nav-tab-icon">
                    <i class="mdi mdi-home"></i>
                </div>
                <span class="nav-tab-label">Home</span>
            </a>

            <!-- 2. Attendance -->
            <a href="<?= base_url('/view-calendar') ?>" class="nav-tab <?= isNavActive(['view-calendar', 'attendence', 'attendance'], $uri, $currentUrl) ? 'active' : '' ?>">
                <div class="nav-tab-icon">
                    <i class="mdi mdi-calendar-check"></i>
                </div>
                <span class="nav-tab-label">Attendance</span>
            </a>

            <!-- 3. Leaves -->
            <a href="<?= base_url('/leaveview') ?>" class="nav-tab <?= isNavActive(['leaveview', 'addleave', 'leave'], $uri, $currentUrl) ? 'active' : '' ?>">
                <div class="nav-tab-icon">
                    <i class="mdi mdi-calendar-clock"></i>
                </div>
                <span class="nav-tab-label">Leaves</span>
            </a>

            <!-- 4. Tasks -->
            <a href="<?= base_url('/taskview') ?>" class="nav-tab <?= isNavActive(['taskview', 'task', 'all_subtask'], $uri, $currentUrl) ? 'active' : '' ?>">
                <div class="nav-tab-icon">
                    <i class="mdi mdi-format-list-checkbox"></i>
                </div>
                <span class="nav-tab-label">Tasks</span>
            </a>

            <!-- 5. More / Menu Toggle -->
            <a href="javascript:void(0)" class="nav-tab" data-bs-toggle="offcanvas" title="Open Full Menu">
                <div class="nav-tab-icon">
                    <i class="mdi mdi-menu"></i>
                </div>
                <span class="nav-tab-label">Menu</span>
            </a>
        <?php endif; ?>
    </div>
</div>

<style>
/* ===================================================
   Mobile Bottom Navigation Bar Styles (Light Clean Theme)
   =================================================== */
.mobile-bottom-nav {
    display: none;
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 1040;
    background: #ffffff;
    border-top: 1px solid #e2e8f0;
    box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.08);
    padding: 6px 6px calc(6px + env(safe-area-inset-bottom, 0px)) 6px;
    transition: transform 0.3s ease, opacity 0.3s ease;
}

.mobile-bottom-nav-inner {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
    max-width: 600px;
    margin: 0 auto;
    gap: 2px;
}

.mobile-bottom-nav .nav-tab {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    color: #334155;
    padding: 6px 4px;
    border-radius: 12px;
    flex: 1;
    min-width: 0;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    user-select: none;
    -webkit-tap-highlight-color: transparent;
}

.mobile-bottom-nav .nav-tab-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 21px;
    line-height: 1;
    margin-bottom: 2px;
    color: #475569;
    transition: transform 0.2s ease, color 0.2s ease;
}

.mobile-bottom-nav .nav-tab-icon i {
    font-size: 21px;
    line-height: 1;
}

.mobile-bottom-nav .nav-tab-label {
    font-size: 10.5px;
    font-weight: 500;
    letter-spacing: 0.1px;
    line-height: 1.1;
    color: #334155;
    transition: color 0.2s ease, font-weight 0.2s ease;
    white-space: nowrap;
    text-align: center;
}

/* Active State (Project Orange: #e66136) */
.mobile-bottom-nav .nav-tab.active {
    color: #e66136;
    background: rgba(230, 97, 54, 0.12);
    border: 1px solid rgba(230, 97, 54, 0.28);
    box-shadow: 0 2px 6px rgba(230, 97, 54, 0.1);
}

.mobile-bottom-nav .nav-tab.active .nav-tab-icon {
    transform: translateY(-1px);
    color: #e66136;
}

.mobile-bottom-nav .nav-tab.active .nav-tab-icon i {
    color: #e66136;
}

.mobile-bottom-nav .nav-tab.active .nav-tab-label {
    color: #e66136;
    font-weight: 600;
}

/* Hover/Touch Active Feedback */
.mobile-bottom-nav .nav-tab:active {
    transform: scale(0.92);
    background: rgba(0, 0, 0, 0.05);
}

/* Responsive display rule */
@media (max-width: 991.98px) {
    .mobile-bottom-nav {
        display: flex !important;
    }
    
    /* Ensure content wrapper doesn't get covered by bottom nav */
    .content-wrapper {
        padding-bottom: calc(80px + env(safe-area-inset-bottom, 0px)) !important;
    }

    footer.footer {
        margin-bottom: calc(65px + env(safe-area-inset-bottom, 0px)) !important;
    }
}

@media (min-width: 992px) {
    .mobile-bottom-nav {
        display: none !important;
    }
}
</style>
