<?php
session_start();
require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/functions/functions.php';

// Auth Guards
require_once __DIR__ . '/middleware/auth_guard.php';

// Controllers
require_once __DIR__ . '/controllers/HomeController.php';
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/CourseController.php';
require_once __DIR__ . '/controllers/CertificateController.php';

// Routing Logic
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Routes
switch ($uri) {
    // Homepage
    case '/':
        (new HomeController())->index();
        break;

    // Auth routes
    case '/auth/login':
        if ($method === 'POST') {
            (new AuthController())->login();
        } else {
            (new AuthController())->showLogin();
        }
        break;

    case '/auth/register':
        if ($method === 'POST') {
            (new AuthController())->register();
        } else {
            (new AuthController())->showRegister();
        }
        break;

    case '/auth/logout':
        (new AuthController())->logout();
        break;

    // Course routes
    case '/courses':
        auth_guard();
        (new CourseController())->index();
        break;

    case '/courses/view':
        auth_guard();
        if (isset($_GET['id'])) {
            (new CourseController())->show((int)$_GET['id']);
        } else {
            redirect('/courses');
        }
        break;

    // Certificate routes
    case '/certificates':
        auth_guard();
        (new CertificateController())->index();
        break;

    case '/certificates/issue':
        auth_guard();
        if (isset($_GET['course_id'])) {
            (new CertificateController())->issue((int)$_GET['course_id']);
        } else {
            redirect('/courses');
        }
        break;

    case '/certificates/show':
        auth_guard();
        if (isset($_GET['id'])) {
            (new CertificateController())->show((int)$_GET['id']);
        } else {
            redirect('/certificates');
        }
        break;

    case '/certificates/download':
        auth_guard();
        if (isset($_GET['id'])) {
            (new CertificateController())->download((int)$_GET['id']);
        } else {
            redirect('/certificates');
        }
        break;

    case '/certificates/public':
        if (isset($_GET['code'])) {
            require_once __DIR__ . '/views/certificates/show_public.php';
        } else {
            echo "Certificate code not provided.";
        }
        break;

    default:
        http_response_code(404);
        echo "404 - Page Not Found";
        break;
}
