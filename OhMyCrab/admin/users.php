<?php
    require_once "../includes/auth.php";
    require_once "../includes/connect.php";
    requireLogin();

    if (!isAdmin()) {
        die("Access denied");
    }

    $result = mysqli_query($conn, "SELECT * FROM users");
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>QUẢN LÝ NGƯỜI DÙNG</title>
        <link rel="stylesheet" href="../assets/css/style.css">
    </head>

    <body>
        <div class="admin-content">
            <div class="admin-sticky-header">
                <h1>QUẢN LÝ NGƯỜI DÙNG</h1>
                <a href="/OhMyCrab/admin/index.php" class="btn-back">
                    Quay lại Admin Panel
                </a>
            </div>

            <table border="1">
                <tr>
                    <th>ID</th>
                    <th>Tài khoản</th>
                    <th>Quyền</th>
                </tr>
                <?php while($user = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td>
                        <?php echo $user['id']; ?>
                    </td>
                    <td>
                        <?php echo htmlspecialchars($user['username']); ?>
                    </td>
                    <td>
                        <?php echo htmlspecialchars($user['role']); ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </body>
</html>