<?php
// ✅ Start session to track user and connect to DB
session_start();
include 'connection.php';

// ✅ Get search/filter/sort values from URL or set default
$search = $_GET['search'] ?? '';
$status = $_GET['status'] ?? 'active';
$sort = $_GET['sort'] ?? 'newest';

// ✅ Build WHERE clause based on filters
$where = "title LIKE '%$search%'";

if ($status === 'active') {
    $where .= " AND end_date >= CURDATE()"; // Only active polls
} elseif ($status === 'expired') {
    $where .= " AND end_date < CURDATE()"; // Only expired
} elseif ($status === 'not_voted') {
    $user_ip = $_SERVER['REMOTE_ADDR']; // Get IP address
    // Exclude polls already voted by this IP
    $where .= " AND id NOT IN (SELECT poll_id FROM votes WHERE ip = '$user_ip')";
}

// ✅ Determine sort order using match
$orderBy = match ($sort) {
    'oldest'   => 'created_at ASC',
    'end_date' => 'end_date ASC',
    default    => 'created_at DESC' // newest
};

// ✅ Fetch total count and actual polls
$totalPolls = $conn->query("SELECT COUNT(*) AS total FROM polls WHERE $where")->fetch_assoc()['total'];
$polls = $conn->query("SELECT * FROM polls WHERE $where ORDER BY $orderBy");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>All Polls - VoteVision</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- ✅ Bootstrap & ChartJS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- ✅ Custom Styles -->
    <style>
        body {
            background: #f5f5f5;
            padding: 30px;
        }

        .poll-card {
            border-left: 5px solid #1d3557;
        }

        .btn-glow {
            box-shadow: 0 0 10px rgba(0, 123, 255, 0.5);
        }

        .btn-glow:hover {
            box-shadow: 0 0 20px rgba(0, 123, 255, 0.7);
        }

        .poll-image {
            max-width: 100%;
            max-height: 200px;
            object-fit: contain;
            border-radius: 10px;
            display: block;
            margin: 0 auto 15px;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- ✅ Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>🗳️ All Polls</h2>
            <div>
                <a href="home.php" class="btn btn-outline-primary me-2">🏠 Home</a>
                <a href="add_poll.php" class="btn btn-success">➕ Create New Poll</a>
            </div>
        </div>

        <!-- ✅ Filter/Search Form -->
        <form class="row g-2 align-items-center mb-4" method="GET">
            <div class="col-md-4">
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control" placeholder="🔎 Search Poll Title...">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="active" <?= ($status == 'active') ? 'selected' : '' ?>>🟢 Active</option>
                    <option value="expired" <?= ($status == 'expired') ? 'selected' : '' ?>>🔴 Expired</option>
                    <option value="all" <?= ($status == 'all') ? 'selected' : '' ?>>📋 All</option>
                    <option value="not_voted" <?= ($status == 'not_voted') ? 'selected' : '' ?>>❌ Not Voted</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="sort" class="form-select">
                    <option value="newest" <?= ($sort == 'newest') ? 'selected' : '' ?>>🆕 Newest First</option>
                    <option value="oldest" <?= ($sort == 'oldest') ? 'selected' : '' ?>>📜 Oldest First</option>
                    <option value="end_date" <?= ($sort == 'end_date') ? 'selected' : '' ?>>⏰ Ending Soon</option>
                </select>
            </div>
            <div class="col-md-2 d-grid">
                <button class="btn btn-primary" type="submit">🔍 Filter</button>
            </div>
        </form>

        <!-- 🔁 Reset Button -->
        <a href="poll.php" class="btn btn-outline-secondary btn-sm mb-4">🔄 Reset Filters</a>

        <!-- 🔢 Poll Count -->
        <h6 class="text-muted mb-4">🔎 Found <?= $totalPolls ?> poll(s)</h6>

        <!-- ✅ Loop through each poll -->
        <?php if ($polls->num_rows > 0): ?>
            <div class="row">
                <?php while ($poll = $polls->fetch_assoc()): ?>
                    <?php
                    $poll_id = $poll['id'];

                    // ✅ Get poll options and vote counts
                    $options = $conn->query("
                    SELECT po.option_text, COUNT(v.id) AS vote_count
                    FROM poll_options po
                    LEFT JOIN votes v ON v.option_id = po.id
                    WHERE po.poll_id = $poll_id
                    GROUP BY po.id
                ");
                    $labels = [];
                    $counts = [];
                    while ($opt = $options->fetch_assoc()) {
                        $labels[] = $opt['option_text'];
                        $counts[] = (int)$opt['vote_count'];
                    }

                    // ✅ Check if poll is ending within 1 day
                    $today = new DateTime();
                    $endDate = new DateTime($poll['end_date']);
                    $interval = $today->diff($endDate)->days;
                    $endingSoon = ($endDate >= $today && $interval <= 1);
                    ?>
                    <div class="col-md-6 mb-4">
                        <div class="card poll-card shadow-sm h-100">
                            <div class="card-body">
                                <!-- ✅ Image (if exists) -->
                                <?php if (!empty($poll['image_url']) && file_exists('images/' . $poll['image_url'])): ?>
                                    <img src="images/<?= htmlspecialchars($poll['image_url']) ?>" class="poll-image" alt="Poll Image">
                                <?php endif; ?>

                                <!-- ✅ Poll Info -->
                                <h5><?= htmlspecialchars($poll['title']) ?></h5>
                                <p class="text-muted"><?= nl2br(htmlspecialchars($poll['description'])) ?></p>
                                <p>
                                    <strong>Ends:</strong> <?= $poll['end_date'] ?>
                                    <?php if ($endingSoon): ?>
                                        <span class="badge bg-danger ms-2">⚡ Ending Soon!</span>
                                    <?php endif; ?>
                                </p>
                                <p id="countdown<?= $poll_id ?>" class="text-muted small"></p>

                                <!-- ✅ ChartJS canvas -->
                                <canvas id="chart<?= $poll_id ?>" height="100"></canvas>

                                <!-- ✅ Action Buttons -->
                                <div class="mt-3">
                                    <?php if ($poll['end_date'] >= date('Y-m-d')): ?>
                                        <a href="vote.php?id=<?= $poll_id ?>" class="btn btn-primary btn-sm btn-glow">Vote</a>
                                    <?php else: ?>
                                        <button class="btn btn-secondary btn-sm" disabled>Voting Closed</button>
                                    <?php endif; ?>
                                    <a href="resultpoll.php?id=<?= $poll_id ?>" class="btn btn-outline-success btn-sm ms-2 btn-glow">View Results</a>
                                </div>

                                <!-- ✅ Shareable Link -->
                                <div class="mt-3">
                                    <p class="mb-1"><strong>Shareable Link:</strong></p>
                                    <div class="input-group">
                                        <input type="text" readonly class="form-control form-control-sm"
                                            value="http://localhost/election/vote.php?id=<?= $poll_id ?>">
                                        <button class="btn btn-outline-secondary btn-sm"
                                            onclick="navigator.clipboard.writeText(this.previousElementSibling.value)">Copy</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ✅ Chart + Countdown JS -->
                    <script>
                        // 📊 Show votes using ChartJS
                        new Chart(document.getElementById('chart<?= $poll_id ?>'), {
                            type: 'bar',
                            data: {
                                labels: <?= json_encode($labels) ?>,
                                datasets: [{
                                    label: 'Votes',
                                    data: <?= json_encode($counts) ?>,
                                    backgroundColor: '#457b9d'
                                }]
                            },
                            options: {
                                animation: {
                                    duration: 1500,
                                    easing: 'easeOutBounce'
                                },
                                indexAxis: 'y',
                                plugins: {
                                    legend: {
                                        display: false
                                    }
                                },
                                scales: {
                                    x: {
                                        beginAtZero: true
                                    }
                                }
                            }
                        });

                        // ⏰ Countdown to poll end
                        const countdown<?= $poll_id ?> = document.getElementById('countdown<?= $poll_id ?>');
                        const endDate<?= $poll_id ?> = new Date('<?= $poll['end_date'] ?>T23:59:59').getTime();

                        function updateCountdown<?= $poll_id ?>() {
                            const now = new Date().getTime();
                            const diff = endDate<?= $poll_id ?> - now;
                            if (diff <= 0) {
                                countdown<?= $poll_id ?>.innerHTML = "⏳ Poll Closed";
                                return;
                            }
                            const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                            const hours = Math.floor((diff / (1000 * 60 * 60)) % 24);
                            const minutes = Math.floor((diff / (1000 * 60)) % 60);
                            countdown<?= $poll_id ?>.innerHTML = `⏰ Ends in ${days}d ${hours}h ${minutes}m`;
                        }

                        updateCountdown<?= $poll_id ?>();
                        setInterval(updateCountdown<?= $poll_id ?>, 60000);
                    </script>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <!-- ❌ No polls message -->
            <p class="text-center text-muted">No polls found matching your search.</p>
        <?php endif; ?>
    </div>
</body>

</html>