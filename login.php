<?php
session_start();

require "includes/db/connection.php";
require "includes/init.php";

global $conn;

$isLoggedIn = Auth::isLoggedIn();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $user = Auth::authAttempt($conn, $username, $password);
    if ($user) {
        Auth::login($user);
        echo "user login successful";
        Url::redirect("index.php");
    }else {
        echo "user login unsuccessful";
        echo " <a href='login.php'>try again </a>";
    }
    die();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>

<h2>Login</h2>

<form method="POST">

    <div>
        <label for="username">Username</label>
        <input type="text" name="username" id="username" required>
    </div>

    <div>
        <label for="password">Password</label>
        <input type="password" name="password" id="password" required>
    </div>

    <button type="submit">Login</button>

</form>

</body>
</html>
