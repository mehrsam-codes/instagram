<?php


class Like
{
    // Like or Unlike a post
    public static function toggle($conn, $userId, $postId)
    {
        // Check whether the user already liked this post
        $stmt = $conn->prepare(
            "SELECT id FROM likes
             WHERE user_id = ? AND post_id = ?"
        );

        $stmt->bind_param("ii", $userId, $postId);
        $stmt->execute();

        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows > 0) {

            // Remove like
            $stmt = $conn->prepare(
                "DELETE FROM likes
                 WHERE user_id = ? AND post_id = ?"
            );

            $stmt->bind_param("ii", $userId, $postId);
            $stmt->execute();
            $stmt->close();

            return false;
        }

        // Add like
        $stmt = $conn->prepare(
            "INSERT INTO likes (user_id, post_id)
             VALUES (?, ?)"
        );

        $stmt->bind_param("ii", $userId, $postId);
        $stmt->execute();
        $stmt->close();

        return true;
    }

    // Get total likes of a post
    public static function getCount($conn, $postId)
    {
        $stmt = $conn->prepare(
            "SELECT COUNT(*) AS total
             FROM likes
             WHERE post_id = ?"
        );

        $stmt->bind_param("i", $postId);
        $stmt->execute();

        $result = $stmt->get_result();
        $count = $result->fetch_assoc()['total'];

        $stmt->close();

        return (int)$count;
    }

    // Check whether a user liked a post
    public static function isLiked($conn, $userId, $postId)
    {
        $stmt = $conn->prepare(
            "SELECT id FROM likes
             WHERE user_id = ? AND post_id = ?"
        );

        $stmt->bind_param("ii", $userId, $postId);
        $stmt->execute();

        $isLiked = $stmt->get_result()->num_rows > 0;

        $stmt->close();

        return $isLiked;
    }
}
