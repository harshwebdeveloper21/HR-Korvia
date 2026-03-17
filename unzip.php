<?php
ini_set('max_execution_time', 900);
session_start();

$zip = new ZipArchive();
$zipFile = '../hrportal.zip';
$extractPath = '../';

if (!file_exists($zipFile)) {
    echo json_encode(['message' => 'Project zip file not found.']);
    exit;
}

if ($zip->open($zipFile) === TRUE) {
    $totalFiles = $zip->numFiles;
    $currentFile = 0;

    for ($i = 0; $i < $totalFiles; $i++) {
        $file = $zip->getNameIndex($i);

        if (strpos($file, 'hrportal/') === 0) {
            $relativePath = substr($file, strlen('hrportal/'));

            if ($relativePath !== '') {
                $targetPath = $extractPath . $relativePath;
                $targetDir = dirname($targetPath);

                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }

                if ($zip->extractTo($extractPath, $file)) {
                    rename($extractPath . $file, $targetPath);
                }
            }
        }

        $currentFile++;
        echo json_encode(['progress' => round(($currentFile / $totalFiles) * 100)]);
        echo "\n";
        ob_flush();
        flush();
    }

    $zip->close();

    rmdir($extractPath . 'hrportal');

    $_SESSION['step'] = '1';

    echo json_encode(['progress' => 100, 'message' => 'success']);
    ob_flush();
    flush();
} else {
    echo json_encode(['message' => 'Failed to unzip the file.']);
    ob_flush();
    flush();
}
?>