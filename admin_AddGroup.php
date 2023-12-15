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
    <link rel="stylesheet" href="style.css">
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

    <div class="w3-container w3-content w3-grey" style="width: 30%; max-width:1400px;margin:120px auto;" id="page-container">
        <div class="w3-container">
            <form  class="form" action="processGroup.php?id=add" method="post">
                <label for="categoryGroup"></label>
                <input class="w3-border" type="text" name="categoryGroup" placeholder="Group Name" required><br>
                <br>
                <button class="w3-button w3-theme">Add</button>
               
            </form>
        </div>
    </div>
    <br>

    
    <script src="script.js"></script>
    <script src="app.js"></script>
</body>
</html> 