<?php

$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASSWORD') ?: '';
$database = getenv('DB_NAME') ?: 'election';

$conn = new mysqli($host, $user, $pass, $database);
if($conn->connect_error){
    die('Unable to connect to the database.');
}

$conn->set_charset('utf8mb4');
