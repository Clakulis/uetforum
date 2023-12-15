<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['message'])) {
    echo "<script>alert('" . $_SESSION['message'] . "');</script>";
    unset($_SESSION['message']); // Xóa thông báo sau khi sử dụng
}
    session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <!-- <link rel="stylesheet" href="styles.css"> -->
    <link rel='stylesheet' type='text/css' media='screen' href='demo.css'>
    <title>Đăng Ký</title>
</head>
<body>
    <h1>Đăng Ký</h1>
    <form action="register.php" method="post">
        <div class = "input-group">
            <input class = "input" type="text" id="firstname" name="firstname"><br>
            <label class = "label" for="firstname">Firstname</label>
        </div>

        <div class = "input-group">
            <input class = "input" type="text" id="lastname" name="lastname"><br>
            <label class = "label" for="lastname">Lastname</label>
        </div>

        <!-- Thêm các trường đăng ký cần thiết vào đây -->
        <div class = "input-group">
            <input class = "input" type="text" id="username" name="username" oninput="validUserName(this)" required><br>
            <label class = "label" for="username">Username</label>
        </div>
        
        <div class = "input-group">
            <input class = "input" type="password" id="password" name="password" required><br>
            <label class = "label" for="password">Password</label>
        </div>

        <div class = "input-group">
            <?php $currentDateTimeObj = new DateTime(); ?>
            <input class = "input" type="date" id="birthday" name="birthday" max="2026-01-01" min="1900-01-01" oninput="validateYear(this)" required><br>
            <label class = "label" for="birthday">Birthday</label>
        </div>
        <br><input class = "input" type="submit" value="Đăng Ký">
    </form>
    <script>
function validateYear(input) {
    // Lấy giá trị năm từ chuỗi ngày

    const year = input.value.split('-')[0];

    // Kiểm tra độ dài của năm và xử lý nếu không phải là 4 chữ số
    if (year.length !== 4) {
        alert("Vui lòng nhập đúng 4 chữ số cho năm.");
        input.value = ''; // Xóa giá trị không hợp lệ
    }
}



</script>
</body>
</html>