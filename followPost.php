<?php
require_once("connection.php");
$userID = '';
$postID = '';

if(isset($_GET['postId']) && isset($_GET['userId'])) {
    $postID = $_GET['postId'];
    $userID = $_GET['userId'];
    $sqlll = "SELECT * from posts where postID = '$postID'";
    $ans = $conn->query($sqlll);
    $row = $ans->fetch_assoc();
    $userIDNotice = $row['userIDPost'];

    $sql = "SELECT * FROM interactposts WHERE userIDInteract = '$userID' AND postIDInteract = '$postID'";
    $result = $conn->query($sql);
    if ($result->num_rows === 0) {
        $stmt = $conn->prepare("INSERT INTO interactposts (userIDInteract, postIDInteract) VALUES (?, ?);");
        $stmt->bind_param("ss", $userID, $postID);
        $stmt->execute();
    }
    $sql2 = "UPDATE interactposts SET isFollowPost = 1 - isFollowPost WHERE userIDInteract = '$userID' AND postIDInteract = '$postID'";
    $result2 = $conn->query($sql2);

    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    if ($row['isFollowPost'] == 0) {
        //delete notices
        $sql = "DELETE FROM notices WHERE userIDNotice = '$userIDNotice' AND userIDDo = '$userID' AND followNotice = 0 AND commentIDNotice is null";
        $result = $conn->query($sql);
    } else {
        //insert notices
        $fullName = '';
        $fullNameQuery = "SELECT * FROM users WHERE userID = '$userID'";
        $fullNameResult = $conn->query($fullNameQuery);
        if ($row = $fullNameResult->fetch_assoc()) {
            $fullName = $row['fullName'];
        }

        $message = 'Người dùng: ' . $fullName . ' đã theo dõi một bài viết của bạn!';
        $noticeID = 'NO' . str_pad(rand(0, 99999999), 8, '0', STR_PAD_LEFT);

        if ($userIDNotice != $userID) {
            $stmt = $conn->prepare("INSERT INTO notices (noticeID, userIDNotice, userIDDO, postIDNotice, message) VALUES (?, ?, ?, ?, ?);");
            $stmt->bind_param("sssss", $noticeID, $userIDNotice, $userID, $postID, $message);
            $result = $stmt->execute();
        }
    }
}


echo '
    <script>
        window.history.back();
    </script>
'; 
?>