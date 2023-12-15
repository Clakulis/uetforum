<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once("connection.php");
include 'comments.inc.php';

$postID = '';
$commentID ='';
$comment = '';
$userID = $_SESSION['userID'];

if (isset($_GET['commentId'])) {
    $commentID = $_GET['commentId'];
}

if (isset($_GET['postId'])) {
    $postID = $_GET['postId'];
}

$sqlPost = "SELECT * FROM posts WHERE postID = '$postID'";
$resultPost = $conn->query($sqlPost);
// Kiểm tra xem có bài viết nào hay không
if ($resultPost->num_rows > 0) {
    $post = $resultPost->fetch_assoc();//bài viết hiện tại
    $userIDPost = $post['userIDPost'];
    $sql1 = "select * from users WHERE userID = '$userIDPost'";
    $result1 = $conn->query($sql1);
    $row1 = $result1->fetch_assoc();//thông tin về user hiện tại
    $groupID = $post['groupIDPost'];
    $sql2 = "select * from groupss where groupID = '$groupID'";
    $result2 = $conn->query($sql2);
    $row2 = $result2->fetch_assoc();
}



if ($_SERVER['REQUEST_METHOD'] ==='POST') {
    
    $id = isset($_GET['id']) ? $_GET['id'] : '';
    if($id == '1') {
        $commentID = $_POST['commentID'];
        $comment = $_POST['comment'];
        $sql = "UPDATE comments SET comment = '$comment', dateOfComment = default WHERE commentID = '$commentID'";
        $result = $conn->query($sql);
    } else if($id == '2') {
        $postID = isset($_GET['postId']) ? $_GET['postId'] : '';
        setComments($conn, $postID);
    } else if($id == '3') {
        $commentID = $_POST['commentID'];
        deleteComments($conn, $userID, $postID, $commentID);
    } else if($id == '4') {
        $commentID = 'CMT'.str_pad(rand(0, 9999999), 7, '0', STR_PAD_LEFT);
        $comment = $_POST['comment'];
        $repCommentID = $_POST['repCommentID'];
        $userIDNotice = $_POST['userIDComment'];//user sẽ nhận tbao

        //cập nhật bảng posts
        $sql = "UPDATE posts SET numberComments = numberComments + 1 WHERE postID = '$postID'";
        $result = $conn->query($sql);

        //insert vào bảng cmt
        $stmt = $conn->prepare("INSERT INTO comments (commentID, userIDComment, postIDComment, repCommentID, comment) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $commentID, $userID, $postID, $repCommentID, $comment);
        $result = $stmt->execute();

        // ghi vào interact post
        $sql = "SELECT * FROM interactposts WHERE userIDInteract = '$userID' AND postIDInteract = '$postID'";
        $result = $conn->query($sql);
        if($result->num_rows == 0) {
            $stmt = $conn->prepare("INSERT INTO interactposts (userIDInteract, postIDInteract) VALUES (?, ?)");
            $stmt->bind_param("ss", $userID, $postID);
            $result = $stmt->execute();
        }
        $sql = "UPDATE interactposts SET isComment = isComment + 1 WHERE userIDInteract = '$userID' AND postIDInteract = '$postID'";
        $result = $conn->query($sql);

        //ghi vào bảng notice (reply)
        $noticeID = 'NO'.str_pad(rand(0, 99999999), 8, '0', STR_PAD_LEFT);
        $fullName = $row1['fullName'];
        $message = 'Người dùng: '.$fullName.' đã reply một comment của bạn.';
        if($userIDNotice != $userID) {
            $stmt = $conn->prepare("INSERT INTO notices (noticeID, userIDNotice, userIDDo, postIDNotice, commentIDNotice, message) VALUES (?, ?, ?, ?, ?, ?);");
            $stmt->bind_param("ssssss", $noticeID, $userIDNotice, $userID, $postID, $commentID, $message);
            $result = $stmt->execute();
        }
        //ghi vào bảng notice(cmt trong post)
        $noticeID = 'NO'.str_pad(rand(0, 99999999), 8, '0', STR_PAD_LEFT);
        $titlePost = $post['titlePost'];
        $userIDNotice = $userIDPost;
        $message = 'Người dùng: '.$fullName.' đã comment bài viết '.$titlePost.' của bạn.';
        if($userIDNotice != $userID) {
            $stmt = $conn->prepare("INSERT INTO notices (noticeID, userIDNotice, userIDDo, postIDNotice, commentIDNotice, message) VALUES (?, ?, ?, ?, ?, ?);");
            $stmt->bind_param("ssssss", $noticeID, $userIDNotice, $userID, $postID, $commentID, $message);
            $result = $stmt->execute();
        }
    }
    header("Location: indexCom.php?postId=$postID");
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>
<title>UET Forum</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="post.css">
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
  <link rel="stylesheet" href="https://www.w3schools.com/lib/w3-theme-blue.css">
  <link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Open+Sans'>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="icon" type="png" href="uploads/uet.png">
  <style>
    html,
    body,
    h1,
    h2,
    h3,
    h4,
    h5 {
      font-family: "Open Sans", sans-serif
    }
  </style>
</head>

<body class="w3-theme-l5">
        <!-- khung đăng bài -->
    <?php
        require_once("comments.inc.php");
        require_once("connection.php");
        upPostForum($conn, "forum.php?category=recently");
    ?>

    <!-- thanh menu -->
    <?php
        require_once("comments.inc.php");
        require_once("connection.php");
        displayMenu($conn, $userID);
    ?>
    <div class="w3-container w3-content" style="max-width:1400px;margin-top:80px;min-height:83.5vh" id="page-container">
        <div class="w3-row">
            <!-- Middle Column -->
            <div class="w3-col m13">
                    <?php
                    $imageHtml = $post['imagePost'] ? "<p style='text-align: center;'><img class='post-image' style='width: 900px; height: auto;object-fit:cover' src='{$post['imagePost']}' alt='Post Image'></p>" : '';
                    ?>
                    <a href="forum.php?category=<?php echo $row2['categoryGroup']?>&page=1">
                        <?php echo $row2['categoryGroup']?>
                    </a>
                    <div class='w3-container w3-card w3-white w3-round w3-margin'><br> 
                        <?php 
                        $sqlll = "SELECT * FROM interactposts WHERE userIDInteract = '$userID' AND postIDInteract = '$postID' and isFollowPost = 1";
                        $ans = $conn->query($sqlll);
                        echo "
                        <div>
                            <span class='w3-right' style='padding-top:10px;text-align:right'>";
                            if($userID !== $userIDPost)
                            {
                                echo "
                                <a href='followPost.php?postId=".$postID."&userId=".$userID."' class='post-actions' style='font-size: small; font-weight: 700;'>
                                    <button style='border: 2px solid;color:black; padding:5px 10px 5px 10px; background-color:  ";
                                    if($ans->num_rows > 0) {
                                        echo "#0c87eb";
                                    } else {
                                        echo "#ffffff";
                                    }
                                    echo "
                                    !important '><i class='fa fa-bell' ></i></button>
                                </a><br>"; 
                            }
                            echo "
                            <br>
                            <p style='text-align: right; font-size: small; font-weight: 600'><i>".$post['dateOfPost']."</i></p>
                            </span>
                        </div>"
                        ?>
                        <a href='profile.php?userId=<?php echo $post['userIDPost']  ?>' style='text-decoration:none'>
                            <img src='<?php echo $row1['linkAva'] ?>' class='w3-left w3-circle w3-margin-right' style='width:65px; height:65px; display:inline-block' alt='Avatar'>
                        </a>
                        <div style='text-align: left;'>     
                            <span class='user-name' style="width: 300px; font-size:20px"><b><?php echo $row1['fullName'] ?></b></span><br>
                        </div>
                        <h1 style="text-align: left; margin: 0px 50px 0px 50px"><?php echo $post['titlePost'];  ?></h1>
                        <hr>
                        <?php echo $imageHtml; ?>
                        <div class='description-container' style='height: auto; max-height: 400px; resize:none; overflow-y: auto;'>
                            <p><?php echo $post['descriptionPost']; ?></p>
                        </div>
                        <br>
                        <div class='like-container' id='likeContainer_{$postID}'>
                        <span class='reaction-count'><?php echo $post['numberReactions'] ?></span>
                        <div class='like-button' id='likeButton_{$postID}'>
                            <a href="processLike.php?userId=<?php echo $userID ?>&postId=<?php echo $postID ?>" class='like-button' style="text-decoration:none;">❤️</a>
                        </div>
                    </div>
                </div>
                    
                <?php
                    getComments($conn, $userID, $postID);
                ?>
                <div class="w3-row-padding" style="margin-bottom: 16px;">
                    <div class="w3-col m12">
                        <div class="w3-card w3-round w3-white">
                            <div class="w3-container w3-padding">
                                <form method='POST' action="indexCom.php?postId=<?php echo $postID; ?>&id=2">
                                    <h6 class="w3-opacity">Add comment</h6>
                                    <textarea class="w3-border w3-padding w3-input"  style='resize:vertical' name='comment' required></textarea><br>
                                    <button type="submit" class="w3-button w3-theme" name='commentSubmit'>Comment</button> 
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <footer class="w3-container w3-theme-d3 w3-padding-16">
        <h5 style="text-align: right;">UET FORUM</h5>
    </footer>

    <script src="script.js"></script>
    <script src="app.js"></script>
    <script>
        function goBackForum() {
            window.location.href = "forum.php?category=recently&page=1";
        }
    </script> 
</body>

</html>