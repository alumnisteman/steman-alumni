<?php
Artisan::call('scout:import', ['model' => 'App\\Models\\Post']);
echo "Posts imported.\n";
Artisan::call('scout:import', ['model' => 'App\\Models\\User']);
echo "Users imported.\n";
