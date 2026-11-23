<?php
if(isset($_GET["id"])){
    include "includes/db/connection.php";
    global $conn;
    require "includes/init.php";
    Posts::deletePost($conn,$_GET["id"]);
    $conn->close();
}