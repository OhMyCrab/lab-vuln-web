<?php
    require_once "../includes/auth.php";
    require_once "../includes/connect.php"; // File kết nối CSDL của bạn
    requireLogin();

    if (!isAdmin()) {
        die("Access denied");
    }

    // Hash mật khẩu trước khi đưa vào câu lệnh SQL
    $admin_hashed  = password_hash('admin123', PASSWORD_DEFAULT);
    $caymai_hashed = password_hash('caymai123', PASSWORD_DEFAULT);
    $kr4v7_hashed  = password_hash('crabmeifucan', PASSWORD_DEFAULT);
    $guest_hashed  = password_hash('guest', PASSWORD_DEFAULT);
    $sql_queries = "
        SET FOREIGN_KEY_CHECKS = 0;

        -- Reset bảng users
        DROP TABLE IF EXISTS users;
        CREATE TABLE users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL,
            password VARCHAR(255) NOT NULL, -- Để VARCHAR(255) để chứa vừa chuỗi hash 60 ký tự
            role VARCHAR(20) NOT NULL
        );
        INSERT INTO users (username, password, role) VALUES 
        ('admin', '{$admin_hashed}', 'admin'),
        ('caymai', '{$caymai_hashed}', 'user'),
        ('kr4v7', '{$kr4v7_hashed}', 'user'),
        ('guest', '{$guest_hashed}', 'user');
    ";

    if (mysqli_multi_query($conn, $sql_queries)) {
        do {
            if ($result = mysqli_store_result($conn)) {
                mysqli_free_result($result);
            }
        } while (mysqli_next_result($conn));

        $status_message = "<p style='color: green; font-weight: bold;'>Thành công: Toàn bộ đã được reset về trạng thái ban đầu!</p>";
    } else {
        $status_message = "<p style='color: red; font-weight: bold;'>Lỗi: Không thể reset. " . mysqli_error($conn) . "</p>";
    }
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Reset </title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="admin-content">
        <h1>Hệ thống Reset</h1>
        <hr>
        <?php echo $status_message; ?>
        <br>
        <a href="/OhMyCrab/admin/index.php">Quay lại Admin Panel</a>
    </div>
</body>
</html>