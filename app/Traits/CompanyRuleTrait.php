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
        $day = (int) $date->format('j'); // Day of month (1–31)

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
        $presentOrHalfDay = $attendanceModel
            ->where('user_id', $userId)
            ->whereIn('status', ['present', 'half-day'])
            ->where('date >=', $startOfMonth->format('Y-m-d'))
            ->where('date <=', $endOfMonth->format('Y-m-d'))
            ->findAll();
        $presentOrHalfDayDates = array_flip(array_column($presentOrHalfDay, 'date'));

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

            while ($start <= $end) {

                $dateStr = $start->format('Y-m-d');
                // Sandwich leaves: include weekends if they fall within an approved leave range.
                if (!isset($holidaySet[$dateStr])) {
                    if (!isset($presentOrHalfDayDates[$dateStr]) && !isset($countedDates[$dateStr])) {
                        $total += $isHalfDay ? 0.5 : 1;
                        $countedDates[$dateStr] = true;
                    }
                }

                $start->modify('+1 day');
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
