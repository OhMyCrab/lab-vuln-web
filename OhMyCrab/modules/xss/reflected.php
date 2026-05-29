<?php
    require_once "../../includes/auth.php";
    $q = $_GET['q'] ?? '';
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Reflected XSS</title>
        <link rel="stylesheet" href="../../assets/css/style.css">
    </head>

    <body>
        <?php require_once "../../includes/navbar.php"; ?>
        <?php require_once "../../includes/sidebar.php"; ?>
        <div class="content">
            <h1>Reflected XSS</h1>
            <form>
                <input type="text" name="q" placeholder="Search">
                <button type="submit">Search</button>
            </form>
            <hr>
            <?php
                if ($q) {
                    echo "Search result for: " . $q;
                }
            ?>
        </div>
        <div class="content">
            <button><a href="/OhMyCrab/modules/xss/fixed/reflec_fix.php">Bản fix</a></button>
        </div>
    </body>
</html>