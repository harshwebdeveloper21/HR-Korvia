<?php

use App\Services\AuthService;

$request = \Config\Services::request();
$authService = new AuthService($request);
$user = $authService->check();

$role = $user ? $user->role : null;
$isAdmin = ($role === 'admin');
$isHr = ($role === 'hr');
$isAdminOrHr = ($isAdmin || $isHr);
$isBranchAdmin = ($role === 'branch_admin');
$isDeptManager = ($role === 'department_manager');
$isEmployee = ($role === 'employee');
?>

<style>
#sidebar.erp-sidebar {
  background: #f5f6fa !important;
  font-family: "Manrope", sans-serif;
}
#sidebar.erp-sidebar .nav {
  padding: 0 0 24px !important;
  margin-bottom: 0 !important;
}
#sidebar.erp-sidebar .erp-search-wrap {
  position: relative;
  margin: 12px 12px 14px;
}
#sidebar.erp-sidebar .erp-search-wrap .mdi-magnify {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  color: #9aa3b2;
  font-size: 18px;
  pointer-events: none;
}
#sidebar.erp-sidebar .erp-search-wrap input {
  width: 100%;
  height: 42px;
  border: 1px solid #e5e7ee;
  border-radius: 10px;
  background: #fff;
  padding: 0 12px 0 38px;
  font-size: 12px;
  font-weight: 600;
  letter-spacing: 0.08em;
  color: #334155;
  outline: none;
  box-shadow: none;
}
#sidebar.erp-sidebar .erp-search-wrap input::placeholder {
  color: #b0b7c3;
  letter-spacing: 0.14em;
  font-weight: 600;
}
#sidebar.erp-sidebar .erp-search-wrap input:focus {
  border-color: #c5cad6;
}

#sidebar.erp-sidebar .nav-link {
  border-radius: 12px !important;
  white-space: normal !important;
}
#sidebar.erp-sidebar .menu-title {
  white-space: normal;
  line-height: 1.3;
}
/* Only the current page is filled with the theme color; open parents get a soft tint. */
#sidebar.erp-sidebar .erp-dashboard-link,
#sidebar.erp-sidebar .erp-module-toggle {
  display: flex !important;
  align-items: center !important;
  margin: 3px 12px !important;
  padding: 8px 12px !important;
  font-weight: 600 !important;
  font-size: 14px !important;
  background: #ffffff !important;
  color: #3a4252 !important;
  border: 1px solid #eceef3 !important;
  transition: background 0.15s ease, color 0.15s ease, border-color 0.15s ease;
}
#sidebar.erp-sidebar .erp-dashboard-link {
  margin-bottom: 6px !important;
}
#sidebar.erp-sidebar .erp-dashboard-link .menu-icon,
#sidebar.erp-sidebar .erp-module-toggle .menu-icon {
  font-size: 18px !important;
  margin-right: 10px !important;
  line-height: 1;
  color: #6b7280 !important;
  background: transparent !important;
}
#sidebar.erp-sidebar .erp-dashboard-link:hover,
#sidebar.erp-sidebar .erp-module-toggle:hover {
  background: rgba(var(--hr-primary-rgb, 230, 97, 54), 0.06) !important;
  border-color: rgba(var(--hr-primary-rgb, 230, 97, 54), 0.2) !important;
}
#sidebar.erp-sidebar .erp-chevron {
  margin-left: auto;
  font-size: 18px;
  color: #9aa3b2 !important;
  transition: transform 0.2s ease;
}
#sidebar.erp-sidebar .erp-module-toggle[aria-expanded="true"] .erp-chevron {
  transform: rotate(180deg);
}

/* Open module (e.g. HR): soft tint, no heavy fill */
#sidebar.erp-sidebar .erp-module-toggle[aria-expanded="true"] {
  background: rgba(var(--hr-primary-rgb, 230, 97, 54), 0.08) !important;
  border-color: rgba(var(--hr-primary-rgb, 230, 97, 54), 0.18) !important;
  color: var(--hr-primary-text, #e66136) !important;
}
#sidebar.erp-sidebar .erp-module-toggle[aria-expanded="true"] .menu-icon,
#sidebar.erp-sidebar .erp-module-toggle[aria-expanded="true"] .erp-chevron {
  color: var(--hr-primary-text, #e66136) !important;
}

