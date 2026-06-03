<?php

namespace App\Traits;

use App\Models\AttendanceModel;
use DateTime;

trait CompanyRuleTrait
{
    /**
     * Check if a given date is a working day based on company rules
     */
    protected function isWorkingDay(DateTime $date, array $rules): bool
    {
        $dayOfWeek = (int) $date->format('w'); // 0=Sunday, 6=Saturday
        $day = (int) $date->format('j'); // Day of month (1-31)

        if ($dayOfWeek === 0 && ($rules['sunday_off'] ?? 0) == 1) {
            return false;
        }

        if ($dayOfWeek === 6 && ($rules['saturday_off_enabled'] ?? 0) == 1) {

            if (($rules['saturday_off_type'] ?? '') === 'all') {
                return false;
            }

            if (in_array($rules['saturday_off_type'], ['alternate-even', 'alternate-odd'])) {

                $weekNumber = ceil($day / 7);

                if (
                    ($rules['saturday_off_type'] === 'alternate-even' && $weekNumber % 2 === 0) ||
                    ($rules['saturday_off_type'] === 'alternate-odd' && $weekNumber % 2 !== 0)
                ) {
                    return false;
                }
            }

            if (($rules['saturday_off_type'] ?? '') === 'custom') {

                $pattern = array_filter(
                    array_map('intval', explode(',', $rules['saturday_off_pattern'] ?? ''))
                );

                $weekNumber = (int) ceil($day / 7);

                if (in_array($weekNumber, $pattern, true)) {
                    return false;
                }
            }

        }

        return true;
    }

