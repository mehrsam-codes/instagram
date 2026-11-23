<?php

require "../includes/init.php";

session_start();

require "../includes/db/connection.php";

global $conn;

if (isset($_GET['id'])) {
    $post = Posts::getPostById($conn,$_GET['id']);

    if (!$post) {
        die('article not found');
    }
} else {
    die('id not supplied');
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        if (empty($_FILES)) {
            throw new Exception("File upload error");
        }

        switch ($_FILES["file"]["error"]) {
            case UPLOAD_ERR_OK:
                break;

            case UPLOAD_ERR_NO_FILE:
                throw new Exception("No file sent");
                break;

            case UPLOAD_ERR_INI_SIZE:
                throw new Exception("File is too large");
                break;

            default:
                throw new Exception("Unknown errors");
        }

        if ($_FILES["file"]["size"] > 500000) {
            throw new Exception("File is too large");
        }

        $allowed_mime_types = ['image/gif', 'image/png', 'image/jpeg'];

        $finfo = finfo_open(FILEINFO_MIME_TYPE);

        $mime_type = finfo_file($finfo, $_FILES["file"]["tmp_name"]);

        if (!in_array($mime_type, $allowed_mime_types, true)) {
            throw new Exception("Invalid file type");
        }

        finfo_close($finfo);
        $pathinfo = pathinfo($_FILES["file"]["name"]);

        $base = $pathinfo['basename'];

        $base = preg_replace('/[^A-Za-z0-9\-]/', '_', $base);

        $base = mb_substr($base, 0, 200);

        $filename = $base . "." . $pathinfo['extension'];

        $destination = "../uploads/$filename";

        $i = 1;

        while (file_exists($destination)) {
            $filename = $base . "-$i." . $pathinfo['extension'];

            $destination = "../uploads/$filename";

            $i++;
        }

        if (move_uploaded_file($_FILES["file"]["tmp_name"], $destination)) {
            $previous_image = $post['image'];

            if (Posts::setImageFile($conn, $filename, $post['id'])) {
                if ($previous_image) {
                    unlink("../uploads/$previous_image");
                }

                Url::redirect("uploads/edit-image.php?id=" . $post['id']);
            }
        } else {
            throw new Exception("Upload error");
        }

    } catch (Exception $e) {
        echo $e->getMessage();
    }
}

?>
<h2>Edit article image</h2>
<?php if ($post['image']) :  ?>
    <img src="../uploads/<?= $post['image'];?>">
    <a href="../uploads/delete-image.php?id=<?= $post['id']; ?>">delete</a>

<?php endif; ?>

<?php if (isset($error)) : ?>
    <p><?= $error ?></p>
<?php endif; ?>

<form method="post" enctype="multipart/form-data">
    <div>
        <label for="file"> image file </label>
        <input type="file" name="file" id="file">
    </div>
    <button>upload</button>
    <a href="../index.php">بازگشت</a>

</form>
