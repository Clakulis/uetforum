<?php
require_once("connection.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $commentID = $_POST['commentID'];
    $comment = $_POST['comment'];
    $postID = $_POST['postIDComment'];
    //editComments($conn, $commentID, $comment);
    //header("Location: indexCom.php?postId=$postID");
    // exit();
}

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
    <h1 style="text-align: center; color: darkmagenta">EDIT COMMENT</h1>
    <div id="comments-section">
        <form method='POST' action='indexCom.php?postId=<?php echo $postID ?>&id=1'>
            <input type='hidden' name='commentID' value='<?php echo $commentID; ?>'>
            <textarea name='comment' required><?php echo $comment ?></textarea><br>
            <button type='submit' name='editComment'>Update Comment</button>
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