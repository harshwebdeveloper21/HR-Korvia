<?php

use CodeIgniter\Router\RouteCollection;
use App\Controllers\AuthController;
use SebastianBergmann\CodeCoverage\Report\Xml\Report;

/**
 * @var RouteCollection $routes
 */



// ═══════════════════════════════════════════════════════════════════════════
// MULTI-BRANCH ROUTES
// ═══════════════════════════════════════════════════════════════════════════

// ── Branch CRUD (Admin only) ─────────────────────────────────────────────
$routes->get('/branches',                    'api\BranchController::index',   ['filter' => 'admin_only']);
$routes->get('/branches/create',             'api\BranchController::create',  ['filter' => 'admin_only']);
$routes->get('/branches/edit/(:num)',        'api\BranchController::edit/$1', ['filter' => 'admin_only']);
$routes->get('/branches/assign-hr/(:num)',   'api\BranchController::assignHrPage/$1', ['filter' => 'admin_only']);

$routes->get('api/branches',                'api\BranchController::list',    ['filter' => 'admin_only']);
$routes->get('api/branches/list-all',       'api\BranchController::listAll');
$routes->get('api/branches/(:num)',         'api\BranchController::show/$1', ['filter' => 'admin_only']);
$routes->post('api/branches',              'api\BranchController::store',   ['filter' => 'admin_only']);
$routes->put('api/branches/(:num)',        'api\BranchController::update/$1', ['filter' => 'admin_only']);
$routes->delete('api/branches/(:num)',     'api\BranchController::delete/$1', ['filter' => 'admin_only']);
$routes->post('api/branches/assign-hr',    'api\BranchController::assignHr', ['filter' => 'admin_only']);
$routes->post('api/branches/toggle-transfer-permission', 'api\BranchController::toggleTransferPermission', ['filter' => 'admin_only']);
$routes->post('api/branches/set-active',   'api\BranchController::setActiveBranch');

// ── Branch Rules ─────────────────────────────────────────────────────────
$routes->get('/branch-rules/edit',            'api\BranchRulesController::editPage');
$routes->get('/branch-rules/edit/(:num)',     'api\BranchRulesController::editPage/$1');
$routes->get('api/branch-rules/get',          'api\BranchRulesController::get');
$routes->post('api/branch-rules/store',       'api\BranchRulesController::store');

// ── Staff Transfers ───────────────────────────────────────────────────────
$routes->get('/staff-transfer',               'api\StaffTransferController::page');
$routes->post('api/staff-transfer/initiate',  'api\StaffTransferController::initiate');
$routes->get('api/staff-transfer/history',    'api\StaffTransferController::history');
$routes->get('api/staff-transfer/eligible-staff', 'api\StaffTransferController::eligibleStaff');

// ═══════════════════════════════════════════════════════════════════════════

$routes->get("/state", "api\StateController::creates");
$routes->get("/stateView", "api\StateController::display");
$routes->get("payroll/salary", "api\PayrollController::groupsalaryPage");
//account_deatils employee
$routes->get("/accountdetail", "api\AccountController::index");
$routes->get("/account-detail-view", "api\AccountController::display");
$routes->get("edit/detail/(:num)", 'api\AccountController::EditPage/$1');
//dawonload salary slip
$routes->get(
    "payroll/download-slip/(:num)",
    'api\PayrollController::downloadSlip/$1',
);
$routes->post(
    "api/payroll/downloadMultiple",
    "api\PayrollController::downloadMultiple",
);
$routes->post(
    "api/payroll/downloadMultipleByIds",
    "api\PayrollController::downloadMultipleByIds",
);

$routes->get("api/payroll/getEmployees", "api\PayrollController::getEmployees");
$routes->post("api/payroll/downloadYearly", "api\PayrollController::downloadYearly");

$routes->get("payroll/salary-details", "api\PayrollController::salaryDetails");
$routes->post("api/payroll/get-extra-day-details", "api\PayrollController::getExtraDayDetails");


//chat
$routes->get("/chat", "api\ChatController::view");
// SMTP
$routes->post("send-email", "EmailController::sendTestEmail");
$routes->get("smtp-settings", "api\SmtpController::getSmtpSettings");
$routes->post("smtp-settings/update", "api\SmtpController::updateSmtpSettings");
$routes->get("/welcome_mail", "api\SmtpController::display");
$routes->get("/SMTPemail", "api\SmtpController::view");

$routes->get("api/smtp/getSmtpSettings", "api\SmtpController::getSmtpSettings");
$routes->post(
    "api/smtp/updateSmtpSettings",
    "api\SmtpController::updateSmtpSettings",
);

// Location Settings Routes (for check-in location verification)
$routes->get(
    "api/location-settings/get",
    "api\LocationSettingsController::getSettings",
);
$routes->post(
    "api/location-settings/update",
    "api\LocationSettingsController::updateSettings",
);
$routes->get(
    "api/location-settings/checkin",
    "api\LocationSettingsController::getSettingsForCheckIn",
);
$routes->get(
    'api/location/detect',
    'api\LocationSettingsController::getLocationByIP',
);

$routes->get(
    "notification-settings/",
    "api\NotificationSettingsController::view",
);
$routes->get(
    "api/notification-settings/get",
    "api\NotificationSettingsController::getSettings",
);
$routes->post(
    "api/notification-settings/update",
    "api\NotificationSettingsController::updateSettings",
);

$routes->get("theme-settings", "api\ThemeSettingsController::view");
$routes->get("api/theme-settings/get", "api\ThemeSettingsController::getSettings");
$routes->post("api/theme-settings/update", "api\ThemeSettingsController::updateSettings");

$routes->get("/job/display/(:num)", 'api\JobController::singlejob/$1');
$routes->get(
    "/interview/display/(:num)",
    'api\InterviewController::singlejob/$1',
);
$routes->get(
    "/candidate/display/(:num)",
    'api\CandidateController::singlejob/$1',
);
$routes->get(
    "/onboarding/display/(:num)",
    'api\OnboardingController::singlejob/$1',
);
$routes->get(
    "api/get-candidate-job/(:num)",
    'api\InterviewController::getCandidateJob/$1',
);

