<?php
session_start();
require_once("connection.php");
$userID = $_SESSION['userID'];
$userIDNow = '';
if(isset($_GET['userId'])) {
  $userIDNow = $_GET['userId'];
}
$sql = "SELECT * FROM users WHERE userID = '$userID'";
$result = $conn->query($sql);
$userInfo = $result->fetch_assoc();

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
  <!-- Khung đăng bài -->
  <?php
  require_once("comments.inc.php");
  require_once("connection.php");
  upPostForum($conn, "profile.php?userId=$userID");
  ?>

  <!-- hiển thị thanh menu -->
  <?php
  require_once("comments.inc.php");
  require_once("connection.php");
  displayMenu($conn, $userID);
  ?>

  <!-- Page Container -->
  <div class="w3-container w3-content" style="max-width:1400px;margin-top:80px;min-height:81.2vh">
    <div class="w3-row" style="height: 600px;">
      <!-- hiển thị thông tin user bên trái -->
      <div class="w3-col m3" style="display: flex;
                                    flex-direction: column;
                                    align-items: center;
                                    width: 250;
                                    margin-top: 20px;">
        <?php
        require_once("comments.inc.php");
        displayUserProfile($conn, $userID, $userIDNow);
        ?>

        <br>
      </div>

      <!-- hiển thị các bài viết mà user đã đăng -->
      <div class="w3-col m9">
        <?php
        require_once("comments.inc.php");
        require_once("connection.php");
        getPosts($conn, $userID, $userIDNow);
        ?>

      </div>
    </div>
  </div>
  <br>

  <!-- Footer -->
  <footer class="w3-container w3-theme-d3 w3-padding-16">
    <h5 style="text-align: right;">UET FORUM</h5>
  </footer>

  <script src="script.js"></script>

</body>
</html>