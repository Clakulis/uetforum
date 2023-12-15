<?php
    require_once("connection.php");
    session_start();
    $userIDInteracting = $_SESSION['userID'];
    $userIDInteracted = '';
    $noticeID = 'No'.str_pad(rand(0, 99999999), 8, '0', STR_PAD_LEFT);
    $isFollow = intval(true);
    $fullName = '';

    if (isset($_GET['userIDInteracted'])) {
        $userIDInteracted = htmlspecialchars($_GET['userIDInteracted']);
    }
    
    $fullNameQuery = "SELECT fullName FROM users WHERE userID = '$userIDInteracted'";
    $fullNameResult = $conn->query($fullNameQuery);
    if ($fullNameResult->num_rows > 0) {
        $row = $fullNameResult->fetch_assoc();
        $fullName = $row['fullName'];
    }
    
    $stmt = $conn->prepare("INSERT INTO interactusers (userIDInteracting, userIDInteracted, isFollow) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $userIDInteracting, $userIDInteracted, $isFollow);
    $result = $stmt->execute();

    $message = 'Người dùng: '.$fullName. ' đã follow bạn.';

    $stmt = $conn->prepare("INSERT INTO notices (noticeID, userIDNotice, message) VALUES (?, ?, ?);");
    $stmt->bind_param("sss", $noticeID, $userIDInteracted, $message);
    $result = $stmt->execute();

    $updateFollow = "UPDATE users SET followers = followers + 1 WHERE userID = '$userIDInteracted'";
    $followUpdateResult = $conn->query($updateFollow);

    $conn->close();
?>