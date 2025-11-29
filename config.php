<?php
session_start();

$db_host = 'localhost';
$db_user = 'root';
$db_pass = 'toor';
$db_name = 'finance_tracker';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8");

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function redirect($url) {
    header("Location: $url");
    exit;
}

function get_user_id() {
    return $_SESSION['user_id'] ?? null;
}
?>