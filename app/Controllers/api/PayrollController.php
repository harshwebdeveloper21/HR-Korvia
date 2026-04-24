<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use Dompdf\Dompdf;
use App\Models\AccountDetailModel;
use App\Models\PayrollModel;
use App\Models\UserInfoModel;
use App\Models\LeaveTypeModel;
use App\Models\LeaveModel;
use App\Models\CompanyLogoModel;
use App\Models\DepartmentModel;
use App\Models\DesignationModel;
use App\Models\OnboardingModel;
use App\Models\CompanyRulesModel;
use App\Models\AttendanceModel;
use App\Models\HolidayCalendarModel;
use App\Services\AuthService;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\EmployeeLeaveModel;
use App\Traits\CompanyRuleTrait;

class PayrollController extends ResourceController
{
    use CompanyRuleTrait;

    private $payrollModel;
    private $authService;

    public function __construct()
    {
        $this->payrollModel = new PayrollModel();
        $this->authService = new AuthService(service("request"));
    }

    public function page()
    {
        $userModel = new \App\Models\UserModel();
        $role = session()->get("role"); // Assuming the user's role is stored in the session

        $employees = $userModel
            ->where("is_deleted", 0)
            ->where("role", "employee")
            ->findAll();

        if ($role === "admin") {
            $reviewers = $userModel
                ->where("is_deleted", 0)
                ->whereIn("role", ["hr", "employee"])
                ->findAll();
        } elseif ($role === "hr") {
            $reviewers = $employees;
        } else {
            $reviewers = [];
        }
        $leaveTypeModel = new LeaveTypeModel();

        $leaveTypes = $leaveTypeModel->findAll();
        return view("payroll/payroll", [
            "employees" => $reviewers,
            "leaveTypes" => $leaveTypes, // Pass the filtered reviewers array
        ]);
    }

    public function display()
    {
        return view("payroll/view");
    }

    // GET: List payroll details for all users
    public function index()
    {
        $payroll = $this->payrollModel->findAll();
        return $this->response->setJSON([
            "status" => "success",
            "data" => $payroll,
        ]);
    }

    public function getSalary()
    {
        $userId = $this->request->getGet("user_id");
        $userModel = new UserInfoModel();
        $accountModel = new AccountDetailModel();
        $companyRulesModel = new CompanyRulesModel();

        $user = $userModel->where("user_id", $userId)->first();
        $account = $accountModel->where("user_id", $userId)->first();
        $company_rules = $companyRulesModel->first();

        return $this->response->setJSON([
            "salary" => $user["salary"] ?? 0,
            "tax" => $company_rules["tax"] ?? 0,
            "salary_above_tax" => $company_rules["salary_above_tax"] ?? 0,
            "acc_number" => $account["acc_number"] ?? "",
            "bank_name" => $account["bank_name"] ?? "",
            "ifsc_code" => $account["ifsc_code"] ?? "",
            "acc_in_name" => $account["acc_in_name"] ?? "",
            "branch_name" => $account["branch_name"] ?? "",
            "branch_code" => $account["branch_code"] ?? "",
            "company_rules" => $company_rules,
        ]);
    }

    public function create()
    {
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized(
                "Unauthorized: Token missing or invalid",
            );
        }

        if (!in_array($user->role, ["admin", "hr"])) {
            return $this->failForbidden(
                "Forbidden: You do not have access to this resource",
            );
        }

        $data = $this->request->getPost();

        $userId = $data["user_id"] ?? null;
        $paymentDate = $data["payment_date"] ?? null;

        $year = date("Y", strtotime($paymentDate));
        $month = date("m", strtotime($paymentDate));
        $monthYear = sprintf("%04d-%02d", $year, $month);
        $existingPayroll = $this->payrollModel
            ->where("user_id", $userId)
            ->where("month_year", $monthYear)
            ->first();

        if ($existingPayroll) {
            return $this->respond(
                [
                    "status" => "error",
                    "message" =>
                        "This Employee Payroll record already exists for this month.",
                ],
                400,
            );
        }
        $validationRules = [
            "user_id" => [
                "rules" => "required|integer",
                "errors" => [
                    "required" => "Employee field is required.",
                    "integer" => "Employee ID must be a valid number.",
                ],
            ],
            "salary_amount" => [
                "rules" => "required|decimal",
                "errors" => [
                    "required" => "Salary Amount field is required.",
                    "decimal" => "Salary Amount must be a valid decimal value.",
                ],
            ],
            "net_salary" => [
                "rules" => "required|decimal",
                "errors" => [
                    "required" => "Net Salary field is required.",
                    "decimal" => "Net Salary must be a valid decimal value.",
                ],
            ],
            "payment_date" => [
                "rules" => "required|valid_date",
                "errors" => [
                    "required" => "Payment Date field is required.",
                    "valid_date" =>
                        "Please enter a valid date format (YYYY-MM-DD).",
                ],
            ],
            "payment_status" => [
                "rules" => "required|string",
                "errors" => [
                    "required" => "Payment Status field is required.",
                    "string" => "Payment Status must be a valid string.",
                ],
            ],
            "month_year" => [
                "rules" => 'required|regex_match[/^\d{4}-(0[1-9]|1[0-2])$/]',
                "errors" => [
                    "required" => "Month & Year field is required.",
                    "regex_match" =>
                        "Month & Year must be in YYYY-MM format (e.g., 2025-04).",
                ],
            ],
        ];

        if (!$this->validate($validationRules)) {
            return $this->respond(
                [
                    "status" => "error",
                    "message" => $this->validator->getErrors(),
                ],
                400,
            );
        }
        $data["created_by"] = $user->sub;
        $data["total_half_day"] = $data["total_halfday_leaves"] ?? 0;

        if ($this->payrollModel->insert($data)) {
            $payrollId = $this->payrollModel->insertID(); // ✅ Get the last inserted ID

            if ($payrollId) {
                // Notify Admin, HR, and the specific Employee
                $notificationModel = new \App\Models\NotificationModel();
                $userModel = new \App\Models\UserModel();
                $employee = $userModel->find($data["user_id"]);
                $sender = $userModel->find($user->sub);

                $recipients = $userModel
                    ->whereIn("role", ["admin", "hr"])
                    ->orWhere("id", $data["user_id"]) // Include employee
                    ->findAll();

                foreach ($recipients as $recipient) {
                    $notificationModel->insert([
                        "sender_id" => $user->sub,
                        "recipient_id" => $recipient["id"],
                        "data" => json_encode([
                            "type" => "payroll",
                            "username" => $sender["username"],
                            "employee" => $employee["username"],
                            "user_id" => $data["user_id"],
                            "message" =>
                                "Payroll processed for " .
                                $employee["username"] .
                                " on " .
                                date(
                                    "M d, Y",
                                    strtotime($data["payment_date"]),
                                ),
                        ]),
                        "is_read" => 0,
                    ]);
                }
            }

            return $this->respond(
                [
                    "status" => "success",
                    "message" => "Payroll record added successfully",
                    "data" => $this->payrollModel->find(
                        $this->payrollModel->insertID(),
                    ),
                ],
                201,
            );
        }

