
<?php

class Comments
{
    // Create a new comment
    public static function createComment($conn, $comment)
    {
        $sql = "INSERT INTO comments (post_id, user_id, body)
                VALUES (?, ?, ?)";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "iis",
            $comment['post_id'],
            $comment['user_id'],
            $comment['body']
        );

        $result = $stmt->execute();

        $stmt->close();

        return $result;
    }

    // Get all comments of a post with username
    public static function getCommentsByPostId($conn, $postId)
    {
        $sql = "SELECT
                    comments.id,
                    comments.post_id,
                    comments.user_id,
                    comments.body,
                    comments.created_at,
                    users.username
                FROM comments
                LEFT JOIN users
                    ON comments.user_id = users.id
                WHERE comments.post_id = ?
                ORDER BY comments.created_at DESC";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param("i", $postId);

        $stmt->execute();

        $result = $stmt->get_result();

        $stmt->close();

        return $result;
    }
}
