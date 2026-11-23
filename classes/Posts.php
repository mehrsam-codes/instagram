<?php

class Posts
{
    public static function getAll($conn)
    {
        $sql = "SELECT * FROM posts";
        $result = $conn->query($sql);
        return $result;
    }
    public static function getPostById($conn, $id)
    {
        $sql = "SELECT * FROM posts WHERE id = $id";
        $result = $conn->query($sql);
        return $result->fetch_assoc();
    }
    public static function updatePost($conn, $post)
    {
        $id = $post['id'];
        $title = $post['title'];
        $body = $post['body'];
        $author = $post['author'];

        $sql = "UPDATE posts
            SET title = ?, body = ?, author = ?
            WHERE id = ?";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param("sssi", $title, $body, $author, $id);

        if ($stmt->execute()) {
            echo "Record updated successfully";
        } else {
            echo "Error updating record: " . $stmt->error;
        }

        $stmt->close();
    }
    public static function deletePost($conn, $id){
        $sql = "DELETE FROM posts WHERE id = $id";
        if ($conn->query($sql) === TRUE) {
            echo "Record deleted successfully";
        }else{
            echo "Error deleting record: " . $conn->error;
        }

    }
    public static function addPost($conn, $post){
        $title = $post['title'];
        $body = $post['body'];
        $author = $post['author'];
        $category = $post['category'];
        $sql = "INSERT INTO posts (author, title, body, category_id) VALUES ('$author', '$title', '$body', '$category')";
        if ($conn->query($sql) === TRUE) {
            $last_id = $conn->insert_id;
            echo "New record created successfully".$last_id;
        }else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
    public static function getPage($conn, $limit, $offset)
    {
        $sql = "SELECT * FROM posts ORDER BY id  LIMIT  $limit OFFSET $offset";
        $result = $conn->query($sql);
        return $result;
    }
    public static function getTotalCount($conn)
    {
        $sql = "SELECT COUNT(*) as total FROM posts";
        $result = $conn->query($sql);
        return $result->fetch_assoc()["total"];
    }
    public static function setImageFile($conn, $filename , $id)
    {
        $sql = "UPDATE posts SET image = '$filename' WHERE id = '$id'";
        if ($conn->query($sql) === TRUE) {
            return true;
        }
        return false;
    }
    public static function getAllByCategory($conn, $category_id) {
        $sql = "SELECT * FROM posts where category_id = $category_id";
        $result = $conn->query($sql);
        return$result;
    }

}