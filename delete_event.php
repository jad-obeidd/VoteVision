<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['user_id'])) {
    die("Access denied.");
}

$event_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Verify ownership
$stmt = $conn->prepare("SELECT * FROM events WHERE id = ? AND created_by = ?");
$stmt->bind_param("ii", $event_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Event not found or access denied.");
}

// Delete event
$delete_stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
$delete_stmt->bind_param("i", $event_id);
$delete_stmt->execute();

header("Location: events_overview.php");
exit();
