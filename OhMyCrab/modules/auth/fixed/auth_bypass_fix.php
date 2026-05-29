<?php
    require_once "../../../includes/auth.php";
    require_once "../../../includes/connect.php";
    require_once "../../../includes/logger.php";

    $message = "";
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $json_data = json_decode(file_get_contents('php://input'), true);
        $username = $json_data['username'] ?? ($_POST['username'] ?? '');
        $password = $json_data['password'] ?? ($_POST['password'] ?? '');
        $stmt = mysqli_prepare($conn, "SELECT password FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $login_success = false;
        if ($user = mysqli_fetch_assoc($result)) {
            $stored_hash = $user['password'];
            if (is_string($password) && password_verify($password, $stored_hash)) {
                $login_success = true;
            }
        }
        if ($login_success) {
            $message = "<p style='color:lime'>Đăng nhập thành công</p>";
        } else {
            $message = "<p style='color:red'>Thông tin xác thực không hợp lệ</p>";
        }
        mysqli_stmt_close($stmt);
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Authentication Bypass - Bản Vá An Toàn</title>
        <link rel="stylesheet" href="../../../assets/css/style.css">
    </head>

    <body>
        <?php require_once "../../../includes/navbar.php"; ?>
        <?php require_once "../../../includes/sidebar.php"; ?>
        <div class="content">
            <h1>Authentication Bypass (Fixed)</h1>
            <h2>Hệ thống đã được vá lỗi Type Juggling thành công.</h2>
            <form method="POST">
                <input type="text" name="username" placeholder="Tên đăng nhập">
                <br><br>
                <input type="password" name="password" placeholder="Mật khẩu">
                <br><br>
                <button type="submit">Đăng nhập</button>
            </form>
            <br>
            <hr>
            <?php echo $message; ?>
        </div>
        <div class="content">
            <button>
                <a href="../auth_bypass.php">Back to lab</a>
            </button>
        </div>
    </body>
</html>