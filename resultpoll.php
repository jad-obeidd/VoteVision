<?php
// ✅ Connect to database
include 'connection.php';

// ✅ Get poll ID from URL
$poll_id = $_GET['id'] ?? 0;
if (!$poll_id) {
    die("Poll not found."); // ❌ If no ID, stop
}

// ✅ Fetch poll info (title, description, etc.)
$poll = $conn->query("SELECT * FROM polls WHERE id = $poll_id")->fetch_assoc();
if (!$poll) {
    die("Poll not found."); // ❌ If invalid ID, stop
}

// ✅ Get each option and its vote count
$options = $conn->query("
    SELECT po.id, po.option_text,
           (SELECT COUNT(*) FROM votes v WHERE v.option_id = po.id) AS vote_count
    FROM poll_options po
    WHERE po.poll_id = $poll_id
    ORDER BY po.id
");

// ✅ Prepare arrays for Chart.js
$labels = [];        // Option names
$votes = [];         // Number of votes per option
$totalVotes = 0;     // Total votes

while ($row = $options->fetch_assoc()) {
    $labels[] = $row['option_text'];
    $votes[] = (int)$row['vote_count'];
    $totalVotes += (int)$row['vote_count'];
}

// ✅ Find highest vote count (used to highlight the winner)
$maxVotes = !empty($votes) ? max($votes) : 1;
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Results - <?= htmlspecialchars($poll['title']) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- ✅ Load Bootstrap and Chart.js -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- ✅ Some page styles -->
    <style>
        body {
            background: #f5f5f5;
            padding: 30px;
        }

        .btn-back {
            box-shadow: 0 0 8px rgba(0, 123, 255, 0.4);
        }

        canvas {
            max-height: 500px;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <!-- ✅ Poll Title and Description -->
        <h2 class="mb-3">📊 Results for: <?= htmlspecialchars($poll['title']) ?></h2>
        <p class="text-muted mb-4"><?= nl2br(htmlspecialchars($poll['description'])) ?></p>

        <!-- ✅ Total Votes -->
        <div class="mb-4">
            <h5>Total Votes Cast: <span class="badge bg-success"><?= $totalVotes ?></span></h5>
        </div>

        <!-- ✅ Chart Container -->
        <div class="card shadow-sm p-4">
            <canvas id="resultsChart"></canvas>
        </div>

        <!-- ✅ Back Button -->
        <div class="mt-4">
            <a href="poll.php" class="btn btn-primary btn-back">⬅️ Back to All Polls</a>
        </div>
    </div>

    <!-- ✅ Chart Script -->
    <script>
        // ✅ Bring PHP data into JavaScript
        const labels = <?= json_encode($labels) ?>;
        const votes = <?= json_encode($votes) ?>;
        const maxVote = Math.max(...votes);
        const totalVotes = <?= $totalVotes ?>;

        // ✅ Color logic: winner = red, others = blue
        const colors = votes.map(v => v === maxVote ? '#e63946' : '#1d3557');

        // ✅ Calculate vote percentages
        const percentages = votes.map(v => totalVotes > 0 ? ((v / totalVotes) * 100).toFixed(1) + '%' : '0%');

        // ✅ Display Chart
        new Chart(document.getElementById('resultsChart'), {
            type: 'bar',
            data: {
                labels: labels.map((label, i) => `${label} (${percentages[i]})`),
                datasets: [{
                    label: 'Votes',
                    data: votes,
                    backgroundColor: colors
                }]
            },
            options: {
                indexAxis: 'x', // vertical chart
                scales: {
                    x: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        display: false // ❌ no legend needed
                    }
                }
            }
        });
    </script>
</body>

</html>