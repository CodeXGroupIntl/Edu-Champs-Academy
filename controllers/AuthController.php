<?php

class AuthController
{
    private $db;

    public function __construct()
    {
        require_once __DIR__ . '/../config/database.php'; // Assumes $conn
        $this->db = $conn;

        // Secure session
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_strict_mode', 1);
        session_start();
    }

    public function login()
    {
        require_once __DIR__ . '/../views/auth/login.php';
    }

    public function handleLogin()
    {
        $email = filter_var($_POST["email"] ?? '', FILTER_VALIDATE_EMAIL);
        $password = trim($_POST["password"] ?? '');

        if (!$email || !$password) {
            $_SESSION['error'] = "Email and password are required!";
            header("Location: /auth/login");
            exit;
        }

        if ($this->isTooManyAttempts($email)) {
            $_SESSION['error'] = "Too many login attempts. Try again later.";
            header("Location: /auth/login");
            exit;
        }

        $stmt = $this->db->prepare("SELECT id, name, password FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['success'] = "Login successful. Welcome back!";
            unset($_SESSION['login_attempts'][$email]); // Reset attempts
            header("Location: /dashboard");
            exit;
        } else {
            // Increment failed login attempts
            $_SESSION['login_attempts'][$email]['count'] = ($_SESSION['login_attempts'][$email]['count'] ?? 0) + 1;
            $_SESSION['login_attempts'][$email]['last_attempt'] = time();
            $_SESSION['error'] = "Invalid credentials.";
            header("Location: /auth/login");
            exit;
        }
    }

    public function register()
    {
        require_once __DIR__ . '/../views/auth/register.php';
    }

    public function handleRegister()
    {
        $name = htmlspecialchars(trim($_POST["name"] ?? ''));
        $email = filter_var($_POST["email"] ?? '', FILTER_VALIDATE_EMAIL);
        $password = trim($_POST["password"] ?? '');

        if (!$name || !$email || !$password) {
            $_SESSION['error'] = "All fields are required!";
            header("Location: /auth/register");
            exit;
        }

        $checkStmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0) {
            $_SESSION['error'] = "Email already registered.";
            header("Location: /auth/register");
            exit;
        }

        $hashedPassword = password_hash($password, PASSWORD_ARGON2ID);
        $stmt = $this->db->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $hashedPassword);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Registration successful. You can now log in.";
            header("Location: /auth/login");
            exit;
        } else {
            $_SESSION['error'] = "Registration failed. Try again.";
            header("Location: /auth/register");
            exit;
        }
    }

    public function logout()
    {
        session_unset();
        session_destroy();
        setcookie(session_name(), '', time() - 3600, '/');
        header("Location: /auth/login");
        exit;
    }

    private function isTooManyAttempts($email)
    {
        $maxAttempts = 5;
        $lockoutTime = 300; // 5 minutes

        if (!isset($_SESSION['login_attempts'])) {
            $_SESSION['login_attempts'] = [];
        }

        if (!isset($_SESSION['login_attempts'][$email])) {
            $_SESSION['login_attempts'][$email] = [
                'count' => 0,
                'last_attempt' => time(),
                'locked_until' => null
            ];
        }

        $attemptData = &$_SESSION['login_attempts'][$email];

        if ($attemptData['locked_until'] && time() < $attemptData['locked_until']) {
            return true;
        }

        if (time() - $attemptData['last_attempt'] > $lockoutTime) {
            $attemptData['count'] = 0;
            $attemptData['locked_until'] = null;
        }

        if ($attemptData['count'] >= $maxAttempts) {
            $attemptData['locked_until'] = time() + $lockoutTime;
            return true;
        }

        return false;
    }
}