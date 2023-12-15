<?php
    session_start();
    $userIDNotice = $_SESSION['userID'];
    require_once("connection.php");

// Query to get posts from the database
echo $userIDNotice;


// Fetch posts as an associative array
?>
