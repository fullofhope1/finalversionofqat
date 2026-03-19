<?php
$dir = 'c:/xampp/htdocs/qat/requests/';
$files = glob($dir . '*.php');
echo "--- Files missing Autoloader.php check ---\n";
foreach ($files as $file) {
    if (basename($file) === 'find_missing_autoloader.php') continue;

    $content = file_get_contents($file);
    // Look for class instantiation: new NameRepository or new NameService
    $usesClasses = preg_match('/new\s+\w+(Repository|Service)/i', $content);
    $hasAutoloader = (strpos($content, 'Autoloader.php') !== false) || (strpos($content, "include 'includes/header.php'") !== false);
    // Header often includes autoloader, but requests usually don't include header.

    if ($usesClasses && !$hasAutoloader) {
        echo "MISSING: " . basename($file) . "\n";
    }
}