/* Current page: Dashboard */
#sidebar.erp-sidebar .erp-dashboard-wrap.active > .nav-link,
#sidebar.erp-sidebar .erp-dashboard-link.active {
  background: var(--hr-primary, #e66136) !important;
  border-color: var(--hr-primary, #e66136) !important;
  color: var(--hr-on-primary, #ffffff) !important;
}
#sidebar.erp-sidebar .erp-dashboard-wrap.active > .nav-link .menu-icon,
#sidebar.erp-sidebar .erp-dashboard-wrap.active > .nav-link .menu-title,
#sidebar.erp-sidebar .erp-dashboard-link.active .menu-icon,
#sidebar.erp-sidebar .erp-dashboard-link.active .menu-title {
  color: var(--hr-on-primary, #ffffff) !important;
}

#sidebar.erp-sidebar .erp-group-list {
  padding: 2px 0 4px !important;
  margin: 0 !important;
}
#sidebar.erp-sidebar .erp-group-list > .nav-item > .nav-link {
  display: flex !important;
  align-items: center !important;
  background: transparent !important;
  color: #3a4252 !important;
  margin: 1px 12px !important;
  padding: 6px 12px !important;
  font-weight: 500 !important;
  font-size: 13.5px !important;
  line-height: 1.35 !important;
  border: none !important;
}
#sidebar.erp-sidebar .erp-group-list > .nav-item > .nav-link i.menu-icon {
  color: #6b7280 !important;
  font-size: 18px !important;
  margin-right: 10px !important;
  width: 18px;
  text-align: center;
}
#sidebar.erp-sidebar .erp-group-list > .nav-item > .nav-link .menu-arrow {
  color: #9aa3b2 !important;
}
#sidebar.erp-sidebar .nav .nav-item .nav-link i.menu-arrow:before {
  transform: rotate(0deg) !important;
}
#sidebar.erp-sidebar .nav .nav-item .nav-link[aria-expanded="true"] i.menu-arrow:before {
  transform: rotate(180deg) !important;
}
#sidebar.erp-sidebar .erp-group-list > .nav-item > .nav-link:hover {
  background: rgba(var(--hr-primary-rgb, 230, 97, 54), 0.06) !important;
  color: var(--hr-primary-text, #e66136) !important;
}
#sidebar.erp-sidebar .erp-group-list > .nav-item > .nav-link:hover i.menu-icon {
  color: var(--hr-primary-text, #e66136) !important;
}

/* Open parent menu (e.g. Employees): colored text + light tint */
#sidebar.erp-sidebar .erp-group-list > .nav-item > .nav-link[data-bs-toggle][aria-expanded="true"],
#sidebar.erp-sidebar .erp-group-list > .nav-item > .nav-link[data-bs-toggle].active {
  background: rgba(var(--hr-primary-rgb, 230, 97, 54), 0.08) !important;
  color: var(--hr-primary-text, #e66136) !important;
  font-weight: 600 !important;
}
#sidebar.erp-sidebar .erp-group-list > .nav-item > .nav-link[data-bs-toggle][aria-expanded="true"] i,
#sidebar.erp-sidebar .erp-group-list > .nav-item > .nav-link[data-bs-toggle].active i {
  color: var(--hr-primary-text, #e66136) !important;
}

/* Current page as a direct link (e.g. Staff Transfer, report items) */
#sidebar.erp-sidebar .erp-group-list > .nav-item > .nav-link:not([data-bs-toggle]).active {
  background: var(--hr-primary, #e66136) !important;
  color: var(--hr-on-primary, #ffffff) !important;
  font-weight: 600 !important;
}
#sidebar.erp-sidebar .erp-group-list > .nav-item > .nav-link:not([data-bs-toggle]).active i {
  color: var(--hr-on-primary, #ffffff) !important;
}

#sidebar.erp-sidebar .nav.sub-menu {
  padding: 1px 0 4px !important;
  margin: 0 !important;
  background: transparent !important;
}
#sidebar.erp-sidebar .nav.sub-menu .nav-item::before {
  display: none !important;
}
#sidebar.erp-sidebar .nav.sub-menu .nav-item .nav-link {
  color: #4b5568 !important;
  background: transparent !important;
  margin: 1px 12px 1px 36px !important;
  padding: 5px 12px !important;
  line-height: 1.35 !important;
  border-radius: 10px !important;
  font-size: 13px !important;
  font-weight: 500 !important;
  border-left: 2px solid transparent !important;
}
#sidebar.erp-sidebar .nav.sub-menu .nav-item .nav-link:hover {
  background: rgba(var(--hr-primary-rgb, 230, 97, 54), 0.06) !important;
  color: var(--hr-primary-text, #e66136) !important;
}

/* Current page inside a sub-menu (e.g. Manage Employee) */
#sidebar.erp-sidebar .nav.sub-menu .nav-item .nav-link.active {
  background: var(--hr-primary, #e66136) !important;
  color: var(--hr-on-primary, #ffffff) !important;
  font-weight: 600 !important;
}

#sidebar.erp-sidebar .erp-geofence > .nav-link {
  display: flex !important;
  align-items: center !important;
  margin: 6px 12px !important;
  padding: 7px 12px !important;
  color: #3a4252 !important;
  background: transparent !important;
  font-weight: 500 !important;
}
#sidebar.erp-sidebar .erp-geofence > .nav-link .menu-icon {
  color: #6b7280 !important;
  font-size: 18px !important;
  margin-right: 10px !important;
}
@media (min-width: 992px) {
  body.sidebar-icon-only #sidebar.erp-sidebar .erp-search-wrap,
  body.sidebar-icon-only #sidebar.erp-sidebar .erp-chevron,
  body.sidebar-icon-only #sidebar.erp-sidebar .menu-title {
    display: none !important;
  }
}
</style>

