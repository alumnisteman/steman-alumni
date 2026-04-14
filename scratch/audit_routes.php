<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$routes = Route::getRoutes();
$errors = [];

foreach ($routes as $route) {
    if ($route->getActionName() === 'Closure') continue;
    
    $action = explode('@', $route->getActionName());
    if (count($action) < 2) {
        $action = explode(':', $route->getActionName());
    }
    
    if (count($action) >= 2) {
        $controller = $action[0];
        $method = $action[1];
        
        if (!class_exists($controller)) {
            $errors[] = "Controller class $controller does not exist for route " . $route->uri();
        } elseif (!method_exists($controller, $method)) {
            $errors[] = "Method $method does not exist on controller $controller for route " . $route->uri();
        }
    }
}

if (empty($errors)) {
    echo "SUCCESS: All routes have valid controllers and methods.\n";
} else {
    echo "ERRORS FOUND:\n";
    foreach ($errors as $err) {
        echo "- $err\n";
    }
}
