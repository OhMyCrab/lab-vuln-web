<?php
    require_once "../../includes/auth.php";
    require_once "../../includes/connect.php";
    require_once "../../includes/logger.php";
    requireLogin();
    $message = "";
    if (isset($_GET['Change'])) {
        $new_password = $_GET['new_password'] ?? '';
        $confirm_password = $_GET['confirm_password'] ?? '';
        if ($new_password !== $confirm_password) {
            $message = "<p style='color:red'>Mật khẩu xác nhận không khớp</p>";
        } else {
            $username = $_SESSION['user'];
            $hash = password_hash($new_password, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET password='$hash' WHERE username='$username'";
            mysqli_query($conn, $sql);
            logAttack(3, $new_password, 200);
            $message = "<p style='color:lime'>Thay đổi mật khẩu thành công</p>";
        }
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>CSRF Password Change</title>
        <link rel="stylesheet" href="../../assets/css/style.css">
    </head>

    <body>
        <?php require_once "../../includes/navbar.php"; ?>
        <?php require_once "../../includes/sidebar.php"; ?>
        <div class="content">
            <h1>CSRF Password Change</h1>
            <p>Thay đổi mật khẩu của bạn.</p>
            <form method="GET">
                <input type="text" name="new_password" placeholder="Mật khẩu mới">
                <br><br>
                <input type="text" name="confirm_password" placeholder="Xác nhận mật khẩu">
                <br><br>
                <input type="submit" name="Change" value="Change">
            </form>
            <br>
            <a href="reset.php">Reset Lab</a>
            <hr>
            <?php echo $message; ?>
        </div>
        <div class="content">
            <a href="/OhMyCrab/modules/csrf/fixed/change_password_fix.php">
                Bản fix
            </a>
            <br><br>
            <a href="../../exploit/exploit_csrf.html" target="_blank">
                Open Exploit PoC
            </a>
        </div>
    </body>
</html>