<nav class="sidebar sidebar-offcanvas erp-sidebar" id="sidebar">
  <div class="sidebar-mobile-header justify-content-between align-items-center d-lg-none px-4 py-3" style="border-bottom: 1px solid #f3f3f3; background: #fff;">
    <img src="<?= getCompanyLogo(); ?>" alt="logo" style="max-height: 35px; width: auto; max-width: 150px; object-fit: contain; margin: 0; flex: 0 0 auto;" />
    <a href="javascript:void(0)" data-bs-toggle="offcanvas" class="text-secondary text-decoration-none" style="width: auto; margin: 0; padding: 0; display: inline-flex; align-items: center; justify-content: center; flex: 0 0 auto;">
      <i class="mdi mdi-close fs-3 text-dark"></i>
    </a>
  </div>

  <div class="erp-search-wrap">
    <i class="mdi mdi-magnify"></i>
    <input type="text" id="erpSidebarSearch" placeholder="SEARCH..." autocomplete="off" aria-label="Search menu">
  </div>

  <ul class="nav">
    <li class="nav-item erp-module erp-dashboard-wrap">
      <a class="nav-link erp-dashboard-link" href="<?= base_url('/dashboard') ?>">
        <i class="mdi mdi-speedometer menu-icon"></i>
        <span class="menu-title">Dashboard</span>
      </a>
    </li>

    <li class="nav-item erp-module">
      <a class="nav-link erp-module-toggle erp-module-hr" data-bs-toggle="collapse" href="#erpHrMenu" aria-expanded="true" aria-controls="erpHrMenu">
        <i class="mdi mdi-badge-account-horizontal menu-icon"></i>
        <span class="menu-title">HR</span>
        <i class="mdi mdi-chevron-down erp-chevron"></i>
      </a>
      <div class="collapse show" id="erpHrMenu">
        <ul class="nav flex-column erp-group-list">

          <?php if ($isAdminOrHr || $isBranchAdmin): ?>
            <li class="nav-item erp-entry">
              <a class="nav-link" data-bs-toggle="collapse" href="#ui-basic" aria-expanded="false" aria-controls="ui-basic">
                <i class="menu-icon mdi mdi-account-multiple"></i>
                <span class="menu-title">Employees</span>
                <i class="menu-arrow"></i>
              </a>
              <div class="collapse" id="ui-basic">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item"> <a class="nav-link" href="/empview">Manage Employee</a></li>
                  <li class="nav-item"> <a class="nav-link" href="/employee">Add Employee</a></li>
                  <li class="nav-item"> <a class="nav-link" href="/gadget-issuance">Gadget Issuance</a></li>
                  <li class="nav-item"> <a class="nav-link" href="/organization-tree">Organization Tree</a></li>
                </ul>
              </div>
            </li>
          <?php elseif ($isDeptManager || $isEmployee): ?>
            <li class="nav-item erp-entry">
              <a class="nav-link" href="<?= base_url('/organization-tree') ?>">
                <i class="menu-icon mdi mdi-sitemap-outline"></i>
                <span class="menu-title">Organization Tree</span>
              </a>
            </li>
          <?php endif; ?>

          <?php if ($isAdminOrHr): ?>
            <li class="nav-item erp-entry">
              <a class="nav-link" data-bs-toggle="collapse" href="#branchesMenu" aria-expanded="false" aria-controls="branchesMenu">
                <i class="menu-icon mdi mdi-office-building"></i>
                <span class="menu-title">Branches</span>
                <i class="menu-arrow"></i>
              </a>
              <div class="collapse" id="branchesMenu">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item"> <a class="nav-link" href="<?= base_url('/branches') ?>">All Branches</a></li>
                  <li class="nav-item"> <a class="nav-link" href="<?= base_url('/branch-managers') ?>">Branch Managers</a></li>
                </ul>
              </div>
            </li>
            <li class="nav-item erp-entry">
              <a class="nav-link" data-bs-toggle="collapse" href="#departmentsMenu" aria-expanded="false" aria-controls="departmentsMenu">
                <i class="menu-icon mdi mdi-briefcase"></i>
                <span class="menu-title">Departments</span>
                <i class="menu-arrow"></i>
              </a>
              <div class="collapse" id="departmentsMenu">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item"> <a class="nav-link" href="<?= base_url('/departmentview') ?>">All Departments</a></li>
                  <li class="nav-item"> <a class="nav-link" href="<?= base_url('/department-managers') ?>">Department Managers</a></li>
                </ul>
              </div>
            </li>
            <li class="nav-item erp-entry">
              <a class="nav-link" href="<?= base_url('/staff-transfer') ?>">
                <i class="menu-icon mdi mdi-swap-horizontal"></i>
                <span class="menu-title">Staff Transfer</span>
              </a>
            </li>
          <?php endif; ?>

          <?php if ($isAdminOrHr || $isBranchAdmin): ?>
            <li class="nav-item erp-entry">
              <a class="nav-link" data-bs-toggle="collapse" href="#form-elements" aria-expanded="false" aria-controls="form-elements">
                <i class="menu-icon mdi mdi-card-text-outline"></i>
                <span class="menu-title">Attendance</span>
                <i class="menu-arrow"></i>
              </a>
              <div class="collapse" id="form-elements">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item"><a class="nav-link" href="/view-calendar">Manage Attendance</a></li>
                  <li class="nav-item"><a class="nav-link" href="/attendence">Attendance</a></li>
                </ul>
              </div>
            </li>
          <?php elseif ($isDeptManager): ?>
            <li class="nav-item erp-entry">
              <a class="nav-link" data-bs-toggle="collapse" href="#form-elements" aria-expanded="false" aria-controls="form-elements">
                <i class="menu-icon mdi mdi-card-text-outline"></i>
                <span class="menu-title">Attendance</span>
                <i class="menu-arrow"></i>
              </a>
              <div class="collapse" id="form-elements">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item"><a class="nav-link" href="/view-calendar">Dept Attendance</a></li>
                  <li class="nav-item"><a class="nav-link" href="/attendence">My Attendance</a></li>
                </ul>
              </div>
            </li>
          <?php elseif ($isEmployee): ?>
            <li class="nav-item erp-entry">
              <a class="nav-link" href="<?= base_url('/view-calendar') ?>">
                <i class="menu-icon mdi mdi-card-text-outline"></i>
                <span class="menu-title">Attendance</span>
              </a>
            </li>
          <?php endif; ?>

          <?php if ($isAdminOrHr || $isBranchAdmin): ?>
            <li class="nav-item erp-entry">
              <a class="nav-link" data-bs-toggle="collapse" href="#cha-rts" aria-expanded="false" aria-controls="cha-rts">
                <i class="menu-icon mdi mdi-calendar"></i>
                <span class="menu-title">Leaves</span>
                <i class="menu-arrow"></i>
              </a>
              <div class="collapse" id="cha-rts">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item"> <a class="nav-link" href="/leaveview">Review Branch Leaves</a></li>
                  <li class="nav-item"> <a class="nav-link" href="/addleave">Apply Leave</a></li>
                  <li class="nav-item"> <a class="nav-link" href="/employee-live-request">Employee Leave Request</a></li>
                </ul>
              </div>
            </li>
          <?php elseif ($isDeptManager): ?>
            <li class="nav-item erp-entry">
              <a class="nav-link" data-bs-toggle="collapse" href="#cha-rts" aria-expanded="false" aria-controls="cha-rts">
                <i class="menu-icon mdi mdi-calendar"></i>
                <span class="menu-title">Leaves</span>
                <i class="menu-arrow"></i>
              </a>
              <div class="collapse" id="cha-rts">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item"> <a class="nav-link" href="/leaveview">Review Team Leaves</a></li>
                  <li class="nav-item"> <a class="nav-link" href="/addleave">Apply Leave</a></li>
                </ul>
              </div>
            </li>
          <?php elseif ($isEmployee): ?>
            <li class="nav-item erp-entry">
              <a class="nav-link" href="<?= base_url('/leaveview') ?>">
                <i class="menu-icon mdi mdi-calendar"></i>
                <span class="menu-title">Leaves</span>
              </a>
            </li>
          <?php endif; ?>

          <?php if ($isAdminOrHr): ?>
            <li class="nav-item erp-entry">
              <a class="nav-link" data-bs-toggle="collapse" href="#tabs" aria-expanded="false" aria-controls="tabs">
                <i class="menu-icon mdi mdi-currency-inr"></i>
                <span class="menu-title">Payroll</span>
                <i class="menu-arrow"></i>
              </a>
              <div class="collapse" id="tabs">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item"> <a class="nav-link" href="/payrollview">Manage Payroll</a></li>
                  <li class="nav-item"> <a class="nav-link" href="/payroll">Add Payroll</a></li>
                  <li class="nav-item"> <a class="nav-link" href="/account-detail-view">Manage Account Detail</a></li>
                </ul>
              </div>
            </li>
            <li class="nav-item erp-entry">
              <a class="nav-link" data-bs-toggle="collapse" href="#tables" aria-expanded="false" aria-controls="tables">
                <i class="menu-icon mdi mdi-table"></i>
                <span class="menu-title lh-base">Recruitment &amp; Onboarding</span>
                <i class="menu-arrow"></i>
              </a>
              <div class="collapse" id="tables">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item"> <a class="nav-link" href="/jobview">Jobs</a></li>
                  <li class="nav-item"> <a class="nav-link" href="/candidateview">Candidates</a></li>
                  <li class="nav-item"> <a class="nav-link" href="/addinterview">Interviews Information</a></li>
                  <li class="nav-item"> <a class="nav-link" href="/onboardingview">Employees Onboarding</a></li>
                  <li class="nav-item"> <a class="nav-link" href="/offer-templates-view">Offer Letter Templates</a></li>
                </ul>
              </div>
            </li>
          <?php endif; ?>

          <?php if ($isAdminOrHr || $isBranchAdmin): ?>
            <li class="nav-item erp-entry">
              <a class="nav-link" data-bs-toggle="collapse" href="#ico-nsss" aria-expanded="false" aria-controls="ico-nsss">
                <i class="menu-icon mdi mdi-gauge"></i>
                <span class="menu-title">Performance</span>
                <i class="menu-arrow"></i>
              </a>
              <div class="collapse" id="ico-nsss">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item"> <a class="nav-link" href="/performanceview">Manage Performance</a></li>
                  <li class="nav-item"> <a class="nav-link" href="/performance">Add Reviews</a></li>
                  <li class="nav-item"> <a class="nav-link" href="/all-empof-month">Employee of the Month</a></li>
                </ul>
              </div>
            </li>
          <?php elseif ($isDeptManager): ?>
            <li class="nav-item erp-entry">
              <a class="nav-link" data-bs-toggle="collapse" href="#ico-nsss" aria-expanded="false" aria-controls="ico-nsss">
                <i class="menu-icon mdi mdi-gauge"></i>
                <span class="menu-title">Performance</span>
                <i class="menu-arrow"></i>
              </a>
              <div class="collapse" id="ico-nsss">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item"> <a class="nav-link" href="/performanceview">Team Performance</a></li>
                  <li class="nav-item"> <a class="nav-link" href="/performance">Add Review</a></li>
                </ul>
              </div>
            </li>
          <?php elseif ($isEmployee): ?>
            <li class="nav-item erp-entry">
              <a class="nav-link" href="/performanceview">
                <i class="menu-icon mdi mdi-gauge"></i>
                <span class="menu-title">Performance</span>
              </a>
            </li>
          <?php endif; ?>

          <?php if ($isAdminOrHr || $isBranchAdmin): ?>
            <li class="nav-item erp-entry">
              <a class="nav-link" data-bs-toggle="collapse" href="#auth" aria-expanded="false" aria-controls="auth">
                <i class="menu-icon mdi mdi-account-circle-outline"></i>
                <span class="menu-title">Training</span>
                <i class="menu-arrow"></i>
              </a>
              <div class="collapse" id="auth">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item"> <a class="nav-link" href="/trainingview">Manage Training</a></li>
                  <li class="nav-item"> <a class="nav-link" href="/training">Add Training</a></li>
                </ul>
              </div>
            </li>
          <?php elseif ($isDeptManager): ?>
            <li class="nav-item erp-entry">
              <a class="nav-link" data-bs-toggle="collapse" href="#auth" aria-expanded="false" aria-controls="auth">
                <i class="menu-icon mdi mdi-account-circle-outline"></i>
                <span class="menu-title">Training</span>
                <i class="menu-arrow"></i>
              </a>
              <div class="collapse" id="auth">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item"> <a class="nav-link" href="/trainingview">Team Training</a></li>
                  <li class="nav-item"> <a class="nav-link" href="/training">Add Training</a></li>
                </ul>
              </div>
            </li>
          <?php elseif ($isEmployee): ?>
            <li class="nav-item erp-entry">
              <a class="nav-link" href="/trainingview">
                <i class="menu-icon mdi mdi-account-circle-outline"></i>
                <span class="menu-title">Training</span>
              </a>
            </li>
          <?php endif; ?>

          <?php if ($isAdminOrHr || $isBranchAdmin || $isDeptManager): ?>
            <li class="nav-item erp-entry">
              <a class="nav-link" data-bs-toggle="collapse" href="#task" aria-expanded="false" aria-controls="task">
                <i class="menu-icon mdi mdi-book-open"></i>
                <span class="menu-title">Task</span>
                <i class="menu-arrow"></i>
              </a>
              <div class="collapse" id="task">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item"> <a class="nav-link" href="/taskview">Manage Task</a></li>
                  <li class="nav-item"> <a class="nav-link" href="/task">Add Task</a></li>
                  <li class="nav-item"> <a class="nav-link" href="/all_subtask">SubTask List</a></li>
                </ul>
              </div>
            </li>
          <?php elseif ($isEmployee): ?>
            <li class="nav-item erp-entry">
              <a class="nav-link" data-bs-toggle="collapse" href="#subtasks" aria-expanded="false" aria-controls="subtasks">
                <i class="menu-icon mdi mdi-book-open"></i>
                <span class="menu-title">Task</span>
                <i class="menu-arrow"></i>
              </a>
              <div class="collapse" id="subtasks">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item"> <a class="nav-link" href="/taskview">Task</a></li>
                  <li class="nav-item"> <a class="nav-link" href="/all_subtask">SubTask List</a></li>
                </ul>
              </div>
            </li>
          <?php endif; ?>

          <?php if ($isAdminOrHr || $isBranchAdmin): ?>
            <li class="nav-item erp-entry">
              <a class="nav-link" data-bs-toggle="collapse" href="#expense" aria-expanded="false" aria-controls="expense">
                <i class="menu-icon mdi mdi-wallet"></i>
                <span class="menu-title">Expense</span>
                <i class="menu-arrow"></i>
              </a>
              <div class="collapse" id="expense">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item"> <a class="nav-link" href="<?= base_url('expenses') ?>">Expense List</a></li>
                  <li class="nav-item"> <a class="nav-link" href="<?= base_url('expenses/create') ?>">Add Expense</a></li>
                  <li class="nav-item"> <a class="nav-link" href="<?= base_url('expenses/categories') ?>">Expense Categories</a></li>
                </ul>
              </div>
            </li>
          <?php elseif ($isEmployee): ?>
            <li class="nav-item erp-entry">
              <a class="nav-link" href="<?= base_url('expenses') ?>">
                <i class="menu-icon mdi mdi-wallet"></i>
                <span class="menu-title">Expense</span>
              </a>
            </li>
          <?php endif; ?>

          <?php if ($isAdminOrHr || $isBranchAdmin): ?>
            <li class="nav-item erp-entry">
              <a class="nav-link" data-bs-toggle="collapse" href="#announcementsMenu" aria-expanded="false" aria-controls="announcementsMenu">
                <i class="menu-icon mdi mdi-bullhorn"></i>
                <span class="menu-title">Announcements</span>
                <i class="menu-arrow"></i>
              </a>
              <div class="collapse" id="announcementsMenu">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item"> <a class="nav-link" href="<?= base_url('announcements/admin') ?>">Manage Announcements</a></li>
                  <li class="nav-item"> <a class="nav-link" href="<?= base_url('announcements/create') ?>">Add Announcement</a></li>
                </ul>
              </div>
            </li>
          <?php elseif ($isDeptManager || $isEmployee): ?>
            <li class="nav-item erp-entry">
              <a class="nav-link" href="<?= base_url('announcements') ?>">
                <i class="menu-icon mdi mdi-bullhorn"></i>
                <span class="menu-title">Announcements</span>
              </a>
            </li>
          <?php endif; ?>

          <?php if ($isAdminOrHr): ?>
            <li class="nav-item erp-entry">
              <a class="nav-link" data-bs-toggle="collapse" href="#complaintsMenu" aria-expanded="false" aria-controls="complaintsMenu">
                <i class="menu-icon mdi mdi-message-alert"></i>
                <span class="menu-title">Complaints &amp; Feedback</span>
                <i class="menu-arrow"></i>
              </a>
              <div class="collapse" id="complaintsMenu">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item"> <a class="nav-link" href="<?= base_url('complaints/admin') ?>">Manage Complaints</a></li>
                  <li class="nav-item"> <a class="nav-link" href="<?= base_url('complaints/create') ?>">Add Complaint</a></li>
                </ul>
              </div>
            </li>
          <?php elseif ($isBranchAdmin || $isDeptManager || $isEmployee): ?>
            <li class="nav-item erp-entry">
              <a class="nav-link" href="<?= base_url('complaints') ?>">
                <i class="menu-icon mdi mdi-message-alert"></i>
                <span class="menu-title">Complaints &amp; Feedback</span>
              </a>
            </li>
          <?php endif; ?>

          <?php if ($isAdminOrHr): ?>
            <li class="nav-item erp-entry">
              <a class="nav-link" data-bs-toggle="collapse" href="#eomletter" aria-expanded="false" aria-controls="eomletter">
                <i class="menu-icon mdi mdi-star-circle"></i>
                <span class="menu-title">EOM</span>
                <i class="menu-arrow"></i>
              </a>
              <div class="collapse" id="eomletter">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item"> <a class="nav-link" href="/all-empof-month">All EOM</a></li>
                  <li class="nav-item"> <a class="nav-link" href="/addemp-month-performance">EOM Generate</a></li>
                  <li class="nav-item"> <a class="nav-link" href="/emp-month-view">EOM Templates</a></li>
                </ul>
              </div>
            </li>
            <li class="nav-item erp-entry">
              <a class="nav-link" data-bs-toggle="collapse" href="#exp" aria-expanded="false" aria-controls="exp">
                <i class="menu-icon mdi mdi-file-account-outline"></i>
                <span class="menu-title">Experience Letter</span>
                <i class="menu-arrow"></i>
              </a>
              <div class="collapse" id="exp">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item"> <a class="nav-link" href="/exprience-templates-view">Experience Template</a></li>
                  <li class="nav-item"> <a class="nav-link" href="/generate-letter">Generate Letter</a></li>
                  <li class="nav-item"> <a class="nav-link" href="/increment-letter-template">Increment / Promotion Template</a></li>
                </ul>
              </div>
            </li>
          <?php endif; ?>

          <?php if ($isEmployee): ?>
            <li class="nav-item erp-entry">
              <a class="nav-link" href="<?= base_url('/company-rules') ?>">
                <i class="menu-icon mdi mdi-file-document"></i>
                <span class="menu-title">Company Rule</span>
              </a>
            </li>
            <li class="nav-item erp-entry">
              <a class="nav-link" href="<?= base_url('/company-holidays') ?>">
                <i class="menu-icon mdi mdi-calendar-star"></i>
                <span class="menu-title">Company Holidays</span>
              </a>
            </li>
          <?php endif; ?>

          <li class="nav-item erp-entry">
            <a class="nav-link" data-bs-toggle="collapse" href="#resignationMenu" aria-expanded="false" aria-controls="resignationMenu">
              <i class="menu-icon mdi mdi-exit-to-app"></i>
              <span class="menu-title">Resignation &amp; Exit</span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="resignationMenu">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item"><a class="nav-link" href="<?= base_url('/resignation') ?>">My Resignation</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= base_url('/resignation/my-handover') ?>">My Handover Tasks</a></li>
                <?php if ($isAdminOrHr || $isBranchAdmin || $isDeptManager): ?>
                  <li class="nav-item"><a class="nav-link" href="<?= base_url('/resignation/manager') ?>">Manager Approvals</a></li>
                  <li class="nav-item"><a class="nav-link" href="<?= base_url('/resignation/clearance') ?>">Clearance Approvals</a></li>
                <?php endif; ?>
                <?php if ($isAdminOrHr): ?>
                  <li class="nav-item"><a class="nav-link" href="<?= base_url('/resignation/hr') ?>">All Resignations (HR)</a></li>
                <?php endif; ?>
              </ul>
            </div>
          </li>

        </ul>
      </div>
    </li>

    <?php if ($isAdminOrHr): ?>
      <li class="nav-item erp-module">
        <a class="nav-link erp-module-toggle erp-module-reports" data-bs-toggle="collapse" href="#erpReportsMenu" aria-expanded="false" aria-controls="erpReportsMenu">
          <i class="mdi mdi-chart-box-outline menu-icon"></i>
          <span class="menu-title">Reports</span>
          <i class="mdi mdi-chevron-down erp-chevron"></i>
        </a>
        <div class="collapse" id="erpReportsMenu">
          <ul class="nav flex-column erp-group-list">
            <li class="nav-item erp-entry"><a class="nav-link" href="/empReport"><i class="menu-icon mdi mdi-account"></i><span class="menu-title">Employee Report</span></a></li>
            <li class="nav-item erp-entry"><a class="nav-link" href="/leaveReport"><i class="menu-icon mdi mdi-calendar"></i><span class="menu-title">Leave Report</span></a></li>
            <li class="nav-item erp-entry"><a class="nav-link" href="/salaryReport"><i class="menu-icon mdi mdi-currency-inr"></i><span class="menu-title">Payrolls Report</span></a></li>
            <li class="nav-item erp-entry"><a class="nav-link" href="/attendanceReport"><i class="menu-icon mdi mdi-card-text-outline"></i><span class="menu-title">Attendance Report</span></a></li>
            <li class="nav-item erp-entry"><a class="nav-link" href="/performReport"><i class="menu-icon mdi mdi-gauge"></i><span class="menu-title">Performance Report</span></a></li>
          </ul>
        </div>
      </li>

      <li class="nav-item erp-module">
        <a class="nav-link erp-module-toggle erp-module-settings" data-bs-toggle="collapse" href="#erpSettingsMenu" aria-expanded="false" aria-controls="erpSettingsMenu">
          <i class="mdi mdi-cog-outline menu-icon"></i>
          <span class="menu-title">Settings</span>
          <i class="mdi mdi-chevron-down erp-chevron"></i>
        </a>
        <div class="collapse" id="erpSettingsMenu">
          <ul class="nav flex-column erp-group-list">
            <li class="nav-item erp-entry"><a class="nav-link" href="/locationview"><i class="menu-icon mdi mdi-map-marker"></i><span class="menu-title">Job Location</span></a></li>
            <li class="nav-item erp-entry"><a class="nav-link" href="/addressview"><i class="menu-icon mdi mdi-map"></i><span class="menu-title">Job Addresses</span></a></li>
            <li class="nav-item erp-entry"><a class="nav-link" href="/SMTPemail"><i class="menu-icon mdi mdi-email-outline"></i><span class="menu-title">SMTP Mail</span></a></li>
            <li class="nav-item erp-entry"><a class="nav-link" href="/view-rules"><i class="menu-icon mdi mdi-file-document-outline"></i><span class="menu-title">Company Rules View</span></a></li>
            <li class="nav-item erp-entry"><a class="nav-link" href="/holidays"><i class="menu-icon mdi mdi-calendar-star"></i><span class="menu-title">Holidays</span></a></li>
            <li class="nav-item erp-entry"><a class="nav-link" href="/notification-settings"><i class="menu-icon mdi mdi-bell-outline"></i><span class="menu-title">Push Notifications</span></a></li>
            <li class="nav-item erp-entry"><a class="nav-link" href="/offer-templates-view"><i class="menu-icon mdi mdi-file-document"></i><span class="menu-title">Templates</span></a></li>
            <li class="nav-item erp-entry"><a class="nav-link" href="/digital-signature"><i class="menu-icon mdi mdi-draw"></i><span class="menu-title">Digital Signature</span></a></li>
            <li class="nav-item erp-entry"><a class="nav-link" href="/theme-settings"><i class="menu-icon mdi mdi-palette-outline"></i><span class="menu-title">Theme Color</span></a></li>
          </ul>
        </div>
      </li>

      <li class="nav-item erp-module">
        <a class="nav-link erp-module-toggle erp-module-masters" data-bs-toggle="collapse" href="#erpMastersMenu" aria-expanded="false" aria-controls="erpMastersMenu">
          <i class="mdi mdi-database-outline menu-icon"></i>
          <span class="menu-title">Masters</span>
          <i class="mdi mdi-chevron-down erp-chevron"></i>
        </a>
        <div class="collapse" id="erpMastersMenu">
          <ul class="nav flex-column erp-group-list">
            <li class="nav-item erp-entry"><a class="nav-link" href="/countryview"><i class="menu-icon mdi mdi-earth"></i><span class="menu-title">Country</span></a></li>
            <li class="nav-item erp-entry"><a class="nav-link" href="/stateView"><i class="menu-icon mdi mdi-map-marker-radius"></i><span class="menu-title">State</span></a></li>
            <li class="nav-item erp-entry"><a class="nav-link" href="/cityview"><i class="menu-icon mdi mdi-city"></i><span class="menu-title">City</span></a></li>
            <li class="nav-item erp-entry"><a class="nav-link" href="/designationview"><i class="menu-icon mdi mdi-briefcase-outline"></i><span class="menu-title">Designations</span></a></li>
            <li class="nav-item erp-entry"><a class="nav-link" href="/leavetypeview"><i class="menu-icon mdi mdi-calendar-check"></i><span class="menu-title">Leave Types</span></a></li>
          </ul>
        </div>
      </li>
    <?php endif; ?>

    <li class="nav-item erp-module erp-geofence">
      <a class="nav-link" href="<?= base_url('/geofence/test') ?>">
        <i class="menu-icon mdi mdi-crosshairs-gps"></i>
        <span class="menu-title">Geofence Test</span>
      </a>
    </li>
  </ul>
