<?php 

    // General app settings
    define('APP_NAME', 'Edu-Champs Academy');
    define('APP_VERSION', '1.0.0');

    // BASE URL (ensure trailing slash)
    define('BASE_URL', 'http://localhost/edu-champs/');

    // PATHS
    define('ASSETS_PATH', BASE_URL . 'public/assets/');    
    define('CSS_PATH', BASE_URL . 'css/');    
    define('JS_PATH', BASE_URL . 'js/');    
    define('IMAGES_PATH', BASE_URL . 'images/');    
    define('UPLOADS_PATH', BASE_URL . '/../public/uploads/');   
    
    // EMAIL SETITNGS
    // EMAIL SETTINGS
    define('ADMIN_EMAIL', 'admin@educhamps.com');
    define('SUPPORT_EMAIL', 'support@educhamps.com');
    
    // CERTIFICATE SETTINGS
    define('CERT_TEMPLATE_PATH', __DIR__ . '/../views/certificates/pdf_template.php');
    define('CERT_DOWNLOAD_DIR', __DIR__ . '/../public/certificates/');
    
    // USER ROLES
    define('ROLE_USER', 'user');
    define('ROLE_INSTRUCTOR', 'instructor');
    define('ROLE_ADMIN', 'admin');
    define('ROLE_SUPER_ADMIN', 'super_admin');
    
    // SESSION KEYS
    define('SESSION_USER_ID', 'user_id');
    define('SESSION_USER_ROLE', 'user_role');
    
    // TIMEZONE
    define('DEFAULT_TIMEZONE', 'Africa/Lagos');
    
    // MISC
    define('TOKEN_EXPIRY_HOURS', 2);
    define('MAX_UPLOAD_SIZE_MB', 5);


?>