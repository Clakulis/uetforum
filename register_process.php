<?php
    require_once("connection.php");
    session_start();
    
    $username = $_SESSION['username'];
    $password = password_hash($_SESSION['password'], PASSWORD_BCRYPT, ['cost' => 12]);
    $birthday = str_replace("-", "/", $_SESSION['birthday']);
    $firstname = $_SESSION['firstname'];
    $lastname = $_SESSION['lastname'];
    $fullname = $firstname . " " . $lastname;
    $gender = $_SESSION["gender"];
    $userID = 'ID'.str_pad(rand(0, 99999999), 8, '0', STR_PAD_LEFT);
    if(strlen($fullname) == 1) 
    {
        $fullname = 'Anonymous';
    }
    $stmt = $conn->prepare("INSERT INTO users (userID, fullName, userName, password, birthday, gender) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $userID,$fullname, $username, $password, $birthday, $gender);

    // Thực thi truy vấn
    $result = $stmt->execute();

    if ($result) {
        $_SESSION['message'] = "Đăng ký thành công!";
    } else {
        $_SESSION['message'] = "Lỗi khi đăng ký: " . $stmt->error;
    }
    header("Location: index.php");
    // Đóng statement và kết nối
    $stmt->close();
    $mysqli->close();
    session_destroy();
?>