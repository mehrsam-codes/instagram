<?php
class Category
{
    public static function getAllCategories($conn)
    {
        $sql = "SELECT * FROM categories";
        $result = $conn->query($sql);
        return $result;
    }
    public static function getCategoryById($conn, $id)
    {
        $sql = "SELECT * FROM categories WHERE id = $id";
        $result = $conn->query($sql);
        return $result->fetch_assoc();
    }
}