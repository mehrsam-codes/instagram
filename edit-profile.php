
<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require "includes/init.php";
require "includes/db/connection.php";

global $conn;

if (
    !isset($_SESSION['is_logged_in']) ||
    !isset($_SESSION['user_id'])
) {
    header("Location: login.php");
    exit;
}

$userId = (int) $_SESSION['user_id'];

$error = "";
$success = "";

// دریافت اطلاعات کاربر
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

// پردازش فرم
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $bio = trim($_POST['bio'] ?? '');

    // حفظ عکس قبلی
    $profileImage = $user['profile_image'] ?? null;
    $newImagePath = null;

    try {

        // بررسی ارسال فایل
        if (
            isset($_FILES['file']) &&
            $_FILES['file']['error'] !== UPLOAD_ERR_NO_FILE
        ) {

            switch ($_FILES['file']['error']) {

                case UPLOAD_ERR_OK:
                    break;

                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    throw new Exception("File is too large");

                default:
                    throw new Exception("File upload error");
            }

            // بررسی حجم
            if ($_FILES['file']['size'] > 500000) {
                throw new Exception("File is too large");
            }

            // بررسی نوع واقعی فایل
            $allowedMimeTypes = [
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
                'image/gif'  => 'gif'
            ];

            $finfo = finfo_open(FILEINFO_MIME_TYPE);

            $mimeType = finfo_file(
                $finfo,
                $_FILES['file']['tmp_name']
            );

            finfo_close($finfo);

            if (!array_key_exists($mimeType, $allowedMimeTypes)) {
                throw new Exception("Invalid file type");
            }

            // ساخت نام یکتا
            $extension = $allowedMimeTypes[$mimeType];

            $filename = bin2hex(random_bytes(16)) . "." . $extension;

            $destination = __DIR__ . "/uploads/" . $filename;

            // انتقال فایل
            if (!move_uploaded_file(
                $_FILES['file']['tmp_name'],
                $destination
            )) {
                throw new Exception("Upload error");
            }

            $newImagePath = $destination;
            $profileImage = $filename;
        }

        // ذخیره اطلاعات در دیتابیس
        $sql = "UPDATE users
                SET first_name = ?,
                    last_name = ?,
                    bio = ?,
                    profile_image = ?
                WHERE id = ?";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "ssssi",
            $firstName,
            $lastName,
            $bio,
            $profileImage,
            $userId
        );

        if (!$stmt->execute()) {
            throw new Exception("Database update failed");
        }

        $stmt->close();

        // حذف عکس قبلی بعد از ذخیره موفق عکس جدید
        if (
            $newImagePath !== null &&
            !empty($user['profile_image'])
        ) {
            $oldImage = __DIR__ . "/uploads/" . basename($user['profile_image']);

            if (is_file($oldImage)) {
                unlink($oldImage);
            }
        }

        header("Location: profile.php");
        exit;

    } catch (Exception $e) {

        // حذف عکس جدید در صورت شکست ذخیره
        if (
            $newImagePath !== null &&
            is_file($newImagePath)
        ) {
            unlink($newImagePath);
        }

        $error = $e->getMessage();
    }
}

?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>ویرایش پروفایل</title>

    <style>
        body {
            font-family: Tahoma;
            max-width: 600px;
            margin: 40px auto;
            padding: 20px;
        }

        input,
        textarea {
            display: block;
            width: 100%;
            padding: 10px;
            margin: 10px 0 20px;
            box-sizing: border-box;
        }

        button {
            padding: 10px 20px;
            cursor: pointer;
        }

        .avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
        }

        .error {
            color: red;
        }
    </style>
</head>

<body>

<h2>ویرایش پروفایل</h2>

<?php if ($error): ?>
    <p class="error">
        <?= htmlspecialchars($error) ?>
    </p>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">

    <div>
        <label>نام</label>

        <input
            type="text"
            name="first_name"
            value="<?= htmlspecialchars($user['first_name'] ?? '') ?>"
        >
    </div>

    <div>
        <label>نام خانوادگی</label>

        <input
            type="text"
            name="last_name"
            value="<?= htmlspecialchars($user['last_name'] ?? '') ?>"
        >
    </div>

    <div>
        <label>بیوگرافی</label>

        <textarea name="bio" rows="4"><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
    </div>

    <div>
        <label>عکس فعلی پروفایل</label>

        <?php if (!empty($user['profile_image'])): ?>

            <img
                class="avatar"
                src="uploads/<?= htmlspecialchars($user['profile_image']) ?>"
                alt="Profile"
            >

        <?php else: ?>

            <p>هنوز عکس پروفایلی انتخاب نکرده‌ای.</p>

        <?php endif; ?>
    </div>

    <div>
        <label for="file">انتخاب عکس جدید</label>

        <input
            type="file"
            name="file"
            id="file"
            accept="image/jpeg,image/png,image/gif"
        >

        <small>حداکثر حجم: 500 کیلوبایت</small>
    </div>

    <button type="submit">ذخیره تغییرات</button>

    <a href="profile.php">بازگشت</a>

</form>

</body>
</html>
