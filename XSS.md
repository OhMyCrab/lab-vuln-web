1. Reflected XSS

- URL: http://127.0.0.1/OhMyCrab/modules/xss/reflected.php
  
- Ứng dụng tồn tại lỗ hổng Reflected Cross-Site Scripting (XSS) do dữ liệu đầu vào từ tham số q được phản chiếu trực tiếp lên trang HTML mà không được encode hoặc sanitize đúng cách. Attacker có thể chèn payload JavaScript độc hại để thực thi mã phía client trong trình duyệt nạn nhân.

- PoC:

+ Intercept request `/OhMyCrab/modules/xss/reflected.php?q=1`.
  
+ thay đổi param `1` với payload: `<script>alert('1')</script>`.
  
+ Forward request → alert 1 xuất hiện trên browser.
  
+ Kết quả PoC cho lỗ hổng Reflected XSS:

<img width="907" height="530" alt="image" src="https://github.com/user-attachments/assets/df7e4933-c88f-45b4-96b8-7c3b768f49f9" />

- Phân tích source code

```
<?php
    require_once "../../includes/auth.php";
    $q = $_GET['q'] ?? '';
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Reflected XSS</title>
        <link rel="stylesheet" href="../../assets/css/style.css">
    </head>

    <body>
        <?php require_once "../../includes/navbar.php"; ?>
        <?php require_once "../../includes/sidebar.php"; ?>
        <div class="content">
            <h1>Reflected XSS</h1>
            <form>
                <input type="text" name="q" placeholder="Search">
                <button type="submit">Search</button>
            </form>
            <hr>
            <?php
                if ($q) {
                    echo "Search result for: " . $q;
                }
            ?>
        </div>
    </body>
</html>
```
Biến `$q` nhận dữ liệu từ `$_GET['q']` và được echo trực tiếp ra HTML thông qua: `echo "Search result for: " . $q;`.

Do không sử dụng `htmlspecialchars()` hay cơ chế output encoding phù hợp, attacker có thể chèn payload JavaScript như `<script>alert('1')</script>` để thực thi mã phía client.

- Cách khắc phục

```
<?php
    require_once "../../../includes/auth.php";
    $q = $_GET['q'] ?? '';
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Reflected XSS - Fixed</title>
        <link rel="stylesheet" href="../../../assets/css/style.css">
    </head>

    <body>
        <?php require_once "../../../includes/navbar.php"; ?>
        <?php require_once "../../../includes/sidebar.php"; ?>
        <div class="content">
            <h1>Reflected XSS - Fixed</h1>
            <form>
                <input type="text" name="q" placeholder="Search">
                <button type="submit">Search</button>
            </form>
            <hr>
            <?php
                if ($q) {

                    $safe_q = htmlspecialchars($q, ENT_QUOTES, 'UTF-8');
                    echo "Search result for: " . $safe_q;
                }
            ?>
        </div>
        <div class="content">
            <button>
                <a href="/OhMyCrab/modules/xss/reflected.php">
                    Back To Vulnerable Version
                </a>
            </button>
        </div>
    </body>
</html>
```

Do không sử dụng htmlspecialchars() hoặc cơ chế output encoding phù hợp, attacker có thể chèn payload JavaScript như <script>alert('1')</script> để thực thi mã phía client. Để khắc phục, dữ liệu đầu vào cần được encode trước khi render ra HTML:

`$safe_q = htmlspecialchars($q, ENT_QUOTES, 'UTF-8');
echo "Search result for: " . $safe_q;`

Sau khi encode, các ký tự đặc biệt như `<` và `>` sẽ được chuyển thành entity HTML, khiến payload không thực thi được và hiển thị dưới dạng văn bản.

- Script khai thác

```
from selenium import webdriver
from selenium.webdriver.common.by import By
import time

payload = "<script>alert('1')</script>"

url = f"http://127.0.0.1/OhMyCrab/modules/xss/reflected.php?q={payload}"

driver = webdriver.Chrome()

driver.get(url)

time.sleep(20)

driver.quit()
```

Script dùng selenium tự động mở browser, truy cập URL chứa payload XSS để trigger lỗ hổng Reflected XSS.
