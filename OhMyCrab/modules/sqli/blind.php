<?php
    require_once "../../includes/auth.php";
    require_once "../../includes/connect.php";
    require_once "../../includes/logger.php";

    $message = "";
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        /* Vulnerable Query */
        $sql = " SELECT * FROM sqli_accounts WHERE id='$id'";
        $result = mysqli_query($conn, $sql);
        /* Logging */
        logAttack(2, $id, 200);
        /* Blind Result */
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
        <link rel="stylesheet" href="../../assets/css/style.css">
    </head>

    <body>
        <?php require_once "../../includes/navbar.php"; ?>
        <?php require_once "../../includes/sidebar.php"; ?>
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
            <a href="/OhMyCrab/modules/sqli/fixed/blind_fix.php">
                Bản fix
            </a>
        </div>
    </body>
</html>