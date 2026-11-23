<?php
require "../includes/init.php";

session_start();

Auth::requireAuth();

require "../includes/db/connection.php";
global $conn;

if (isset($_GET['id'])){
    $post = Posts::getPostById($conn , $_GET['id']);

    if (!$post){
        die("Article not found");
    }
}else {
    die(" id not found");
}
if ($_SERVER["REQUEST_METHOD"] == "POST"){

    $previous_image = $post['image'];
    if (Posts::setImageFile($conn,null , $post['id'])){
        unlink("../uploads/$previous_image");
        Url::redirect("uploads/edit-image.php?id=$post[id]");
    }
}
?>

<h2>Delete article image</h2>

<?php if ($post['image']) : ?>

    <img src="../uploads/<?= $post['image']; ?>">
<?php endif; ?>

<form method="post">
    <h2>Are you sure?</h2>
    <button>Delete</button>
    <a href="../uploads/edit-image.php?id=<?= $post['id']; ?>">Cancel</a>
    <a href="../index.php">بازگشت</a>

</form>

