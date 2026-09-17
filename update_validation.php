<?php

$directory = 'd:/xampp/htdocs/HR/fableadhrportal/app/Views';

// Pattern that looks for:
// $.each( VAR , function ( VAR , VAR ) {
// ...
// .addClass('is-invalid');
// ...
// .after( ... invalid-feedback ... );
// ...
// });
$pattern = '/\$\.each\(\s*([a-zA-Z0-9_\.]+)\s*,\s*function\s*\(\s*[a-zA-Z0-9_]+\s*,\s*[a-zA-Z0-9_]+\s*\)\s*\{.*?(?:addClass|is-invalid).*?(?:invalid-feedback).*?\}\s*\);/s';

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
$count = 0;

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $filepath = $file->getPathname();
        $content = file_get_contents($filepath);
        $new_content = $content;
        
        $new_content = preg_replace_callback($pattern, function ($matches) {
            $errors_var = $matches[1];
            // Safety check: only replace if it looks like the validation loop
            if (strpos($matches[0], 'is-invalid') !== false && strpos($matches[0], 'invalid-feedback') !== false && strpos($matches[0], 'after(') !== false) {
                return "if (typeof displayValidationErrors === 'function') displayValidationErrors($errors_var);";
            }
            return $matches[0];
        }, $new_content);

        if ($new_content !== $content) {
            file_put_contents($filepath, $new_content);
            echo "Updated $filepath\n";
            $count++;
        }
    }
}

echo "Total files updated: $count\n";
