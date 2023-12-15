<?php
require_once("connection.php");
$userID = '';
$postID = '';
if (isset($_GET['postId']) && isset($_GET['userId'])) {
    $postID = $_GET['postId'];
    $userID = $_GET['userId'];
}
$sql = "SELECT * FROM interactposts WHERE userIDInteract = '$userID' AND postIDInteract = '$postID'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
if($result->num_rows == 0) {
    $stmt = $conn->prepare("INSERT INTO interactposts (userIDInteract, postIDInteract) VALUES (?, ?)");
    $stmt->bind_param("ss", $userID, $postID);
    $result = $stmt->execute();
} 
if($row['isLike'] == 0) {
    //cập nhận hoạt động like và số cmt
    $sql = "UPDATE interactposts SET isLike = 1 - isLike WHERE userIDInteract = '$userID' AND postIDInteract = '$postID'";
    $result = $conn->query($sql);
    $sql = "UPDATE posts SET numberReactions = numberReactions + 1 WHERE postID = '$postID'";
    $result = $conn->query($sql);

    //gửi thông báo
    $noticeID = 'NO' . str_pad(rand(0, 99999999), 8, '0', STR_PAD_LEFT);
    $fullName = '';
    $fullNameQuery = "SELECT fullName FROM users WHERE userID = '$userID'";
    $fullNameResult = $conn->query($fullNameQuery);
    if ($row = $fullNameResult->fetch_assoc()) {
        $fullName = $row['fullName'];
    }

    $titlePost = '';
    $titlePostQuery = "SELECT titlePost FROM posts WHERE postID = '$postID'";
    $titlePostResult = $conn->query($titlePostQuery);
    if ($row = $titlePostResult->fetch_assoc()) {
        $titlePost = $row['titlePost'];
    }

    $message = 'Người dùng: ' . $fullName . ' đã thích bài viết ' . $titlePost . ' của bạn.';

    $userIDNotice = '';
    $userIDNoticeQuery = "
        SELECT userID FROM users u
        INNER JOIN posts p ON p.userIDPost = u.userID 
        WHERE postID = '$postID'";

    $userIDNoticeResult = $conn->query($userIDNoticeQuery);
    if ($row = $userIDNoticeResult->fetch_assoc()) {
        $userIDNotice = $row['userID'];
    }

    if ($userIDNotice != $userID) {
        $like = 1;
        $stmt = $conn->prepare("INSERT INTO notices (noticeID, userIDNotice, userIDDO, postIDNotice, message, likeNotice) VALUES (?, ?, ?, ?, ?, ?);");
        $stmt->bind_param("sssssi", $noticeID, $userIDNotice, $userID, $postID, $message, $like);
        $result = $stmt->execute();
    }
} else {
    // xóa like cần cập nhật cả trong interactposts, posts và notices
    $sql = "UPDATE interactposts SET isLike = 1 - isLike WHERE userIDInteract = '$userID' AND postIDInteract = '$postID'";
    $result = $conn->query($sql);
    $sql = "UPDATE posts SET numberReactions = greatest(numberReactions - 1, 0) WHERE postID = '$postID'";
    $result = $conn->query($sql);

    $sql = "DELETE FROM notices WHERE userIDDo = ? AND postIDNotice = ? AND likeNotice = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $userID, $postID);
    $stmt->execute();
}
header("Location: indexCom.php?postId=$postID");
exit();
?>