<?php
ini_set('max_execution_time', 300);
// unzip.php
session_start();
$zip = new ZipArchive();
$zipFile = '../fablead-crm.zip';

if (!file_exists($zipFile)) {
  echo 'Project zip file not found.';
  exit;
}

if ($zip->open($zipFile) === TRUE) {
  // old===
  // $zip->extractTo('../');

  // new====
  $extractPath = 'C:/xampp/htdocs/';

  // if ($zip->extractTo($extractPath)) {
  //     echo 'Extracted to C:/xampp/htdocs successfully.';
  // } else {
  //     echo 'Extraction failed.';
  // }

  $zip->close();
  echo 'success';
  $_SESSION['step'] = '1';
} else {
  echo 'Failed to unzip the file.';
}

?>