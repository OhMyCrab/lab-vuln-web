<?php
    require_once "../../includes/auth.php";
    require_once "../../includes/connect.php";
    require_once "../../includes/logger.php";
    requireLogin(); 

    // Khởi tạo CSRF TOKEN nếu chưa có
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    $message = "";

    // Sử dụng phương thức POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['Change'])) {   
        //KIỂM TRA ORIGIN
        $allowed_host = $_SERVER['HTTP_HOST'];
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            $origin = parse_url($_SERVER['HTTP_ORIGIN'], PHP_URL_HOST);
            if ($origin !== $allowed_host) {
                die("Yêu cầu bị từ chối do không trùng khớp Origin.");
            }
        }
        // Kiểm tra bảo mật cốt lõi: CSRF TOKEN
        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            $message = "<p style='color:red'>Lỗi xác thực CSRF Token!</p>";
        } else {
            $current_password = $_POST['current_password'] ?? '';
            $new_password = $_POST['new_password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            $username = $_SESSION['user'];
            // Xác thực mật khẩu hiện tại bằng Prepared Statement
            $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            if (!$user || !password_verify($current_password, $user['password'])) {
                $message = "<p style='color:red'>Mật khẩu hiện tại không chính xác.</p>";
            }
            // Kiểm tra mật khẩu mới
            elseif ($new_password !== $confirm_password) {
                $message = "<p style='color:red'>Mật khẩu xác nhận không khớp.</p>";
            } 
            elseif (strlen($new_password) < 6) {
                $message = "<p style='color:red'>Mật khẩu mới phải từ 6 ký tự trở lên.</p>";
            }
            else {
                // Cập nhật mật khẩu mới vào cơ sở dữ liệu
                $hash = password_hash($new_password, PASSWORD_DEFAULT);
                $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");
                $update_stmt->bind_param("ss", $hash, $username);
                if ($update_stmt->execute()) {
                    //LÀM MỚI SESSION ID
                    session_regenerate_id(true);
                    // Tạo lại một CSRF token mới cho session vừa được làm mới
                    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                    $message = "<p style='color:lime'>Thay đổi mật khẩu thành công.</p>";
                } else {
                    $message = "<p style='color:red'>Có lỗi hệ thống xảy ra, vui lòng thử lại.</p>";
                }
            }
        }
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>CSRF Password Change (Secured)</title>
        <link rel="stylesheet" href="../../assets/css/style.css">
    </head>

    <body>
        <?php require_once "../../includes/navbar.php"; ?>
        <?php require_once "../../includes/sidebar.php"; ?>
        <div class="content">
            <h1>CSRF Password Change</h1>
            <p>Thay đổi mật khẩu của bạn.</p>
            
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

                <input type="password" name="current_password" placeholder="Mật khẩu hiện tại" required>
                <br><br>
                <input type="password" name="new_password" placeholder="Mật khẩu mới" required>
                <br><br>
                <input type="password" name="confirm_password" placeholder="Xác nhận mật khẩu" required>
                <br><br>
                <input type="submit" name="Change" value="Change">
            </form>
            <br>
            <a href="../reset.php">Reset Lab</a>
            <hr>
            <?php echo $message; ?>
        </div>
        <div class="content">
            <a href="/OhMyCrab/modules/csrf/change_password.php">
                Back to lab
            </a>
            <br><br>
            <a href="../../exploit/exploit_csrf.html" target="_blank">
                Open Exploit PoC
            </a>
    </body>
</html>