$routes->put(
    "/api/interviews/(:num)/status",
    'api\InterviewController::updateStatus/$1',
);
$routes->put(
    "/api/interviews/(:num)/convert",
    'api\InterviewController::updateConvertToEmployee/$1',
);
$routes->get(
    "api/interview/export",
    'api\InterviewController::exportExcel'
);
$routes->get(
    "api/assessment/export",
    'InterviewAssessments::exportExcel'
);

$routes->get("/dashboard", "api\AdminController::index");
$routes->get("/geofence/test", "api\GeofenceController::testPage");
$routes->get(
    "/auto-leave-daily",
    "api\AdminController::triggerAutoLeaveOnceDaily",
);
$routes->get("api/admin/getTaskData", "api\AdminController::getTaskData");

$routes->get("/change_password", "api\AuthController::display");
$routes->post("api/changePassword", "api\AuthController::changePassword");
$routes->get("/forgot_password", "api\ForgotPasswordController::index");
$routes->get("/reset_password", "api\ForgotPasswordController::display");
$routes->post(
    "api/resetPassword",
    "api\ForgotPasswordController::resetPassword",
);
$routes->post(
    "api/sendResetLink",
    "api\ForgotPasswordController::sendResetLink",
);

$routes->get(
    "api/performance/yearly",
    "api\AdminController::getYearlyPerformanceData",
);

$routes->get("/", "api\AuthController::index");
$routes->get("/login", "api\AuthController::index");

$routes->get("/employee", "api\EmployeeController::creates"); // New employee creation
$routes->get("employee/(:num)", 'api\EmployeeController::creates/$1'); // Existing employee details

$routes->get("/employee/profile/(:num)", 'api\EmployeeController::profile/$1');
$routes->get("/employee/profile/view/(:num)", 'api\EmployeeController::profileview/$1');
$routes->get("/employee/details/(:num)", 'api\EmployeeController::details/$1');

$routes->get("/empview", "api\EmployeeController::display");
$routes->get("/employee-live-request", "api\EmployeeController::liveRequest");

$routes->get("/job", "api\JobController::creates");
$routes->get("/jobview", "api\JobController::display");
$routes->get("/applyjob", "api\JobController::applyjob");
$routes->get("api/applyjob", "api\JobController::applyget");

$routes->get("/interviews", "api\InterviewController::creates");
$routes->get("/interviews/(:num)", 'api\InterviewController::creates/$1');
$routes->get("/interview/pdf/(:num)", 'api\InterviewController::pdf/$1');
$routes->get("/addinterview", "api\InterviewController::display");

$routes->get("/candidate", "api\CandidateController::create");
$routes->get("/candidate/(:num)", 'api\CandidateController::create/$1');
$routes->get("/candidateview", "api\CandidateController::display");
$routes->get("/assessment", "InterviewAssessments::index");
$routes->get("/assessment/create", "InterviewAssessments::create");
$routes->post("/assessment/store", "InterviewAssessments::store");
$routes->get("/assessment/edit/(:num)", "InterviewAssessments::edit/$1");
$routes->post("/assessment/update/(:num)", "InterviewAssessments::update/$1");
$routes->get("/assessment/delete/(:num)", "InterviewAssessments::delete/$1");
$routes->get("/assessment/view/(:num)", "InterviewAssessments::show/$1");
$routes->get("/assessment/pdf/(:num)", "InterviewAssessments::pdf/$1");

$routes->get("/candidate-documents", "CandidateDocumentsController::index");
$routes->post("/candidate-documents/upload", "CandidateDocumentsController::upload");
$routes->post("/candidate-documents/remove", "CandidateDocumentsController::remove");

$routes->get("/onboarding", "api\OnboardingController::create");
$routes->get("/onboarding/(:num)", 'api\OnboardingController::create/$1');
$routes->get("/onboardingview", "api\OnboardingController::display");

$routes->get("/performance", "api\PerformanceController::creates");
$routes->get("/performance/(:num)", 'api\PerformanceController::creates/$1');
$routes->get("/performanceview", "api\PerformanceController::display");
$routes->get(
    "/performance/profile/(:num)",
    'api\PerformanceController::profilePage/$1',
);
$routes->get(
    "/performance/details/(:num)",
    'api\PerformanceController::getProfile/$1',
);

$routes->get("/notifications", "api\NotificationsController::display");
$routes->post(
    "/notifications/clearAll",
    "api\NotificationsController::clearAll",
);

$routes->get("/training", "api\TrainingController::create");
$routes->get("/training/get/(:num)", 'api\TrainingController::create/$1');
$routes->get(
    "/training/profile/(:num)",
    'api\TrainingController::profilePage/$1',
);
$routes->get(
    "/training/details/(:num)",
    'api\TrainingController::getProfile/$1',
);
$routes->get("/trainingview", "api\TrainingController::display");

$routes->get("/task", "api\TaskController::creates");
$routes->get("/task/(:num)", 'api\TaskController::creates/$1');
$routes->get("/taskview", "api\TaskController::displays");
$routes->get("/task/profile/(:num)", 'api\TaskController::profilePage/$1');
$routes->get("/task/details/(:num)", 'api\TaskController::getProfile/$1');

// SubTask Routes

$routes->get("/add_subtask", "api\SubTaskController::CreatePage");
$routes->get("/all_subtask", "api\SubTaskController::displays");
$routes->get("/subtask/edit/(:num)", 'api\SubTaskController::EditPage/$1');

$routes->get("/payroll", "api\PayrollController::page");
$routes->get("/payrollview", "api\PayrollController::display");
$routes->get("/payroll/(:num)", 'api\PayrollController::page/$1');
$routes->get(
    "/payroll/profile/(:num)",
    'api\PayrollController::profilePage/$1',
);
$routes->get("/payroll/details/(:num)", 'api\PayrollController::getProfile/$1');

$routes->get("/city", "api\CityController::creates");
$routes->get("/cityview", "api\CityController::display");

$routes->get("/country", "api\CountryController::creates");
$routes->get("/countryview", "api\CountryController::display");

$routes->get("/designation", "api\DesignationController::creates");
$routes->get("/designationview", "api\DesignationController::display");

$routes->get("/department", "api\DepartmentController::creates");
$routes->get("/departmentview", "api\DepartmentController::display");

//job location
$routes->get("/joblocation", "api\JoblocationController::creates");
$routes->get("/locationview", "api\JoblocationController::display");

