<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['vote_message'] = '❌ You must be logged in to vote.';
    header("Location: events_overview.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$candidate_ids = $_POST['votes'] ?? [];
$event_id = $_POST['event_id'] ?? 0;

if (empty($candidate_ids) || !$event_id) {
    $_SESSION['vote_message'] = '⚠️ Invalid vote submission.';
    header("Location: events_overview.php");
    exit();
}

// ✅ Check if user already voted in this event
$checkEventVote = $conn->query("SELECT * FROM user_votes WHERE user_id = $user_id AND event_id = $event_id");

if ($checkEventVote->num_rows > 0) {
    $_SESSION['vote_message'] = '⚠️ You have already voted in this event.';
    header("Location: events_overview.php");
    exit();
}

// ✅ Save all selected votes
$successCount = 0;
$errorCount = 0;

foreach ($candidate_ids as $candidate_id) {
    $candidate_id = intval($candidate_id);
    $insert = $conn->query("INSERT INTO candidate_votes (user_id, candidate_id, voted_at) VALUES ($user_id, $candidate_id, NOW())");

    if ($insert) {
        $successCount++;
    } else {
        $errorCount++;
    }
}

// ✅ Mark the user as voted in this event
$conn->query("INSERT INTO user_votes (user_id, event_id) VALUES ($user_id, $event_id)");

$_SESSION['vote_message'] = "✅ $successCount vote(s) submitted.";
if ($errorCount > 0) {
    $_SESSION['vote_message'] .= " ❌ $errorCount failed.";
}

header("Location: events_overview.php");
exit();
