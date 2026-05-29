<?php
    require_once "../../includes/auth.php";
    require_once "../../includes/connect.php";
    require_once "../../includes/logger.php";

    $output = "";
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $payload = $_POST['payload'];
        logAttack(8, $payload, 200);
        $template = "Result: " . $payload;
        if (preg_match('/\{\{(.*?)\}\}/', $template, $matches)) {
            $expr = trim($matches[1]);
            try {
                $result = eval("return $expr;");
                $template = str_replace($matches[0], $result, $template);
            } catch (Throwable $e) {
                $template = "Template Error";
            }
        }
        $output = $template;
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>SSTI</title>
        <link rel="stylesheet" href="../../assets/css/style.css">
    </head>

    <body>
        <?php require_once "../../includes/navbar.php"; ?>
        <?php require_once "../../includes/sidebar.php"; ?>
        <div class="content">
            <h1>SSTI</h1>
            <form method="POST">
                <input type="text" name="payload">
                <button type="submit">Render</button>
            </form>
            <hr>
            <div class="result-box" style="background: #141414; padding: 15px; border-radius: 6px; border: 1px solid #333;">
                <strong>Kết quả hiển thị:</strong><br><br>
                <?php echo $output; ?>
            </div>
            <br><br>
            <a href="reset.php">
                Reset Lab
            </a>
        </div>
        <div class="content">
            <button>
                <a href="fixed/ssti_fix.php">Bản fix</a>
            </button>
        </div>
    </body>
</html>