//job location addresses
$routes->get("/offficeaddress", "api\JobaddressController::creates");
$routes->get("/addressview", "api\JobaddressController::display");

$routes->get("/leave_type", "api\LeaveTypeController::creates");
$routes->get("/leavetypeview", "api\LeaveTypeController::display");

$routes->get("/empReport", "api\EmployeeReportController::create");
$routes->post("report/fetchEmployeesByDepartment", "api\EmployeeReportController::fetchEmployeesByDepartment");


$routes->get("/leaveReport", "api\LeaveReportController::create");

$routes->get("/salaryReport", "api\PayrollReportController::create");

$routes->get("/attendanceReport", "api\AttendanceReportController::create");
$routes->get("/get-employees", "api\AttendanceController::getEmployees");
$routes->get("api/getEmployees", "api\LeaveController::getEmployees");
$routes->get("api/get-leaves", "api\LeaveController::getLeaves");
$routes->post(
    "api/leave/update/(:num)",
    'api\LeaveController::updateStatus/$1',
);

// Weekly Off Management
$routes->get("api/weekly-off/employees", "api\WeeklyOffController::getEmployees");
$routes->post("api/weekly-off/assign", "api\WeeklyOffController::assign");

$routes->get("/performReport", "api\PerformanceReportController::create");

// OfferLater

$routes->get(
    "/add-offer-templates",
    "api\OfferLetterTemplateController::index",
);
$routes->get(
    "/offer-templates-view",
    "api\OfferLetterTemplateController::view",
);

$routes->get(
    "template/(:num)",
    'api\OfferLetterTemplateController::EditPage/$1',
);
$routes->get(
    "template/view/(:num)",
    'api\OfferLetterTemplateController::templateView/$1',
);

$routes->get(
    "offer-templates/preview-pdf/(:num)",
    'api\OfferLetterTemplateController::generateOfferLetter/sample/$1',
);

$routes->get(
    "offer-letter/generate/(:num)/(:num)",
    'api\OfferLetterTemplateController::generateOfferLetter/$1/$2',
);

//exprience letter
$routes->get(
    "/add-exprience-templates",
    "api\ExprienceLetterController::index",
);
$routes->get(
    "/exprience-templates-view",
    "api\ExprienceLetterController::view",
);
$routes->get(
    "edit/template/(:num)",
    'api\ExprienceLetterController::EditPage/$1',
);
$routes->get(
    "exprience/view/(:num)",
    'api\ExprienceLetterController::templateView/$1',
);
$routes->get(
    "/add-emp-exprience",
    "api\ExprienceLetterController::addemployeePage",
);

$routes->get("/generate-letter", "api\ExprienceLetterController::display");

// Employee Of The Month PDF

$routes->get("/add-empof-month", "api\EmployeeOfTheMonthController::index");
$routes->get("/emp-month-view", "api\EmployeeOfTheMonthController::view");
$routes->get(
    "empof_month/(:num)",
    'api\EmployeeOfTheMonthController::EditPage/$1',
);
$routes->get(
    "empof_month/view/(:num)",
    'api\EmployeeOfTheMonthController::templateView/$1',
);

// Employee Of the month Performance

$routes->get(
    "/addemp-month-performance",
    "api\EmployeeOftheMonthPerformance::view",
);
$routes->get(
    "/all-empof-month",
    "api\EmployeeOftheMonthPerformance::AllEmpOfMonth",
);
$routes->get(
    "api/employee-of-month/generate-pdf/(:num)/(:num)",
    'api\EmployeeOftheMonthPerformance::generatePerformancePdf/$1/$2',
);

// Report
$routes->post(
    "report/fetchEmployeeReport",
    "api\EmployeeReportController::fetchEmployeeReport",
);
$routes->post("report/fetchLeaveReport", "api\LeaveReportController::fetchLeaveReport");
$routes->post('report/fetchAttendanceEmployeesByDepartment', 'api\AttendanceReportController::fetchEmployeesByDepartment');
$routes->post(
    "report/fetchPayrollReport",
    "api\PayrollReportController::fetchPayrollReport",
);
$routes->post(
    "report/fetchAttendanceReport",
    "api\AttendanceReportController::fetchAttendanceReport",
);
$routes->post(
    "report/fetchPerformanceReport",
    "api\PerformanceReportController::fetchtPerformanceReport",
);
$routes->get("taskcontroller/getTasks", "api\TaskController::getTasks");

$routes->get("/dashboard", "api\AdminController::index");
$routes->get("/profile", "api\AdminController::profile");

// company logo
$routes->get("/logopage", "api\CompanyLogoController::index");
$routes->post("api/updateLogo", "api\CompanyLogoController::updateLogo");
$routes->get("api/getCompanyLogo", "api\CompanyLogoController::getCompanyLogo");

$routes->get("/login", "api\AuthController::index");
$routes->post("login", "api\AuthController::login");
$routes->get('api/get-face-photo-unauth', 'api\AuthController::getFacePhotoUnauth');
$routes->get('api/get-face-data', 'api\AuthController::getFaceData');
$routes->post('api/face-login', 'api\AuthController::faceLogin');
$routes->get("company-holidays", "api\HolidaysController::company_holidays");
$routes->get("holidays", "api\HolidaysController::display_holidays");
$routes->get("add", "api\HolidaysController::add_holidays");
$routes->get("edit-holiday/(:num)", 'api\HolidaysController::edit/$1');

$routes->get("view-rules", "api\CompanyRulesController::display_rules");
$routes->get("/creates-rules", "api\CompanyRulesController::create_rules");
$routes->get("/company-rules", "api\CompanyRulesController::company_rules");

// Announcement Routes
$routes->get("announcements", "api\AnnouncementController::index");
$routes->get("announcements/admin", "api\AnnouncementController::adminIndex", ["filter" => "auth:admin,hr,branch_admin"]);
$routes->get("announcements/create", "api\AnnouncementController::create", ["filter" => "auth:admin,hr,branch_admin"]);
$routes->post("announcements/store", "api\AnnouncementController::store", ["filter" => "auth:admin,hr,branch_admin"]);
$routes->get("announcements/edit/(:num)", "api\AnnouncementController::edit/$1", ["filter" => "auth:admin,hr,branch_admin"]);
$routes->post("announcements/update/(:num)", "api\AnnouncementController::update/$1", ["filter" => "auth:admin,hr,branch_admin"]);
$routes->delete("announcements/delete/(:num)", "api\AnnouncementController::delete/$1", ["filter" => "auth:admin,hr,branch_admin"]);
$routes->post("announcements/mark-read/(:num)", "api\AnnouncementController::markRead/$1");

