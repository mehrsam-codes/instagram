
<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require "includes/db/connection.php";
require "includes/init.php";

global $conn;

// Get post ID
$postId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$postId || $postId < 1) {
    die("Invalid post ID");
}

// Get post
$row = Posts::getPostById($conn, $postId);

if (!$row) {
    die("Post not found");
}

// Check login status
$isLoggedIn = isset($_SESSION['is_logged_in'])
        && $_SESSION['is_logged_in'] === true
        && isset($_SESSION['user_id']);

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!$isLoggedIn) {
        die("Please login first");
    }

    $body = trim($_POST['body'] ?? '');

    if ($body === '') {
        die("Comment cannot be empty");
    }

    $comment = [
            'post_id' => $postId,
            'user_id' => $_SESSION['user_id'],
            'body' => $body
    ];

    if (Comments::createComment($conn, $comment)) {

        header("Location: view.php?id=" . $postId);
        exit;

    } else {

        die("Error saving comment");
    }
}

// Get comments
$comments = Comments::getCommentsByPostId($conn, $postId);

?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>مشاهده پست</title>

    <style>

        body {
            font-family: Tahoma, sans-serif;
            max-width: 750px;
            margin: 40px auto;
            padding: 20px;
            background: #f5f5f5;
        }

        .post,
        .comment,
        .comment-form {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 10px;
        }

        .post img {
            max-width: 100%;
            height: auto;
        }

        .comment {
            border-bottom: 1px solid #ddd;
        }

        .comment-username {
            font-weight: bold;
        }

        .comment-date {
            color: gray;
            font-size: 12px;
        }

        textarea {
            width: 100%;
            min-height: 100px;
            padding: 10px;
            box-sizing: border-box;
        }

        button {
            padding: 10px 20px;
            cursor: pointer;
        }

    </style>

</head>

<body>

<!-- POST -->

<section class="post">

    <h2>
        <?= htmlspecialchars($row['title']) ?>
    </h2>

    <p>
        <?= nl2br(htmlspecialchars($row['body'])) ?>
    </p>

    <p>
        نویسنده:
        <?= htmlspecialchars($row['author']) ?>
    </p>

    <?php if (!empty($row['image'])): ?>

        <img
                src="uploads/<?= htmlspecialchars($row['image']) ?>"
                alt="Post Image"
        >

    <?php endif; ?>

    <time>
        <?= htmlspecialchars($row['published_date']) ?>
    </time>

</section>

<hr>

<!-- COMMENTS -->

<section class="comments">

    <h2>نظرات کاربران</h2>

    <?php if ($comments->num_rows > 0): ?>

        <?php while ($comment = $comments->fetch_assoc()): ?>

            <article class="comment">

                <p class="comment-username">

                    <?= htmlspecialchars(
                            $comment['username'] ?? 'کاربر'
                    ) ?>

                </p>

                <p>
                    <?= nl2br(
                            htmlspecialchars($comment['body'])
                    ) ?>
                </p>

                <small class="comment-date">

                    <?= htmlspecialchars(
                            $comment['created_at']
                    ) ?>

                </small>

            </article>

        <?php endwhile; ?>

    <?php else: ?>

        <p>هنوز هیچ کامنتی ثبت نشده است.</p>

    <?php endif; ?>

</section>

<hr>

<!-- COMMENT FORM -->

<section class="comment-form">

    <h3>ارسال نظر جدید</h3>

    <?php if ($isLoggedIn): ?>

        <form
                action="view.php?id=<?= $postId ?>"
                method="POST"
        >

                <textarea
                        name="body"
                        placeholder="نظر خود را بنویسید..."
                        required
                ></textarea>

            <br><br>

            <button type="submit">
                ارسال کامنت
            </button>

        </form>

    <?php else: ?>

        <p>
            برای ارسال کامنت ابتدا وارد حساب کاربری شوید.
        </p>

        <a href="login.php">
            ورود به حساب کاربری
        </a>

    <?php endif; ?>

</section>

</body>

</html>
