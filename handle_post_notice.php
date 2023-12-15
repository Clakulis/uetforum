<?php
require_once("connection.php");
$postID = isset($_GET['postId']) ? $_GET['postId'] : '';
$noticeID = isset($_GET['noticeId']) ? $_GET['noticeId'] : '';
$sql = "UPDATE notices SET statusReadNotice = 1 WHERE noticeID = '$noticeID'";
$result=$conn->query($sql);
header("Location: indexCom.php?postId=$postID");
?>