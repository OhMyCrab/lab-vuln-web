<?php
    require_once "../../../includes/auth.php";
    require_once "../../../includes/connect.php";
    require_once "../../../includes/logger.php";

    $output = "";
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $ip = $_POST['ip'];
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            $output = "IP không hợp lệ";
        } else {
            $safe_ip = escapeshellarg($ip);
            $cmd = "ping -n 1 " . $safe_ip;
            $output = shell_exec($cmd);
        }
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>OS Command Injection</title>
        <link rel="stylesheet" href="../../../assets/css/style.css">
    </head>

    <body>
        <?php require_once "../../../includes/navbar.php"; ?>
        <?php require_once "../../../includes/sidebar.php"; ?>
        <div class="content">
        <h1>OS Command Injection</h1>
        <h2>Ping a target host</h2>
        <form method="POST">
            <input type="text" name="ip" placeholder="8.8.8.8">
            <button type="submit">Ping</button>
        </form>
        <br>
        <a href="/modules/command_injection/reset.php">Reset Lab</a>
        <hr>
        <pre>
            <?php echo $output; ?>
        </pre>
        </div>
        <div class="content">
            <button>
                <a href="/OhMyCrab/modules/command_injection/ping.php">
                    Back to lab
                </a>
            </button>
        </div>
    </body>
</html>