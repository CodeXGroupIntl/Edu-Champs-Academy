<?php 

    session_start();

    function isAuthenticated(): bool {
        return(isset($_SESSION['user_id']));
    }

    function requireAuth(): void {
        if (!isAuthenticated()) {
            $_SESSION['error'] = "Please log in to access this page.";
            header("Location: /auth/login");
            exit;
        }
    }

    function requireRole(array $allowedRoles): void {
        requireAuth(); // Ensure user is logged in first

        if (!in_array($_SESSION['role'] ?? '', $allowedRoles)) {
            http_response_code(403);
            echo "Access denied. You do not have permission to access this page.";
            exit;
        }
    }

?>