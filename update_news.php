<?php
session_start();
include 'connection.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $content = $conn->real_escape_string($_POST['content']);
    $existing = $conn->query("SELECT * FROM news")->num_rows;

    if ($existing > 0) {
        $conn->query("UPDATE news SET content='$content' WHERE id=1");
    } else {
        $conn->query("INSERT INTO news (content) VALUES ('$content')");
    }

    // Redirect to home page after update
    header("Location: home.php");
    exit;
}

// Load existing news content
$news = $conn->query("SELECT content FROM news LIMIT 1")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Update Latest News - VoteVision</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            padding-top: 60px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .form-container {
            max-width: 700px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="form-container mt-5">
            <h3 class="mb-4 text-center">✏️ Update Latest News</h3>
            <form method="POST">
                <div class="mb-3">
                    <label for="content" class="form-label">News Content</label>
                    <textarea name="content" id="content" class="form-control" rows="7" required><?= $news['content'] ?? '' ?></textarea>
                </div>
                <div class="d-flex justify-content-between">
                    <a href="home.php" class="btn btn-secondary">← Cancel</a>
                    <button type="submit" class="btn btn-primary">Save & Return</button>
                </div>
            </form>
        </div>
    </div>

</body>

</html>