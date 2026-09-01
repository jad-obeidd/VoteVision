<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['isloggedin']) || !isset($_SESSION['user_id'])) {
    $_SESSION['vote_error'] = "🔒 You must be logged in to vote.";
    header("Location: vote.php?id=" . ($_POST['poll_id'] ?? 0));
    exit();
}

$user_id = (int)$_SESSION['user_id'];
$poll_id = (int)($_POST['poll_id'] ?? 0);

// Normalize selection: make sure it's an array
$raw = $_POST['options'] ?? [];
$selected_options = is_array($raw) ? $raw : [$raw];

if (empty($selected_options)) {
    $_SESSION['vote_error'] = "⚠️ Please select at least one option.";
    header("Location: vote.php?id=$poll_id");
    exit();
}

// Check duplicate vote
$check = $conn->query("SELECT * FROM votes WHERE poll_id = $poll_id AND user_id = $user_id");
if ($check->num_rows > 0) {
    $_SESSION['vote_error'] = "❌ You have already voted in this poll.";
    header("Location: vote.php?id=$poll_id");
    exit();
}

// Validate poll
$poll = $conn->query("SELECT * FROM polls WHERE id = $poll_id")->fetch_assoc();
if (!$poll) {
    $_SESSION['vote_error'] = "❌ Invalid poll.";
    header("Location: vote.php?id=$poll_id");
    exit();
}

// Check max selections
$max = (int)$poll['max_selections'];
if (count($selected_options) > $max) {
    $_SESSION['vote_error'] = "❌ You can select up to $max option(s).";
    header("Location: vote.php?id=$poll_id");
    exit();
}

// Insert votes
foreach ($selected_options as $option_id) {
    $option_id = (int)$option_id;
    $conn->query("INSERT INTO votes (poll_id, option_id, user_id) VALUES ($poll_id, $option_id, $user_id)");
}

$_SESSION['vote_success'] = "✅ Your vote has been submitted successfully!";
header("Location: vote.php?id=$poll_id");
exit();
