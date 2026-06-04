<?php
if (!extension_loaded('redis')) {
    echo "no redis extension\n";
    exit(1);
}

$r = new Redis();
$r->connect('redis', 6379);
echo $r->ping() . "\n";
?>
