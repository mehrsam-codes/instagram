<?php
session_start();
require "includes/db/connection.php";
require "includes/init.php";
global $conn;
$result = Posts::getAll($conn);
$page = $_GET['page'] ?? 1;
$total_records = Posts::getTotalCount($conn);

$paginator = new Paginator($page, 6, $total_records);

$result = Posts::getPage(
        $conn,
        $paginator->limit,
        $paginator->offset
);
$categories = Category::getAllCategories($conn);
?>

<!DOCTYPE html>
<html dir="rtl">
<head>
    <?php include "includes/head.php";?>
</head>
<body>
<?php include "includes/header.php";?>

<div class="container">


    <nav>
        <h3>دسته‌بندی پست ها</h3>

        <ul>
            <?php while ($category = $categories->fetch_assoc()): ?>
                <li>
                    <a href="category.php?id=<?= $category['id'] ?>">
                        <?= htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8') ?>
                    </a>
                </li>
            <?php endwhile; ?>
        </ul>
    </nav>
    <a href="create.php">ساخت پست جدید</a>
    <?php while ($row = $result->fetch_assoc()): ?>
        <div>
            <img src="uploads/<?= htmlspecialchars($row['image']) ?>" alt="Post Image">
            <a href="view.php?id=<?= $row['id'] ?>">مشاهده پست</a>
            <h2><?= $row['title'] ?></h2>
            <p><?= $row['author'] ?></p>
            <time><?= $row['published_date'] ?></time>
            <br>
            <a href="edit.php?id=<?= $row['id'] ?>">ویرایش</a>
            <a href="delete.php?id=<?= $row['id'] ?>">حذف</a>
            <a
                    class="action edit-btn"
                    href="uploads/edit-image.php?id=<?= (int)$row["id"] ?>"
            >
               ویرایش تصویر پست
            </a>

            <?php

            $postId = (int) $row['id'];

            $likesCount = Like::getCount($conn, $postId);

            $isLiked = false;

            if (Auth::isLoggedIn()) {
                $isLiked = Like::isLiked(
                        $conn,
                        $_SESSION['user_id'],
                        $postId
                );
            }

            ?>

            <form action="like.php" method="POST">

                <input
                        type="hidden"
                        name="post_id"
                        value="<?= $postId ?>"
                >

                <button
                        type="submit"
                        <?= !Auth::isLoggedIn() ? 'disabled' : '' ?>
                >
                    <?= $isLiked ? '❤️' : '🤍' ?>

                    <?= $likesCount ?>
                </button>

            </form>


        </div>
    <?php endwhile; ?>
    <?php
    if (!$paginator->previous){
        echo "Previous";
    }else {
        echo '<a href="?page=' . $paginator->previous . '">Previous</a>';
    }

    if (!$paginator->next){
        echo "next";
    }else {
        echo '<a href="?page=' . $paginator->next . '">Next</a>';
    }

    ?>
</div>

    <?php include "includes/script.php"; ?>
</body>
</html>
