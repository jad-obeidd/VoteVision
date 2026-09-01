<?php
include 'connection.php';

// Get top 5 users by number of votes
$sql = "
    SELECT u.name, COUNT(v.id) AS vote_count
    FROM user u
    JOIN votes v ON u.id = v.user_id
    GROUP BY u.id
    ORDER BY vote_count DESC
    LIMIT 5
";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    echo "<li><strong>" . htmlspecialchars($row['name']) . "</strong> - " . $row['vote_count'] . " votes</li>";
  }
} else {
  echo "<li>No votes recorded yet.</li>";
}
