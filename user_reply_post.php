<?php
    require_once("connection.php");
    session_start();
    $postID = '';
    if (isset($_GET['postID'])) {
        $postID = htmlspecialchars($_GET['postID']);
    }

    $userIDComment = $_SESSION['userID'];
    $isComment = intval(true);
    $stmt = $conn->prepare("INSERT INTO interactposts (userIDInteract, postIDInteract, isComment) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $userIDComment, $postID, $isComment);
    $result = $stmt->execute();
    //chèn dữ liệu vào bảng interactposts

    $updateNumberCmt = "UPDATE posts SET numberComments = numberComments + 1 WHERE postID = '$postID'";
    mysqli_query($conn, $updateNumberCmt) or die('Error!');

    $commentID = 'CMT'.str_pad(rand(0, 9999999), 7, '0', STR_PAD_LEFT);
    $comment = '';
    if (isset($_GET['comment'])){
        $comment = htmlspecialchars($_GET['comment']);
    }
    $stmt = $conn->prepare("INSERT INTO comments (commentID, userIDComment, postIDComment, comment) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $commentID, $userIDComment, $postID, $comment);
    $result = $stmt->execute();

    $noticeID = 'NO'.str_pad(rand(0, 99999999), 8, '0', STR_PAD_LEFT);
    
    $fullName = '';
    $fullNameQuery = "SELECT fullName FROM users WHERE userID = '$userIDComment'";
    $fullNameResult = $conn->query($fullNameQuery);
    if ($fullNameResult->num_rows > 0) {
        $row = $fullNameResult->fetch_assoc();
        $fullName = $row['fullName'];
    }

    $titlePost = '';
    $titlePostQuery = "SELECT titlePost FROM posts WHERE postID = '$postID'";
    $titlePostResult = $conn->query($titlePostQuery);
    if ($titlePostResult->num_rows > 0) {
        $row = $titlePostResult->fetch_assoc();
        $titlePost = $row['titlePost'];
    }

    $message = 'Người dùng: '. $fullName . ' đã comment bài viết ' . $titlePost . ' của bạn.';

    $userIDNotice ='';
    $userIDNoticeQuery = "
    SELECT userID FROM users u
    INNER JOIN posts p ON p.userIDPost = u.userID 
    WHERE postID = '$postID'";
    $userIDNoticeResult = $conn->query($userIDNoticeQuery);
    if ($userIDNoticeResult->num_rows > 0) {
        $row = $userIDNoticeResult->fetch_assoc();
        $userIDNotice = $row['userID'];
    }

    $stmt = $conn->prepare("INSERT INTO notices (noticeID, userIDNotice, message) VALUES (?, ?, ?);");
    $stmt->bind_param("sss", $noticeID, $userIDNotice, $message);
    $result = $stmt->execute();

    $conn->close();
?>