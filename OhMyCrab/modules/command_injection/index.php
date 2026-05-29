<?php
    require_once "../../includes/auth.php";
    requireLogin();
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Command Injection</title>
        <link rel="stylesheet" href="../../assets/css/style.css">
    </head>

    <body>
        <?php require_once "../../includes/navbar.php"; ?>
        <?php require_once "../../includes/sidebar.php"; ?>
        <div class="content">
            <h1>Command Injection Labs</h1>
            <ul>
                <li>
                    <a href="ping.php">
                        Ping Command Injection
                    </a>
                </li>
            </ul>
        </div>
    </body>
</html>