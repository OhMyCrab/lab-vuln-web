<?php
    require_once "../includes/auth.php";
    require_once "../includes/connect.php";
    requireLogin();

    if (!isAdmin()) {
        die("Access denied");
    }

    $result = mysqli_query($conn, "SELECT * FROM attack_logs ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>ATTACK LOGS</title>
        <link rel="stylesheet" href="../assets/css/style.css">
    </head>

    <body>
        <div class="admin-content">
            
            <div class="admin-sticky-header">
                <h1>ATTACK LOGS</h1>
                <a href="/OhMyCrab/admin/index.php" class="btn-back">
                    Quay lại Admin Panel
                </a>
            </div>

            <table border="1">
                <tr>
                    <th>IP</th>
                    <th>Endpoint</th>
                    <th>Payload</th>
                    <th>Thời gian</th>
                </tr>
                <?php while($log = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td>
                        <?php echo $log['ip_address']; ?>
                    </td>
                    <td>
                        <?php echo htmlspecialchars($log['endpoint']); ?>
                    </td>
                    <td style="color: #ff6600; font-size: 14px;">
                        <?php echo htmlspecialchars($log['payload']); ?>
                    </td>
                    <td>
                        <?php echo $log['created_at']; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </body>
</html>