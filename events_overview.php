<?php
session_start(); // Start session to track user login
include 'connection.php'; // DB connection file

// Set timezone to Beirut and get the current date and time
date_default_timezone_set('Asia/Beirut');
$now = new DateTime();

// Fetch all events from the database, newest first
$events = $conn->query("SELECT * FROM events ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>All Events - VoteVision</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- ✅ Custom Page Styling -->
    <style>
        /* 🔷 Page background and font */
        body {
            background: linear-gradient(to right, #f8f9fa, #e3e6f0);
            /* Light gradient */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding-bottom: 100px;
        }

        /* 🔷 Event card style */
        .event-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
            transition: .3s;
        }

        .event-card:hover {
            transform: scale(1.02);
        }

        /* 🔷 Modal header gradient */
        .modal-header {
            background: linear-gradient(to right, #007bff, #0056b3);
            color: white;
        }

        /* 🔷 Blur glass header at top */
        .glass-header {
            backdrop-filter: blur(6px);
            background: rgba(255, 255, 255, 0.85);
            padding: 20px;
            border-radius: 12px;
            margin-top: 20px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        }

        /* 🔷 List title and action buttons */
        .list-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 30px 0 15px;
        }

        /* 🔷 Candidate image inside list */
        .list-group-item img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
        }

        /* 🔷 Style for selected/voted candidates */
        .candidate-card.selected {
            background-color: #e0f8e9;
            border: 5px solid #28a745;
            box-shadow: 0 0 12px rgba(40, 167, 69, 0.3);
        }

        .voted {
            background-color: #e6f7ff;
            border-left: 5px solid #007bff;
        }

        /* 🔷 Checkbox style */
        .form-check-input {
            width: 2.5em;
            height: 1.3em;
            cursor: pointer;
        }
    </style>
</head>

