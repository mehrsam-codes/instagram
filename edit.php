<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
</head>
<body>
<?php
require "includes/db/connection.php";
require "includes/init.php";
global $conn;
if($_SERVER['REQUEST_METHOD'] == "POST"){
    $post = ["id" => $_POST["id"], "title" => $_POST["title"], "author" => $_POST["author"], "body" => $_POST["body"]];
    Posts::updatePost($conn,$post);
    die();

}

if($_SERVER['REQUEST_METHOD'] == "GET"){
    $row = Posts::getPostById($conn,$_GET["id"]);
}

?>
<form action="edit.php" method="post">
    Title : <input type="text" name="title" value="<?php echo $row['title'];?>"/><br>
    Body : <textarea type="text" name="body" value="<?php echo $row['body'];?>"></textarea><br>
    Author : <input type="text" name="author" value="<?php echo $row['author'];?>"/><br>
    <button type="submit">Update Articles</button>
    <input type="hidden" value="<?php echo $row['id'];?>" name="id">
</form>
</body>
</html>