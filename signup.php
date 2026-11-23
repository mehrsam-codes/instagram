<?php
include "includes/db/connection.php";
include "includes/init.php";

global $conn;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST["first_name"];
    $last_name = $_POST["last_name"];
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $password_confirmation = $_POST["password_confirmation"];
    $signUp = Auth::signUp($conn, $first_name, $last_name, $username, $email, $password, $password_confirmation);
    if ($signUp === true) {
        echo "<script>alert('Sign Up Successful');</script>";
        Url::redirect("index.php");

    } elseif ($signUp === "duplicate") {
        echo "<script>alert('Username or Email already exists');</script>. <a href='login.php'>login</a>";


    } else {
        echo "<script>alert('Sign Up Failed');</script>";
        echo "<a href='signup.php'>Try again</a>";
    }
    die();

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
</head>
<body>

<h2>Sign Up</h2>

<form method="POST">

    <div>
        <label for="first_name">First Name</label>
        <input type="text" name="first_name" id="first_name" required>
    </div>

    <div>
        <label for="last_name">Last Name</label>
        <input type="text" name="last_name" id="last_name" required>
    </div>

    <div>
        <label for="username">Username</label>
        <input type="text" name="username" id="username" required>
    </div>

    <div>
        <label for="email">Email</label>
        <input type="email" name="email" id="email" required>
    </div>

    <div>
        <label for="password">Password</label>
        <input type="password" name="password" id="password" required>
    </div>

    <div>
        <label for="password_confirmation">Confirm Password</label>
        <input type="password" name="password_confirmation" id="password_confirmation" required>
    </div>

    <button type="submit">Sign Up</button>

</form>

</body>
</html>
