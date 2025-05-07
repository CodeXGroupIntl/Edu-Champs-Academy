<?php

// Flash message helpers
function flash(string $key): ?string {
    if (!empty($_SESSION[$key])) {
        $msg = $_SESSION[$key];
        unset($_SESSION[$key]);
        return $msg;
    }
    return null;
}

function setFlash(string $key, string $message): void {
    $_SESSION[$key] = $message;
}

// Input sanitization
function sanitize(string $input): string {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Redirect
function redirect(string $url): void {
    header("Location: $url");
    exit;
}

// Date formatting
function formatDate(string $date): string {
    return date("F j, Y", strtotime($date));
}

// Slugify a string
function slugify(string $text): string {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    return strtolower($text);
}

// Generate certificate code
function generateCertCode(int $userId): string {
    return strtoupper(bin2hex(random_bytes(5))) . '-' . $userId;
}

// Role checkers
function isAdmin(): bool {
    return ($_SESSION['role'] ?? '') === 'admin';
}

function isSuperAdmin(): bool {
    return ($_SESSION['role'] ?? '') === 'super_admin';
}

function isInstructor(): bool {
    return ($_SESSION['role'] ?? '') === 'instructor';
}

// Asset path helper
function asset(string $path): string {
    return "/public/" . ltrim($path, '/');
}

// Course progress
function calculateProgress(int $completedLessons, int $totalLessons): float {
    return $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100, 2) : 0.0;
}

// Password hashing
function hashPassword(string $password): string {
    return password_hash($password, PASSWORD_BCRYPT);
}

// Password verification
function verifyPassword(string $password, string $hashed): bool {
    return password_verify($password, $hashed);
}

// Send email (basic setup)
function sendEmail(string $to, string $subject, string $message, string $from = 'noreply@educhamps.local'): bool {
    $headers = "From: {$from}\r\n";
    $headers .= "Reply-To: {$from}\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";

    return mail($to, $subject, $message, $headers);
}