// Complaint & Feedback Routes
$routes->get("complaints", "api\ComplaintsController::index", ["filter" => "auth"]);
$routes->get("complaints/create", "api\ComplaintsController::create", ["filter" => "auth"]);
$routes->post("complaints/store", "api\ComplaintsController::store", ["filter" => "auth"]);
$routes->get("complaints/admin", "api\ComplaintsController::adminIndex", ["filter" => "auth:admin,hr"]);
$routes->get("complaints/update/(:num)", "api\ComplaintsController::updateView/$1", ["filter" => "auth:admin,hr"]);
$routes->get("api/complaints/list", "api\ComplaintsController::list", ["filter" => "auth:admin,hr"]);
$routes->get("api/complaints/show/(:num)", "api\ComplaintsController::show/$1", ["filter" => "auth:admin,hr"]);
$routes->post("api/complaints/update/(:num)", "api\ComplaintsController::updateComplaint/$1", ["filter" => "auth:admin,hr"]);
$routes->post("api/complaints/store", "api\ComplaintsController::store", ["filter" => "auth"]);
$routes->post("api/complaints/delete/(:num)", "api\ComplaintsController::deleteComplaint/$1", ["filter" => "auth"]); // Any owner can try, logic is in controller

$routes->group(
    "api",
    ["namespace" => "App\Controllers\api", "filter" => "auth"],
    function ($routes) {
        $routes->get(
            "checkTokenValidity",
            "AuthController::checkTokenValidity",
        );
        $routes->get("get_holidays", "HolidaysController::get_holidays");
        $routes->post("holioday/store", "HolidaysController::store");
        $routes->post(
            "holioday/delete/(:num)",
            'HolidaysController::delete/$1',
        );
        $routes->get(
            "holidays/edit_holiday/(:num)",
            'HolidaysController::edit_holiday/$1',
        );
        $routes->post("holidays/update", "HolidaysController::update");
        $routes->get("dashboardData", "AdminController::DashboardData");
        $routes->post(
            "upload_profile_image_admin",
            "AdminController::upload_profile_image_admin",
        );

        $routes->post(
            "change-password-user",
            "EmployeeController::change_password_user",
        );
        $routes->post("change_image", "EmployeeController::change_image");
        $routes->post("save_overview", "EmployeeController::save_overview");
        $routes->get(
            "get_user_address_data/(:num)",
            'EmployeeController::get_user_address_data/$1',
        );
        $routes->post(
            "update_user_address",
            "EmployeeController::update_user_address",
        );
        $routes->get(
            "get_user_bank_data/(:num)",
            'EmployeeController::get_user_bank_data/$1',
        );
        $routes->post(
            "update_user_bank_data",
            "EmployeeController::update_user_bank_data",
        );
        $routes->post(
            "update_user_job_data",
            "EmployeeController::update_user_job_data",
        );
        $routes->get(
            "get_user_details/(:num)",
            'EmployeeController::get_user_details/$1',
        ); // Existing employee details
    
        $routes->get("get_holidays", "HolidaysController::get_holidays");
        $routes->post("holioday/store", "HolidaysController::store");
        $routes->post(
            "holioday/delete/(:num)",
            'HolidaysController::delete/$1',
        );
        $routes->get(
            "holidays/edit_holiday/(:num)",
            'HolidaysController::edit_holiday/$1',
        );
        $routes->post("holidays/update", "HolidaysController::update");
        $routes->get("dashboardData", "AdminController::DashboardData");
        $routes->post(
            "upload_profile_image_admin",
            "AdminController::upload_profile_image_admin",
        );

        $routes->get("company-rules/create", "CompanyRulesController::create");
        $routes->get("company-rules/rules", "CompanyRulesController::rules");
        $routes->post("company-rules/store", "CompanyRulesController::store");
        $routes->get("rules_get", "CompanyRulesController::rules_get");
        $routes->get(
            "company-rules/edit/(:num)",
            'CompanyRulesController::edit/$1',
        );
        $routes->post(
            "company-rules/update/(:num)",
            'CompanyRulesController::update/$1',
        );
        $routes->delete("rules/(:num)", 'CompanyRulesController::delete/$1');
        //login
        $routes->post("logout", "AuthController::logout");

        $routes->get("admin", "AdminController::index", [
            "filter" => "auth:admin",
        ]);
        $routes->get("hr", "HrController::index", ["filter" => "auth:hr"]);
        $routes->get("employee", "EmployeeController::index", [
            "filter" => "auth:employee",
        ]);

        $routes->post("leave", "LeaveController::create");
        $routes->get(
            "attendance/absent-reason",
            "AttendanceController::getAbsentReason",
        );
        $routes->post(
            "attendance/update-reason",
            "AttendanceController::updateStatus",
        );

        //account deatil employee
        $routes->post("account-detail/store", "AccountController::store");
        $routes->get("account-detail/view", "AccountController::fetch");
        $routes->get(
            "account-detail/getdata/(:num)",
            'AccountController::getById/$1',
        );
        $routes->get(
            "account-detail-get/(:num)",
            'AccountController::getAccountDetail/$1',
        );
        $routes->post(
            "account-detail-update/(:num)",
            'AccountController::updatedata/$1',
        );
        $routes->delete(
            "account-detail/delete/(:num)",
            'AccountController::delete/$1',
        );
        // // Notifications
        $routes->get(
            "notifications/getNotifications",
            "NotificationsController::getNotifications",
        );
        $routes->get(
            "notifications/getNotificationsAll",
            "NotificationsController::getNotificationsAll",
        );
        $routes->post(
            "notifications/markAsRead/(:num)",
            'NotificationsController::markAsRead/$1',
        );

        // OffrerLater
    
        $routes->post(
            "offer-templates/saveTemplate",
            "OfferLetterTemplateController::saveTemplate",
        );
        $routes->post(
            "offer-template/save",
            "OfferLetterTemplateController::saveTemplate",
        );
        $routes->get(
            "offer-template/list",
            "OfferLetterTemplateController::listTemplates",
        );
        $routes->delete(
            "offer-template/delete/(:num)",
            'OfferLetterTemplateController::delete/$1',
        );
        $routes->get(
            "offer-templates/get-template/(:num)",
            'OfferLetterTemplateController::getTemplate/$1',
        );
        $routes->post(
            "offer-template/update-template/(:num)",
            'OfferLetterTemplateController::updateTemplate/$1',
        );

        // Employee Of The Month
    
        $routes->post(
            "empof-month/SaveEmpMonth",
            "EmployeeOfTheMonthController::SaveEmpMonth",
        );
        $routes->get(
            "empof-month/list",
            "EmployeeOfTheMonthController::listTemplates",
        );
        $routes->delete(
            "empof-month/delete/(:num)",
            'EmployeeOfTheMonthController::delete/$1',
        );
        $routes->get(
            "empof_month-templates/get-template/(:num)",
            'EmployeeOfTheMonthController::getTemplate/$1',
        );
        $routes->post(
            "empof-month-template/update-template/(:num)",
            'EmployeeOfTheMonthController::updateTemplate/$1',
        );

        // EOP Performance Apis
    
        $routes->post(
            "employee-ofthe-month-performance/generate",
            "EmployeeOftheMonthPerformance::generateAndSaveCertificate",
        );
        $routes->get(
            "employee-of-month/all",
            "EmployeeOftheMonthPerformance::getAllPerformances",
        );
        $routes->delete(
            "employee-of-month/delete/(:num)",
            'EmployeeOftheMonthPerformance::deletePerformance/$1',
        );

        //exprience letter
        $routes->post(
            "exprience-templates/savedata",
            "ExprienceLetterController::saveTemplate",
        );
        $routes->get(
            "exprience-template/exprience",
            "ExprienceLetterController::listTemplates",
        );
        $routes->delete(
            "exprience-template/delete/(:num)",
            'ExprienceLetterController::delete/$1',
        );
        $routes->get(
            "exprience-templates/get-data/(:num)",
            'ExprienceLetterController::getTemplate/$1',
        );
        $routes->post(
            "exprience-template/update-data/(:num)",
            'ExprienceLetterController::updateTemplate/$1',
        );
        $routes->get(
            "employee/joining-date/(:num)",
            'ExprienceLetterController::getJoiningDate/$1',
        );
        $routes->get("getdata", "ExprienceLetterController::getAll");
        $routes->get(
            "generate-experience/(:num)",
            'ExprienceLetterController::downloadExperienceLetter/$1',
        );
        $routes->delete(
            "exprience-data/delete/(:num)",
            'ExprienceLetterController::deleteletter/$1',
        );
        $routes->post(
            "generate-experience-letter",
            "ExprienceLetterController::generateExperienceLetter",
        );

        //chat
        $routes->get(
            "chat/getMessages/(:num)",
            'ChatController::getMessages/$1',
        );
        $routes->post("chat/sendMessage", "ChatController::sendMessage");
        $routes->post(
            "chat/sendMessage_api",
            "ChatController::sendMessage_api",
        );
        $routes->get("chat/getUsers", "ChatController::getUsers");
        $routes->post("chat/getUsers_api", "ChatController::getUsers_api");
        $routes->get("chat/searchUsers", "ChatController::searchUsers");
        $routes->get("chat/searchUsers", "ChatController::searchUsers");

        $routes->post("onboarding", "OnboardingController::creates");
        $routes->get("onboarding", "OnboardingController::getAll");
        $routes->get("onboarding/(:num)", 'OnboardingController::get/$1');
        $routes->get("onboarding/(:num)", 'OnboardingController::getById/$1');
        $routes->post("onboarding/(:num)", 'OnboardingController::update/$1');
        $routes->delete("onboarding/(:num)", 'OnboardingController::delete/$1');
        //employee
        $routes->post("emp/create", "EmployeeController::create");
        $routes->delete("employee/(:num)", 'EmployeeController::delete/$1');
        $routes->get("employee/(:num)", 'EmployeeController::show/$1'); // Fetch employee details
        $routes->post(
            "employee/update/(:num)",
            'EmployeeController::update/$1',
        ); // Update employee data
        $routes->get("employees", "EmployeeController::index");
        $routes->post(
            "employee/validateStep",
            "EmployeeController::validateStep",
        );
        $routes->get(
            "employee/lastEmployeeId",
            "EmployeeController::lastEmployeeId",
        );

        // Salary Increment routes
        $routes->post("employee/increment-salary", "EmployeeController::incrementSalary");
        $routes->get("employee/increment-history/(:num)", "EmployeeController::getIncrementHistory/$1");
        $routes->get("employee/increment-history-user/(:num)", "EmployeeController::getIncrementHistoryByUserId/$1");

        // Country CRUD routes
        $routes->post("country", "CountryController::create"); // Create country
        $routes->get("country", "CountryController::getAll"); // Display all country
        $routes->delete("country/(:num)", 'CountryController::delete/$1'); // Delete country
        $routes->post("country/(:num)", 'CountryController::update/$1'); // Update country
        $routes->get("country/(:num)", 'CountryController::getById/$1'); // Fetch single country by ID
        $routes->get("countries", "CountryController::getAllCountry");

        $routes->post("state", "StateController::create"); // Create country
        $routes->get("state", "StateController::getAll"); // Display all country
        $routes->delete("state/(:num)", 'StateController::delete/$1'); // Delete country
        $routes->post("state/(:num)", 'StateController::update/$1'); // Update country
        $routes->get("state/(:num)", 'StateController::getById/$1'); // Fetch single country by ID
        $routes->get("countries", "StateController::getAllCountry");
        $routes->post("add-state", "StateController::addState");

        // City CRUD routes
        $routes->post("city", "CityController::create"); // Create city
        $routes->get("city", "CityController::getAll"); // Display all cities
        $routes->get("city/(:num)", 'CityController::getById/$1'); // Display single city
        $routes->post("city/(:num)", 'CityController::update/$1'); // Update city
        $routes->delete("city/(:num)", 'CityController::delete/$1'); // Delete city
        $routes->get("cities", "CityController::getAllCities");

        //job location  routes
        $routes->post("joblocation", "JoblocationController::create");
        $routes->get("joblocation", "JoblocationController::index");
        $routes->get("joblocation/(:num)", 'JoblocationController::getById/$1');
        $routes->post("joblocation/(:num)", 'JoblocationController::update/$1');
        $routes->delete(
            "joblocation/(:num)",
            'JoblocationController::delete/$1',
        ); // Create department
    
        // job location address
        $routes->post("save-location-address", "JobaddressController::store");
        $routes->get("job_address", "JobaddressController::getAll");
        $routes->get("job_address/(:num)", 'JobaddressController::getById/$1');
        $routes->delete(
            "job_address/(:num)",
            'JobaddressController::delete/$1',
        ); // Create department
        $routes->post(
            "save-location-address/(:num)",
            'JobaddressController::update/$1',
        );
        // Department CRUD routes
        $routes->post("department", "DepartmentController::create"); // Create department
        $routes->get("department", "DepartmentController::index"); // Display all departments
        $routes->get("department/(:num)", 'DepartmentController::getById/$1'); // Display single department
        $routes->post("department/(:num)", 'DepartmentController::update/$1'); // Update department
        $routes->delete("department/(:num)", 'DepartmentController::delete/$1'); // Delete department
        $routes->get("departments", "DepartmentController::getAllDepartement");
        $routes->get("getdepartments", "DepartmentController::getDepartments");
        // Designation CRUD routes
        $routes->post("designation", "DesignationController::create"); // Create designation
        $routes->get("designation", "DesignationController::index"); // Display all designations
        $routes->get("designation/(:num)", 'DesignationController::getById/$1'); // Display single designation
        $routes->post("designation/(:num)", 'DesignationController::update/$1'); // Update designation
        $routes->delete(
            "designation/(:num)",
            'DesignationController::delete/$1',
        ); // Delete designation
        $routes->get(
            "designations",
            "DesignationController::getAllDesignation",
        );
        //Report
        $routes->get("report/empReport", "ReportController::empReport");
        $routes->get("report/leaveReport", "ReportController::leaveReport");
        //interviews
        $routes->post("interviews", "InterviewController::create");
        $routes->get("interviews", "InterviewController::getAll");
        $routes->get("interviews/(:num)", 'InterviewController::get/$1');
        $routes->post("interviews/(:num)", 'InterviewController::update/$1');
        $routes->delete("interviews/(:num)", 'InterviewController::delete/$1');
        // Leaves_type CRUD routes
        $routes->post("leavetype", "LeaveTypeController::create"); // Create leave_type
        $routes->get("leavetype", "LeaveTypeController::getAll"); // Display all leave_type
        $routes->post("leavetype/(:num)", 'LeaveTypeController::update/$1'); // Update leave_type
        $routes->delete("leavetype/(:num)", 'LeaveTypeController::delete/$1'); // Delete leave_type
        $routes->get("leavetype/(:segment)", 'LeaveTypeController::getById/$1');
        $routes->post("leavetype/add", "LeaveController::add"); // Create leave_type00000000000000000
        $routes->get("leaves/remaining", "LeaveController::getRemainingLeaves");
        $routes->get(
            "leave/summary/(:num)",
            'LeaveController::getEmployeeLeaveSummary/$1',
        );
        $routes->post("leave/remainingLeaves", "LeaveController::remainingLeaves");
        $routes->post("leave/cancel/(:num)", "LeaveController::cancelLeave/$1");
        $routes->post("leave/update-dates/(:num)", "LeaveController::updateDates/$1");

        // Employee Leave Balance Management
         $routes->get("employee-leaves/details", "EmployeeLeaveController::getLeaveDetails");
        $routes->get("employee-leaves", "EmployeeLeaveController::getAll");
        $routes->post("employee-leaves/store", "EmployeeLeaveController::store");
        $routes->get("employee-leaves/(:num)", "EmployeeLeaveController::getByEmployee/$1");

        $routes->post("attendance/checkin", "AttendanceController::checkIn");
        $routes->post("attendance/checkout", "AttendanceController::checkOut");
        $routes->get("attendance/status", "AttendanceController::getStatus");
        $routes->post("geofence/ping", "GeofenceController::pingLocation");

        $routes->get(
            "attendance/getAttendanceData",
            "AttendanceController::getAttendanceData",
        );
        $routes->get(
            "attendance/getAttendance/(:num)/(:num)",
            'AttendanceController::getAttendance/$1/$2',
        );
        $routes->get(
            "attendance/export",
            "AttendanceController::exportExcel",
        );
        $routes->get(
            "dashboard-stats",
            "AttendanceController::getDashboardStats",
        );
        $routes->get(
            "leave/getLeaveDetails/(:num)",
            'AttendanceController::getLeaveDetails/$1',
        );

        // Face Recognition Attendance Routes
        $routes->get(
            "attendance/get-face-photo",
            "AttendanceController::getFacePhoto",
        );
        $routes->post(
            "attendance/face-checkin",
            "AttendanceController::faceCheckIn",
        );

        // Delete today's attendance records
        $routes->delete(
            "attendance/delete-today",
            "AttendanceController::deleteTodayAttendance",
        );
        $routes->get(
            "attendance/day-records",
            "AttendanceController::getDayAttendanceRecords",
        );
        $routes->post(
            "attendance/update-day-records",
            "AttendanceController::updateDayAttendanceRecords",
        );
        $routes->post(
            "attendance/bulk-update",
            "AttendanceController::bulkUpdateAttendanceRecords",
        );

        // Push Notification Routes
        $routes->get(
            "push/public-key",
            "PushNotificationController::getPublicKey",
        );
        $routes->post(
            "push/subscribe",
            "PushNotificationController::subscribe",
        );
        $routes->post(
            "push/unsubscribe",
            "PushNotificationController::unsubscribe",
        );


        $routes->get("push/test", "PushNotificationController::test");
        $routes->get("push/status", "PushNotificationController::getStatus");

        $routes->post("users/register", "UserController::register");
        $routes->post("users/login", "UserController::login");
        $routes->get("users", "UserController::index");
        $routes->get("users/(:num)", 'UserController::show/$1');
        $routes->put("users/(:num)", 'UserController::update/$1');
        $routes->delete("users/(:num)", 'UserController::delete/$1');
        $routes->post(
            "chat/update-activity",
            "UserController::updateLastActivity",
        );
        $routes->post(
            "chat/mark-inactive-users-offline",
            "UserController::markInactiveUsersOffline",
        );

        $routes->post("leave", "LeaveController::create");
        $routes->get("leave", "LeaveController::getAll");
        $routes->put("leave/(:num)", 'LeaveController::update/$1');

        $routes->delete("leave/(:num)", 'LeaveController::delete/$1');

        $routes->post("job", "JobController::create");
        $routes->get("job", "JobController::getAll");
        $routes->get("job/(:num)", 'JobController::get/$1');
        $routes->get("jobupdate/(:segment)", 'JobController::getById/$1');
        $routes->post("job/(:num)", 'JobController::update/$1');
        $routes->get("/job/display/(:num)", 'JobController::singlejob/$1');
        $routes->delete("job/(:num)", 'JobController::delete/$1');
        $routes->get(
            "getAddressesByLocation",
            "JobController::getAddressesByLocation",
        );
        $routes->post("getAddresses", "JobController::getAddresses");
        $routes->post("save-location", "JobController::savelocation");
        $routes->post("addAddress", "JobController::addAddress");
        $routes->post("location/add", "JobController::add");

        $routes->post("candidate", "CandidateController::creates");
        $routes->get("candidate", "CandidateController::getAll");
        $routes->get("candidate/(:num)", 'CandidateController::get/$1');
        $routes->post("candidate/(:num)", 'CandidateController::update/$1');
        $routes->get(
            "candidateedit/(:segment)",
            'CandidateController::getById/$1',
        );
        $routes->delete("candidate/(:num)", 'CandidateController::delete/$1');
        $routes->get(
            "candidate/download-resume/(:num)",
            'CandidateController::downloadResume/$1',
        );

        $routes->post("onboarding", "OnboardingController::creates");
        $routes->get("onboarding", "OnboardingController::getAll");
        $routes->get("onboarding/(:num)", 'OnboardingController::get/$1');
        $routes->get(
            "onboardingedit/(:segment)",
            'OnboardingController::getById/$1',
        );
        $routes->post("onboarding/(:num)", 'OnboardingController::update/$1');
        $routes->delete("onboarding/(:num)", 'OnboardingController::delete/$1');

        $routes->post("performance/create", "PerformanceController::create");
        $routes->get("performance/getAll", "PerformanceController::getAll");
        $routes->get(
            "performance/(:num)",
            'PerformanceController::getByEmployee/$1',
        );
        $routes->post(
            "performance/update/(:num)",
            'PerformanceController::update/$1',
        );
        $routes->delete(
            "performance/(:num)",
            'PerformanceController::delete/$1',
        );
        $routes->post("designation/add", "PerformanceController::add");
        $routes->get(
            "get-user-designation/(:num)",
            'PerformanceController::getUserDesignation/$1',
        );

        $routes->post("training/creates", "TrainingController::creates");
        $routes->get("training/getAll", "TrainingController::getAll");
        $routes->get(
            "training/get/(:num)",
            'TrainingController::getByEmployee/$1',
        );
        $routes->post(
            "training/update/(:num)",
            'TrainingController::update/$1',
        );
        $routes->delete("training/(:num)", 'TrainingController::delete/$1');
        $routes->post("department/add", "TrainingController::addDepartment");

        $routes->post("payroll/create", "PayrollController::create");
        $routes->get("payroll/getAll", "PayrollController::getAll");
        $routes->get("payroll/(:num)", 'PayrollController::getByEmployee/$1');
        $routes->post("payroll/update/(:num)", 'PayrollController::update/$1');
        $routes->delete("payroll/(:num)", 'PayrollController::delete/$1');
        $routes->post(
            "payroll/get-worked-hours",
            "PayrollController::getWorkedHours",
        );
        $routes->post(
            "payroll/get-working-days",
            "PayrollController::getWorkingDays",
        );
        $routes->post(
            "payroll/calculate-payroll",
            "PayrollController::calculatePayroll",
        );
        $routes->get("get-salary", "PayrollController::getSalary");
        $routes->post("account/save", "PayrollController::save");

        $routes->post("payroll/save", "PayrollController::savedata"); // For single save
        $routes->post("payroll/saveAll", "PayrollController::saveAll"); // For bulk save
    
        //calculate salary
        $routes->post(
            "payroll/get-monthly-leaves",
            "PayrollController::getMonthLeaves",
        );
        $routes->post(
            "payroll/get-leave-details",
            "PayrollController::getLeaveDetails",
        );
        $routes->post(
            "payroll/get-remaining-leaves/(:num)",
            'PayrollController::getRemainingPaidLeaves/$1',
        );
        $routes->post(
            "payroll/get-deduction-breakdown",
            "PayrollController::getDeductionBreakdown",
        );
        $routes->get(
            "payroll/get-deduction-breakdown",
            "PayrollController::getDeductionBreakdown",
        );

        $routes->post("task/create", "TaskController::create");
        $routes->get("task/getAll", "TaskController::getAll");
        $routes->get("task/(:num)", 'TaskController::getByEmployee/$1');
        $routes->post("task/update/(:num)", 'TaskController::update/$1');
        $routes->delete("task/(:num)", 'TaskController::delete/$1');
        $routes->post("department/add", "TaskController::addDepartment");
        $routes->post("task/updateStatus", "TaskController::updateStatus");
        //task comment
    
        // Sub Task Routes
        $routes->get(
            "subtasks/get-tasks-by-user/(:num)",
            'SubTaskController::getTasksByUser/$1',
        );
        $routes->post("subtasks/create", "SubTaskController::create");
        $routes->get("subtask/getAll", "SubTaskController::getAll");
        $routes->delete(
            "subtask/(:num)",
            'SubTaskController::subTaskdelete/$1',
        );
        $routes->post(
            "subtask/updateStatus",
            "SubTaskController::subTaskupdateStatus",
        );
        $routes->get(
            "subtask-detail-get/(:num)",
            'SubTaskController::getSubtaskDetail/$1',
        );
        $routes->post(
            "subtasks/update/(:num)",
            'SubTaskController::updateSubtask/$1',
        );
        $routes->post(
            "subtask/update-status/(:num)",
            'SubTaskController::ProfileSubtaskUpdateStatus/$1',
        );
        //task commnets
        $routes->post("task/addComment", "CommnetController::addComment");

        $routes->get("profile", "ProfileController::getProfile");
        $routes->get("editprofile", "ProfileController::editProfile");
        $routes->post("profile/update", "ProfileController::updateProfile");
        $routes->post(
            "profile/removeImage",
            "ProfileController::removeProfileImage",
        );
        $routes->post("company/update", "ProfileController::updatecompany");
        $routes->post("update", "ProfileController::uploadLogo");

        //dropdawon disaply
        $routes->post("department/add", "DepartmentController::addDepartment");
        $routes->post(
            "designation/add",
            "DesignationController::addDesignation",
        );

        $routes->post("add-country", "CountryController::addCountry");
        $routes->post("add-city", "CityController::addCity");
        $routes->get(
            "template/getEmpMonthTemplate/(:num)",
            'EmployeeOftheMonthPerformance::getEmpMonthTemplate/$1',
        );
        $routes->get(
            "template/get/(:num)",
            'ExprienceLetterController::getTemplateById/$1',
        );
        $routes->get(
            "offer-template/(:num)",
            'OfferLetterTemplateController::getOfferTemplate/$1',
        );

        // PDF Recorder API routes
        $routes->post("pdf-recorder/upload", "PdfRecorderController::upload");
        $routes->get(
            "pdf-recorder/filtered-data",
            "PdfRecorderController::getFilteredData",
        );
        $routes->get("pdf-recorder/export", "PdfRecorderController::export");
    },
);

