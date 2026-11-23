<?php
session_start();

require "includes/init.php";
Auth::logout();
echo "user logged out";
Url::redirect("index.php");