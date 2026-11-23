<?php
class Url
{
    public static function redirect($path)
    {
        $protocol = (
            !empty($_SERVER["HTTPS"]) &&
            $_SERVER["HTTPS"] !== "off"
        ) ? "https" : "http";

        $host = $_SERVER["HTTP_HOST"];

        $url = $protocol . "://" . $host . "/instagram/" . $path;

        header("Location: " . $url);
        exit;
    }
}