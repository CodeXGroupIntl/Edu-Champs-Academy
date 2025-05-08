<?php 

    // Start session
    if (session_status() === PHP_SESSION_NONE)
    {
        session_start();
    }

    // Environment settings
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'edu-champs');
    define('DB_USER', 'root');
    define('DB_PASS', '');

    // Error reporting, set to 0 in production
    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    // Default timezone
    date_default_timezone_set('Africa/Lagos');

    // Database connection
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Check connection
    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }

?>