<?php
require_once("connection.php");
$id = '';
$postID = '';
if(isset($_GET['id']) && isset($_GET['postId'])) {
    $id = $_GET['id'];
    $postID = $_GET['postId'];
}
if($id === "del") {
    $sql = "DELETE FROM posts WHERE postID = '$postID'";
    $result = $conn->query($sql);
} else {
    $sql = "UPDATE posts SET isAccepted = 1 WHERE postID = '$postID'";
    $result = $conn->query($sql);
}
echo "
    <script>
        window.history.back();
    </script>
";
?>