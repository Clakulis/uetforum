<?php
require_once("connection.php");
session_start();

$id = '';
if(isset($_GET['id'])) {
    $id = $_GET['id'];
}

if($id === "del") {
    $categoryGroup = '';
    if(isset($_GET['categoryGroup'])) {
        $categoryGroup = $_GET['categoryGroup'];
    }
    $sql = "DELETE FROM groupss WHERE categoryGroup = '$categoryGroup'";
    $result = $conn->query($sql);
    header('Location: admin_Groups.php');
} else {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $categoryGroup = $_POST["categoryGroup"];
        $categoryGroup = strtolower($categoryGroup);
        $categoryGroup = trim($categoryGroup);
    
        $stmt = $conn->prepare("SELECT categoryGroup FROM groupss WHERE categoryGroup = LOWER(TRIM(?))");
        $stmt->bind_param("s", $categoryGroup);
        $result = $stmt->execute();
    
        if ($result) {
            $stmt->store_result();
            $numRows = $stmt->num_rows;
    
            $stmt->close();
    
            $stmt2 = $conn->prepare("SELECT * FROM groupss");
            $result2 = $stmt2->execute();
            $stmt2->store_result();
            $num = $stmt2->num_rows + 1;
    
            if ($numRows === 0) {
                $stmt1 = $conn->prepare("INSERT INTO groupss (groupID, categoryGroup) VALUES(?, ?)");
                $stmt1->bind_param("ss", $num, $categoryGroup);
                $result1 = $stmt1->execute();
                $stmt1->close();
                $_SESSION['message'] = "Bạn thêm group mới thành công";
            } else {
                $_SESSION['message'] = "Group này đã tồn tại!";
            }
        } else {
            // Xử lý lỗi nếu có
            echo "Lỗi: " . $stmt->error;
        }
    
        $stmt2->close();
        $conn->close();
    
        echo "<script>";
        echo "alert('" . $_SESSION['message'] . "');";
        echo "window.location.href = 'admin_AddGroup.php';";
        echo "</script>";
        exit();
    }
}


?>