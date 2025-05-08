<?php

require_once __DIR__ . '/../config/database.php'; // $conn
require_once __DIR__ . '/../models/Certificate.php';
require_once __DIR__ . '/../models/Course.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../vendor/autoload.php'; // dompdf

use Dompdf\Dompdf;

class CertificateController
{
    private $db;

    public function __construct()
    {
        session_start();
        $this->db = $GLOBALS['conn'];
    }

    // Authenticated User: List certificates
    public function index()
    {
        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            header("Location: /auth/login");
            exit;
        }

        $stmt = $this->db->prepare("SELECT certificates.*, courses.title AS course_title 
                                    FROM certificates 
                                    JOIN courses ON courses.id = certificates.course_id 
                                    WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $certificates = $result->fetch_all(MYSQLI_ASSOC);

        require __DIR__ . '/../views/certificates/index.php';
    }

    // Authenticated User: Issue certificate
    public function issue($courseId)
    {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            $_SESSION['error'] = "You must be logged in.";
            header("Location: /auth/login");
            exit;
        }

        $check = $this->db->prepare("SELECT id FROM certificates WHERE user_id = ? AND course_id = ?");
        $check->bind_param("ii", $userId, $courseId);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $_SESSION['info'] = "Certificate already issued.";
            header("Location: /certificates");
            exit;
        }

        $certCode = strtoupper(bin2hex(random_bytes(5))) . '-' . $userId;

        $stmt = $this->db->prepare("INSERT INTO certificates (user_id, course_id, cert_code, awarded_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iis", $userId, $courseId, $certCode);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Certificate issued successfully!";
        } else {
            $_SESSION['error'] = "Certificate issuance failed.";
        }

        header("Location: /certificates");
    }

    // Authenticated User: Show a certificate
    public function show($id)
    {
        $userId = $_SESSION['user_id'] ?? null;

        $stmt = $this->db->prepare("SELECT certificates.*, courses.title AS course_title, users.name AS user_name 
                                    FROM certificates 
                                    JOIN courses ON courses.id = certificates.course_id 
                                    JOIN users ON users.id = certificates.user_id 
                                    WHERE certificates.id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $certificate = $result->fetch_assoc();

        if (!$certificate || $certificate['user_id'] != $userId) {
            http_response_code(403);
            echo "Unauthorized";
            exit;
        }

        require __DIR__ . '/../views/certificates/show.php';
    }

    // Authenticated User: Download certificate PDF
    public function download($id)
    {
        $userId = $_SESSION['user_id'] ?? null;

        $stmt = $this->db->prepare("SELECT certificates.*, courses.title AS course_title, users.name AS user_name 
                                    FROM certificates 
                                    JOIN courses ON courses.id = certificates.course_id 
                                    JOIN users ON users.id = certificates.user_id 
                                    WHERE certificates.id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $certificate = $result->fetch_assoc();

        if (!$certificate || $certificate['user_id'] != $userId) {
            http_response_code(403);
            echo "Unauthorized";
            exit;
        }

        ob_start();
        require __DIR__ . '/../views/certificates/pdf_template.php';
        $html = ob_get_clean();

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->render();

        $filename = "certificate_" . $certificate['cert_code'] . ".pdf";
        $dompdf->stream($filename, ["Attachment" => true]);
    }

    // Public Access: Preview by certificate code
    public function preview()
    {
        $certCode = $_GET['code'] ?? null;

        if (!$certCode) {
            http_response_code(400);
            echo "Missing certificate code.";
            exit;
        }

        $stmt = $this->db->prepare("SELECT certificates.*, users.name AS user_name, courses.title AS course_title
                                    FROM certificates
                                    JOIN users ON users.id = certificates.user_id
                                    JOIN courses ON courses.id = certificates.course_id
                                    WHERE cert_code = ?");
        $stmt->bind_param("s", $certCode);
        $stmt->execute();
        $result = $stmt->get_result();
        $certificate = $result->fetch_assoc();

        if (!$certificate) {
            http_response_code(404);
            echo "Certificate not found.";
            exit;
        }

        require __DIR__ . '/../views/certificates/show_public.php';
    }

    // Public Access: Download PDF by certificate code
    public function downloadByCode()
    {
        $certCode = $_GET['code'] ?? null;

        if (!$certCode) {
            http_response_code(400);
            echo "Missing certificate code.";
            exit;
        }

        $stmt = $this->db->prepare("SELECT certificates.*, users.name AS user_name, courses.title AS course_title
                                    FROM certificates
                                    JOIN users ON users.id = certificates.user_id
                                    JOIN courses ON courses.id = certificates.course_id
                                    WHERE cert_code = ?");
        $stmt->bind_param("s", $certCode);
        $stmt->execute();
        $result = $stmt->get_result();
        $certificate = $result->fetch_assoc();

        if (!$certificate) {
            http_response_code(404);
            echo "Certificate not found.";
            exit;
        }

        ob_start();
        require __DIR__ . '/../views/certificates/pdf_template.php';
        $html = ob_get_clean();

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->render();

        $filename = "certificate_" . $certificate['cert_code'] . ".pdf";
        $dompdf->stream($filename, ["Attachment" => true]);
    }
}