$routes->get("/view", "api\AttendanceController::view");
$routes->get("/view-calendar", "api\AttendanceController::viewCalendar");
$routes->get("/attendence", "api\AttendanceController::display");
$routes->get("/leaveview", "api\LeaveController::view");
$routes->get("/addleave", "api\LeaveController::display");
$routes->get("/manage-leaves", "api\LeaveController::manage_index");
// $routes->get("/manage-leaves", "App\Controllers\Api\EmployeeLeaveController::index");
$routes->get("public/upload/resumes/(:any)", function ($file) {
    return redirect()->to(base_url("public/upload/resumes/" . $file));
});

// PDF Recorder Routes
$routes->get("/pdf-recorder", "api\PdfRecorderController::index");
$routes->get("/pdf-recorder/preview", "api\PdfRecorderController::preview");

$routes->group("api", ["filter" => "auth"], function ($routes) { });

$routes->group("", ["filter" => "noauth"], function ($routes) {
    $routes->get("/job", "api\JobController::creates");
    $routes->get("/jobview", "api\JobController::display");
});

$routes->get("run_db_update", "\App\Controllers\DBUpdateController::index");
$routes->get("/candidate-documents/(:num)", "CandidateDocumentsController::show/$1");

$routes->get(
    "api/interview/export",
    'api\InterviewController::exportExcel'
);
$routes->get(
    "api/assessment/export",
    'InterviewAssessments::exportExcel'
);

