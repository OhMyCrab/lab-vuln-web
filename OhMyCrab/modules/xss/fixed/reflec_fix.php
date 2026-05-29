<?php
    require_once "../../../includes/auth.php";
    $q = $_GET['q'] ?? '';
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Reflected XSS - Fixed</title>
        <link rel="stylesheet" href="../../../assets/css/style.css">
    </head>

    <body>
        <?php require_once "../../../includes/navbar.php"; ?>
        <?php require_once "../../../includes/sidebar.php"; ?>
        <div class="content">
            <h1>Reflected XSS - Fixed</h1>
            <form>
                <input type="text" name="q" placeholder="Search">
                <button type="submit">Search</button>
            </form>
            <hr>
            <?php
                if ($q) {

                    $safe_q = htmlspecialchars($q, ENT_QUOTES, 'UTF-8');
                    echo "Search result for: " . $safe_q;
                }
            ?>
        </div>
        <div class="content">
            <button>
                <a href="/OhMyCrab/modules/xss/reflected.php">
                    Back to lab
                </a>
            </button>
        </div>
    </body>
</html>