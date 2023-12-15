<?php
session_start();

if (isset($_SESSION['message'])) {
    echo "<script>alert('" . $_SESSION['message'] . "');</script>";
    unset($_SESSION['message']); // Xóa thông báo sau khi sử dụng
}
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">

<!DOCTYPE html>
<html>
<head>
<title>UET Forum</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://www.w3schools.com/lib/w3-theme-blue.css">
<link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Open+Sans'>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="icon" type="image/x-icon" href="https://cdn.haitrieu.com/wp-content/uploads/2021/10/Logo-DH-Cong-Nghe-UET.png">
<style>
html, body, h1, h2, h3, h4, h5 {font-family: "Open Sans", sans-serif}
#gender {
            font-size: 120%;
        }
</style>
</head>

<body>
    <div class="w3-top">
        <div class="w3-bar w3-theme-d2 w3-left-align w3-large">
            <a href="#" class="w3-bar-item w3-button w3-padding-large w3-theme-d4"><i class="fa fa-home w3-margin-right"></i>UET Forum</a>
        </div>
    </div>
    <?php
            $loginType = 'none';
            $registerType = 'none';
            if (isset($_SESSION['loginSucces'])) {
                $loginType = 'block';
                $registerType = 'none';
            } else {
                $loginType = 'none';
                $registerType = 'block';
            }
    ?>
    <div class="w3-container w3-content" style="max-width:1400px;margin-top:80px;min-height:83.5vh">    
    <!-- The Grid -->
        <div class="w3-row">
            <!-- Middle Column -->
                <div class="w3-col m7">
                    <div class="w3-row-padding">
                        <div class="w3-col m12">
                            <div class="w3-card w3-round w3-white">
                                    <div class="w3-container w3-padding">
                                        <form id="login-form" class="form" action="login_process.php" method="post" style="display: <?= $loginType ?>;">
                                            <h6>ĐĂNG NHẬP</h6>
                                            <label for="">Tài khoản: <input class="w3-input w3-border w3-padding" type="text" id="username" name="username" required></label><br>
                                            <label for="">Mật khẩu: <input class="w3-input w3-border w3-padding" type="password" id="password" name="password" required></label>
                                            <br><input class="w3-button w3-theme" type="submit" value="Đăng nhập">
                                            <br><a onclick="registerForm()">Chưa có tài khoản? Đăng ký tại đây</a>
                                        </form>
                                        <form id="register-form" class="form" action="register.php" method="post" style="display: <?= $registerType ?>;">
                                            <h6>ĐĂNG KÝ</h6>
                                            <label for="">Tên: <input class="w3-input w3-border w3-padding" type="text" id="firstname" name="firstname"> </label>
                                            <label for="">Họ và Tên đệm: <input class="w3-input w3-border w3-padding" type="text" id="lastname" name="lastname"></label>
                                            <label for="">Tài khoản: <input class="w3-input w3-border w3-padding" type="text" id="username" name="username" required></label>                                          
                                            <label for="">Mật khẩu: <input class="w3-input w3-border w3-padding" type="password" id="password" name="password" required></label>
                                            <label for="">Xác nhận mật khẩu: <input class="w3-input w3-border w3-padding" type="password" id="confirm-password" name="confirm-password" required></label>
                                            <label for="">Ngày sinh: <input class="w3-input w3-border w3-padding" type="date" id="birthday" name="birthday" style="margin-bottom: 20px;" required></label>
                                            <select id="gender" name="gender">
                                                <option value="Male">Male</option>
                                                <option value="Female">Female</option>
                                                <option value="Other">Other</option>
                                            </select>
                                            <br><input class="w3-button w3-theme" style="margin-top:30px;" type="submit" value="Đăng Ký">
                                            <br><a onclick="loginForm()">Đã có tài khoản? Đăng nhập tại đây</a>
                                        </form>     
                                    </div>
                            </div>
                        </div>
                    </div>        
                <!-- End Middle Column -->
                </div>
        </div>
    </div>
    <footer class="w3-container w3-theme-d3 w3-padding-16">
    <h5 style="text-align: right;">UET FORUM</h5>
    </footer>
    <script src="script.js"></script>
</body>

</html>