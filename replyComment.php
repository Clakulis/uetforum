<?php
session_start();
require_once("connection.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $repCommentID = $_POST['repCommentID'];
    $userIDComment = $_POST['userIDComment'];
    $postIDComment = $_POST['postIDComment'];
}
//echo $repCommentID. ' ' . $userIDComment. ' '.$postIDComment;
$commentID = 'CMT'.str_pad(rand(0, 9999999), 7, '0', STR_PAD_LEFT);

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Title of the document</title>
    <link rel="stylesheet" type="text/css" href="comment.css">
    <link rel="stylesheet" type="text/css" href="post.css">
    <link rel="icon" type="png" href="uploads/uet.png">
</head>

<body style="background-color: #C6E6F2">
    <h1 style="text-align: center; color: darkmagenta">REP COMMENT</h1>
    <div id="comments-section">
        <form method='POST' action='indexCom.php?postId=<?php echo $postIDComment; ?>&id=4'>
            <input type='hidden' name='repCommentID' value='<?php echo $repCommentID; ?>'>
            <input type='hidden' name='userIDComment' value='<?php echo $userIDComment; ?>'>
            <input type='hidden' name='commentID' value='<?php echo $commentID; ?>'>
            <textarea name='comment' required></textarea><br>
            <button type='submit' name='replyComment'>Reply</button>
        </form>
        <button type='submit' onclick="goBackPost()"> BACK</button>
        <hr>
    </div>

    <script>
        function goBackPost() {
            window.history.back();
        }
    </script>
</body>

</html>