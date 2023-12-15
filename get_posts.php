<?php
    require_once("connection.php");
    session_start();
    // $userIDPost = $_SESSION['userID'];
     //$userIDNow = $_SESSION['userIDNow'];
    // if($wherePost === "profile") {
    //     $sql = "SELECT * FROM posts WHERE userIDPost = '$userIDNow' ORDER BY dateOfPost DESC";
    // }
    $categoryGroup = $_SESSION['category'];
    
    $wherePost = $_SESSION['wherePost'];
    $sql = "";
    if ($categoryGroup === "recently") {
        $sql = "SELECT * FROM posts ORDER BY dateOfPost DESC";
        // if($wherePost === "profile") {
        //     $sql = "SELECT * FROM posts WHERE userIDPost = '$userIDNow' ORDER BY dateOfPost DESC";
        // }
    } else {
        $sql1 = "select * from groupss WHERE categoryGroup = '$categoryGroup'";
        $result1 = $conn->query($sql1);
        $row = $result1->fetch_assoc();
        $groupID = $row['groupID'];
  
        $sql = "SELECT * FROM posts WHERE groupIDPost = '$groupID' ORDER BY dateOfPost DESC";
    }
    $result = $conn->query($sql);
    if ($result) {
      $posts = [];
      while ($row = $result->fetch_assoc()) {
          $posts[] = $row;
      }
      $conn->close();
  
      header('Content-Type: application/json');
      echo json_encode($posts);
      exit;
    }
?>
