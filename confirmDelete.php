<?php
require_once("connection.php");
$postID = '';
$userID = '';
if(isset($_GET['postId']) && isset($_GET['userId'])) {
    $postID = $_GET['postId'];
    $userID = $_GET['userId'];

    $sql = "DELETE FROM posts WHERE postID = '$postID'";
    $result = $conn->query($sql);

    $sql = "DELETE FROM notices WHERE postIDNotice = '$postID'";
    $result = $conn->query($sql);

    $sql = "DELETE FROM comments WHERE postIDComment = '$postID'";
    $result = $conn->query($sql);

    $sql = "DELETE FROM interactposts WHERE postIDInteract = '$postID'";
    $result = $conn->query($sql);
    header("Location: profile.php?userId=$userID");
}
?>