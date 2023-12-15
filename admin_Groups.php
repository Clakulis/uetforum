<!DOCTYPE html>
<html lang="en">
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
    <!-- <link rel="stylesheet" href="style.css"> -->
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
<body>
    <div class="w3-top">
        <?php
        require_once("comments.inc.php");
        require_once("connection.php");
        displayMenuAdmin($conn, $userID);
        ?>
    </div>
    <!-- khung đăng bài -->
    <div class="w3-container w3-content" style="width: 75%; max-width:1400px;margin:120px auto;min-height:64.2vh" id="page-container">
        <div class='reaction-comment-container'>
            <div class='like-container'>
                <h1>Group</h1>
            </div>
        
            <div>  
                <a href="admin_AddGroup.php" class='post-actions' style='font-size: small; font-weight: 700; display: inline-block;'>
                    <button style='border: 2px solid; border-radius: 50%; background-color: green '><i class='fa fa-plus' ></i></button>
                </a>
            </div>
        </div>    
    
        <div class="w3-row">
            <div class="w3-col m3">
                <div class="w3-card w3-round" style="max-width: 0%; overflow:auto; max-height: 500px;">
                    <div class="w3-white">

                    </div>
                </div>
            </div>
            <div class="w3-col m9" style="width: 100%;padding: 50px auto">
                <div style="width: 100%;">
                    <?php
                    require_once("comments.inc.php");
                    require_once("connection.php");
                    checkGroup($conn);
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
    
    <!-- <div class="container">
    <form class="form" action="processGroup.php" method="post">
        <label for="categoryGroup"></label>
        <input type="text" name="categoryGroup" placeholder="Group Name" required><br>

        <button>Update</button>
    </form>
    </div> -->

</body>
</html>
