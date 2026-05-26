<?php
$dir = __DIR__ . '/templates';
$files = glob($dir . '/vcard*.php');
$count = 0;

foreach ($files as $file) {
    $content = file_get_contents($file);
    if (strpos($content, 'A unit of <strong>Mr Print World</strong>') !== false) {
        $content = str_replace(
            'A unit of <strong>Mr Print World</strong>', 
            'An innovative Product From : <strong>Mr Print World Pvt Ltd.</strong>', 
            $content
        );
        file_put_contents($file, $content);
        $count++;
    }
}
echo "Successfully updated the footer text in $count template files.\n";
