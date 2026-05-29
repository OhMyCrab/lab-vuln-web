<?php
    // Bản Fix bảo mật: Loại bỏ hoàn toàn PHP Deserialization, chuyển sang dùng JSON thuần túy
    require_once "../../../includes/auth.php";
    require_once "../../../includes/connect.php";
    require_once "../../../includes/logger.php";

    $current_username = isset($_SESSION['user']) ? $_SESSION['user'] : "guest";
    $current_role = isset($_SESSION['role']) ? $_SESSION['role'] : "user";
    $currentUserData = [
        "username" => $current_username,
        "role" => $current_role
    ];
    $current_json_data = json_encode($currentUserData);
    $output = "";
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $payload = $_POST['payload'];
        logAttack(11, $payload, 200);
        $data = json_decode($payload, true);

        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            $output = "<p style='color:red'><strong>[Thất bại]</strong> Định dạng dữ liệu JSON không hợp lệ.</p>";
        } else {
            if ($current_role === "admin") {
                $output = "<p style='color:lime'><strong>[Thành công]</strong> Xác thực đặc quyền Admin từ Server Session!</p>";
            } else {
                $output = "<p style='color:yellow'><strong>[Từ chối]</strong> Dữ liệu đã được xử lý an toàn dưới dạng mảng thuần túy. Tài khoản hiện tại không có đặc quyền Admin.</p>";
            }
        }
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Insecure Deserialization - Bản Fix An Toàn</title>
        <link rel="stylesheet" href="../../../assets/css/style.css">
    </head>

    <body>
        <?php require_once "../../../includes/navbar.php"; ?>
        <?php require_once "../../../includes/sidebar.php"; ?>
        <div class="content">
            <h1>Insecure Deserialization</h1>
            <p style="color: cyan;">Hệ thống đã chuyển đổi cấu trúc sang định dạng JSON an toàn.</p>
            <p><strong>JSON</strong></p>
            <pre><?php echo htmlspecialchars($current_json_data); ?></pre>
            
            <form method="POST">
                <textarea name="payload" rows="6" cols="80" style="margin-top: 20px;"></textarea>
                <br><br>
                <button type="submit">JSON Decode</button>
            </form>
            <br>
            <hr>
            <?php echo $output; ?>
        </div>
        <div class="content">
            <button>
                <a href="../form.php">Back to lab</a>
            </button>
        </div>
    </body>
</html>