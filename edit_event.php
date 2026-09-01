<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['user_id'])) {
    die("Access denied.");
}

$event_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Fetch event
$stmt = $conn->prepare("SELECT * FROM events WHERE id = ? AND created_by = ?");
$stmt->bind_param("ii", $event_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$event = $result->fetch_assoc();

if (!$event) {
    die("Event not found or access denied.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];

    $update_stmt = $conn->prepare("UPDATE events SET title = ? WHERE id = ?");
    $update_stmt->bind_param("si", $title, $event_id);
    $update_stmt->execute();

    header("Location: events_overview.php");
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit Event</title>
</head>

<body>
    <h2>Edit Event</h2>
    <form method="POST">
        <label>Title:</label>
        <input type="text" name="title" value="<?= htmlspecialchars($event['title']) ?>" required>
        <button type="submit">Save</button>
    </form>
</body>

</html>