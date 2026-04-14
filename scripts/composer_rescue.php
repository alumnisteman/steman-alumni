<?php
// Composer Rescue Script
echo "Downloading Composer installer...\n";
copy('https://getcomposer.org/installer', 'composer-setup.php');

echo "Installing Composer...\n";
$output = shell_exec('php composer-setup.php');
echo $output;

echo "Running Optimized Autoload Dump...\n";
$output = shell_exec('php composer.phar dump-autoload -o');
echo $output;

echo "Clearing Artisan Optimization...\n";
$output = shell_exec('php artisan optimize:clear');
echo $output;

echo "Cleanup...\n";
unlink('composer-setup.php');
echo "RESCUE COMPLETE.\n";
