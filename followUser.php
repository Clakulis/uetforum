<?php
$userID = '';
require_once("connection.php");
$userIDNow = '';
if (isset($_GET['userId']) && isset($_GET['userIDNow'])) {
    $userID = $_GET['userId'];
    $userIDNow = $_GET['userIDNow'];
    $sql = "SELECT * FROM interactusers WHERE userIDInteracting = '$userID' AND userIDInteracted = '$userIDNow'";
    $result = $conn->query($sql);
    if ($result->num_rows === 0) {
        $stmt = $conn->prepare("INSERT INTO interactusers (userIDInteracting, userIDInteracted) VALUES (?, ?);");
        $stmt->bind_param("ss", $userID, $userIDNow);
        $stmt->execute();
    }
    $sql2 = "UPDATE interactusers SET isFollow = 1 - isFollow WHERE userIDInteracting = '$userID' AND userIDInteracted = '$userIDNow'";
    $result2 = $conn->query($sql2);
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    if ($row['isFollow'] == 0) {
        $sqlDecreaseFl = "UPDATE users SET followers = greatest(followers - 1, 0) WHERE userID = '$userIDNow'";
        $resultDe = $conn->query($sqlDecreaseFl);

        //delete notices
        $sql = "DELETE FROM notices WHERE userIDNotice = '$userIDNow' AND userIDDo = '$userID' AND followNotice = 1";
        $result = $conn->query($sql);
    } else {
        $sqlIncreaseFl = "UPDATE users SET followers = followers + 1 WHERE userID = '$userIDNow'";
        $resultIn = $conn->query($sqlIncreaseFl);

        //insert notices
        $fullName = '';
        $fullNameQuery = "SELECT * FROM users WHERE userID = '$userID'";
        $fullNameResult = $conn->query($fullNameQuery);
        if ($row = $fullNameResult->fetch_assoc()) {
            $fullName = $row['fullName'];
        }

        $message = 'Người dùng: ' . $fullName . ' đã theo dõi bạn!';
        $userIDNotice = $userIDNow;
        $noticeID = 'NO' . str_pad(rand(0, 99999999), 8, '0', STR_PAD_LEFT);

        if ($userIDNotice != $userID) {
            $follow = 1;
            $stmt = $conn->prepare("INSERT INTO notices (noticeID, userIDNotice, userIDDO, message, followNotice) VALUES (?, ?, ?, ?, ?);");
            $stmt->bind_param("ssssi", $noticeID, $userIDNotice, $userID, $message, $follow);
            $result = $stmt->execute();
        }
    }

    echo '
        <script>
            window.history.back();
        </script>
    ';
}
?>