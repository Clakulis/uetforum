<?php
session_start();
require_once("connection.php");
if (isset($_SESSION['message'])) {
    echo "<script>alert('" . $_SESSION['message'] . "');</script>";
    unset($_SESSION['message']); // Xóa thông báo sau khi sử dụng
}

$userID = '';
if (isset($_GET['userId'])) {
    $userID = $_GET['userId'];
}
$sql = "SELECT * FROM users WHERE userID = '$userID'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = $_POST['fullname'];
    //$oldPass = password_hash($_POST['oldpassword'], PASSWORD_BCRYPT, ['cost' => 12]);
    $oldPass = $_POST['oldpassword'];
    $newPass = $_POST['newpassword'];
    $rePass = $_POST['repassword'];
    if(strlen($oldPass) + strlen($newPass) + strlen($rePass) == 0) {
        $file = $user['linkAva'];
        if (isset($_FILES["file"])) {
            $file_name = $_FILES["file"]["name"];
            $file_tmp = $_FILES["file"]["tmp_name"];
            $file_size = $_FILES["file"]["size"];

            $upload_directory = "avaUser/";

            // Generate a unique filename using timestamp and original file extension
            $new_filename = $userID . '.' . pathinfo($file_name, PATHINFO_EXTENSION);

            $target_file = $upload_directory . $new_filename;

            if ($file_size > 0 && move_uploaded_file($file_tmp, $target_file)) {
                $file = $target_file;
            }
        }
        if (empty($file))
            $file = 'avaUser/anonymous.png';
        $stmt = $conn->prepare("UPDATE users SET fullName = ?, linkAva = ? WHERE userID = ?");
        $stmt->bind_param('sss', $fullName, $file, $userID);
        $result = $stmt->execute();
        header("Location: profile.php?userId=$userID");
        exit();
    } else if (!password_verify($oldPass, $user['password'])) {
        $_SESSION['message'] = "Mật khẩu cũ của bạn nhập không đúng!";
        header("Location: editProfile.php?userId=$userID");
        exit();
    } else {
        if (password_verify($newPass, $user['password'])) {
            $_SESSION['message'] = "Mật khẩu mới của bạn phải khác mật khẩu gần đây!";
            header("Location: editProfile.php?userId=$userID");
            exit();
        } else {
            if (strlen($newPass) < 6) {
                $_SESSION['message'] = "Mật khẩu mới cần ít nhất 6 ký tự.";
                header("Location: editProfile.php?userId=$userID");
                exit();
            } else {
                if ($newPass !== $rePass) {
                    $_SESSION['message'] = "Mật khẩu mới và mật khẩu nhập lại không khớp!";
                    header("Location: editProfile.php?userId=$userID");
                    exit();
                } else {
                    $file = $user['linkAva'];
                    if (isset($_FILES["file"])) {
                        $file_name = $_FILES["file"]["name"];
                        $file_tmp = $_FILES["file"]["tmp_name"];
                        $file_size = $_FILES["file"]["size"];

                        $upload_directory = "avaUser/";

                        // Generate a unique filename using timestamp and original file extension
                        $new_filename = $userID . '.' . pathinfo($file_name, PATHINFO_EXTENSION);

                        $target_file = $upload_directory . $new_filename;

                        if ($file_size > 0 && move_uploaded_file($file_tmp, $target_file)) {
                            $file = $target_file;
                        }
                    }
                    if (empty($file))
                        $file = 'avaUser/anonymous.png';
                    $stmt = $conn->prepare("UPDATE users SET fullName = ?, password = ?, linkAva = ? WHERE userID = ?");

                    if (!empty($newPass)) {
                        // Mã hóa mật khẩu mới
                        $hashedNewPass = password_hash($newPass, PASSWORD_BCRYPT, ['cost' => 12]);
                    }

                    $stmt->bind_param('ssss', $fullName, $hashedNewPass, $file, $userID);

                    $result = $stmt->execute();
                    header("Location: profile.php?userId=$userID");
                    exit();
                }
            }
        }
    }

}

; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <title>UET Forum</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
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
    <div class="w3-container w3-content" style="max-width:1400px;margin-top:80px;min-height:83.5vh;">
        <div class="w3-row">
            <div class="w3-col m12">
                <div class="w3-container w3-card w3-white w3-round w3-margin"><br>
                    <form action="editProfile.php?userId=<?php echo $userID ?>" method="post" enctype="multipart/form-data">
                        <img id="avatar-preview" src="<?php echo $user['linkAva']; ?>" alt="Avatar Preview" class="w3-circle w3-margin" style="width:200px; height:200px;object-fit:cover">
                        <br><label for="file" class="w3-opacity">Link Avatar</label>
                        <input type="file" name="file" id="file" style="display: none;" accept="image/*">
                        <br><label class="w3-button w3-theme-d1" style="padding:0 10px 0 5px;display: inline-block"
                        onclick="document.getElementById('file').click()">Choose File</label>
                        <br><br><label class="w3-opacity" for="fullname" >Full Name</label>
                        <input class="w3-input w3-border w3-padding" type="text" id="fullname" name="fullname" value="<?php echo $user['fullName'] ?>" minlength="1" required>
                        <br><label class="w3-opacity" for="oldpassword" class="w3-opacity">Old Password</label>
                        <input class="w3-input w3-border w3-padding" type="password" id="oldpassword" name="oldpassword">
                        <br><label class="w3-opacity" for="password" class="w3-opacity">New Password</label>
                        <input class="w3-input w3-border w3-padding" type="password" id="newpassword" name="newpassword">
                        <br><label class="w3-opacity" for="repassword" class="w3-opacity">New Password(re-enter)</label>
                        <input class="w3-input w3-border w3-padding" type="password" id="repassword" name="repassword">
                        <br><button class="w3-button w3-theme w3-margin-bottom" type="submit" style="margin-right: 10%;">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <footer class="w3-container w3-theme-d3 w3-padding-16">
    <h5 style="text-align: right;">UET FORUM</h5>
  </footer>
    <script src="script.js"></script>
    <script>
        function goBackProfile() {
            //window.location.href = "profile.php?";
            window.location.href = "profile.php?userId" + $userID;
        }
    </script>

    <script>
        document.getElementById('file').addEventListener('change', function () {
            var preview = document.getElementById('avatar-preview');
            var fileInput = document.getElementById('file');
            var file = fileInput.files[0];

            if (file) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block'; // Hiển thị khung ảnh
                };
                reader.readAsDataURL(file);
            } else {
                preview.src = ''; // Xóa đường dẫn ảnh nếu không có ảnh
                preview.style.display = 'none'; // Ẩn khung ảnh
            }
        });
    </script>
</body>

</html>