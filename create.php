
<?php

session_start();

require "includes/db/connection.php";
require "includes/init.php";

global $conn;

// Authentication
if (!Auth::isLoggedIn()) {
    header("Location: login.php");
    exit;
}

// Get categories
$categories = Category::getAllCategories($conn);

$error = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title = trim($_POST['title'] ?? '');
    $body = trim($_POST['body'] ?? '');
    $categoryId = filter_input(
        INPUT_POST,
        'category_id',
        FILTER_VALIDATE_INT
    );

    $userId = (int) $_SESSION['user_id'];
    $author = $_SESSION['username'];

    if (
        empty($title) ||
        empty($body) ||
        !$categoryId ||
        $categoryId <= 0
    ) {
        $error = "لطفاً تمام فیلدها را تکمیل کنید.";
    } else {

        // Check category existence
        $check = $conn->prepare(
            "SELECT id FROM categories WHERE id = ?"
        );

        $check->bind_param("i", $categoryId);
        $check->execute();

        $categoryExists = $check->get_result()->num_rows > 0;
        $check->close();

        if (!$categoryExists) {
            $error = "دسته‌بندی انتخاب‌شده معتبر نیست.";
        } else {

            $stmt = $conn->prepare(
                "INSERT INTO posts
                (title, author, body, published_date, category_id, user_id)
                VALUES (?, ?, ?, NOW(), ?, ?)"
            );

            $stmt->bind_param(
                "sssii",
                $title,
                $author,
                $body,
                $categoryId,
                $userId
            );

            if ($stmt->execute()) {
                $stmt->close();

                header("Location: index.php");
                exit;
            }

            $error = "خطا در ثبت پست.";
            $stmt->close();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>ایجاد پست جدید</title>
</head>

<body>

<div class="container">

    <h1>ایجاد پست جدید</h1>

    <p>پست جدید خودت را منتشر کن.</p>

    <?php if (!empty($error)): ?>
        <p style="color:red;">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php endif; ?>

    <form method="POST" action="">

        <div>
            <label for="title">عنوان پست:</label>

            <input
                type="text"
                name="title"
                id="title"
                required
                value="<?= htmlspecialchars($_POST['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
            >
        </div>

        <br>

        <div>
            <label for="body">متن پست:</label>

            <textarea
                name="body"
                id="body"
                rows="8"
                required
            ><?= htmlspecialchars($_POST['body'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>

        <br>

        <div>
            <label for="category_id">دسته‌بندی:</label>

            <select name="category_id" id="category_id" required>

                <option value="">انتخاب دسته‌بندی</option>

                <?php while ($category = $categories->fetch_assoc()): ?>

                    <option
                        value="<?= (int) $category['id'] ?>"
                        <?= (($_POST['category_id'] ?? '') == $category['id']) ? 'selected' : '' ?>
                    >
                        <?= htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8') ?>
                    </option>

                <?php endwhile; ?>

            </select>
        </div>

        <br>

        <button type="submit">
            انتشار پست
        </button>

        <a href="index.php">بازگشت</a>

    </form>

</div>

</body>
</html>
