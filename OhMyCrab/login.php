<?php
    require_once "includes/connect.php";
    require_once "includes/session.php";
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $sql = "SELECT * FROM users WHERE username='$username'";
        $result = mysqli_query($conn, $sql);
        $user = mysqli_fetch_assoc($result);
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            // ROLE ROUTING
            if ($user['role'] === 'admin') {
                header("Location: /OhMyCrab/admin/index.php");
            } else {
                header("Location: index.php");
            }
            exit();
        }
    }
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>OhMyCrab - Terminal Login</title>
</head>
<body>

<div class="auth-container">
    <h2>OHMYCRAB_LOGIN</h2>
    
    <div class="terminal-meta">
        SECURE CONNECTION: ACTIVE<br>
        HOST: <?php echo $_SERVER['SERVER_ADDR'] ?? '127.0.0.1'; ?><br>
        MODULE: OHMYCRAB_VULN_PLATFORM<br>
        TARGET: INSECURE_AUTHENTICATION_TEST
    </div>

    <form method="POST">
        <div class="input-group">
            <label>// TÀI KHOẢN</label>
            <input type="text" name="username" placeholder="Nhập tên tài khoản..." required autocomplete="off">
        </div>
        
        <div class="input-group">
            <label>// MẬT KHẨU</label>
            <input type="password" name="password" placeholder="Nhập mật khẩu cấu trúc..." required>
        </div>
        
        <button type="submit">ĐĂNG NHẬP</button>
    </form>

    <?php if (!empty($error)): ?>
        <div class="error-msg">[!] <?php echo $error; ?></div>
    <?php endif; ?>

    <div class="terminal-footer">
        WARNING: Unauthorized intercept attempts will be logged into admin/logs.php.
    </div>
</div>

</body>
</html>