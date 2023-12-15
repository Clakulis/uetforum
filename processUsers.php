<?php
require_once("connection.php");
$id = '';
$userID = '';
if(isset($_GET['id']) && isset($_GET['userId'])) {
    $id = $_GET['id'];
    $userID = $_GET['userId'];
}
if($id === "del") {
    $sql = "DELETE FROM users WHERE userID = '$userID'";
    $result = $conn->query($sql);
} else {
    $sql = "UPDATE users SET isAccept = 1 WHERE userID = '$userID'";
    $result = $conn->query($sql);
}
echo "
    <script>
        window.history.back();
    </script>
";
?>