// Digital Signature Routes
$routes->get("/digital-signature", "DigitalSignatureController::index");
$routes->post("/digital-signature/save", "DigitalSignatureController::save");
$routes->get("/digital-signature/delete/(:num)", "DigitalSignatureController::delete/$1");
$routes->get("/digital-signature/set-default/(:num)", "DigitalSignatureController::setDefault/$1");

// Gadget Issuance Routes
$routes->get("/gadget-issuance", "GadgetIssuanceController::index");
$routes->get("/api/gadget-issuance", "GadgetIssuanceController::getAll");
$routes->get("/api/gadget-issuance/types", "GadgetIssuanceController::getTypes");
$routes->post("/api/gadget-issuance/add-type", "GadgetIssuanceController::addType");
$routes->get("/api/gadget-issuance/get/(:num)", "GadgetIssuanceController::getOne/$1");
$routes->post("/api/gadget-issuance/save", "GadgetIssuanceController::save");
$routes->get("/api/gadget-issuance/delete/(:num)", "GadgetIssuanceController::delete/$1");

// ── Resignation Module ────────────────────────────────────────────────────────
// Employee routes
$routes->get('/resignation',                   'ResignationController::index');
$routes->post('/resignation/submit',           'ResignationController::submit');
$routes->get('/resignation/detail/(:num)',     'ResignationController::myDetail/$1');
$routes->get('/resignation/withdraw/(:num)',   'ResignationController::withdraw/$1');
$routes->get('/resignation/handover/(:num)',   'ResignationController::handoverPage/$1');
$routes->get('/resignation/my-handover',       'ResignationController::myHandoverTasks');

