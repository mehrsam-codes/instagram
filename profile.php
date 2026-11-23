
<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require "includes/db/connection.php";
require "includes/init.php";

global $conn;

if (
    !isset($_SESSION['is_logged_in']) ||
    !isset($_SESSION['user_id'])
) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];

// Get user information
$sql = "SELECT * FROM users WHERE id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

$stmt->close();

if (!$user) {
    die("User not found");
}

// Get user's posts
$sql = "SELECT * FROM posts WHERE user_id = ? ORDER BY published_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();

$posts = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>پروفایل <?= htmlspecialchars($user['username']) ?></title>

    <style>
        body {
            font-family: Tahoma;
            max-width: 900px;
            margin: 40px auto;
            padding: 20px;
        }

        .profile {
            display: flex;
            align-items: center;
            gap: 30px;
            margin-bottom: 40px;
        }

        .avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
        }

        .posts {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }

        .post img {
            width: 100%;
            aspect-ratio: 1;
            object-fit: cover;
        }
    </style>
</head>

<body>

<section class="profile">

    <img
        class="avatar"
        src="<?= !empty($user['profile_image'])
            ? 'uploads/' . htmlspecialchars($user['profile_image'])
            : 'uploads/default-avatar.png' ?>"
        alt="Profile"
    >

    <div>

        <h2>
            <?= htmlspecialchars($user['username']) ?>
        </h2>

        <p>
            <?= htmlspecialchars($user['first_name']) ?>
            <?= htmlspecialchars($user['last_name']) ?>
        </p>

        <p>
            تعداد پست‌ها: <?= $posts->num_rows ?>
        </p>

        <p>
            <?= nl2br(htmlspecialchars($user['bio'] ?? '')) ?>
        </p>

        <a href="edit-profile.php">
            ویرایش پروفایل
        </a>

    </div>

</section>

<hr>

<h2>پست‌های من</h2>

<section class="posts">

    <?php while ($post = $posts->fetch_assoc()): ?>

        <div class="post">

            <a href="view.php?id=<?= (int)$post['id'] ?>">

                <?php if (!empty($post['image'])): ?>

                    <img
                        src="uploads/<?= htmlspecialchars($post['image']) ?>"
                        alt="Post"
                    >

                <?php else: ?>

                    <p>بدون تصویر</p>

                <?php endif; ?>

            </a>

        </div>

    <?php endwhile; ?>

</section>

</body>
</html>
