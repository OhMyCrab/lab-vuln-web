<?php
    require_once "../../includes/auth.php";
    requireLogin();
?>

<!DOCTYPE html>
<html>
    <head>
        <title>JWT Labs</title>
        <link rel="stylesheet" href="../../assets/css/style.css">
    </head>

    <body>
        <?php require_once "../../includes/navbar.php"; ?>
        <?php require_once "../../includes/sidebar.php"; ?>
        <div class="content">
            <h1>JWT Labs</h1>
            <ul>
                <li>
                    <a href="none_algorithm.php">
                        None Algorithm
                    </a>
                </li>
            </ul>
        </div>
    </body>
</html>