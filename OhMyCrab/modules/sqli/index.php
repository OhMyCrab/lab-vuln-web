<?php
    require_once "../../includes/auth.php";
    requireLogin();
?>

<!DOCTYPE html>
<html>
    <head>
        <title>SQL Injection</title>
        <link rel="stylesheet"
        href="../../assets/css/style.css">
    </head>

    <body>
        <?php require_once "../../includes/navbar.php"; ?>
        <?php require_once "../../includes/sidebar.php"; ?>
        <div class="content">
            <h1>SQL Injection Labs</h1>
            <ul>
                <li>
                    <a href="inband.php">
                        In-Band SQL Injection
                    </a>
                </li>
                <li>
                    <a href="blind.php">
                        Blind SQL Injection
                    </a>
                </li>
            </ul>
        </div>
    </body>
</html>