<?php
    require_once "../../includes/auth.php";
    requireLogin();
?>

<!DOCTYPE html>
<html>
    <head>
        <title>OAuth Labs</title>
        <link rel="stylesheet" href="../../assets/css/style.css">
    </head>

    <body>
        <?php require_once "../../includes/navbar.php"; ?>
        <?php require_once "../../includes/sidebar.php"; ?>
        <div class="content">
            <h1>OAuth Labs</h1>
            <ul>
                <li>
                    <a href="redirect_uri.php">
                        OAuth 2.0 Authorization Code Flow
                    </a>
                </li>
            </ul>
        </div>
    </body>
</html>