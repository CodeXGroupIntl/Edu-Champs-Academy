<?php
// Start the session
session_start();

// Autoload configs or DB if needed
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/constants.php';

// Get the requested URL and break it into parts
$url = isset($_GET['url']) ? explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL)) : ['home'];

// Define controller and method
$controllerName = !empty($url[0]) ? ucfirst($url[0]) . 'Controller' : 'HomeController';
$method = isset($url[1]) ? $url[1] : 'index';

// Build the path to controller
$controllerFile = __DIR__ . '/../app/controllers/' . $controllerName . '.php';

// Check if controller file exists
if (file_exists($controllerFile)) {
    require_once $controllerFile;
    $controller = new $controllerName();

    // Check if method exists
    if (method_exists($controller, $method)) {
        // Get any additional parameters
        $params = array_slice($url, 2);
        call_user_func_array([$controller, $method], $params);
    } else {
        echo "Method '$method' not found in $controllerName.";
    }
} else {
    echo "Controller '$controllerName' not found.";
}
