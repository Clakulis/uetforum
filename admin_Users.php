<!DOCTYPE html>
<html>

<head>
  <title>UET Forum</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="styles.css">
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
  <div class="w3-top">
    <?php
      require_once("comments.inc.php");
      require_once("connection.php");
      displayMenuAdmin($conn, $userID);
    ?>
    </div>
    <!-- khung đăng bài -->
    <div class="w3-container w3-content" style="width: 75%; max-width:1400px;margin:120px auto;" id="page-container">
    <h1 >User</h1>
      <div class="w3-row" style="height: 600px;">
        <div class="w3-col m3">
          <div class="w3-card w3-round" style="max-width: 0%; overflow:auto; max-height: 500px;">
            <div class="w3-white">

            <!-- <img style="max-width: 100%;" src="uploads/27.jpg"> -->
            </div>
          </div>
        </div>
        <div class="w3-col m9" style="width: 100%;padding: 50px auto">
            <div style="width: 100%;">

            <?php
              require_once("comments.inc.php");
              require_once("connection.php");
              checkUser($conn);
            ?>
            </div>
        </div>
      </div>
    </div>
    <br>


    <footer class="w3-container w3-theme-d3 w3-padding-16">
      <h5 style="text-align: right;">UET FORUM</h5>
    </footer>
    <script src="script.js"></script>
    <script src="app.js"></script>
  </div>
</body>

</html>