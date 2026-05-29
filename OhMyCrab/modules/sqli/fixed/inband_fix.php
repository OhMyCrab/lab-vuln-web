<?php
    require_once "../../../includes/auth.php";
    require_once "../../../includes/connect.php";
    require_once "../../../includes/logger.php";

    $message = "";
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $stmt = mysqli_prepare($conn, "SELECT * FROM sqli_accounts WHERE username=? AND password=?");
        mysqli_stmt_bind_param($stmt, "ss", $username, $password);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        logAttack(1, $username . " | " . $password, 200);
        if (mysqli_num_rows($result) > 0) {
            $message = "<p style='color:lime'>Đăng nhập thành công</p>";
        } else {
            $message = "<p style='color:red'>Thông tin xác thực không hợp lệ</p>";
        }
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>In-Band SQL Injection</title>
        <link rel="stylesheet" href="../../../assets/css/style.css">
    </head>

    <body>
        <?php require_once "../../../includes/navbar.php"; ?>
        <?php require_once "../../../includes/sidebar.php"; ?>
        <div class="content">
            <h1>In-Band SQL Injection</h1>
            <h2>Bypass form đăng nhập bằng SQL Injection.</h2>
            <form method="POST">
                <input type="text" name="username" placeholder="Username">
                <br><br>
                <input type="password" name="password" placeholder="Password">
                <br><br>
                <button type="submit">Login</button>
            </form>
            <br>
            <a href="../reset.php">Reset Lab</a>
            <hr>
            <?php echo $message; ?>
        </div>
        <div class="content">
            <button>
                <a href="/OhMyCrab/modules/sqli/inband.php">
                    Back to lab
                </a>
            </button>
        </div>
    </body>
</html>