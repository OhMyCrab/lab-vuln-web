<?php 
    require_once "../../includes/auth.php";
    requireLogin(); 
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Authentication Labs</title>
        <link rel="stylesheet" href="../../assets/css/style.css">
    </head>

    <body>
        <?php require_once "../../includes/navbar.php"; ?>
        <?php require_once "../../includes/sidebar.php"; ?>
        <div class="content">
            <h1>Authentication Labs</h1>
            <ul>
                <li>
                    <a href="auth_bypass.php">
                        Authentication Bypass
                    </a>
                </li>
            </ul>
        </div>
    </body>
</html>