<body>

    <!-- ✅ Top Section -->
    <div class="container">
        <!-- Header with nav buttons -->
        <div class="glass-header d-flex flex-wrap justify-content-between align-items-center mb-4">
            <h2 class="mb-0">🧑‍💼 Candidate Management</h2>
            <div class="d-flex gap-2 mt-3 mt-sm-0">
                <a href="home.php" class="btn btn-outline-dark">🏠 Home</a>
                <a href="voted_candidates.php" class="btn btn-success">🏆 Voted Candidates</a>
                <a href="add_event.php" class="btn btn-primary">+ Add Event</a>
                <a href="results.php" class="btn btn-outline-info">📊 Results</a>
            </div>
        </div>

        <!-- Events List -->
        <h3 class="text-center text-primary mb-5">🗳️ All Events</h3>
        <div class="row">

            <?php while ($event = $events->fetch_assoc()):
                // Parse event time
                $start = new DateTime($event['start_time']);
                $end = new DateTime($event['end_time']);
                $notStarted = ($now < $start);
                $ended = ($now > $end);

                // Check if user already voted in this event
                $alreadyVoted = false;
                $votedIds = [];
                if (isset($_SESSION['user_id'])) {
                    $uid = $_SESSION['user_id'];
                    $qc = $conn->query("SELECT 1 FROM user_votes WHERE user_id = $uid AND event_id = {$event['id']}");
                    $alreadyVoted = $qc->num_rows > 0;

                    // If voted, get candidate IDs
                    if ($alreadyVoted) {
                        $vr = $conn->query("SELECT cv.candidate_id
                            FROM candidate_votes cv
                            JOIN candidates c ON cv.candidate_id = c.id
                            WHERE cv.user_id = $uid
                            AND c.list_id IN (
                                SELECT id FROM lists WHERE event_id = {$event['id']}
                            )");
                        while ($r = $vr->fetch_assoc()) {
                            $votedIds[] = $r['candidate_id'];
                        }
                    }
                }

                // Voting conditions
                $canVote = isset($_SESSION['user_id']) &&
                    !$alreadyVoted &&
                    $_SESSION['user_id'] != $event['created_by'] &&
                    !$notStarted &&
                    !$ended;
            ?>

                <!-- ✅ Event Card -->
                <div class="col-md-4 mb-4">
                    <div class="card event-card h-100">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title text-primary"><?= htmlspecialchars($event['title']) ?></h5>
                            <p class="mb-2"><small>
                                    Opens: <?= $start->format('Y-m-d H:i') ?><br>
                                    Closes: <?= $end->format('Y-m-d H:i') ?>
                                </small></p>
                            <div class="mt-auto">
                                <button class="btn btn-outline-primary btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#eventModal<?= $event['id'] ?>">
                                    🔍 View
                                </button>

                                <?php if ($_SESSION['user_id'] == $event['created_by']): ?>
                                    <a href="edit_event.php?id=<?= $event['id'] ?>" class="btn btn-outline-warning btn-sm">✏️ Edit</a>
                                    <a href="delete_event.php?id=<?= $event['id'] ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Delete this event and its content?')">🗑️ Delete</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ✅ Modal for Voting -->
                <div class="modal fade" id="eventModal<?= $event['id'] ?>" data-start="<?= $event['start_time'] ?>" data-end="<?= $event['end_time'] ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-xl modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title"><?= htmlspecialchars($event['title']) ?> – Candidate Lists</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">

                                <?php if ($canVote): ?>
                                    <form method="POST" action="vote_candidate.php" onsubmit="return confirmVote(event, <?= $event['id'] ?>)">
                                        <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
                                    <?php endif; ?>

                                    <?php
                                    $lists = $conn->query("SELECT * FROM lists WHERE event_id = {$event['id']}");
                                    while ($list = $lists->fetch_assoc()):
                                    ?>
                                        <!-- ✅ Candidate List Block -->
                                        <div class="list-header">
                                            <h6 class="text-info"><?= htmlspecialchars($list['name']) ?></h6>
                                            <?php if ($_SESSION['user_id'] == $list['created_by']): ?>
                                                <div>
                                                    <a href="edit_list.php?id=<?= $list['id'] ?>" class="btn btn-sm btn-outline-warning">✏️ Edit List</a>
                                                    <a href="delete_list.php?id=<?= $list['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this list and its candidates?')">🗑️ Delete List</a>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <!-- ✅ Candidate Cards -->
                                        <ul class="list-group mb-4">
                                            <?php
                                            $cands = $conn->query("SELECT * FROM candidates WHERE list_id = {$list['id']}");
                                            while ($c = $cands->fetch_assoc()):
                                                $isV = in_array($c['id'], $votedIds);
                                                $isCreator = $_SESSION['user_id'] == $c['created_by'];
                                            ?>
                                                <li class="list-group-item candidate-card d-flex justify-content-between align-items-center flex-wrap <?= $isV ? 'voted' : '' ?>" id="card<?= $c['id'] ?>">
                                                    <div class="d-flex align-items-center gap-3 flex-wrap">
                                                        <?php if (!empty($c['image_url'])): ?>
                                                            <img src="images/<?= htmlspecialchars($c['image_url']) ?>" alt="">
                                                        <?php endif; ?>
                                                        <div>
                                                            <strong><?= htmlspecialchars($c['name']) ?></strong><br>
                                                            <small><strong>Party:</strong> <?= htmlspecialchars($c['party']) ?> | <strong>Title:</strong> <?= htmlspecialchars($c['title']) ?></small><br>
                                                            <small><?= htmlspecialchars($c['description']) ?></small>
                                                        </div>
                                                    </div>

                                                    <!-- Edit/Delete or Vote -->
                                                    <?php if ($isCreator): ?>
                                                        <div class="d-flex gap-2">
                                                            <a href="edit_candidate.php?id=<?= $c['id'] ?>" class="btn btn-sm btn-outline-warning">✏️ Edit</a>
                                                            <a href="delete_candidate.php?id=<?= $c['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this candidate?')">🗑️ Delete</a>
                                                        </div>
                                                    <?php elseif ($canVote): ?>
                                                        <input class="form-check-input vote-checkbox" type="checkbox"
                                                            name="votes[]" value="<?= $c['id'] ?>"
                                                            data-event="<?= $event['id'] ?>"
                                                            onchange="handleCardHighlight(this, <?= $c['id'] ?>, <?= $event['id'] ?>, <?= $event['max_votes'] ?>)">
                                                    <?php endif; ?>
                                                </li>
                                            <?php endwhile; ?>
                                        </ul>
                                    <?php endwhile; ?>

                                    <?php if ($canVote): ?>
                                        <div class="text-end">
                                            <button type="submit" class="btn btn-success submit-votes">Submit Votes</button>
                                        </div>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- ✅ Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- ✅ JavaScript Voting Logic -->
    <script>
        // Highlight card when selected
        function handleCardHighlight(checkbox, id, eventId, maxAllowed) {
            updateLimit(eventId, maxAllowed);
            document.getElementById(`card${id}`).classList.toggle('selected', checkbox.checked);
        }

        // Enforce vote limit
        function updateLimit(eventId, maxAllowed) {
            const boxes = document.querySelectorAll(`.vote-checkbox[data-event='${eventId}']`);
            const count = [...boxes].filter(cb => cb.checked).length;
            if (count >= maxAllowed) {
                boxes.forEach(cb => {
                    if (!cb.checked) cb.disabled = true;
                });
                alert("Max selections reached.");
            } else {
                boxes.forEach(cb => cb.disabled = false);
            }
        }

        // Prevent empty vote submission
        function confirmVote(e, eventId) {
            const sel = document.querySelectorAll(`.vote-checkbox[data-event='${eventId}']:checked`);
            if (!sel.length) {
                alert("Select at least one candidate.");
                e.preventDefault();
                return false;
            }
            return true;
        }

        // Format date nicely
        function human(dt) {
            return dt.toLocaleString('en-US', {
                month: 'long',
                day: 'numeric',
                year: 'numeric',
                hour: 'numeric',
                minute: '2-digit',
                hour12: true
            });
        }

        // Show modal status (open/closed)
        document.querySelectorAll('.modal.fade').forEach(modal => {
            modal.addEventListener('show.bs.modal', () => {
                const start = new Date(modal.dataset.start.replace(' ', 'T'));
                const end = new Date(modal.dataset.end.replace(' ', 'T'));
                const now = new Date();

                const form = modal.querySelector('form');
                const submit = modal.querySelector('.submit-votes');
                const checks = modal.querySelectorAll('.vote-checkbox');
                const body = modal.querySelector('.modal-body');

                body.querySelectorAll('.alert').forEach(a => a.remove());

                if (now < start) {
                    body.insertAdjacentHTML('afterbegin', `<div class="alert alert-info text-center">⏳ Opens at ${human(start)}.</div>`);
                    if (submit) submit.style.display = 'none';
                    checks.forEach(cb => cb.disabled = true);
                } else if (now > end) {
                    body.insertAdjacentHTML('afterbegin', `<div class="alert alert-danger text-center">🛑 Closed on ${human(end)}.</div>`);
                    if (form) form.style.display = 'none';
                    checks.forEach(cb => cb.disabled = true);
                } else {
                    if (form) form.style.display = '';
                    if (submit) submit.disabled = false;
                    checks.forEach(cb => cb.disabled = false);
                    checks.forEach(cb => {
                        if (cb.checked) document.getElementById(`card${cb.value}`).classList.add('selected');
                    });
                }
            });
        });
    </script>
</body>

</html>