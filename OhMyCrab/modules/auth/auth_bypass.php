<?php
    require_once "../../includes/auth.php";
    require_once "../../includes/connect.php";
    require_once "../../includes/logger.php";

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
            $password_string = (string)$password; 
            if (password_verify($password_string, $stored_hash) == $password) {
                $login_success = true;
            }
        }
        logAttack(12, "$username | " . (is_array($password) ? "Array" : $password), 200);

        if ($login_success) {
            $message = "<p style='color:lime'>Đăng nhập thành công</p>";
        } else {
            $message = "<p style='color:red'>Thông tin xác thực không hợp lệ</p>";
        }
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Authentication Bypass</title>
        <link rel="stylesheet" href="../../assets/css/style.css">
    </head>

    <body>
        <?php require_once "../../includes/navbar.php"; ?>
        <?php require_once "../../includes/sidebar.php"; ?>
        <div class="content">
            <h1>Authentication Bypass</h1>
            <h2>Kiểm tra lỗi logic trong quá trình xác thực tài khoản.</h2>
            <form method="POST">
                <input type="text" name="username" placeholder="Tên đăng nhập">
                <br><br>
                <input type="password" name="password" placeholder="Mật khẩu">
                <br><br>
                <button type="submit">Đăng nhập</button>
            </form>
            <br>
            <a href="reset.php">Reset Lab</a>
            <hr>
            <?php echo $message; ?>
        </div>
        <div class="content">     
            <button>
                <a href="fixed/auth_bypass_fix.php">Bản fix</a>
            </button>
        </div>
    </body>
</html>