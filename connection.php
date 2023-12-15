<?php
// Thông tin kết nối đến cơ sở dữ liệu MySQL
$servername = "localhost";
$db_username = "admin";
$db_password = "123456";
$dbname = "csdl1";

// Tạo kết nối đến MySQL
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối đến cơ sở dữ liệu thất bại: " . $conn->connect_error);
}
?>