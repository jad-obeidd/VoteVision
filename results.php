<?php
include 'connection.php'; // ✅ Connect to the database

// ✅ SQL query to get vote results for each candidate with list and event info
$sql = "
  SELECT 
    c.name AS candidate_name,                            -- Candidate's name
    l.name AS list_name,                                 -- List name
    IFNULL(e.title, 'No Event') AS event_title,          -- Event title or 'No Event' fallback
    COUNT(v.id) AS vote_count                            -- Number of votes this candidate got
  FROM candidates c
  LEFT JOIN candidate_votes v ON c.id = v.candidate_id   -- Join vote data
  LEFT JOIN lists l ON c.list_id = l.id                  -- Join the list data
  LEFT JOIN events e ON l.event_id = e.id                -- Join event data
  GROUP BY c.id, l.name, e.title                         -- Grouping for correct counting
  ORDER BY e.title, vote_count DESC                      -- Sort by event then highest votes
";

// ✅ Run the query
$result = $conn->query($sql);

// ✅ Group data by event title
$events = [];
while ($row = $result->fetch_assoc()) {
    $eventTitle = $row['event_title']; // Get the event title
    $events[$eventTitle][] = $row;     // Add candidate row under the event group
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Vote Results - VoteVision</title>

    <!-- ✅ Bootstrap for styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- ✅ Custom Styling -->
    <style>
        /* 🔷 Page background and font setup */
        body {
            background: linear-gradient(to right, #f8f9fa, #e3e6f0);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 40px;
        }

        /* 🔷 Header style */
        .results-header {
            backdrop-filter: blur(6px);
            background: rgba(255, 255, 255, 0.85);
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            text-align: center;
        }

        .results-header h2 {
            color: #0d6efd;
            font-weight: bold;
        }

        /* 🔷 Box for each event results */
        .results-table {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            margin-bottom: 40px;
        }

        /* 🔷 Table header style */
        table th {
            background: #0d6efd;
            color: white;
        }

        /* 🔷 Hover effect on rows */
        table tbody tr:hover {
            background-color: #f1f9ff;
        }

        /* 🔷 Vote count style */
        .vote-count {
            font-weight: bold;
            color: #28a745;
            text-shadow: 0 0 4px rgba(40, 167, 69, 0.3);
        }
    </style>
</head>

<body>
    <div class="container">

        <!-- ✅ Page header with button -->
        <div class="results-header">
            <h2>📊 Vote Results</h2>
            <p class="text-muted">Final count of all candidates ranked by votes</p>
            <a href="events_overview.php" class="btn btn-outline-dark btn-sm">🏠 Back to Home</a>
        </div>

        <!-- ✅ Loop through all events and show tables -->
        <?php if (!empty($events)): ?>
            <?php foreach ($events as $eventTitle => $candidates): ?>
                <div class="results-table p-4">
                    <h4 class="mb-4 text-primary"><?= htmlspecialchars($eventTitle) ?></h4>

                    <!-- ✅ Results Table -->
                    <table class="table table-bordered text-center align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Candidate Name</th>
                                <th>List</th>
                                <th>Total Votes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $rank = 1; // Start ranking from 1 
                            ?>
                            <?php foreach ($candidates as $row): ?>
                                <tr>
                                    <td><?= $rank++ ?></td> <!-- Rank -->
                                    <td><?= htmlspecialchars($row['candidate_name']) ?></td> <!-- Candidate -->
                                    <td><?= htmlspecialchars($row['list_name'] ?? 'No List') ?></td> <!-- List -->
                                    <td class="vote-count"><?= $row['vote_count'] ?></td> <!-- Votes -->
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <!-- ✅ If there are no candidates -->
            <div class="alert alert-warning text-center">No candidates found.</div>
        <?php endif; ?>

    </div>
</body>

</html>