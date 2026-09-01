<?php
include 'connection.php';

$id = $_GET['id'];
$conn->query("DELETE FROM candidates WHERE id = $id");
header("Location: events_overview.php");
exit();
