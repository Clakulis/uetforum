<?php
require_once("connection.php");
$userID = isset($_GET['userId']) ? $_GET['userId'] : '';
$noticeID = isset($_GET['noticeId']) ? $_GET['noticeId'] : '';
$sql = "UPDATE notices SET statusReadNotice = 1 WHERE noticeID = '$noticeID'";
$result=$conn->query($sql);
header("Location: profile.php?userId=$userID");
?>