<?php
session_start();
require_once("connection.php");
if ($_SERVER['REQUEST_METHOD'] ==='POST') {
    $search = $_POST['search'];
    $_SESSION['search'] = $search;
    //echo $_SESSION['search'];


    header("Location: forum.php?category=search&page=1");
}    
?>