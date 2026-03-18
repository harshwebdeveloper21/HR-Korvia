<?php
namespace App\Validation;

class CustomRules
{
    
    public function check_end_date($endDate, string $fields, array $data): bool
    {
        if (!isset($data[$fields])) {
            return false; // If start_date is missing, validation fails
        }

        return strtotime($endDate) >= strtotime($data[$fields]);
    }
    public function validate_no_of_day($noOfDay, string $fields, array $data): bool
    {
        if (!isset($data['start_date']) || !isset($data['end_date'])) {
            return false; // Both dates are required
        }

        // Calculate the difference in days
        $start = strtotime($data['start_date']);
        $end = strtotime($data['end_date']);
        $calculatedDays = ($end - $start) / 86400 + 1; // +1 to include both start and end dates

        return $noOfDay == $calculatedDays;
    }
    public $rules = [];

public function check_close_date(string $str, string $fields, array $data): bool
{
    if (!isset($data['post_date']) || empty($data['post_date'])) {
        $data['post_date'] = date('Y-m-d'); // Default to today if post_date is not provided
    }

    return strtotime($data['close_date']) > strtotime($data['post_date']);
}

public function after_created_at(string $schedule_date,string $fields, array $data): bool
    {
        if (!isset($data['created_by']) || empty($data['created_by'])) {
            $data['created_by'] = date('Y-m-d'); // Default to today if post_date is not provided
        }
    
        return strtotime($data['schedule_date']) > strtotime($data['created_by']);
    }
    public function validDateOrder(string $str, string $fields, array $data): bool
    {
        return strtotime($data['from_date']) <= strtotime($str);
    }
    
}
?>
