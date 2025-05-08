<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Certificate Preview</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            text-align: center;
            padding: 50px;
            background-color: #f0f8ff;
        }
        .certificate-border {
            border: 10px solid #28a745;
            padding: 40px;
            background: #fff;
            max-width: 800px;
            margin: auto;
        }
        h1 {
            font-size: 36px;
            color: #222;
        }
        h2 {
            font-size: 28px;
            margin: 20px 0;
            color: #333;
        }
        .meta {
            font-size: 18px;
            margin-top: 30px;
            color: #444;
        }
        .cert-code {
            margin-top: 15px;
            font-size: 14px;
            color: #888;
        }
        .signature {
            margin-top: 50px;
            font-style: italic;
            color: #555;
        }
        .logo {
            margin-bottom: 20px;
        }
        .download-btn {
            margin-top: 40px;
        }
        .btn {
            background-color: #28a745;
            color: white;
            text-decoration: none;
            padding: 12px 25px;
            border-radius: 5px;
            font-weight: bold;
        }
        .btn:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="certificate-border">
        <div class="logo">
            <img src="https://yourdomain.com/assets/logo.png" alt="Edu-Champs Logo" width="100">
        </div>

        <h1>Certificate of Completion</h1>
        <p>This is to proudly certify that</p>

        <h2><?= htmlspecialchars($certificate['user_name']) ?></h2>
        <p>has completed the course</p>
        <h2>“<?= htmlspecialchars($certificate['course_title']) ?>”</h2>

        <div class="meta">
            Awarded on <?= date("F j, Y", strtotime($certificate['awarded_at'])) ?>
        </div>

        <div class="cert-code">
            Certificate Code: <?= $certificate['cert_code'] ?>
        </div>

        <div class="signature">
            Edu-Champs Team
        </div>

        <div class="download-btn">
            <a class="btn" href="/certificates/download.php?code=<?= urlencode($certificate['cert_code']) ?>">Download PDF</a>
        </div>
    </div>
</body>
</html>
