<?php
    require_once "../../includes/auth.php";
    require_once "../../includes/connect.php";
    require_once "../../includes/logger.php";

    $response = "";
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $url = $_POST['url'];
        $response = @file_get_contents($url);
        logAttack(4, $url, 200);
        if (!$response) {
            $response = "Không thể fetch URL";
        }
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>SSRF</title>
        <link rel="stylesheet" href="../../assets/css/style.css">
    </head>

    <body>
        <?php require_once "../../includes/navbar.php"; ?>
        <?php require_once "../../includes/sidebar.php"; ?>
        <div class="content">
            <h1>SSRF</h1>
            <h2>Fetch remote URLs.</h2>
            <hr>
            <form method="POST">
                <input type="text" name="url" placeholder="http://example.com" style="width:500px;">
                <button type="submit">Fetch</button>
            </form>
            <br>
            <a href="reset.php">Reset Lab</a>
            <hr>
            <pre><?php echo htmlspecialchars($response); ?></pre>
        </div>
        <div class="content">
            <button>
                <a href="fixed/ssrf_fix.php">Bản fix</a>
            </button>
        </div>
    </body>
</html>