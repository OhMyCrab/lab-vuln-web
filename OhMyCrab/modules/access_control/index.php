<?php
    require_once "../../includes/auth.php";
    requireLogin();
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Access Control</title>
        <link rel="stylesheet"
        href="../../assets/css/style.css">
    </head>

    <body>
        <?php require_once "../../includes/navbar.php"; ?>
        <?php require_once "../../includes/sidebar.php"; ?>
        <div class="content">
            <h1>Access Control Labs</h1>
            <ul>
                <li>
                    <a href="idor.php">
                        IDOR Profile Access
                    </a>
                </li>
            </ul>
        </div>
    </body>
</html>