<?php
    require_once "../../includes/auth.php";
    requireLogin();
?>

<!DOCTYPE html>
<html>
    <head>
        <title>XSS Labs</title>
        <link rel="stylesheet" href="../../assets/css/style.css">
    </head>

    <body>
        <?php require_once "../../includes/navbar.php"; ?>
        <?php require_once "../../includes/sidebar.php"; ?>
        <div class="content">
            <h1>XSS Labs</h1>
            <ul>
                <li>
                    <a href="stored.php">
                        Stored XSS
                    </a>
                </li>
                <li>
                    <a href="reflected.php">
                        Reflected XSS
                    </a>
                </li>
                <li>
                    <a href="dom.php">
                        DOM XSS
                    </a>
                </li>
            </ul>
        </div>
    </body>
</html>