    /**
     * Count approved leave days in a month (working days only).
     * Sundays and holidays are excluded so they "count as present".
     * Attendance overrides leave: if employee has present or half-day on a date, that day is NOT counted as leave.
     *
     * Sandwich Leave Rule:
     *   - If an approved leave falls on Friday (day 5), the following Saturday & Sunday are
     *     automatically included in the leave count (Friday leave = 3 days: Fri + Sat + Sun).
     *   - If an approved leave falls on Monday (day 1), the preceding Saturday & Sunday are
     *     automatically included (Monday leave = 3 days: Sat + Sun + Mon).
     *   - When both Friday AND Monday are on leave the Sat/Sun are counted only once (5 days total).
     *   - The rule does NOT apply to half-day leaves.
     *   - The rule does NOT apply if the employee has a check-in record on the Friday/Monday
     *     (meaning they came to the office or were out for official work that day).
     *   - Weekend days that are public holidays or marked present/half-day are NOT added.
     *
     * @param array $holidayDates Optional list of holiday dates (Y-m-d) for this month
     */
    protected function countMonthlyLeaves(
        int $userId,
        string $month, // YYYY-MM
        array $rules,
        $leaveModel,
        array $holidayDates = []
    ): float {
        $startOfMonth = new DateTime($month . '-01');
        $endOfMonth = new DateTime(date('Y-m-t', strtotime($month . '-01')));
        $holidaySet = array_flip($holidayDates);

        $attendanceModel = new AttendanceModel();

        // Fetch present/half-day attendance records so we can skip those dates from leave count
        $presentOrHalfDay = $attendanceModel
            ->where('user_id', $userId)
            ->whereIn('status', ['present', 'half-day'])
            ->where('date >=', $startOfMonth->format('Y-m-d'))
            ->where('date <=', $endOfMonth->format('Y-m-d'))
            ->findAll();
        $presentOrHalfDayDates = array_flip(array_column($presentOrHalfDay, 'date'));

        // Fetch ALL attendance rows for the month so we can check check_in_time on any given day.
        // An employee who has a check_in_time is physically present (even if marked absent/on-leave
        // for administrative reasons like off-site/field work). The sandwich rule must NOT apply
        // for such days — their leave is only 1 day, not 3.
        $allAttendance = $attendanceModel
            ->where('user_id', $userId)
            ->where('date >=', $startOfMonth->format('Y-m-d'))
            ->where('date <=', $endOfMonth->format('Y-m-d'))
            ->findAll();
        $checkedInDates = []; // date => true if employee has any check_in_time that day
        foreach ($allAttendance as $att) {
            $attDate = substr($att['date'], 0, 10);
            if (!empty($att['check_in_time'])) {
                $checkedInDates[$attDate] = true;
            }
        }

        $leaves = $leaveModel
            ->where('user_id', $userId)
            ->whereIn('status', ['approved', 'Approved'])
            ->where('start_date <=', $endOfMonth->format('Y-m-d'))
            ->where('end_date >=', $startOfMonth->format('Y-m-d'))
            ->findAll();

        $total = 0.0;
        $countedDates = [];

        foreach ($leaves as $leave) {

            $start = new DateTime($leave['start_date']);
            $end = new DateTime($leave['end_date']);

            if ($start < $startOfMonth)
                $start = clone $startOfMonth;
            if ($end > $endOfMonth)
                $end = clone $endOfMonth;

            $isHalfDay = isset($leave['leave_duration']) && $leave['leave_duration'] === 'half_day';

            $current = clone $start;
            while ($current <= $end) {

                $dateStr = $current->format('Y-m-d');
                $dow = (int) $current->format('w'); // 0=Sun, 1=Mon ... 5=Fri, 6=Sat

                if (!isset($holidaySet[$dateStr])) {
                    // Only count as a leave day if the employee has NO present/half-day record
                    if (!isset($presentOrHalfDayDates[$dateStr]) && !isset($countedDates[$dateStr])) {
                        $total += $isHalfDay ? 0.5 : 1;
                        $countedDates[$dateStr] = true;

                        // ── Sandwich Leave Rule ──────────────────────────────────────────────
                        // Apply only when:
                        //   1. It is a full-day leave (not half-day)
                        //   2. The employee did NOT check in on this Friday/Monday.
                        //      If they have a check_in_time, they came to office (or were
                        //      on official outside duty) — treat as 1 leave day only.
                        $employeeCheckedInToday = isset($checkedInDates[$dateStr]);

                        if (!$isHalfDay && !$employeeCheckedInToday) {
                            // Friday leave -> also count following Saturday (+1) and Sunday (+2)
                            if ($dow === 5) {
                                $sat = (clone $current)->modify('+1 day');
                                $sun = (clone $current)->modify('+2 days');

                                foreach ([$sat, $sun] as $weekend) {
                                    $wStr = $weekend->format('Y-m-d');
                                    if (
                                        !isset($presentOrHalfDayDates[$wStr]) &&
                                        !isset($countedDates[$wStr])
                                    ) {
                                        $total += 1;
                                        $countedDates[$wStr] = true;
                                    }
                                }
                            }
                            // Monday leave -> also count preceding Sunday (-1) and Saturday (-2)
                            if ($dow === 1) {
                                $sun = (clone $current)->modify('-1 day');
                                $sat = (clone $current)->modify('-2 days');

                                foreach ([$sat, $sun] as $weekend) {
                                    $wStr = $weekend->format('Y-m-d');
                                    if (
                                        !isset($presentOrHalfDayDates[$wStr]) &&
                                        !isset($countedDates[$wStr])
                                    ) {
                                        $total += 1;
                                        $countedDates[$wStr] = true;
                                    }
                                }
                            }
                        }
                        // ────────────────────────────────────────────────────────────────────
                    }
                }

                $current->modify('+1 day');
            }
        }

        $absences = $attendanceModel
            ->where('user_id', $userId)
            ->where('status', 'absent')
            ->where('date >=', $startOfMonth->format('Y-m-d'))
            ->where('date <=', $endOfMonth->format('Y-m-d'))
            ->findAll();

        foreach ($absences as $absence) {
            $dateStr = $absence['date'];
            if ($this->isWorkingDay(new DateTime($dateStr), $rules) && !isset($holidaySet[$dateStr])) {
                if (!isset($presentOrHalfDayDates[$dateStr]) && !isset($countedDates[$dateStr])) {
                    $total += 1;
                }
            }
        }

        return (float) $total;
    }
}
