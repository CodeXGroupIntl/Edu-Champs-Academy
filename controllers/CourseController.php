<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Course.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Enrollment.php';

class CourseController
{
    private $db;

    public function __construct()
    {
        session_start();
        $this->db = $GLOBALS['conn'];
    }

    private function requireAuth()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /auth/login");
            exit;
        }
    }

    private function requireAdmin()
    {
        $this->requireAuth();

        $role = $_SESSION['role'] ?? null;
        if (!in_array($role, ['admin', 'super_admin', 'instructor'])) {
            http_response_code(403);
            echo "Unauthorized";
            exit;
        }
    }

    public function index()
    {
        $stmt = $this->db->query("SELECT * FROM courses ORDER BY created_at DESC");
        $courses = $stmt->fetch_all(MYSQLI_ASSOC);

        require __DIR__ . '/../views/courses/index.php';
    }

    public function show($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM courses WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $course = $result->fetch_assoc();

        if (!$course) {
            http_response_code(404);
            echo "Course not found.";
            exit;
        }

        $userId = $_SESSION['user_id'] ?? null;
        $enrolled = false;

        if ($userId) {
            $check = $this->db->prepare("SELECT id FROM enrollments WHERE user_id = ? AND course_id = ?");
            $check->bind_param("ii", $userId, $id);
            $check->execute();
            $check->store_result();
            $enrolled = $check->num_rows > 0;
        }

        require __DIR__ . '/../views/courses/show.php';
    }

    public function enroll($courseId)
    {
        $this->requireAuth();
        $userId = $_SESSION['user_id'];

        $check = $this->db->prepare("SELECT id FROM enrollments WHERE user_id = ? AND course_id = ?");
        $check->bind_param("ii", $userId, $courseId);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $_SESSION['info'] = "You're already enrolled.";
            header("Location: /courses/$courseId");
            exit;
        }

        $stmt = $this->db->prepare("INSERT INTO enrollments (user_id, course_id, enrolled_at) VALUES (?, ?, NOW())");
        $stmt->bind_param("ii", $userId, $courseId);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Enrollment successful!";
        } else {
            $_SESSION['error'] = "Enrollment failed.";
        }

        header("Location: /courses/$courseId");
    }

    public function create()
    {
        $this->requireAdmin();
        require __DIR__ . '/../views/courses/create.php';
    }

    public function store()
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /courses/create");
            exit;
        }

        $title = trim($_POST['title']);
        $description = trim($_POST['description']);

        if (empty($title) || empty($description)) {
            $_SESSION['error'] = "All fields are required.";
            header("Location: /courses/create");
            exit;
        }

        $stmt = $this->db->prepare("INSERT INTO courses (title, description, created_at) VALUES (?, ?, NOW())");
        $stmt->bind_param("ss", $title, $description);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Course created successfully!";
            header("Location: /courses");
        } else {
            $_SESSION['error'] = "Course creation failed.";
            header("Location: /courses/create");
        }
    }

    public function edit($id)
    {
        $this->requireAdmin();

        $stmt = $this->db->prepare("SELECT * FROM courses WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $course = $result->fetch_assoc();

        if (!$course) {
            http_response_code(404);
            echo "Course not found.";
            exit;
        }

        require __DIR__ . '/../views/courses/edit.php';
    }

    public function update($id)
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /courses/edit/$id");
            exit;
        }

        $title = trim($_POST['title']);
        $description = trim($_POST['description']);

        $stmt = $this->db->prepare("UPDATE courses SET title = ?, description = ? WHERE id = ?");
        $stmt->bind_param("ssi", $title, $description, $id);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Course updated successfully.";
        } else {
            $_SESSION['error'] = "Update failed.";
        }

        header("Location: /courses/$id");
    }

    public function delete($id)
    {
        $this->requireAdmin();

        $stmt = $this->db->prepare("DELETE FROM courses WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Course deleted.";
        } else {
            $_SESSION['error'] = "Failed to delete course.";
        }

        header("Location: /courses");
    }
}
