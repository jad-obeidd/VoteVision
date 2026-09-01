<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_title = trim($_POST['event_title']);
    // $max_votes   = (int)$_POST['max_votes'];
    $user_id     = $_SESSION['user_id'];
    $lists       = $_POST['lists'];

    // get & convert the datetime-local inputs
    $rawStart   = $_POST['start_time']; // e.g. "2025-05-13T14:30"
    $rawEnd     = $_POST['end_time'];
    $start_time = str_replace('T', ' ', $rawStart) . ':00'; // "YYYY-MM-DD HH:MM:00"
    $end_time   = str_replace('T', ' ', $rawEnd)   . ':00';

    // Insert into events table (now with start_time & end_time)
    $stmt = $conn->prepare("
      INSERT INTO events
        (title, max_votes, start_time, end_time, created_by)
      VALUES (?,       ?,         ?,          ?,        ?)
    ");
    $stmt->bind_param(
        "sissi",
        $event_title,
        $max_votes,
        $start_time,
        $end_time,
        $user_id
    );
    $stmt->execute();
    $event_id = $stmt->insert_id;

    // Loop through lists
    foreach ($lists as $listIndex => $list) {
        $list_name = trim($list['name']);

        // Insert into lists table
        $stmt = $conn->prepare("
          INSERT INTO lists
            (event_id, name)
          VALUES (?,        ?)
        ");
        $stmt->bind_param("is", $event_id, $list_name);
        $stmt->execute();
        $list_id = $stmt->insert_id;

        // Handle candidates in this list
        foreach ($list['candidates'] as $candIndex => $cand) {
            $name  = trim($cand['name']);
            $party = trim($cand['party']);
            $title = trim($cand['title']);
            $desc  = trim($cand['description']);

            // Fetch image data from $_FILES
            $imageField = $_FILES['lists']['name'][$listIndex]['candidates'][$candIndex]['image'] ?? '';
            $tmpField   = $_FILES['lists']['tmp_name'][$listIndex]['candidates'][$candIndex]['image'] ?? '';

            if ($imageField && $tmpField) {
                $filename   = time() . '_' . basename($imageField);
                $targetPath = 'images/' . $filename;
                move_uploaded_file($tmpField, $targetPath);
            } else {
                $filename = ''; // or a default placeholder
            }

            // Insert into candidates table
            $stmt = $conn->prepare("
              INSERT INTO candidates
                (list_id, name, party, title, description, image_url, created_by)
              VALUES (?,       ?,    ?,     ?,     ?,           ?,         ?)
            ");
            $stmt->bind_param(
                "isssssi",
                $list_id,
                $name,
                $party,
                $title,
                $desc,
                $filename,
                $user_id
            );
            $stmt->execute();
        }
    }

    // Redirect on success
    header("Location: events_overview.php");
    exit();
}
