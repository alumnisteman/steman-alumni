<?php
// fix_500.php - Forceful Cache Purge
echo "<h1>Steman Alumni - Force Cache Purge</h1>";

$cachePath = __DIR__ . '/../bootstrap/cache/';
$files = ['config.php', 'routes-v7.php', 'services.php', 'packages.php', 'events.php'];

echo "<ul>";
foreach ($files as $file) {
    if (file_exists($cachePath . $file)) {
        if (unlink($cachePath . $file)) {
            echo "<li style='color: green;'>Successfully deleted: $file</li>";
        } else {
            echo "<li style='color: red;'>Failed to delete: $file (Check permissions)</li>";
        }
    } else {
        echo "<li>File not found: $file (Skipped)</li>";
    }
}
echo "</ul>";

echo "<p>Permissions check:</p>";
echo "Current user: " . get_current_user() . "<br>";
echo "Storage writable: " . (is_writable(__DIR__ . '/../storage') ? 'YES' : 'NO') . "<br>";
echo "Cache writable: " . (is_writable($cachePath) ? 'YES' : 'NO') . "<br>";

echo "<h2>Next Step:</h2>";
echo "<p>Try to access the <a href='/alumni/dashboard'>Dashboard</a> again.</p>";
