<?php 
    require_once "../../includes/auth.php";
    requireLogin(); 
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Insecure Deserialization</title>
        <link rel="stylesheet" href="../../assets/css/style.css">
    </head>

    <body>
        <?php require_once "../../includes/navbar.php"; ?>
        <?php require_once "../../includes/sidebar.php"; ?>
        <div class="content">
            <h1>Insecure Deserialization</h1>
            <ul>
                <li>
                    <a href="form.php">
                        Insecure Deserialization
                    </a>
                </li>
            </ul>
        </div>
    </body>
</html>