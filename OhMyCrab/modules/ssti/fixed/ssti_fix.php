<?php
    require_once "../../../includes/auth.php";
    require_once "../../../includes/connect.php";
    require_once "../../../includes/logger.php";

    $output = "";
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $payload = $_POST['payload'];
        $output = "Result: " . htmlspecialchars($payload, ENT_QUOTES, 'UTF-8');
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>SSTI</title>
        <link rel="stylesheet" href="../../../assets/css/style.css">
    </head>

    <body>
        <?php require_once "../../../includes/navbar.php"; ?>
        <?php require_once "../../../includes/sidebar.php"; ?>
        <div class="content">
            <h1>SSTI</h1>
            <form method="POST">
                <input type="text" name="payload">
                <button type="submit">Render</button>
            </form>
            <br>    
            <div class="result-box" style="background: #141414; padding: 15px; border-radius: 6px; border: 1px solid #333;">
                <strong>Kết quả hiển thị:</strong><br><br>
                <?php echo $output; ?>
            </div>
        </div>
        <div class="content">
            <button>
                <a href="../ssti.php">Back to lab</a>
            </button>
        </div>
    </body>
</html>