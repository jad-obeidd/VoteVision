<?php
require_once 'connection.php';

if (
    isset($_POST['email']) && !empty($_POST['email']) &&
    isset($_POST['password']) && !empty($_POST['password'])
) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare('SELECT id, name, email, password, role_id FROM user WHERE email = ? LIMIT 1');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row && password_verify($password, $row['password'])) {
        session_start();
        session_regenerate_id(true);
        $_SESSION['isloggedin'] = 1;
        $_SESSION['email'] = $row['email'];
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['role_id'] = $row['role_id'];
        $_SESSION['name'] = $row['name'];
        header('Location: home.php');
        exit();
    } else {
        header('Location: login1.php?error=invalid_credentials');
        exit();
    }
}
