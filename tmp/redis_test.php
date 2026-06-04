<?php
if (!extension_loaded('redis')) {
    echo "no redis ext"; exit;
}
$r = new Redis();
$r->connect('redis', 6379);
echo $r->ping();
?>
