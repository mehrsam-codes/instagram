<?php
class Auth
{
    public static function isLoggedIn()
    {
        return isset($_SESSION['is_logged_in'])
            && isset($_SESSION['user_id']);
    }
    public static function authAttempt($conn,$username, $password)
    {
        $sql = "SELECT * FROM users WHERE username = '$username'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if(password_verify($password, $user["password"])){
                return  $user;
            }
        }else {
            return false;
        }
        return false;
    }
    public static function login($user)
    {
        session_regenerate_id(true);

        $_SESSION['is_logged_in'] = true;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
    }
    public static function logout(){
        session_destroy();
    }
    public static function requireAuth(){
        if (!isset($_SESSION['is_logged_in'])){
            echo "you are not login to access this page";
        }
    }
    public static function signUp($conn, $first_name, $last_name, $username, $email, $password, $password_confirmation)
    {
        if (!empty($first_name) && !empty($last_name) && !empty($username) && !empty($email) && !empty($password)) {

            if ($password == $password_confirmation) {

                $password = password_hash($password, PASSWORD_DEFAULT);

                $sql = "INSERT INTO users (first_name, last_name, username, email, password)
                    VALUES ('$first_name', '$last_name', '$username', '$email', '$password')";

                try {
                    return $conn->query($sql);
                } catch (mysqli_sql_exception $e) {

                    if ($e->getCode() == 1062) {
                        return "duplicate";
                    }

                    return false;
                }
            }
        }

        return false;
    }
}