// Manager routes
$routes->get('/resignation/manager',              'ResignationController::managerList',  ['filter' => 'hr_or_manager']);
$routes->post('/resignation/manager/action/(:num)','ResignationController::managerAction/$1', ['filter' => 'hr_or_manager']);

// HR routes
$routes->get('/resignation/hr',                    'ResignationController::hrList',      ['filter' => 'admin_only']);
$routes->get('/resignation/hr/detail/(:num)',       'ResignationController::hrDetail/$1', ['filter' => 'admin_only']);
$routes->post('/resignation/hr/action/(:num)',      'ResignationController::hrAction/$1', ['filter' => 'admin_only']);
$routes->post('/resignation/hr/notice/(:num)',      'ResignationController::updateNotice/$1', ['filter' => 'admin_only']);
$routes->post('/resignation/hr/advance-handover/(:num)', 'ResignationController::advanceHandover/$1', ['filter' => 'admin_only']);
$routes->post('/resignation/hr/fnf/prepare/(:num)', 'ResignationController::fnfPrepare/$1', ['filter' => 'admin_only']);

// Clearance
$routes->get('/resignation/clearance',             'ResignationController::clearanceList', ['filter' => 'hr_or_manager']);
$routes->post('/api/resignation/clearance/(:num)', 'ResignationController::clearanceAction/$1');

// Handover API
$routes->post('/api/resignation/handover/add',           'ResignationController::addHandoverTask');
$routes->post('/api/resignation/handover/update/(:num)', 'ResignationController::updateHandoverTask/$1');

// F&F API
$routes->post('/api/resignation/fnf/finance-approve/(:num)', 'ResignationController::fnfFinanceApprove/$1');
$routes->post('/api/resignation/fnf/mark-paid/(:num)',       'ResignationController::fnfMarkPaid/$1');

// Detail API
$routes->get('/api/resignation/(:num)', 'ResignationController::apiDetail/$1');
