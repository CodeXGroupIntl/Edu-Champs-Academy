<!-- views/certificates/show_public.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Certificate Preview</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: #f4f4f4;
            padding: 50px;
            text-align: center;
        }
        .certificate {
            background: #fff;
            border: 10px solid #007BFF;
            padding: 30px;
            display: inline-block;
            width: 80%;
            max-width: 800px;
            box-shadow: 0 0 15px rgba(0,0,0,0.2);
        }
        .certificate h1 {
            color: #007BFF;
        }
        .certificate h2 {
            margin-top: 30px;
            color: #333;
        }
        .certificate p {
            font-size: 18px;
            color: #555;
        }
        .certificate .code {
            margin-top: 20px;
            font-size: 14px;
            color: #999;
        }
        .download-btn {
            margin-top: 30px;
        }
        .download-btn a {
            display: inline-block;
            background: #28A745;
            color: #fff;
            padding: 10px 20px;
            text-decoration: none;
            font-weight: bold;
            border-radius: 5px;
        }
        .download-btn a:hover {
            background: #218838;
        }
    </style>
</head>
<body>
    <div class="certificate">
        <h1>Certificate of Completion</h1>
        <p>This is to certify that</p>
        <h2><?= htmlspecialchars($certificate['user_name']) ?></h2>
        <p>has successfully completed the course:</p>
        <h2><?= htmlspecialchars($certificate['course_title']) ?></h2>
        <p>Awarded on: <?= date("F j, Y", strtotime($certificate['awarded_at'])) ?></p>
        <div class="code">Certificate Code: <strong><?= htmlspecialchars($certificate['cert_code']) ?></strong></div>

        <div class="download-btn">
            <a href="/index.php?route=certificates/download-by-code&code=<?= urlencode($certificate['cert_code']) ?>">Download PDF</a>
        </div>
    </div>
</body>
</html>
