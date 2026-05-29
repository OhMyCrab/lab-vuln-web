<?php
    require_once "includes/auth.php";
    requireLogin();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="/OhMyCrab/assets/css/style.css">
        <title>Trang chủ</title>
    </head>

    <body>
        <?php include "includes/navbar.php"; ?>
        <?php include "includes/sidebar.php"; ?>
        <div class="content">
            <div class="main">
                <h1>Xin chào <?php echo $_SESSION['user']; ?></h1>
                <p>Trang chủ người dùng</p>
                <a href="logout.php">Đăng xuất</a>
            </div>
        </div>
    </body>
</html>
