<?php
$content = file_get_contents('app/Views/payroll/view.php');
$count = substr_count($content, 'generateSalarySheetPDF');
echo "Count: $count\n";
// Show context around all occurrences
$pos = 0;
while (($pos = strpos($content, 'generateSalarySheetPDF', $pos)) !== false) {
    echo "Found at byte $pos: " . substr($content, max(0, $pos - 50), 120) . "\n---\n";
    $pos++;
}
