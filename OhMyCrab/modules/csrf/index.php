<?php
    require_once "../../includes/auth.php";
    requireLogin();
?>

<!DOCTYPE html>
<html>
    <head>
        <title>CSRF Lab</title>
        <link rel="stylesheet"
        href="../../assets/css/style.css">
    </head>
    <body>
        <?php require_once "../../includes/navbar.php"; ?>
        <?php require_once "../../includes/sidebar.php"; ?>
        <div class="content">
            <h1>CSRF Labs</h1>
            <ul>
                <li>
                    <a href="change_password.php">
                        Password Change
                    </a>
                </li>
            </ul>
        </div>
    </body>
</html>