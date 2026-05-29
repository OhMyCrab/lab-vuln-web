<?php
    require_once "../../../includes/auth.php";
    require_once "../../../includes/connect.php";
    require_once "../../../includes/logger.php";

    $message = "";
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        // Sử dụng Prepared Statement để ngăn chặn SQL Injection
        $stmt = mysqli_prepare($conn, "SELECT * FROM sqli_accounts WHERE id=?");
        mysqli_stmt_bind_param($stmt, "i", $id); // "i" là kiểu dữ liệu int
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        logAttack(2, $id, 200);
        if (mysqli_num_rows($result) > 0) {
            $message = "<p style='color:lime'>Người dùng tồn tại</p>";
        } else {
            $message = "<p style='color:red'>Người dùng không tồn tại</p>";
        }
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Blind SQL Injection</title>
        <link rel="stylesheet" href="../../../assets/css/style.css">
    </head>

    <body>
        <?php require_once "../../../includes/navbar.php"; ?>
        <?php require_once "../../../includes/sidebar.php"; ?>
        <div class="content">
            <h1>Blind SQL Injection</h1>
            <form method="GET">
                <input type="text" name="id" placeholder="User ID">
                <button type="submit">Kiểm tra</button>
            </form>
            <br>
            <a href="reset.php">
                Reset Lab
            </a>
            <hr>
            <?php echo $message; ?>
        </div>
        <div class="content">
            <a href="/OhMyCrab/modules/sqli/blind.php">
                Back to lab
            </a>
        </div>
    </body>
</html>