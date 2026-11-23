<?php


session_start();

require "includes/db/connection.php";
require "includes/init.php";
global $conn;

// Check authentication
if (!Auth::isLoggedIn()) {
    header("Location: login.php");
    exit;
}

// Only POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

// Get user ID from session
$userId = (int)$_SESSION['user_id'];

// Get post ID
$postId = filter_input(
    INPUT_POST,
    'post_id',
    FILTER_VALIDATE_INT
);

if (!$postId || $postId <= 0) {
    header("Location: index.php");
    exit;
}

// Toggle like
Like::toggle($conn, $userId, $postId);

// Return to homepage
header("Location: index.php");
exit;
