<?php

session_start();

require "includes/db/connection.php";
require "includes/init.php";

global $conn;

$category_id = $_GET['id'] ?? 0;

$category = Category::getCategoryById($conn, $category_id);

if (!$category) {
    die("Category not found");
}

$result = Posts::getAllByCategory($conn, $category_id);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($category['name']) ?></title>
</head>
<body>

<h1><?= htmlspecialchars($category['name']) ?></h1>

<?php while ($row = $result->fetch_assoc()): ?>

    <div>
        <a href="view.php?id=<?= $row['id'] ?>">
            مشاهده پست
        </a>

        <h2><?= htmlspecialchars($row['title']) ?></h2>

        <p><?= htmlspecialchars($row['author']) ?></p>
    </div>

<?php endwhile; ?>

</body>
</html>