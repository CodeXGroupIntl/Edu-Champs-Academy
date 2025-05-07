<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Certificate of Completion</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            text-align: center;
            padding: 50px;
            background: #f9f9f9;
        }
        .certificate-border {
            border: 10px solid #28a745;
            padding: 40px;
        }
        .certificate-content {
            background-color: #fff;
            padding: 30px;
            border: 2px dashed #28a745;
        }
        h1 {
            font-size: 36px;
            color: #333;
            margin-bottom: 10px;
        }
        h2 {
            font-size: 28px;
            color: #555;
            margin: 20px 0;
        }
        .details {
            font-size: 18px;
            margin-top: 30px;
            color: #222;
        }
        .cert-code {
            font-size: 14px;
            color: #777;
            margin-top: 20px;
        }
        .signature {
            margin-top: 50px;
            font-style: italic;
            color: #444;
        }
        .logo {
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="certificate-border">
        <div class="certificate-content">
            <div class="logo">
                <img src="https://yourdomain.com/assets/logo.png" alt="Edu-Champs Logo" width="100">
            </div>
            <h1>Certificate of Completion</h1>
            <p>This is to proudly certify that</p>
            <h2><?= htmlspecialchars($certificate['user_name']) ?></h2>
            <p>has successfully completed the course</p>
            <h2>“<?= htmlspecialchars($certificate['course_title']) ?>”</h2>

            <div class="details">
                Awarded on <?= date("F j, Y", strtotime($certificate['awarded_at'])) ?>
            </div>

            <div class="cert-code">
                Certificate Code: <?= $certificate['cert_code'] ?>
            </div>

            <div class="signature">
                Edu-Champs Team
            </div>
        </div>
    </div>
</body>
</html>
