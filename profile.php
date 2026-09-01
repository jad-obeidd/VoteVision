<?php
session_start();
include 'connection.php';

// Redirect if not logged in
if (!isset($_SESSION['isloggedin']) || !isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get user info
$user_id = $_SESSION['user_id'];
$name = $_SESSION['name'];
$email = $_SESSION['email'];

// Get how many polls the user voted in
$votes_count = $conn->query("SELECT COUNT(DISTINCT poll_id) AS total FROM votes WHERE user_id = $user_id")
    ->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Profile - VoteVision</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #83a4d4, #b6fbff);
            padding: 60px 20px;
        }

        .profile-card {
            background: white;
            padding: 40px 30px;
            border-radius: 16px;
            max-width: 600px;
            margin: auto;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .profile-card h2 {
            font-weight: bold;
            color: #1d3557;
        }

        .profile-card small {
            color: #6c757d;
        }

        .badge {
            font-size: 1.2rem;
            padding: 10px 16px;
        }

        .btn-lg {
            font-size: 1.1rem;
        }

        .btn-outline-warning:hover {
            color: white;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="profile-card text-center">

            <h2>👤 <?= htmlspecialchars($name) ?></h2>
            <small><?= htmlspecialchars($email) ?></small>

            <hr class="my-4">

            <div class="mb-4">
                <h5>🗳️ Polls Voted In</h5>
                <span class="badge bg-primary"><?= $votes_count ?></span>
            </div>

            <div class="d-grid mb-3">
                <a href="edit_profile.php" class="btn btn-outline-warning btn-lg">✏️ Edit Profile</a>
            </div>

            <div class="d-flex justify-content-center gap-3">
                <a href="home.php" class="btn btn-outline-primary">🏠 Home</a>
                <a href="logout.php" class="btn btn-outline-danger">🚪 Logout</a>
            </div>

        </div>
    </div>

</body>

</html>