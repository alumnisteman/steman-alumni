#!/bin/sh
php artisan scout:import "App\Models\User" 2>&1
php artisan optimize 2>&1