        return $this->respond(
            ["status" => "error", "message" => "Failed to add payroll record"],
            500,
        );
    }

    // Get All Payroll Records (Admin & HR Only)
    // public function getAll()
    // {
    //     $user = $this->authService->check();
    //     if (!$user) {
    //         return $this->failUnauthorized(
    //             "Unauthorized: Token missing or invalid",
    //         );
    //     }

    //     // Get month filter from query params
    //     $month = $this->request->getGet("month");

    //     $this->payrollModel
    //         ->select(
    //             "payroll.id, payroll.user_id as employee_id, payroll.salary_amount, payroll.month_year, payroll.net_salary, payroll.payment_date, payroll.created_at, user_info.profile_image, users.username",
    //         )
    //         ->join("users", "users.id = payroll.user_id")
    //         ->join("user_info", "user_info.user_id = payroll.user_id");

    //     // Apply month filter if provided
    //     if ($month) {
    //         $this->payrollModel->where("payroll.month_year", $month);
    //     }

    //     // Role-based filtering
    //     if ($user->role === "admin") {
    //         // Admin can see all records (no filter)
    //         $records = $this->payrollModel
    //             ->orderBy("created_at", "DESC")
    //             ->findAll();
    //     } elseif ($user->role === "hr") {
    //         // HR can only see employee records (exclude admin & HR)
    //         $records = $this->payrollModel
    //             ->where("users.role", "employee")
    //             ->orderBy("created_at", "DESC")
    //             ->findAll();
    //     } elseif ($user->role === "employee") {
    //         // Employee can only see their own records
    //         $records = $this->payrollModel
    //             ->where("payroll.user_id", $user->sub)
    //             ->orderBy("created_at", "DESC")
    //             ->findAll();
    //     } else {
    //         return $this->failForbidden("Forbidden: Unauthorized role");
    //     }

    //     return $this->respond(["status" => "success", "data" => $records]);
    // }

      public function getAll()
    {
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized(
                "Unauthorized: Token missing or invalid",
            );
        }

        // Get month filter from query params
        $month = $this->request->getGet("month");

        $this->payrollModel
            ->select(
                "payroll.id, payroll.user_id as employee_id, payroll.salary_amount, payroll.month_year, payroll.net_salary, payroll.payment_date, payroll.created_at, user_info.profile_image, users.username",
            )
            ->join("users", "users.id = payroll.user_id")
            ->join("user_info", "user_info.user_id = payroll.user_id");

        // Apply month filter if provided
        if ($month) {
            $this->payrollModel->where("payroll.month_year", $month);
        }

        // Role-based filtering
        if ($user->role === "admin") {
            // Admin can see all records (no filter)
            $records = $this->payrollModel
                ->orderBy("created_at", "DESC")
                ->findAll();
        } elseif ($user->role === "hr") {
            // HR can see employee records and their own records
            $records = $this->payrollModel
                ->groupStart()
                    ->where("users.role", "employee")
                    ->orWhere("payroll.user_id", $user->sub)
                ->groupEnd()
                ->orderBy("created_at", "DESC")
                ->findAll();
        } elseif ($user->role === "employee") {
            // Employee can only see their own records
            $records = $this->payrollModel
                ->where("payroll.user_id", $user->sub)
                ->orderBy("created_at", "DESC")
                ->findAll();
        } else {
            return $this->failForbidden("Forbidden: Unauthorized role");
        }

        return $this->respond(["status" => "success", "data" => $records]);
    }
    // Get Payroll Records for a Specific Employee (Employee-Specific Access)
    public function getByEmployee($employeeId = null)
    {
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized(
                "Unauthorized: Token missing or invalid",
            );
        }

        // Employees can only view their own payroll records
        if ($user->role === "employee" && $user->sub !== $employeeId) {
            return $this->failForbidden(
                "Forbidden: You can only access your own performance records",
            );
        }

        $records = $this->payrollModel->where("id", $employeeId)->findAll();
        return $this->respond(["status" => "success", "data" => $records]);
    }

    // Update Payroll Record (Admin & HR Only)
    public function update($id = null)
    {
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized(
                "Unauthorized: Token missing or invalid",
            );
        }

        if (!in_array($user->role, ["admin", "hr"])) {
            return $this->failForbidden(
                "Forbidden: You do not have access to this resource",
            );
        }

        // Check if payroll record exists
        $existingPayroll = $this->payrollModel->find($id);
        if (!$existingPayroll) {
            return $this->respond(
                ["status" => "error", "message" => "Payroll record not found"],
                404,
            );
        }

        $data = $this->request->getPost();

        // Validation with custom error messages
        $validationRules = [
            "salary_amount" => [
                "rules" => "required|decimal",
                "errors" => [
                    "required" => "Salary Amount is required.",
                    "decimal" => "Salary Amount must be a valid decimal value.",
                ],
            ],
            "tax_deduction" => [
                // permit_empty so a 0 value (or missing field) passes cleanly
                "rules" => "permit_empty|decimal",
                "errors" => [
                    "decimal" => "Tax Deduction must be a valid decimal value.",
                ],
            ],
            // "bonuses" is intentionally omitted — the field is not rendered in the
            // Edit Payroll form, so requiring it always caused a 400 Bad Request.
            "net_salary" => [
                "rules" => "required|decimal",
                "errors" => [
                    "required" => "Net Salary is required.",
                    "decimal" => "Net Salary must be a valid decimal value.",
                ],
            ],
            "payment_date" => [
                "rules" => "required|valid_date",
                "errors" => [
                    "required" => "Payment Date is required.",
                    "valid_date" =>
                        "Please enter a valid date format (YYYY-MM-DD).",
                ],
            ],
            "payment_status" => [
                "rules" => "required|string",
                "errors" => [
                    "required" => "Payment Status is required.",
                    "string" => "Payment Status must be a valid string.",
                ],
            ],
            "month_year" => [
                "rules" => 'required|regex_match[/^\d{4}-(0[1-9]|1[0-2])$/]',
                "errors" => [
                    "required" => "Month & Year is required.",
                    "regex_match" =>
                        "Month & Year must be in YYYY-MM format (e.g., 2025-04).",
                ],
            ],
        ];

        if (!$this->validate($validationRules)) {
            return $this->respond(
                [
                    "status" => "error",
                    "message" => $this->validator->getErrors(),
                ],
                400,
            );
        }

        // Map total_halfday_leaves to total_half_day (consistent with create method)
        if (isset($data["total_halfday_leaves"])) {
            $data["total_half_day"] = $data["total_halfday_leaves"];
            unset($data["total_halfday_leaves"]);
        }

        // Remove fields that shouldn't be updated
        unset($data["id"]);
        unset($data["created_by"]);
        unset($data["created_at"]);

        // Update the record
        if ($this->payrollModel->update($id, $data)) {
            return $this->respond([
                "status" => "success",
                "message" => "Payroll record updated successfully",
                "data" => $this->payrollModel->find($id),
            ]);
        }

        return $this->respond(
            [
                "status" => "error",
                "message" => "Failed to update payroll record",
            ],
            500,
        );
    }

    // Delete Payroll Record (Admin Only)
    public function delete($id = null)
    {
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized(
                "Unauthorized: Token missing or invalid",
            );
        }

        if (!in_array($user->role, ["admin", "hr"])) {
            return $this->failForbidden(
                "Forbidden: Only Admin and HR can delete payroll records",
            );
        }

        if ($this->payrollModel->delete($id)) {
            return $this->respond([
                "status" => "success",
                "message" => "Payroll record deleted successfully",
            ]);
        }

        return $this->respond(
            [
                "status" => "error",
                "message" => "Failed to delete payroll record",
            ],
            500,
        );
    }

    public function profilePage()
    {
        return view("payroll/profile");
    }

    public function getProfile($id = null)
    {
        $user = $this->authService->check();
        $companyRulesModel = new CompanyRulesModel();
        $company_rules = $companyRulesModel->first();

        $salary_above_tax = $company_rules["salary_above_tax"] ?? 0;
        $tax = $company_rules["tax"] ?? 0;
        if (!$user) {
            return $this->failUnauthorized(
                "Unauthorized: Token missing or invalid",
            );
        }

        // Fetch payroll details along with user and designation info
        $record = $this->payrollModel
            ->select(
                "payroll.*,payroll.salary_amount, users.username as employee_name, user_info.profile_image, user_info.firstname, user_info.lastname, user_info.email, user_info.employee_id,leave_type.leave_type",
            )
            ->join("users", "users.id = payroll.user_id", "left")
            ->join("leave_type", "leave_type.id = payroll.leave_type", "left")
            ->join("user_info", "user_info.user_id = users.id", "left") // Join user_info table
            ->where("payroll.id", $id)
            ->first();

        if (!$record) {
            return $this->failNotFound("Payroll record not found");
        }

        return $this->respond([
            "status" => "success",
            "data" => $record,
            "salary_above_tax" => $salary_above_tax,
            "tax" => $tax,
        ]);
    }

    public function getMonthLeaves()
    {
        $userId = $this->request->getPost("user_id");
        $month = (int) $this->request->getPost("month");
        $year = (int) $this->request->getPost("year");

        if (!$userId || $month < 1 || $month > 12 || $year < 2000) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "Invalid input data",
            ]);
        }

        $leaveModel = new LeaveModel();
        $attendanceModel = new AttendanceModel();

        $startOfMonth =
            "$year-" . str_pad($month, 2, "0", STR_PAD_LEFT) . "-01";
        $endOfMonth = date("Y-m-t", strtotime($startOfMonth));

        $companyRulesModel = new CompanyRulesModel();
        $leaveModel = new LeaveModel();

        $rules = $companyRulesModel->first();

        $holidayCalendarModel = new HolidayCalendarModel();
        $holidayRows = $holidayCalendarModel
            ->where("holiday_date >=", $startOfMonth)
            ->where("holiday_date <=", $endOfMonth)
            ->findAll();
        $holidayDates = array_column($holidayRows, "holiday_date");

        $totalLeaveDaysInMonth = $this->countMonthlyLeaves(
            $userId,
            "$year-" . str_pad($month, 2, "0", STR_PAD_LEFT),
            $rules,
            $leaveModel,
            $holidayDates,
        );

        // Half-day leaves from attendance
        $halfDays = $attendanceModel
            ->where("user_id", $userId)
            ->where("status", "half-day")
            ->where("date >=", $startOfMonth)
            ->where("date <=", $endOfMonth)
            ->countAllResults();

        return $this->response->setJSON([
            "status" => "success",
            "total_leaves" => $totalLeaveDaysInMonth,
            "total_half_day_leaves" => $halfDays,
        ]);
    }

    public function getWorkedHours()
    {
        $userId = $this->request->getPost("user_id");
        $month = (int) $this->request->getPost("month");
        $year = (int) $this->request->getPost("year");

        if (!$userId || $month < 1 || $month > 12 || $year < 2000) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "Invalid input data",
            ]);
        }

        $attendanceModel = new AttendanceModel();

        $startOfMonth =
            "$year-" . str_pad($month, 2, "0", STR_PAD_LEFT) . "-01";
        $endOfMonth = date("Y-m-t", strtotime($startOfMonth));

        // Convert TIME to seconds and sum
        $result = $attendanceModel
            ->select(
                "
                SUM(TIME_TO_SEC(work_hours)) AS work_seconds,
                SUM(TIME_TO_SEC(overtime)) AS overtime_seconds
            ",
            )
            ->where("user_id", $userId)
            ->where("date >=", $startOfMonth)
            ->where("date <=", $endOfMonth)
            ->first();

        $totalSeconds =
            ($result["work_seconds"] ?? 0) + ($result["overtime_seconds"] ?? 0);

        // Convert seconds → hours (decimal)
        $totalHours = round($totalSeconds / 3600, 2);
        $overtimeHours = round(($result["overtime_seconds"] ?? 0) / 3600, 2);

        return $this->response->setJSON([
            "status" => "success",
            "total_worked_hours" => $totalHours,
            "total_overtime_hours" => $overtimeHours,
            "total_worked_time" => gmdate("H:i:s", $totalSeconds),
        ]);
    }
    /**
     * Get working days for a month (same logic as group salary-details: holidays and Sundays excluded).
     */
    public function getWorkingDays()
    {
        $month = (int) $this->request->getPost("month");
        $year = (int) $this->request->getPost("year");

        if ($month < 1 || $month > 12 || $year < 2000) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "Invalid month/year",
            ]);
        }

        $companyRulesModel = new CompanyRulesModel();
        $holidayCalendarModel = new HolidayCalendarModel();
        $rules = $companyRulesModel->first();

        $startOfMonth = sprintf("%04d-%02d-01", $year, $month);
        $endOfMonth = date("Y-m-t", strtotime($startOfMonth));
        $holidayRows = $holidayCalendarModel
            ->where("holiday_date >=", $startOfMonth)
            ->where("holiday_date <=", $endOfMonth)
            ->findAll();
        $holidayDates = array_column($holidayRows, "holiday_date");

        $workingDaysData = $this->getWorkingDaysData($month, $year, $rules, $holidayDates);
        $workingDays = $workingDaysData["working_days"];
        $daysInMonth = $workingDaysData["total_days"];
        $sundays = $workingDaysData["sundays"];
        $saturdays = $workingDaysData["saturdays"];

        return $this->response->setJSON([
            "status" => "success",
            "working_days" => max($workingDays, 1),
            "total_days" => $daysInMonth,
            "sundays" => $sundays,
            "saturdays" => $saturdays,
        ]);
    }

    public function calculatePayroll()
    {
        $userId = $this->request->getPost("user_id");
        $month = (int) $this->request->getPost("month");
        $year = (int) $this->request->getPost("year");
        $payrollType = $this->request->getPost("payroll_type");
        $baseSalary = (float) $this->request->getPost("salary_amount");
        $usedPaidLeaves =
            (float) ($this->request->getPost("usedPaidLeaves") ?? 0);

        $companyRulesModel = new CompanyRulesModel();
        $attendanceModel = new AttendanceModel();
        $leaveModel = new LeaveModel();
        $holidayCalendarModel = new HolidayCalendarModel();

        $rules = $companyRulesModel->first();

        $startOfMonth =
            "$year-" . str_pad($month, 2, "0", STR_PAD_LEFT) . "-01";
        $endOfMonth = date("Y-m-t", strtotime($startOfMonth));

        $holidayRows = $holidayCalendarModel
            ->where("holiday_date >=", $startOfMonth)
            ->where("holiday_date <=", $endOfMonth)
            ->findAll();
        $holidayDates = array_column($holidayRows, "holiday_date");

        $workingDaysData = $this->getWorkingDaysData($month, $year, $rules, $holidayDates);
        $workingDays = $workingDaysData["working_days"];

        $totalLeavesInput = $this->request->getPost("total_leaves");
        if ($totalLeavesInput !== null) {
            $totalLeaves = (float) $totalLeavesInput;
        } else {
            $totalLeaves = $this->countMonthlyLeaves(
                $userId,
                "$year-" . str_pad($month, 2, "0", STR_PAD_LEFT),
                $rules,
                $leaveModel,
                $holidayDates,
            );
        }

        // Half-days – fetch rows so we can base deduction on missing hours (using gross worked time).
        $halfDayRows = $attendanceModel
            ->select("work_hours, date, status, check_in_time, check_out_time")
            ->where("user_id", $userId)
            ->where("status", "half-day")
            ->where("date >=", $startOfMonth)
            ->where("date <=", $endOfMonth)
            ->findAll();

        $fullDayHoursRule = isset($rules["working_hours_per_day"])
            ? (float) $rules["working_hours_per_day"]
            : 8.0;

        $halfDaysInput = $this->request->getPost("total_halfday_leaves");
        if ($halfDaysInput !== null) {
            $halfDays = (float) $halfDaysInput;
        } else {
            $halfDays = 0;
            foreach ($halfDayRows as $row) {
                // Use gross worked hours from check-in/out to decide if it is truly a half‑day.
                $displayWorkedSeconds = 0;
                if (!empty($row["check_in_time"]) && !empty($row["check_out_time"])) {
                    $inTs = strtotime($row["date"] . " " . $row["check_in_time"]);
                    $outTs = strtotime($row["date"] . " " . $row["check_out_time"]);
                    if ($outTs > $inTs) {
                        $displayWorkedSeconds = $outTs - $inTs;
                    }
                }
                $displayWorkedHours = $displayWorkedSeconds / 3600;

                // Count as half‑day only when gross worked hours are clearly below a full day
                if ($displayWorkedHours + 0.01 < $fullDayHoursRule) {
                    $halfDays++;
                }
            }
        }

        // Calculate how paid leave covers full days and half days
        // If usedPaidLeaves = 1.5, it means 1 full day + 1 half day (0.5)
        $fullDaysCoveredByPaidLeave = min(floor($usedPaidLeaves), $totalLeaves);
        $halfDaysCoveredByPaidLeave =
            ($usedPaidLeaves - $fullDaysCoveredByPaidLeave) * 2; // 0.5 = 1 half day

        // Unpaid leaves only (full days not covered by paid leave)
        $unpaidLeaves = max($totalLeaves - $fullDaysCoveredByPaidLeave, 0);

        // Unpaid half days (half days not covered by paid leave)
        $unpaidHalfDays = max($halfDays - $halfDaysCoveredByPaidLeave, 0);

        $deduction = 0;
        $workedHours = 0;
        $overtimeSeconds = 0;

        // Get overtime data for all payroll types
        $overtimeResult = $attendanceModel
            ->select("SUM(TIME_TO_SEC(overtime)) AS overtime_seconds")
            ->where("user_id", $userId)
            ->where("date >=", $startOfMonth)
            ->where("date <=", $endOfMonth)
            ->first();
        $overtimeSeconds = $overtimeResult["overtime_seconds"] ?? 0;

        // Late arrival is tracked for info purposes only – NOT deducted from salary
        $lateDeduction = 0;
        $totalLateMinutes = 0;
        if ($workingDays > 0 && ($rules["working_hours_per_day"] ?? 8) > 0) {
            $lateResult = $attendanceModel
                ->select("COALESCE(SUM(late_minutes), 0) AS total_late_minutes")
                ->where("user_id", $userId)
                ->where("date >=", $startOfMonth)
                ->where("date <=", $endOfMonth)
                ->where("status !=", "half-day")
                ->first();
            $totalLateMinutes = (int) ($lateResult["total_late_minutes"] ?? 0);
            // $lateDeduction intentionally remains 0 – late arrival does not affect salary
        }

        switch ($payrollType) {
            case "hourly":
                // Convert TIME → seconds
                $result = $attendanceModel
                    ->select(
                        "
                        SUM(TIME_TO_SEC(work_hours)) AS work_seconds,
                        SUM(TIME_TO_SEC(overtime)) AS overtime_seconds
                    ",
                    )
                    ->where("user_id", $userId)
                    ->where("date >=", $startOfMonth)
                    ->where("date <=", $endOfMonth)
                    ->first();

                $totalSeconds =
                    ($result["work_seconds"] ?? 0) +
                    ($result["overtime_seconds"] ?? 0);

                $workedHours = round($totalSeconds / 3600, 2);

                // Expected hours reduced by PAID leaves
                $expectedHours =
                    ($workingDays - $usedPaidLeaves) *
                    ($rules["working_hours_per_day"] ?? 8);

                $expectedHours = max($expectedHours, 0);

                $perHourRate =
                    $baseSalary /
                    ($workingDays * ($rules["working_hours_per_day"] ?? 8));

                $missingHours = max($expectedHours - $workedHours, 0);
                $deduction = $missingHours * $perHourRate; // late arrival excluded
                break;

            case "daily":
                $perDayRate = $baseSalary / $workingDays;
                $deduction = $unpaidLeaves * $perDayRate; // late arrival excluded
                break;

            case "monthly":
            default:
                $perDayRate = $baseSalary / $workingDays;

                // For monthly payroll, align with breakdown popup logic:
                // full-day deduction is based on *all* leave days in the month,
                // regardless of how many paid leaves are configured in the form.
                $fullDayDeduction = $totalLeaves * $perDayRate;

                // Half-day deduction based on missing hours across all half-day records,
                // mirroring the logic used in getDeductionBreakdown and salary-details grid.
                $halfDayMissingHoursTotal = 0.0;
                foreach ($halfDayRows as $row) {
                    $wh = $row["work_hours"] ?? null;
                    $workedHoursFloat = 0.0;
                    if (!empty($wh) && $wh !== "00:00:00") {
                        $parts = array_map("intval", explode(":", $wh));
                        $workedHoursFloat =
                            ($parts[0] ?? 0) +
                            ($parts[1] ?? 0) / 60 +
                            ($parts[2] ?? 0) / 3600;
                    }
                    // Treat entries with full-day or more hours as full present (no half-day deduction)
                    if ($workedHoursFloat + 0.01 >= $fullDayHoursRule) {
                        continue;
                    }
                    $requiredHours = ($rules["working_hours_per_day"] ?? 8);
                    $missingHours = max($requiredHours - $workedHoursFloat, 0);
                    $halfDayMissingHoursTotal += $missingHours;
                }

                $perHourRateForHalfDay =
                    $baseSalary /
                    ($workingDays * ($rules["working_hours_per_day"] ?? 8));
                $halfDayDeduction = $perHourRateForHalfDay > 0
                    ? round($halfDayMissingHoursTotal * $perHourRateForHalfDay, 2)
                    : 0;

                $deduction = $fullDayDeduction + $halfDayDeduction; // late arrival excluded
                break;
        }

        // Tax
        $taxDeduction = 0;
        if (
            ($rules["enable_tax"] ?? 0) == 1 &&
            $baseSalary > ($rules["salary_above_tax"] ?? 0)
        ) {
            $taxDeduction = $rules["tax"] ?? 0;
        }

        // Overtime pay calculation
        $overtimePay = 0;
        if (($rules["enable_overtime"] ?? 0) == 1 && $overtimeSeconds > 0) {
            $overtimeHours = round($overtimeSeconds / 3600, 2);

            if ($overtimeHours > 0) {
                $hourlyRate =
                    $baseSalary /
                    ($workingDays * ($rules["working_hours_per_day"] ?? 8));

                if (
                    ($rules["overtime_rate_type"] ?? "multiplier") ===
                    "multiplier"
                ) {
                    $overtimeRate =
                        $hourlyRate * ($rules["overtime_multiplier"] ?? 1.5);
                } else {
                    $overtimeRate = $hourlyRate;
                }

                $overtimePay = $overtimeHours * $overtimeRate;
            }
        }

        $netSalary = max(
            $baseSalary - $deduction - $taxDeduction,
            0,
        );

        // $netSalary = max($baseSalary - $deduction - $taxDeduction, 0);

        return $this->response->setJSON([
            "status" => "success",
            "data" => [
                "base_salary" => round($baseSalary, 2),
                "working_days" => $workingDays,
                "total_leaves" => $totalLeaves,
                "paid_leaves_used" => $usedPaidLeaves,
                "unpaid_leaves" => $unpaidLeaves,
                "half_days" => $halfDays,
                "unpaid_half_days" => $unpaidHalfDays,
                "worked_hours" => $workedHours,
                "per_hour_salary" => round(
                    $baseSalary /
                    ($workingDays * ($rules["working_hours_per_day"] ?? 8)),
                    2,
                ),
                "total_overtime_hours" => round($overtimeSeconds / 3600, 2),
                "overtime_pay" => round($overtimePay, 2),
                "late_deduction" => round($lateDeduction, 2),
                "salary_deduction" => round($deduction, 2),
                "tax_deduction" => round($taxDeduction, 2),
                "net_salary" => round($netSalary, 2),
                "pattern" => $rules["saturday_off_pattern"],
            ],
        ]);
    }

    /**
     * Helper function to calculate working days.
     * Holiday and Sunday (when off) are excluded so they "count as present" for payroll.
     *
     * @param array $holidayDates List of holiday dates in Y-m-d format for this month (optional)
     */
    private function getWorkingDaysData($month, $year, $rules, $holidayDates = [])
    {
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $sundays = 0;
        $saturdays = 0;

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $dayOfWeek = date("w", strtotime("$year-$month-$day"));
            if ($dayOfWeek == 0) {
                $sundays++;
            }
            if ($dayOfWeek == 6) {
                $saturdays++;
            }
        }

        $workingDays = $daysInMonth;
        if (
            isset($rules["include_holidays_in_working_days"]) &&
            $rules["include_holidays_in_working_days"] == 1
        ) {
            // Holidays count as working days (paid)
        } else {
            if ($rules["sunday_off"] == 1) {
                $workingDays -= $sundays;
            }

            if ($rules["saturday_off_enabled"] == 1) {
                if ($rules["saturday_off_type"] == "all") {
                    $workingDays -= $saturdays;
                } elseif ($rules["saturday_off_type"] == "alternate-even") {
                    $workingDays -= count(
                        explode(",", $rules["saturday_off_pattern"] ?? ""),
                    );
                } elseif ($rules["saturday_off_type"] == "alternate-odd") {
                    $workingDays -= count(
                        explode(",", $rules["saturday_off_pattern"] ?? ""),
                    );
                } elseif ($rules["saturday_off_type"] == "custom") {
                    $workingDays -= count(
                        explode(",", $rules["saturday_off_pattern"] ?? ""),
                    );
                }
            } else {
                if ($rules["saturday_off_type"] == "all") {
                    $workingDays -= $saturdays;
                } elseif ($rules["saturday_off_type"] == "alternate-even") {
                    $workingDays -= count(
                        explode(",", $rules["saturday_off_pattern"] ?? ""),
                    );
                } elseif ($rules["saturday_off_type"] == "alternate-odd") {
                    $workingDays -= count(
                        explode(",", $rules["saturday_off_pattern"] ?? ""),
                    );
                } elseif ($rules["saturday_off_type"] == "custom") {
                    $workingDays -= count(
                        explode(",", $rules["saturday_off_pattern"] ?? ""),
                    );
                }
            }

            // Exclude holidays from working days so holiday counts as present (no deduction)
            foreach ($holidayDates as $hd) {
                $d = (int) date("w", strtotime($hd));
                if ($d === 0 && ($rules["sunday_off"] ?? 0) == 1) {
                    continue;
                }
                if ($d === 6 && ($rules["saturday_off_enabled"] ?? 0) == 1) {
                    continue;
                }
                $workingDays--;
            }
        }
        $countHalfDays = 0;
        if (
            isset($rules["saturday_half_day_enabled"]) &&
            $rules['saturday_half_day_enabled'] == 1
        ) {
            $countHalfDays = count(explode(",", $rules['saturday_half_day_pattern']));
            // $workingDays -= $countHalfDays;
        }
        return [
            // "working_days" => max($workingDays, 1),
            "working_days" => max($workingDays, 1),
            "half_days" => $countHalfDays,
            "total_days" => $daysInMonth,
            "sundays" => $sundays,
            "saturdays" => $saturdays,
        ];
    }

    public function getLeaveDetails()
    {
        $leaveId = $this->request->getPost("leave_id");
        $userId = $this->request->getPost("user_id");
        $monthYear = $this->request->getPost("month_year");

        $leaveTypeModel = new \App\Models\LeaveTypeModel();
        $attendanceModel = new AttendanceModel();
        // Get number_of_leaves for selected leave type
        $leaveType = $leaveTypeModel->find($leaveId);
        $totalLeaves = 0;

        if ($leaveId == 1 || $leaveId == 2) { // Paid or Casual
            $employeeLeaveModel = new EmployeeLeaveModel();
            $employeeBalance = $employeeLeaveModel->where('employee_id', $userId)->first();
            if ($employeeBalance) {
                $totalLeaves = ($leaveId == 1) ? $employeeBalance['paid_leave'] : $employeeBalance['casual_leave'];
            } else {
                $totalLeaves = $leaveType ? (int) $leaveType["number_of_leaves"] : 0;
            }
        } else {
            $totalLeaves = $leaveType ? (int) $leaveType["number_of_leaves"] : 0;
        }

        // employee_leaves stores the current master balance for paid/casual leave.
        $remaining = max((float) $totalLeaves, 0);
        $halfDays = $attendanceModel
            ->where("user_id", $userId)
            ->where("status", "half-day")
            ->where("date >=", $monthYear)
            ->where("date <=", $monthYear)
            ->countAllResults(); // count only

        // Get allow_half_day from leave type
        $allowHalfDay = $leaveType
            ? (bool) ($leaveType["allow_half_day"] ?? 0)
            : false;

        return $this->response->setJSON([
            "total_leaves" => $totalLeaves,
            "used_leaves" => 0,
            "remaining_leaves" => $remaining,
            "total_half_day_leaves" => $halfDays,
            "allow_half_day" => $allowHalfDay,
        ]);
    }

    public function getRemainingPaidLeaves($userId)
    {
        $employeeLeaveModel = new EmployeeLeaveModel();
        $leaveBalance = $employeeLeaveModel
            ->where("employee_id", $userId)
            ->first();

        return $this->respond([
            "status" => "success",
            "remaining_paid_leaves" => (float) ($leaveBalance["paid_leave"] ?? 0),
        ]);
    }

    public function downloadMultiple()
    {
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized(
                "Unauthorized: Token missing or invalid",
            );
        }

        // Check if user is admin or hr
        $role = session()->get("role");
        if (!in_array($role, ["admin", "hr"])) {
            return $this->failForbidden(
                "Only admin and hr can download multiple slips",
            );
        }

        $input = $this->request->getJSON();
        $employeeIds = $input->employee_ids ?? [];
        $month = $input->month ?? null;

        if (empty($employeeIds) || !$month) {
            return $this->failValidationErrors(
                "Employee IDs and month are required",
            );
        }

        // Validate month format
        try {
            $date = new \DateTime($month . "-01");
        } catch (\Exception $e) {
            return $this->failValidationErrors("Invalid month format");
        }

        $payrollModel = new PayrollModel();
        $userInfoModel = new UserInfoModel();
        $companyModel = new CompanyLogoModel();
        $designationModel = new DesignationModel();
        $departmentModel = new DepartmentModel();
        $onboardingModel = new OnboardingModel();
        $attendanceModel = new AttendanceModel();
        $companyRulesModel = new CompanyRulesModel();

        $successCount = 0;
        $failedCount = 0;
        $slipsHtml = [];

        foreach ($employeeIds as $empId) {
            try {
                $payroll = $payrollModel
                    ->where("user_id", $empId)
                    ->where("month_year", $month)
                    ->first();

                if (!$payroll) {
                    $failedCount++;
                    continue;
                }

                // Get user info (employee details)
                $userInfo = $userInfoModel
                    ->where("user_id", $payroll["user_id"])
                    ->first();
                if (!$userInfo) {
                    $failedCount++;
                    continue;
                }

                // Get designation name
                $designation = $designationModel->find(
                    $userInfo["designation_id"],
                );

                // Get department name
                $department = $departmentModel->find(
                    $userInfo["department_id"],
                );

                $onboarding = $onboardingModel
                    ->where("job_id", $userInfo["job_id"])
                    ->first();

                // Get latest company info (logo, name, address)
                $company = $companyModel->orderBy("id", "DESC")->first();
                $companyLogoBase64 = "";
                if ($company && !empty($company["logo_img"])) {
                    $companyLogoPath =
                        FCPATH . "upload/" . $company["logo_img"];
                    if (file_exists($companyLogoPath)) {
                        $type = pathinfo($companyLogoPath, PATHINFO_EXTENSION);
                        $data = file_get_contents($companyLogoPath);
                        $companyLogoBase64 =
                            "data:image/" .
                            $type .
                            ";base64," .
                            base64_encode($data);
                    }
                }

                // Calculate working days and leave details from payroll month_year
                $monthYear = $payroll["month_year"];
                [$year, $monthNum] = explode("-", $monthYear);

                $rules = $companyRulesModel->first();

                // Get working days data
                $workingDaysData = $this->getWorkingDaysData(
                    (int) $monthNum,
                    (int) $year,
                    $rules,
                    [],
                );
                $workingDays = $workingDaysData["working_days"];

                // Get attendance data for the month
                $startOfMonth =
                    "$year-" . str_pad($monthNum, 2, "0", STR_PAD_LEFT) . "-01";
                $endOfMonth = date("Y-m-t", strtotime($startOfMonth));

                $attendanceData = $attendanceModel
                    ->where("user_id", $payroll["user_id"])
                    ->where("date >=", $startOfMonth)
                    ->where("date <=", $endOfMonth)
                    ->findAll();

                // Count present and absent days
                $presentDays = 0;
                $absentDays = 0;
                $halfDays = 0;

                foreach ($attendanceData as $record) {
                    if ($record["status"] === "present") {
                        $presentDays++;
                    } elseif ($record["status"] === "absent") {
                        $absentDays++;
                    } elseif ($record["status"] === "half-day") {
                        $halfDays++;
                    }
                }

                // Get leave details
                $totalLeaves = $payroll["total_leaves"] ?? 0;
                $usedPaidLeaves = $payroll["used_paid_leaves"] ?? 0;
                $unpaidLeaves = max($totalLeaves - $usedPaidLeaves, 0);

                // Calculate salary components
                $baseSalary = $payroll["salary_amount"] ?? 0;
                $overtimePay = $payroll["overtime_pay"] ?? 0;
                $bonuses = $payroll["bonuses"] ?? 0;
                $taxDeduction = $payroll["tax_deduction"] ?? 0;
                $salaryDeduction = $payroll["salary_deduction"] ?? 0;
                $totalLeaves = $payroll["total_leaves"] ?? 0;
                $usedPaidLeaves = $payroll["used_paid_leaves"] ?? 0;

                // Calculate total earnings (base + overtime + bonuses)
                $totalEarnings = $baseSalary + $overtimePay + $bonuses;

                // Calculate total deductions
                $totalDeductions = $salaryDeduction + $taxDeduction;

                // Build calculated data array
                $calculatedData = [
                    "working_days" => $workingDays,
                    "present_days" => $presentDays,
                    "absent_days" => $absentDays,
                    "total_leaves" => $totalLeaves,
                    "used_paid_leaves" => $usedPaidLeaves,
                    "unpaid_leaves" => $unpaidLeaves,
                    "half_days" => $halfDays,
                    "worked_hours" => $payroll["worked_hours"] ?? 0,
                    "total_overtime_hours" =>
                        $payroll["total_overtime_hours"] ?? 0,
                    "total_earnings" => $totalEarnings,
                    "salary_deduction" => $salaryDeduction,
                    "total_deductions" => $totalDeductions,
                ];

                // Combine all data to pass to view
                $data = [
                    "payroll" => $payroll,
                    "user" => $userInfo,
                    "designation" => $designation,
                    "department" => $department,
                    "company" => $company,
                    "companyLogoBase64" => $companyLogoBase64,
                    "onboarding" => $onboarding,
                    "calculatedData" => $calculatedData,
                ];

                // Generate HTML for this slip
                $slipHtml = view("payroll/salary_slip", $data);
                $slipsHtml[] = $slipHtml;
                $successCount++;
            } catch (\Exception $e) {
                log_message(
                    "error",
                    "Failed to generate slip for employee " .
                    $empId .
                    ": " .
                    $e->getMessage(),
                );
                $failedCount++;
                continue;
            }
        }

        if ($successCount === 0 || empty($slipsHtml)) {
            return $this->fail("No salary slips could be generated");
        }

        try {
            // '<div style="page-break-after: always;"></div>',
            $combinedHtml = implode($slipsHtml);

            $dompdf = new \Dompdf\Dompdf([
                "isRemoteEnabled" => true,
                "isHtml5ParserEnabled" => true,
                "isFontSubsettingEnabled" => true,
            ]);

            $dompdf->loadHtml($combinedHtml);
            $dompdf->setPaper("A4", "portrait");
            $dompdf->render();

            $filename = "salary-slips-" . $month . ".pdf";

            return $this->response
                ->setContentType("application/pdf")
                ->setBody($dompdf->output())
                ->setHeader(
                    "Content-Disposition",
                    'attachment; filename="' . $filename . '"',
                );
        } catch (\Exception $e) {
            log_message("error", "PDF generation failed: " . $e->getMessage());
            return $this->fail("Failed to generate PDF: " . $e->getMessage());
        }
    }

    /**
     * Download multiple salary slips by payroll IDs
     * Used by the payroll view page
     */
    public function downloadMultipleByIds()
    {
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized("Unauthorized: Token missing or invalid");
        }

        $role = session()->get("role");
        if (!in_array($role, ["admin", "hr"])) {
            return $this->failForbidden("Only admin and hr can download multiple slips");
        }

        $input = $this->request->getJSON();
        $payrollIds = $input->payroll_ids ?? [];
        $month = $input->month ?? date("Y-m");

        if (empty($payrollIds)) {
            return $this->failValidationErrors("Payroll IDs are required");
        }

        $pdfOutput = $this->generateCombinedSlipPdf($payrollIds);

        if (!$pdfOutput) {
            return $this->fail("No salary slips could be generated");
        }

        $filename = "salary-slips-" . $month . ".pdf";

        return $this->response
            ->setContentType("application/pdf")
            ->setBody($pdfOutput)
            ->setHeader("Content-Disposition", 'attachment; filename="' . $filename . '"');
    }

    public function downloadSlip($id)
    {
        $payrollModel = new PayrollModel();
        $userInfoModel = new UserInfoModel();
        $companyModel = new CompanyLogoModel();
        $designationModel = new DesignationModel();
        $departmentModel = new DepartmentModel();
        $onboardingModel = new OnboardingModel();
        $attendanceModel = new AttendanceModel();
        $companyRulesModel = new CompanyRulesModel();

        $payroll = $payrollModel->find($id);

        if (!$payroll) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException(
                "Payroll record not found",
            );
        }

        // Get user info (employee details)
        $userInfo = $userInfoModel
            ->where("user_id", $payroll["user_id"])
            ->first();

        // Get designation name
        $designation = $userInfo
            ? $designationModel->find($userInfo["designation_id"])
            : null;

        // Get department name
        $department = $userInfo
            ? $departmentModel->find($userInfo["department_id"])
            : null;
        $onboarding = $userInfo
            ? $onboardingModel->where("job_id", $userInfo["job_id"])->first()
            : null;

        // Get latest company info (logo, name, address)
        $company = $companyModel->orderBy("id", "DESC")->first();
        $companyLogoBase64 = "";
        if ($company && !empty($company["logo_img"])) {
            $companyLogoPath = FCPATH . "upload/" . $company["logo_img"];
            if (file_exists($companyLogoPath)) {
                $type = pathinfo($companyLogoPath, PATHINFO_EXTENSION);
                $data = file_get_contents($companyLogoPath);
                $companyLogoBase64 =
                    "data:image/" . $type . ";base64," . base64_encode($data);
            }
        }

        // Calculate working days and leave details from payroll month_year (same logic as group/single)
        $monthYear = $payroll["month_year"]; // Format: YYYY-MM
        [$year, $month] = explode("-", $monthYear);

        $rules = $companyRulesModel->first();

        $holidayCalendarModel = new HolidayCalendarModel();
        $startOfMonth =
            "$year-" . str_pad($month, 2, "0", STR_PAD_LEFT) . "-01";
        $endOfMonth = date("Y-m-t", strtotime($startOfMonth));
        $holidayRows = $holidayCalendarModel
            ->where("holiday_date >=", $startOfMonth)
            ->where("holiday_date <=", $endOfMonth)
            ->findAll();
        $holidayDates = array_column($holidayRows, "holiday_date");

        // Get working days data (exclude holidays so slip matches group/single)
        $workingDaysData = $this->getWorkingDaysData(
            (int) $month,
            (int) $year,
            $rules,
            $holidayDates,
        );
        $workingDays = $workingDaysData["working_days"];

        // Get attendance data for the month
        $attendanceData = $attendanceModel
            ->where("user_id", $payroll["user_id"])
            ->where("date >=", $startOfMonth)
            ->where("date <=", $endOfMonth)
            ->findAll();

        // Count present and absent days
        $presentDays = 0;
        $absentDays = 0;
        $halfDays = 0;

        foreach ($attendanceData as $record) {
            if ($record["status"] === "present") {
                $presentDays++;
            } elseif ($record["status"] === "absent") {
                $absentDays++;
            } elseif ($record["status"] === "half-day") {
                $halfDays++;
            }
        }

        // Get leave details
        $totalLeaves = $payroll["total_leaves"] ?? 0;
        $usedPaidLeaves = $payroll["used_paid_leaves"] ?? 0;
        $unpaidLeaves = max($totalLeaves - $usedPaidLeaves, 0);

        // Calculate salary components
        $baseSalary = $payroll["salary_amount"] ?? 0;
        $overtimePay = $payroll["overtime_pay"] ?? 0;
        $bonuses = $payroll["bonuses"] ?? 0;
        $taxDeduction = $payroll["tax_deduction"] ?? 0;
        $salaryDeduction = $payroll["salary_deduction"] ?? 0;

        // Calculate total earnings (base + overtime + bonuses)
        $totalEarnings = $baseSalary + $overtimePay + $bonuses;

        // Calculate total deductions
        $totalDeductions = $salaryDeduction + $taxDeduction;

        // Build calculated data array
        $calculatedData = [
            "working_days" => $workingDays,
            "present_days" => $presentDays,
            "absent_days" => $absentDays,
            "total_leaves" => $totalLeaves,
            "used_paid_leaves" => $usedPaidLeaves,
            "unpaid_leaves" => $unpaidLeaves,
            "half_days" => $halfDays,
            "worked_hours" => $payroll["worked_hours"] ?? 0,
            "total_overtime_hours" => $payroll["total_overtime_hours"] ?? 0,
            "total_earnings" => $totalEarnings,
            "salary_deduction" => $salaryDeduction,
            "total_deductions" => $totalDeductions,
        ];

        // Combine all data to pass to view
        $data = [
            "payroll" => $payroll,
            "user" => $userInfo,
            "designation" => $designation,
            "department" => $department,
            "company" => $company,
            "companyLogoBase64" => $companyLogoBase64,
            "onboarding" => $onboarding,
            "calculatedData" => $calculatedData,
        ];

        // Prepare the HTML for the PDF
        $html = view("payroll/salary_slip", $data);

        // Load DOMPDF
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper("A4", "portrait");
        $dompdf->render();
        $username = $userInfo
            ? preg_replace(
                "/\s+/",
                "_",
                strtolower($userInfo["firstname"] ?? "user"),
            )
            : "user";
        return $this->response
            ->setContentType("application/pdf")
            ->setBody($dompdf->output())
            ->setHeader(
                "Content-Disposition",
                'attachment; filename="salary-slip-' .
                $username .
                "-" .
                $id .
                '.pdf"',
            );
    }

    public function save()
    {
        $user = $this->authService->check();
        if (!$user) {
            return $this->failUnauthorized(
                "Unauthorized: Token missing or invalid",
            );
        }

        if (!in_array($user->role, ["admin", "hr"])) {
            return $this->failForbidden(
                "Forbidden: You do not have access to this resource",
            );
        }

        $accountModel = new \App\Models\AccountDetailModel();
        $data = $this->request->getPost();
        $data["created_by"] = $user->sub;

        $validation = \Config\Services::validation();

        $validationRules = [
            "users_id" => [
                "rules" => "required|integer",
                "errors" => [
                    "required" => "Employee is required.",
                    "integer" => "Invalid employee ID.",
                ],
            ],
            "acc_numbers" => [
                "rules" => "required|is_unique[account_detail.acc_number]",
                "errors" => [
                    "required" => "Account number is required.",
                    "is_unique" => "This account number already exists.",
                ],
            ],
            "bank_names" => [
                "rules" => "required",
                "errors" => ["required" => "Bank name is required."],
            ],
            "ifsc_codes" => [
                "rules" => "required",
                "errors" => ["required" => "IFSC code is required."],
            ],
            "acc_in_names" => [
                "rules" => "required",
                "errors" => ["required" => "Account holder name is required."],
            ],
            "branch_names" => [
                "rules" => "required",
                "errors" => ["required" => "Branch name is required."],
            ],
            "branch_codes" => [
                "rules" => "required",
                "errors" => ["required" => "Branch code is required."],
            ],
            "created_by" => [
                "rules" => "required",
                "errors" => ["required" => "Creator information is missing."],
            ],
        ];

        if (!$validation->setRules($validationRules)->run($data)) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON(["errors" => $validation->getErrors()]);
        }
        $model = new \App\Models\AccountDetailModel();
        $data = [
            "user_id" => $this->request->getPost("users_id"),
            "acc_in_name" => $this->request->getPost("acc_in_names"),
            "acc_number" => $this->request->getPost("acc_numbers"),
            "bank_name" => $this->request->getPost("bank_names"),
            "ifsc_code" => $this->request->getPost("ifsc_codes"),
            "branch_name" => $this->request->getPost("branch_names"),
            "branch_code" => $this->request->getPost("branch_codes"),
            "created_by" => session()->get("user_id"),
        ];
        $model->save($data);

        // Fetch the latest inserted data to return back
        $account = $model->where("user_id", $data["user_id"])->first();

        return $this->response->setJSON([
            "status" => "success",
            "data" => $account,
        ]);
    }

    public function groupsalaryPage()
    {
        // Redirect to salary-details with current month if accessed directly
        $currentMonth = date("Y-m");
        return redirect()->to("/payroll/salary-details?month=" . $currentMonth);
    }

    public function salaryDetails()
    {
        $month = $this->request->getGet("month"); // format: YYYY-MM
        if (!$month) {
            // Default to last month
            $lastMonth = new \DateTime("first day of last month");
            $month = $lastMonth->format("Y-m");
        }

        try {
            $date = new \DateTime($month . "-01");
            $daysInMonth = (int) $date->format("t");
            $startOfMonth = $date->format("Y-m-01");
            $endOfMonth = $date->format("Y-m-t");
            $year = (int) $date->format("Y");
            $monthNum = (int) $date->format("m");
        } catch (\Exception $e) {
            return redirect()
                ->to("/payroll")
                ->with("error", "Invalid month format.");
        }

        $userModel = new \App\Models\UserModel();
        $userInfoModel = new \App\Models\UserInfoModel();
        $leaveModel = new \App\Models\LeaveModel();
        $payrollModel = new \App\Models\PayrollModel();
        $companyRulesModel = new \App\Models\CompanyRulesModel();
        $attendanceModel = new \App\Models\AttendanceModel();
        $holidayCalendarModel = new HolidayCalendarModel();

        $rules = $companyRulesModel->first();

        $holidayRows = $holidayCalendarModel
            ->where("holiday_date >=", $startOfMonth)
            ->where("holiday_date <=", $endOfMonth)
            ->findAll();
        $holidayDates = array_column($holidayRows, "holiday_date");

        // Calculate working days (holidays and Sundays excluded = count as present)
        $workingDaysData = $this->getWorkingDaysData($monthNum, $year, $rules, $holidayDates);
        $workingDays = $workingDaysData["working_days"];
        $workingHoursPerDay = (float) ($rules["working_hours_per_day"] ?? 8);
        $workingHours = $workingDaysData["working_days"] * $workingHoursPerDay;
        $saturday_half_day_enabled = $rules['saturday_half_day_enabled'] ?? 0;
        $saturday_half_day_pattern = $rules['saturday_half_day_pattern'] ?? '';
        $half_day_hours = isset($rules['half_day_hours']) && $rules['half_day_hours'] !== '' && $rules['half_day_hours'] !== null
            ? (float) $rules['half_day_hours'] : 5;
        $halfDays = $workingDaysData['half_days'];
        $halfDayWorkHours = $half_day_hours * $halfDays;
        $totalWorkHours = $workingHours + $halfDayWorkHours;

        $employees = $userInfoModel
            ->select('user_info.*')
            ->join('users', 'users.id = user_info.user_id')
            ->where('users.is_deleted', 0)
            ->whereIn('user_info.role', ['employee', 'hr'])
            ->findAll();

        $employeeLeaveModel = new \App\Models\EmployeeLeaveModel();
        foreach ($employees as &$emp) {
            // Get existing payroll if saved
            $payroll = $payrollModel
                ->where("user_id", $emp["user_id"])
                ->where("month_year", $month)
                ->first();
            $emp["is_saved"] = $payroll ? true : false;
            $emp["days_in_month"] = $workingDays;
            $emp["hours_in_month"] = $totalWorkHours;

            // If a payroll record already exists for this month, use its saved salary_amount
            // (the value entered when payroll was created, e.g. ₹100,000) instead of the
            // employee profile salary from user_info (which may be a different/default value).
            if ($payroll && !empty($payroll["salary_amount"]) && (float) $payroll["salary_amount"] > 0) {
                $emp["salary"] = (float) $payroll["salary_amount"];
            }

            $emp["per_day"] = round($emp["salary"] / $workingDays, 2);
            $emp["per_hour"] = round($emp["salary"] / ($workingDays * $workingHoursPerDay), 2);

            $emp["tax_amount"] =
                $emp["salary"] > $rules["salary_above_tax"] ? $rules["tax"] : 0;
            $emp["tax"] =
                $emp["tax_amount"] > 0 ? "₹" . $emp["tax_amount"] : "No Tax";

            if ($payroll) {
                // ── Load payroll values ──────────────────────────────────────────────
                $emp["leaves"] = (float) ($payroll["total_leaves"] ?? 0);
                $emp["half_days"] = (float) ($payroll["total_half_day"] ?? 0);
                $emp["used_paid_leaves"] = (float) ($payroll["used_paid_leaves"] ?? 0);
                $emp["used_sick_leaves"] = (float) ($payroll["used_sick_leaves"] ?? 0);

                // For the management page, we ensure deductions match the counts shown to fix 
                // inconsistencies (e.g., leaves=1 but deduction=0).
                $suggestedBaseDed = ($emp["leaves"] * $emp["per_day"]) + ($emp["half_days"] * ($emp["per_day"] / 2));
                $paidLeaveCredit = ($emp["used_paid_leaves"] + $emp["used_sick_leaves"]) * $emp["per_day"];

                $storedDed = (float) ($payroll["salary_deduction"] ?? 0);
                // Override if stored deduction is 0 but leaves/half-days exist
                if ($storedDed == 0 && ($emp["leaves"] > 0 || $emp["half_days"] > 0)) {
                    $emp["salary_deduction"] = round(max($suggestedBaseDed - $paidLeaveCredit, 0), 2);
                } else {
                    $emp["salary_deduction"] = round($storedDed, 2);
                }

                $emp["tax_deduction"] = round((float) ($payroll["tax_deduction"] ?? 0), 2);
                $emp["overtime_pay"] = round((float) ($payroll["overtime_pay"] ?? 0), 2);
                $emp["total_overtime_hours"] = (float) ($payroll["total_overtime_hours"] ?? 0);
                $emp["late_deduction"] = 0;

                // base_deduction is used by JS for re-computation.
                $emp["base_deduction"] = round($emp["salary_deduction"] + $paidLeaveCredit, 2);

                // Recalculate net salary based on updated components
                $emp["net_salary"] = round($emp["salary"] - $emp["salary_deduction"] - $emp["tax_deduction"] + ($emp["overtime_pay"] ?? 0), 2);

                $emp["tax_amount"] = $emp["tax_deduction"];
                $emp["tax"] = $emp["tax_amount"] > 0
                    ? "₹" . number_format($emp["tax_amount"], 2)
                    : "No Tax";
            } else {
                $totalLeaves = $this->countMonthlyLeaves(
                    $emp["user_id"],
                    sprintf("%04d-%02d", $year, $monthNum),
                    $rules,
                    $leaveModel,
                    $holidayDates,
                );
                $usedPaidLeaves = 0;

                $emp["leaves"] = $totalLeaves;
                $emp["used_paid_leaves"] = $usedPaidLeaves;
                $emp["used_sick_leaves"] = 0;

                $halfDayRows = $attendanceModel
                    ->select("work_hours, status, date, check_in_time, check_out_time")
                    ->where("user_id", $emp["user_id"])
                    ->where("status", "half-day")
                    ->where("date >=", $startOfMonth)
                    ->where("date <=", $endOfMonth)
                    ->findAll();
                $emp["half_days"] = count($halfDayRows);

                $fullDaysCoveredByPaidLeave = min(floor($usedPaidLeaves + $emp["used_sick_leaves"]), $totalLeaves);
                $halfDaysCoveredByPaidLeave = ($usedPaidLeaves + $emp["used_sick_leaves"] - $fullDaysCoveredByPaidLeave) * 2;
                $unpaidFullDays = max($totalLeaves - $fullDaysCoveredByPaidLeave, 0);
                $unpaidHalfDays = max($emp["half_days"] - $halfDaysCoveredByPaidLeave, 0);

                $fullDayDeduction = $unpaidFullDays * $emp["per_day"];

                // Half-day deduction based on missing hours (same logic as breakdown)
                $halfDayMissingHoursTotal = 0.0;
                foreach ($halfDayRows as $row) {
                    $displayWorkedSeconds = 0;
                    if (!empty($row["check_in_time"]) && !empty($row["check_out_time"])) {
                        $inTs = strtotime(substr($row["date"], 0, 10) . " " . $row["check_in_time"]);
                        $outTs = strtotime(substr($row["date"], 0, 10) . " " . $row["check_out_time"]);
                        if ($outTs > $inTs) {
                            $displayWorkedSeconds = $outTs - $inTs;
                        }
                    }

                    $displayWorkedHours = $displayWorkedSeconds / 3600;
                    $fullDayHoursRule = isset($rules["working_hours_per_day"])
                        ? (float) $rules["working_hours_per_day"]
                        : 8.0;
                    if ($displayWorkedHours + 0.01 >= $fullDayHoursRule) {
                        continue;
                    }

                    $requiredHours = $workingHoursPerDay > 0 ? $workingHoursPerDay : $fullDayHoursRule;
                    $missingHours = max($requiredHours - $displayWorkedHours, 0);
                    $halfDayMissingHoursTotal += $missingHours;
                }

                $perHourRateForHalfDay = $workingDays > 0 && $workingHoursPerDay > 0
                    ? $emp["salary"] / ($workingDays * $workingHoursPerDay)
                    : 0;
                $halfDayDeduction = $perHourRateForHalfDay > 0
                    ? round($halfDayMissingHoursTotal * $perHourRateForHalfDay, 2)
                    : 0;

                $leaveHalfDeduction = $fullDayDeduction + $halfDayDeduction;

                // Late arrival is tracked for info only – NOT deducted from salary
                $lateDeduction = 0;
                $overtimePay = 0;
                $overtimeSeconds = 0;
                if ($workingDays > 0 && $workingHoursPerDay > 0) {
                    if (($rules["enable_overtime"] ?? 0) == 1) {
                        $otResult = $attendanceModel
                            ->select("SUM(TIME_TO_SEC(overtime)) AS overtime_seconds")
                            ->where("user_id", $emp["user_id"])
                            ->where("date >=", $startOfMonth)
                            ->where("date <=", $endOfMonth)
                            ->first();
                        $overtimeSeconds = (int) ($otResult["overtime_seconds"] ?? 0);
                        if ($overtimeSeconds > 0) {
                            $overtimeHours = round($overtimeSeconds / 3600, 2);
                            $hourlyRate = $emp["salary"] / ($workingDays * $workingHoursPerDay);
                            $multiplier = ($rules["overtime_rate_type"] ?? "multiplier") === "multiplier"
                                ? ($rules["overtime_multiplier"] ?? 1.5) : 1;
                            $overtimePay = round($overtimeHours * $hourlyRate * $multiplier, 2);
                        }
                    }
                }

                $emp["late_deduction"] = 0; // late arrival not deducted
                $baseFullDayDeduction = $totalLeaves * $emp["per_day"];
                $baseLeaveHalfDeduction = $baseFullDayDeduction + $halfDayDeduction;
                $emp["base_deduction"] = round($baseLeaveHalfDeduction, 2);
                $emp["overtime_pay"] = $overtimePay;
                $emp["total_overtime_hours"] = round($overtimeSeconds / 3600, 2);
                $emp["salary_deduction"] = round($leaveHalfDeduction, 2);
                $emp["tax_deduction"] = $emp["tax_amount"];
                $emp["net_salary"] = round(
                    $emp["salary"] - $emp["salary_deduction"] - $emp["tax_deduction"] + ($emp["overtime_pay"] ?? 0),
                    2,
                );
            }

            $leaveBalance = $employeeLeaveModel
                ->where('employee_id', $emp['user_id'])
                ->first();
            $currentPaidBalance = (float) ($leaveBalance['paid_leave'] ?? 0);
            $currentSickBalance = (float) ($leaveBalance['casual_leave'] ?? 0);
            $savedHalfDayEquivalent = $payroll ? max((float) ($payroll['total_half_day'] ?? 0), 0) / 2 : 0;

            // Use employee_leaves as the master balance, then rebuild the row's
            // editable opening balance by adding back the currently saved month.
            $emp['opening_paid_leaves'] = $currentPaidBalance
                + (float) ($payroll['used_paid_leaves'] ?? 0)
                + $savedHalfDayEquivalent;
            $emp['opening_casual_leaves'] = $currentSickBalance
                + (float) ($payroll['used_sick_leaves'] ?? 0);
            $emp['remaining_paid_leaves'] = max(
                $emp['opening_paid_leaves']
                    - (max((float) ($emp['half_days'] ?? 0), 0) * 0.5)
                    - (float) ($emp['used_paid_leaves'] ?? 0),
                0,
            );
            $emp['remaining_casual_leaves'] = max(
                $emp['opening_casual_leaves'] - (float) ($emp['used_sick_leaves'] ?? 0),
                0,
            );

            // Calculate Total Adjustment (Additions - Deductions)
            $emp['total_adjustment'] = ($emp['overtime_pay'] ?? 0) - ($emp['salary_deduction'] ?? 0) - ($emp['tax_deduction'] ?? 0);
        }

        return view("payroll/salary-details", [
            "month" => $month,
            "employees" => $employees,
        ]);
    }

    public function saveAll()
    {
        $request = $this->request;

        $employeeIds = $request->getPost("employee_id");
        $salaries = $request->getPost("salary");
        $leaves = $request->getPost("leaves");
        $half_day = $request->getPost("half_day");
        $paid_leave = $request->getPost("paid_leave");
        $sick_leave = $request->getPost("sick_leave") ?? [];
        $deductions = $request->getPost("deduction");
        $netSalaries = $request->getPost("net_salary");
        $overtime_pay = $request->getPost("overtime_pay");
        $total_overtime_hours = $request->getPost("total_overtime_hours");
        $month = $request->getPost("month");

        $payrollModel = new \App\Models\PayrollModel();
        $employeeLeaveModel = new EmployeeLeaveModel();
        $companyRulesModel = new \App\Models\CompanyRulesModel();
        $rules = $companyRulesModel->first();
        $db = \Config\Database::connect();

        $db->transBegin();

        try {
            foreach ($employeeIds as $index => $empId) {
                $existing = $payrollModel
                    ->where("user_id", $empId)
                    ->where("month_year", $month)
                    ->first();

                $totalHalfDays = (float) ($half_day[$index] ?? 0);
                $usedPaidLeaves = (float) ($paid_leave[$index] ?? 0);
                $usedSickLeaves = (float) ($sick_leave[$index] ?? 0);
                $remainingLeaves = $this->syncEmployeeLeaveBalance(
                    $employeeLeaveModel,
                    (int) $empId,
                    (string) $month,
                    $totalHalfDays,
                    $usedPaidLeaves,
                    $usedSickLeaves,
                    $existing
                );

                // Determine tax deduction: preserve existing tax_deduction if record already
                // exists (may have been customised via Add Payroll), otherwise calculate it.
                $taxDeduction = $existing
                    ? $existing["tax_deduction"]
                    : ($salaries[$index] > $rules["salary_above_tax"] ? $rules["tax"] : 0);

                $data = [
                    "user_id" => $empId,
                    "month_year" => $month,
                    "salary_amount" => $salaries[$index],
                    "total_leaves" => $leaves[$index],
                    "total_half_day" => $half_day[$index],
                    "used_paid_leaves" => $usedPaidLeaves,
                    "used_sick_leaves" => $usedSickLeaves,
                    "remaining_paid_leaves" => $remainingLeaves["paid_leave"],
                    "remaining_sick_leaves" => $remainingLeaves["casual_leave"],
                    "salary_deduction" => $deductions[$index],
                    "tax_deduction" => $taxDeduction,
                    "net_salary" => $netSalaries[$index],
                    "overtime_pay" => isset($overtime_pay[$index]) ? (float) $overtime_pay[$index] : 0,
                    "total_overtime_hours" => isset($total_overtime_hours[$index]) ? (float) $total_overtime_hours[$index] : 0,
                    "payment_date" => $existing ? $existing["payment_date"] : date("Y-m-d H:i:s"),
                    "payment_status" => $existing ? $existing["payment_status"] : "Paid",
                ];

                if ($existing) {
                    // Update existing record with latest calculated values from salary-details page
                    $data["id"] = $existing["id"];
                    $payrollModel->save($data);
                } else {
                    $data["created_at"] = date("Y-m-d H:i:s");
                    $payrollModel->insert($data);
                }

                if ($payrollModel->errors() || $employeeLeaveModel->errors()) {
                    throw new \RuntimeException("Failed to save payroll leave balances.");
                }
            }

            if ($db->transStatus() === false) {
                throw new \RuntimeException("Database transaction failed.");
            }
        } catch (\Throwable $e) {
            $db->transRollback();

            return $this->response->setStatusCode(500)->setJSON([
                "status" => "error",
                "message" => "Failed to save payroll data.",
            ]);
        }

        $db->transCommit();

        return $this->response->setJSON([
            "status" => "success",
            "message" => "Payroll data saved successfully for all employees.",
        ]);
    }

    /**
     * Get deduction breakdown for an employee in a month (for info modal).
     * Returns: leaves (dates), late (date + minutes), absent (dates), half-day (dates), overtime (date + hours), and amount breakdown.
     */
    public function getDeductionBreakdown()
    {
        $userId = $this->request->getPost("user_id") ?: $this->request->getGet("user_id");
        $month = $this->request->getPost("month") ?: $this->request->getGet("month");
        if (!$userId || !$month) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "user_id and month are required.",
            ])->setStatusCode(400);
        }
        $date = new \DateTime($month . "-01");
        $startOfMonth = $date->format("Y-m-01");
        $endOfMonth = $date->format("Y-m-t");

        $attendanceModel = new AttendanceModel();
        $leaveModel = new LeaveModel();
        $companyRulesModel = new CompanyRulesModel();
        $holidayCalendarModel = new HolidayCalendarModel();
        $rules = $companyRulesModel->first();
        $workingHoursPerDay = (float) ($rules["working_hours_per_day"] ?? 8);

        $holidayRows = $holidayCalendarModel
            ->where("holiday_date >=", $startOfMonth)
            ->where("holiday_date <=", $endOfMonth)
            ->findAll();
        $holidayDates = array_column($holidayRows, "holiday_date");

        $workingDaysData = $this->getWorkingDaysData(
            (int) $date->format("m"),
            (int) $date->format("Y"),
            $rules,
            $holidayDates,
        );
        $workingDays = $workingDaysData["working_days"];

        $manualSalary = $this->request->getPost("salary_amount") ?: $this->request->getGet("salary_amount");
        if ($manualSalary !== null && (float) $manualSalary > 0) {
            $salary = (float) $manualSalary;
        } else {
            $userInfo = (new UserInfoModel())->where("user_id", $userId)->first();
            $salary = $userInfo["salary"] ?? 0;
        }
        $perDay = $workingDays > 0 ? round($salary / $workingDays, 2) : 0;
        $perHour = ($workingDays > 0 && $workingHoursPerDay > 0) ? round($salary / ($workingDays * $workingHoursPerDay), 2) : 0;

        $attendance = $attendanceModel
            ->where("user_id", $userId)
            ->where("date >=", $startOfMonth)
            ->where("date <=", $endOfMonth)
            ->orderBy("date", "ASC")
            ->findAll();

        // Group statuses by date so that if a day has any present/half-day/etc record,
        // it is not treated as absent for payroll purposes.
        $statusesByDate = [];
        foreach ($attendance as $row) {
            $dateKey = substr($row["date"], 0, 10);
            $statusKey = strtolower($row["status"] ?? "");
            if (!isset($statusesByDate[$dateKey])) {
                $statusesByDate[$dateKey] = [];
            }
            if ($statusKey !== "") {
                $statusesByDate[$dateKey][] = $statusKey;
            }
        }

        // Build a quick lookup of dates that are covered by approved leave applications
        $leaveApplications = $leaveModel
            ->where("user_id", $userId)
            ->whereIn("status", ["approved", "Approved"])
            ->where("start_date <=", $endOfMonth)
            ->where("end_date >=", $startOfMonth)
            ->orderBy("start_date", "ASC")
            ->findAll();
        $leaveCoveredDates = [];
        foreach ($leaveApplications as $lv) {
            $start = max($lv["start_date"], $startOfMonth);
            $end = min($lv["end_date"], $endOfMonth);
            $current = strtotime($start);
            $endTs = strtotime($end);
            while ($current <= $endTs) {
                $leaveCoveredDates[date("Y-m-d", $current)] = true;
                $current = strtotime("+1 day", $current);
            }
        }

        $holidayLookup = array_flip($holidayDates);
        $absentDates = [];
        $halfDayDates = [];
        $lateList = [];
        $overtimeList = [];
        $halfDayMissingHoursTotal = 0.0;
        foreach ($attendance as $row) {
            $dateStr = $row["date"];
            $dateKey = substr($dateStr, 0, 10);
            $allStatusesForDate = $statusesByDate[$dateKey] ?? [];
            $hasNonAbsentStatusForDate = false;
            foreach ($allStatusesForDate as $st) {
                if ($st !== "absent" && $st !== "") {
                    $hasNonAbsentStatusForDate = true;
                    break;
                }
            }
            $dayOfWeek = (int) date("w", strtotime($dateStr)); // 0=Sun,6=Sat

            // Determine if this day is a non-working day (holiday/Sunday/Saturday-off).
            $isHoliday = isset($holidayLookup[$dateStr]);
            $isSundayOff = ($dayOfWeek === 0 && ($rules["sunday_off"] ?? 0) == 1);
            $isSaturdayOff = false;
            if (($rules["saturday_off_enabled"] ?? 0) == 1 && $dayOfWeek === 6) {
                if (($rules["saturday_off_type"] ?? "") === "all") {
                    $isSaturdayOff = true;
                }
            }
            $isNonWorking = $isHoliday || $isSundayOff || $isSaturdayOff;

            // Only treat a day as "absent" for payroll if:
            // - status is 'absent'
            // - it is a working day (not holiday/week off)
            // - it is NOT already covered by an approved leave
            if (
                $row["status"] === "absent" &&
                !$isNonWorking &&
                empty($leaveCoveredDates[$dateKey]) &&
                !$hasNonAbsentStatusForDate
            ) {
                $absentDates[] = ["date" => $dateStr, "label" => date("d M Y", strtotime($dateStr))];
            }
            if ($row["status"] === "half-day") {
                // Compute gross worked time from check-in/out (same as attendance history).
                $displayWorkedSeconds = 0;
                if (!empty($row["check_in_time"]) && !empty($row["check_out_time"])) {
                    $inTs = strtotime(substr($row["date"], 0, 10) . " " . $row["check_in_time"]);
                    $outTs = strtotime(substr($row["date"], 0, 10) . " " . $row["check_out_time"]);
                    if ($outTs > $inTs) {
                        $displayWorkedSeconds = $outTs - $inTs;
                    }
                }

                $displayWorkedHours = $displayWorkedSeconds / 3600;
                $fullDayHoursRule = isset($rules["working_hours_per_day"])
                    ? (float) $rules["working_hours_per_day"]
                    : 8.0;

                // If gross worked hours reach (or exceed) a full day, treat as full present (no half‑day here).
                if ($displayWorkedHours + 0.01 >= $fullDayHoursRule) {
                    continue;
                }

                // Calculate missing hours for this date based on gross worked time
                $requiredHours = $workingHoursPerDay > 0
                    ? $workingHoursPerDay
                    : (float) ($rules["working_hours_per_day"] ?? 8);
                $missingHours = max($requiredHours - $displayWorkedHours, 0);
                $halfDayMissingHoursTotal += $missingHours;

                $workedLabel = $displayWorkedSeconds > 0
                    ? sprintf(
                        "%dh %dm",
                        floor($displayWorkedHours),
                        round(($displayWorkedHours - floor($displayWorkedHours)) * 60)
                    )
                    : "0h 0m";
                $missingLabel = $missingHours > 0
                    ? sprintf("%.0fh %.0fm", floor($missingHours), round(($missingHours - floor($missingHours)) * 60))
                    : "0h 0m";

                $halfDayDates[] = [
                    "date" => $row["date"],
                    "label" => date("d M Y", strtotime($row["date"])),
                    "worked_text" => $workedLabel,
                    "missing_text" => $missingLabel,
                    "missing_hours" => $missingHours,
                ];
            }
            if ($row["status"] != "half-day") {
                if (!empty($row["is_late"]) && (int) $row["late_minutes"] > 0) {
                    $mins = (int) $row["late_minutes"];
                    $lateList[] = [
                        "date" => $row["date"],
                        "label" => date("d M Y", strtotime($row["date"])),
                        "late_minutes" => $mins,
                        "late_text" => $mins >= 60 ? floor($mins / 60) . "h " . ($mins % 60) . "m" : $mins . " min",
                    ];
                }
            }
            if (!empty($row["overtime"]) && $row["overtime"] !== "00:00:00") {
                $parts = array_map("intval", explode(":", $row["overtime"]));
                $secs = ($parts[0] ?? 0) * 3600 + ($parts[1] ?? 0) * 60 + ($parts[2] ?? 0);
                $hours = round($secs / 3600, 2);
                $overtimeList[] = [
                    "date" => $row["date"],
                    "label" => date("d M Y", strtotime($row["date"])),
                    "overtime_hours" => $hours,
                    "overtime_text" => ($parts[0] ?? 0) . "h " . ($parts[1] ?? 0) . "m",
                ];
            }
        }

        $leaveList = [];
        foreach ($leaveApplications as $lv) {
            $start = $lv["start_date"];
            $end = $lv["end_date"];
            if ($start < $startOfMonth)
                $start = $startOfMonth;
            if ($end > $endOfMonth)
                $end = $endOfMonth;
            $leaveList[] = [
                "start_date" => $start,
                "end_date" => $end,
                "label" => date("d M", strtotime($start)) . " - " . date("d M Y", strtotime($end)),
                "reason" => $lv["reason"] ?? "",
            ];
        }

        $totalLeavesInput = $this->request->getPost("total_leaves") ?: $this->request->getGet("total_leaves");
        if ($totalLeavesInput !== null) {
            $totalLeaves = (float) $totalLeavesInput;
        } else {
            $totalLeaves = $this->countMonthlyLeaves($userId, $month, $rules, $leaveModel, $holidayDates);
        }

        $halfDaysInput = $this->request->getPost("total_halfday_leaves") ?: $this->request->getGet("total_halfday_leaves");
        if ($halfDaysInput !== null) {
            $halfDayCount = (float) $halfDaysInput;
            $halfDayMissingHoursTotal = $halfDayCount * ($workingHoursPerDay / 2);
        } else {
            $halfDayCount = count($halfDayDates);
        }

        $totalLateMinutes = array_sum(array_column($lateList, "late_minutes"));
        $lateDeduction = round(($totalLateMinutes / 60) * $perHour, 2);
        $leaveDeduction = $totalLeaves * $perDay;
        // Half-day deduction is proportional to total missing hours across all half-day dates
        $halfDayDeduction = $perHour > 0
            ? round($halfDayMissingHoursTotal * $perHour, 2)
            : 0;
        $totalOvertimeSeconds = 0;
        foreach ($attendance as $row) {
            if (!empty($row["overtime"]) && $row["overtime"] !== "00:00:00") {
                $parts = array_map("intval", explode(":", $row["overtime"]));
                $totalOvertimeSeconds += ($parts[0] ?? 0) * 3600 + ($parts[1] ?? 0) * 60 + ($parts[2] ?? 0);
            }
        }
        $overtimeHours = round($totalOvertimeSeconds / 3600, 2);
        $multiplier = ($rules["overtime_rate_type"] ?? "multiplier") === "multiplier" ? ($rules["overtime_multiplier"] ?? 1.5) : 1;
        $overtimePay = ($workingDays > 0 && $workingHoursPerDay > 0 && $overtimeHours > 0)
            ? round($overtimeHours * $perHour * $multiplier, 2) : 0;

        return $this->response->setJSON([
            "status" => "success",
            "data" => [
                "employee_name" => $userInfo["firstname"] ?? "Employee",
                "month_label" => $date->format("F Y"),
                "leaves" => ["dates" => $leaveList, "count" => $totalLeaves, "deduction_amount" => round($leaveDeduction, 2)],
                "absent" => ["dates" => $absentDates, "count" => count($absentDates)],
                "half_day" => [
                    "dates" => $halfDayDates,
                    "count" => $halfDayCount,
                    "deduction_amount" => round($halfDayDeduction, 2),
                    "total_missing_hours" => $halfDayMissingHoursTotal,
                ],
                "late" => ["list" => $lateList, "total_minutes" => $totalLateMinutes, "deduction_amount" => $lateDeduction],
                "overtime" => ["list" => $overtimeList, "total_hours" => $overtimeHours, "pay_amount" => $overtimePay],
                "summary" => [
                    "total_deduction" => round($leaveDeduction + $halfDayDeduction, 2), // late arrival excluded
                    "overtime_added" => $overtimePay,
                    "per_day_salary" => $perDay,
                    "per_hour_salary" => $perHour,
                ],
            ],
        ]);
    }

    public function savedata()
    {
        $userId = $this->request->getPost("employee_id");
        $month = $this->request->getPost("month");
        $salary = $this->request->getPost("salary");

        $payrollModel = new PayrollModel();
        $employeeLeaveModel = new EmployeeLeaveModel();
        $companyRulesModel = new \App\Models\CompanyRulesModel();
        $rules = $companyRulesModel->first();
        $salaryAboveTax = $rules["salary_above_tax"] ?? PHP_INT_MAX;
        $tax = $rules["tax"] ?? 0;
        $taxDeduction =
            $salary > $salaryAboveTax ? $tax : 0;

        $totalHalfDays = (float) ($this->request->getPost("half_day") ?? 0);
        $usedPaidLeaves = (float) ($this->request->getPost("paid_leave") ?? 0);
        $usedSickLeaves = (float) ($this->request->getPost("sick_leave") ?? 0);

        $existing = $payrollModel
            ->where("user_id", $userId)
            ->where("month_year", $month)
            ->first();

        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            $remainingLeaves = $this->syncEmployeeLeaveBalance(
                $employeeLeaveModel,
                (int) $userId,
                (string) $month,
                $totalHalfDays,
                $usedPaidLeaves,
                $usedSickLeaves,
                $existing
            );

            $data = [
                "user_id" => $userId,
                "month_year" => $month,
                "salary_amount" => $salary,
                "total_leaves" => $this->request->getPost("leaves"),
                "total_half_day" => $this->request->getPost("half_day"),
                "used_paid_leaves" => $usedPaidLeaves,
                "used_sick_leaves" => $usedSickLeaves,
                "remaining_paid_leaves" => $remainingLeaves["paid_leave"],
                "remaining_sick_leaves" => $remainingLeaves["casual_leave"],
                "salary_deduction" => $this->request->getPost("deduction"),
                "tax_deduction" => $taxDeduction,
                "net_salary" => $this->request->getPost("net_salary"),
                "overtime_pay" => (float) ($this->request->getPost("overtime_pay") ?? 0),
                "total_overtime_hours" => (float) ($this->request->getPost("total_overtime_hours") ?? 0),
                "payment_date" => $existing ? $existing["payment_date"] : date("Y-m-d H:i:s"),
                "payment_status" => $existing ? $existing["payment_status"] : "Paid",
            ];

            if ($existing) {
                $data["id"] = $existing["id"];
                $payrollModel->save($data);
            } else {
                $payrollModel->insert($data);
            }

            if ($payrollModel->errors() || $employeeLeaveModel->errors() || $db->transStatus() === false) {
                throw new \RuntimeException("Failed to save salary data.");
            }
        } catch (\Throwable $e) {
            $db->transRollback();

            return $this->response->setStatusCode(500)->setJSON([
                "status" => "error",
                "message" => "Failed to save salary for the employee.",
            ]);
        }

        $db->transCommit();

        return $this->response->setStatusCode(200)->setJSON([
            "status" => "success",
            "message" => $existing
                ? "Salary updated for the employee."
                : "Salary saved for the employee.",
        ]);
    }

    private function syncEmployeeLeaveBalance(
        EmployeeLeaveModel $employeeLeaveModel,
        int $employeeId,
        string $monthYear,
        float $totalHalfDays,
        float $usedPaidLeaves,
        float $usedSickLeaves,
        ?array $existingPayroll = null
    ): array {
        $leaveBalance = $employeeLeaveModel
            ->where("employee_id", $employeeId)
            ->first();
        $currentPaidBalance = (float) ($leaveBalance["paid_leave"] ?? 0);
        $currentSickBalance = (float) ($leaveBalance["casual_leave"] ?? 0);
        $existingHalfDayPaidLeaveEquivalent = max((float) ($existingPayroll["total_half_day"] ?? 0), 0) / 2;
        $halfDayPaidLeaveEquivalent = max($totalHalfDays, 0) / 2;
        $openingPaidLeave = $currentPaidBalance
            + (float) ($existingPayroll["used_paid_leaves"] ?? 0)
            + $existingHalfDayPaidLeaveEquivalent;
        $openingSickLeave = $currentSickBalance
            + (float) ($existingPayroll["used_sick_leaves"] ?? 0);
        $remainingPaidLeave = max(
            $openingPaidLeave - $usedPaidLeaves - $halfDayPaidLeaveEquivalent,
            0,
        );
        $remainingSickLeave = max(
            $openingSickLeave - $usedSickLeaves,
            0,
        );

        $leaveData = [
            "employee_id" => $employeeId,
            "paid_leave" => $remainingPaidLeave,
            "casual_leave" => $remainingSickLeave,
        ];

        if (!empty($leaveBalance["id"])) {
            $leaveData["id"] = $leaveBalance["id"];
        }

        $employeeLeaveModel->save($leaveData);

        return [
            "opening_paid_leave" => $openingPaidLeave,
            "opening_casual_leave" => $openingSickLeave,
            "paid_leave" => $remainingPaidLeave,
            "casual_leave" => $remainingSickLeave,
        ];
    }

    private function generateCombinedSlipPdf(array $payrollIds)
    {
        $payrollModel = new PayrollModel();
        $userInfoModel = new UserInfoModel();
        $companyModel = new CompanyLogoModel();
        $designationModel = new DesignationModel();
        $departmentModel = new DepartmentModel();
        $onboardingModel = new OnboardingModel();
        $attendanceModel = new AttendanceModel();
        $companyRulesModel = new CompanyRulesModel();

        $slipsHtml = [];

        foreach ($payrollIds as $payrollId) {
            try {
                $payroll = $payrollModel->find($payrollId);
                if (!$payroll)
                    continue;

                $userInfo = $userInfoModel->where("user_id", $payroll["user_id"])->first();
                if (!$userInfo)
                    continue;

                $designation = $designationModel->find($userInfo["designation_id"]);
                $department = $departmentModel->find($userInfo["department_id"]);
                $onboarding = $onboardingModel->where("job_id", $userInfo["job_id"])->first();
                $company = $companyModel->orderBy("id", "DESC")->first();

                $companyLogoBase64 = "";
                if ($company && !empty($company["logo_img"])) {
                    $companyLogoPath = FCPATH . "upload/" . $company["logo_img"];
                    if (file_exists($companyLogoPath)) {
                        $type = pathinfo($companyLogoPath, PATHINFO_EXTENSION);
                        $data = file_get_contents($companyLogoPath);
                        $companyLogoBase64 = "data:image/" . $type . ";base64," . base64_encode($data);
                    }
                }

                $monthYear = $payroll["month_year"];
                [$year, $monthNum] = explode("-", $monthYear);
                $rules = $companyRulesModel->first();

                $workingDaysData = $this->getWorkingDaysData((int) $monthNum, (int) $year, $rules, []);
                $workingDays = $workingDaysData["working_days"];

                $startOfMonth = "$year-" . str_pad($monthNum, 2, "0", STR_PAD_LEFT) . "-01";
                $endOfMonth = date("Y-m-t", strtotime($startOfMonth));
                $attendanceData = $attendanceModel->where("user_id", $payroll["user_id"])
                    ->where("date >=", $startOfMonth)->where("date <=", $endOfMonth)->findAll();

                $presentDays = 0;
                $absentDays = 0;
                $halfDays = 0;
                $unpaidLeaves = 0;
                foreach ($attendanceData as $att) {
                    $status = strtolower($att["status"]);
                    if ($status === "present")
                        $presentDays++;
                    elseif ($status === "absent")
                        $absentDays++;
                    elseif ($status === "half-day" || $status === "halfday")
                        $halfDays++;
                    elseif ($status === "unpaid leave")
                        $unpaidLeaves++;
                }

                $baseSalary = floatval($payroll["salary_amount"]);
                $totalEarnings = $baseSalary + floatval($payroll["overtime_pay"] ?? 0) + floatval($payroll["bonuses"] ?? 0);
                $totalDeductions = floatval($payroll["salary_deduction"] ?? 0) + floatval($payroll["tax_deduction"] ?? 0);

                $calculatedData = [
                    "working_days" => $workingDays,
                    "present_days" => $presentDays,
                    "absent_days" => $absentDays,
                    "total_leaves" => $payroll["total_leaves"] ?? 0,
                    "used_paid_leaves" => $payroll["used_paid_leaves"] ?? 0,
                    "unpaid_leaves" => $unpaidLeaves,
                    "half_days" => $halfDays,
                    "worked_hours" => $payroll["worked_hours"] ?? 0,
                    "total_overtime_hours" => $payroll["total_overtime_hours"] ?? 0,
                    "total_earnings" => $totalEarnings,
                    "total_deductions" => $totalDeductions,
                    "salary_deduction" => $payroll["salary_deduction"] ?? 0,
                ];

                $slipData = [
                    "payroll" => $payroll,
                    "user" => $userInfo,
                    "designation" => $designation,
                    "department" => $department,
                    "company" => $company,
                    "companyLogoBase64" => $companyLogoBase64,
                    "onboarding" => $onboarding,
                    "calculatedData" => $calculatedData,
                ];

                $slipsHtml[] = view("payroll/salary_slip", $slipData);
            } catch (\Exception $e) {
                log_message("error", "Failed to generate slip: " . $e->getMessage());
            }
        }

        if (empty($slipsHtml))
            return null;

        try {
            $dompdf = new \Dompdf\Dompdf([
                "isRemoteEnabled" => true,
                "isHtml5ParserEnabled" => true,
                "isFontSubsettingEnabled" => true,
            ]);
            $dompdf->loadHtml(implode($slipsHtml));
            $dompdf->setPaper("A4", "portrait");
            $dompdf->render();
            return $dompdf->output();
        } catch (\Exception $e) {
            log_message("error", "PDF generation failed: " . $e->getMessage());
            return null;
        }
    }

    public function getEmployees()
    {
        $user = $this->authService->check();
        if (!$user || !in_array($user->role, ['admin', 'hr'])) {
            return $this->failUnauthorized('Unauthorized');
        }

        $userInfoModel = new \App\Models\UserInfoModel();
        $employees = $userInfoModel->select('user_id, firstname, lastname')
            ->orderBy('firstname', 'ASC')
            ->findAll();

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $employees
        ]);
    }

    public function downloadYearly()
    {
        $userAuth = $this->authService->check();
        if (!$userAuth || !in_array($userAuth->role, ['admin', 'hr'])) {
            return $this->failUnauthorized('Unauthorized');
        }

        $input = $this->request->getJSON();
        $userId = $input->user_id ?? null;
        $year = $input->year ?? null;
        $startMonth = $input->start_month ?? '01';
        $endMonth = $input->end_month ?? '12';

        if (!$userId || !$year) {
            return $this->failValidationErrors('User ID and Year are required');
        }

        $startStr = "$year-" . str_pad($startMonth, 2, "0", STR_PAD_LEFT);
        $endStr = "$year-" . str_pad($endMonth, 2, "0", STR_PAD_LEFT);

        $payrollModel = new \App\Models\PayrollModel();
        $payrolls = $payrollModel->where('user_id', $userId)
            ->where('month_year >=', $startStr)
            ->where('month_year <=', $endStr)
            ->orderBy('month_year', 'ASC')
            ->findAll();

        if (empty($payrolls)) {
            return $this->failNotFound('No payroll records found for this range');
        }

        $payrollIds = array_column($payrolls, 'id');
        $filename = "salary-slips-$startStr-to-$endStr.pdf";

        $pdfOutput = $this->generateCombinedSlipPdf($payrollIds);

        if (!$pdfOutput) {
            return $this->fail("No salary slips could be generated");
        }

        return $this->response
            ->setContentType("application/pdf")
            ->setBody($pdfOutput)
            ->setHeader("Content-Disposition", 'attachment; filename="' . $filename . '"');
    }
}
