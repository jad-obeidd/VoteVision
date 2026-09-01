<?php
// ✅ Connect to the database
include 'connection.php';

// ✅ Start the session to access logged-in user info
session_start();

// ❌ If the user is not logged in, redirect them to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// ✅ Get the logged-in user's ID
$user_id = $_SESSION['user_id'];

// ✅ Query to get user's voting history
$sql = "
    SELECT p.title, o.option_text, v.voted_at
    FROM votes v
    JOIN poll_options o ON v.option_id = o.id
    JOIN polls p ON v.poll_id = p.id
    WHERE v.user_id = $user_id
    ORDER BY v.voted_at DESC
";

// ✅ Run the query
$result = $conn->query($sql);
?>

<!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <title>My Voting History - VoteVision</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- ✅ Bootstrap for responsive layout -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

        <!-- ✅ Page styling -->
        <style>
            body {
                background: linear-gradient(to right, #f8f9fa, #e3e6f0);
                min-height: 100vh;
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                padding: 40px 15px;
            }

            .history-box {
                background: rgba(255, 255, 255, 0.9);
                backdrop-filter: blur(6px);
                border-radius: 16px;
                padding: 40px;
                box-shadow: 0 12px 30px rgba(0, 0, 0, 0.1);
                max-width: 950px;
                margin: auto;
            }

            h3 {
                color: #0d6efd;
                font-weight: bold;
                text-align: center;
                margin-bottom: 30px;
            }

            table th {
                background-color: #0d6efd;
                color: white;
            }

            table td {
                vertical-align: middle;
            }

            .alert-info {
                background-color: #d1ecf1;
                color: #0c5460;
            }

            .back-btn {
                text-align: center;
                margin-top: 30px;
            }

            .btn-home {
                background-color: #0d6efd;
                color: white;
                padding: 10px 30px;
                border-radius: 30px;
                font-weight: 500;
                transition: 0.3s ease;
            }

            .btn-home:hover {
                background-color: #0a58ca;
            }
        </style>
    </head>

    <body>

        <div class="container">
            <div class="history-box">
                <!-- ✅ Page Title -->
                <h3>🧾 My Voting History</h3>

                <!-- ✅ If user has voting records, show them -->
                <?php if ($result->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover text-center">
                            <thead>
                                <tr>
                                    <th>📌 Poll Title</th>
                                    <th>✅ Option Voted</th>
                                    <th>⏰ Vote Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- ✅ Loop through each vote row -->
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <!-- ✅ Escape output to prevent XSS -->
                                        <td><?= htmlspecialchars($row['title']) ?></td>
                                        <td><?= htmlspecialchars($row['option_text']) ?></td>
                                        <td><?= date('Y-m-d H:i:s', strtotime($row['voted_at'])) ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- ❌ If no votes found -->
                <?php else: ?>
                    <div class="alert alert-info text-center">You haven't voted in any polls yet.</div>
                <?php endif; ?>

                <!-- 🔙 Back to home button -->
                <div class="back-btn">
                    <a href="home.php" class="btn btn-home">← Back to Home</a>
                </div>
            </div>
        </div>

    </body>

    </html>