</nav>

<script>
(function () {
  var input = document.getElementById('erpSidebarSearch');
  if (!input) return;

  input.addEventListener('input', function () {
    var query = this.value.trim().toLowerCase();
    var modules = document.querySelectorAll('#sidebar .erp-module');

    modules.forEach(function (module) {
      if (module.classList.contains('erp-dashboard-wrap') || module.classList.contains('erp-geofence')) {
        var label = (module.textContent || '').toLowerCase();
        module.style.display = (!query || label.indexOf(query) !== -1) ? '' : 'none';
        return;
      }

      var entries = module.querySelectorAll('.erp-entry');
      var any = false;
      entries.forEach(function (entry) {
        var text = (entry.textContent || '').toLowerCase();
        var match = !query || text.indexOf(query) !== -1;
        entry.style.display = match ? '' : 'none';
        if (match) any = true;
      });

      var title = '';
      var toggle = module.querySelector('.erp-module-toggle .menu-title');
      if (toggle) title = (toggle.textContent || '').toLowerCase();
      if (query && title.indexOf(query) !== -1) {
        entries.forEach(function (entry) { entry.style.display = ''; });
        any = true;
      }

      module.style.display = any ? '' : 'none';
      var panel = module.querySelector(':scope > .collapse');
      if (query && any && panel) panel.classList.add('show');
    });
  });
})();
</script>
