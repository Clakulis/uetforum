<?php
require_once("connection.php");
session_start();
$uerIDpersonal = '';
if (isset($_GET['userId'])) {
    $uerIDpersonal = $_GET['userId'];
}
$isLogout = 1;
$stmt = $conn->prepare("INSERT INTO personalusers (userIDpersonal, isLogout) VALUES (?, ?)");
$stmt->bind_param("si", $uerIDpersonal, $isLogout);
$result3 = $stmt->execute();
session_destroy();
header("Location: index.php");
?>