<?php
/**
 * Debug script: extract PDF text and show block before TRANSACTION TOTAL.
 * Run: php debug_axis_last_row.php
 * Output written to writable/logs/axis_debug_output.txt
 */
$outFile = __DIR__ . '/writable/logs/axis_debug_output.txt';
@mkdir(dirname($outFile), 0755, true);
$out = function ($s) use (&$outFile) { file_put_contents($outFile, $s, FILE_APPEND); };

file_put_contents($outFile, '');

$pdfPath = __DIR__ . '/uploads/pdf_recorder/AXIS BANK STATEMENT 01-01-2025 TO 31-12-2025.pdf';
if (!is_file($pdfPath)) {
    $pdfPath = __DIR__ . '/uploads/AXIS_BANK_STATEMENT_01-01-2025_TO_31-12-2025.pdf';
}
if (!is_file($pdfPath)) {
    $out("PDF not found. Tried: $pdfPath\n");
    echo "Output written to $outFile\n";
    exit(1);
}

require __DIR__ . '/vendor/autoload.php';

$parser = new \Smalot\PdfParser\Parser();
$pdf = $parser->parseFile($pdfPath);

$text = '';
if (method_exists($pdf, 'getPages')) {
    $pages = $pdf->getPages();
    foreach ($pages as $page) {
        try {
            $text .= $page->getText() . "\n";
        } catch (\Exception $e) {}
    }
}
$fullText = $pdf->getText();
if (strlen($fullText) > strlen($text)) {
    $text = $fullText;
}

$len = strlen($text);
$out("PDF text length: $len\n\n");

$totalPos = stripos($text, 'TRANSACTION TOTAL');
$out("Position of 'TRANSACTION TOTAL': " . ($totalPos === false ? 'NOT FOUND' : $totalPos) . "\n");

if ($totalPos !== false && $totalPos > 700) {
    $block = substr($text, $totalPos - 700, 700);
    $out("\n--- RAW block (700 chars before TRANSACTION TOTAL) ---\n");
    $out($block);
    $out("\n--- END RAW ---\n\n");

    $normalized = preg_replace('/\s+/', ' ', $block);
    $out("--- NORMALIZED (single spaces) ---\n");
    $out($normalized);
    $out("\n--- END NORMALIZED ---\n\n");
}

$beforeTotal = ($totalPos !== false && $totalPos > 0) ? substr($text, 0, $totalPos) : '';
$p = 0;
$sn4764Pos = false;
while (($p = strpos($beforeTotal, '4764', $p)) !== false) {
    $nextCh = (strlen($beforeTotal) > $p + 4) ? $beforeTotal[$p + 4] : ' ';
    if ($nextCh !== '5') {
        $sn4764Pos = $p;
    }
    $p += 1;
}
$out("Last '4764' (S.NO) position (before TRANSACTION TOTAL): " . ($sn4764Pos === false ? 'NOT FOUND' : $sn4764Pos) . "\n");

if ($sn4764Pos !== false && $totalPos !== false) {
    $segment4764 = substr($text, $sn4764Pos, $totalPos - $sn4764Pos);
    $segment4764 = trim($segment4764);
    $out("\n--- SEGMENT from 4764 to TRANSACTION TOTAL (length " . strlen($segment4764) . ") ---\n");
    $out($segment4764);
    $out("\n--- END SEGMENT ---\n\n");

    $normSeg = preg_replace('/\s+/', ' ', $segment4764);
    $out("Segment contains 19,706 or 19706: " . (preg_match('/19[,]?706|19706/', $normSeg) ? 'YES' : 'NO') . "\n");
    $out("Segment contains 6,00,684.93 or 600684.93: " . (preg_match('/6[,]?00[,]?684\.93|600684\.93/', $normSeg) ? 'YES' : 'NO') . "\n");
    $out("Segment contains date (DD/MM/YYYY): " . (preg_match('/(\d{2}[-\/]\d{2}[-\/]\d{4})/', $normSeg) ? 'YES' : 'NO') . "\n");
}

$out("\nDone.\n");
echo "Debug output written to: $outFile\n";
