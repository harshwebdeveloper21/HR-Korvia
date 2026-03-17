<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Smalot\PdfParser\Parser;
use App\Controllers\Api\PdfRecorderController;

/**
 * Test Axis Bank PDF parsing - verifies last row (19,706 / 6,00,684.93) is included.
 * Usage: php spark test:axispdf [path_to_pdf]
 */
class TestAxisPdf extends BaseCommand
{
    protected $group       = 'PDF';
    protected $name        = 'test:axispdf';
    protected $description = 'Test Axis Bank PDF parsing and verify last transaction row';
    protected $usage       = 'test:axispdf [path_to_pdf]';

    public function run(array $params)
    {
        $baseDir = defined('FCPATH') ? dirname(FCPATH) : __DIR__ . '/../..';
        $candidates = [
            $params[0] ?? null,
            $baseDir . '/uploads/pdf_recorder/AXIS BANK STATEMENT 01-01-2025 TO 31-12-2025.pdf',
            $baseDir . '/uploads/AXIS_BANK_STATEMENT_01-01-2025_TO_31-12-2025.pdf',
            FCPATH . 'uploads/pdf_recorder/AXIS BANK STATEMENT 01-01-2025 TO 31-12-2025.pdf',
        ];
        $pdfPath = null;
        foreach ($candidates as $p) {
            if ($p && is_file($p)) {
                $pdfPath = $p;
                break;
            }
        }
        if (!$pdfPath) {
            $pdfPath = $params[0] ?? $candidates[1];
        }

        if (!is_file($pdfPath)) {
            CLI::error('PDF file not found: ' . $pdfPath);
            CLI::write('Usage: php spark test:axispdf /full/path/to/AXIS_BANK_STATEMENT.pdf', 'yellow');
            return 1;
        }

        CLI::write('Testing Axis PDF: ' . $pdfPath, 'green');

        try {
            $parser = new Parser();
            $pdf    = $parser->parseFile($pdfPath);
            $text   = $pdf->getText();
        } catch (\Exception $e) {
            CLI::error('Failed to parse PDF: ' . $e->getMessage());
            return 1;
        }

        $textLen = strlen($text);
        CLI::write('Extracted text length: ' . $textLen . ' chars', 'cyan');

        // Check that expected content exists in PDF text
        $has19706   = (strpos($text, '19,706') !== false || strpos($text, '19706') !== false);
        $has600684  = (strpos($text, '6,00,684.93') !== false || strpos($text, '600684.93') !== false);
        $hasClosing = (stripos($text, 'Closing Balance') !== false);
        $hasTotal   = (stripos($text, 'TRANSACTION TOTAL') !== false);

        CLI::write('PDF contains 19,706: ' . ($has19706 ? 'YES' : 'NO'), $has19706 ? 'green' : 'red');
        CLI::write('PDF contains 6,00,684.93: ' . ($has600684 ? 'YES' : 'NO'), $has600684 ? 'green' : 'red');
        CLI::write('PDF contains Closing Balance: ' . ($hasClosing ? 'YES' : 'NO'), 'cyan');
        CLI::write('PDF contains TRANSACTION TOTAL: ' . ($hasTotal ? 'YES' : 'NO'), 'cyan');

        // Run the actual Axis parser via reflection
        $controller = new PdfRecorderController();
        $reflection = new \ReflectionClass($controller);
        $method     = $reflection->getMethod('parseAxisBankStatement');
        $method->setAccessible(true);

        $result = $method->invoke($controller, $text);

        $count   = count($result['transactions'] ?? []);
        $lastTxn = null;
        if ($count > 0) {
            $lastTxn = $result['transactions'][$count - 1];
        }

        CLI::newLine();
        CLI::write('Parsed transaction count: ' . $count, 'cyan');
        CLI::write('Expected: 4764 (or 4763 + 1 adjustment)', 'yellow');
        CLI::write('Closing balance (PDF): ' . ($result['closing_balance'] ?? 'N/A'), 'cyan');

        if ($lastTxn) {
            CLI::write('Last transaction:', 'cyan');
            CLI::write('  Date: ' . ($lastTxn['date'] ?? 'N/A'));
            CLI::write('  Debit: ' . ($lastTxn['debit'] ?? 0));
            CLI::write('  Credit: ' . ($lastTxn['credit'] ?? 0));
            CLI::write('  Balance: ' . ($lastTxn['balance'] ?? 'N/A'));
            CLI::write('  Particulars: ' . substr($lastTxn['particulars'] ?? '', 0, 60) . '...');

            $lastBalance = floatval($lastTxn['balance'] ?? 0);
            $expectedBal = floatval($result['closing_balance'] ?? 0);
            $hasLastRow  = ($count >= 4764) && (abs($lastBalance - 600684.93) < 0.02);

            if ($hasLastRow || abs($lastBalance - $expectedBal) < 0.02) {
                CLI::newLine();
                CLI::write('PASS: Last row is present (balance matches closing ' . $expectedBal . ')', 'green');
                return 0;
            }
        }

        CLI::newLine();
        CLI::write('FAIL: Last row missing or balance mismatch. Last balance: ' . ($lastTxn['balance'] ?? 'N/A') . ', expected: 600684.93', 'red');
        return 1;
    }
}

