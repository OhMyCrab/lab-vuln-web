<?php
    require_once "../../includes/auth.php";
    requireLogin();
?>

<!DOCTYPE html>
<html>
    <head>
        <title>SSRF Labs</title>
        <link rel="stylesheet" href="../../assets/css/style.css">
    </head>

    <body>
        <?php require_once "../../includes/navbar.php"; ?>
        <?php require_once "../../includes/sidebar.php"; ?>
        <div class="content">
            <h1>SSRF Labs</h1>
            <ul>
                <li>
                    <a href="ssrf.php">
                        SSRF
                    </a>
                </li>
            </ul>
        </div>
    </body>
</html>