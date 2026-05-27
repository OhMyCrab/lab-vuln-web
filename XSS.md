1. Reflected XSS

- URL: `http://127.0.0.1/OhMyCrab/modules/xss/reflected.php`
  
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

2. Stored XSS

- URL: `http://127.0.0.1/OhMyCrab/modules/xss/stored.php`
  
- Ứng dụng tồn tại lỗ hổng Stored Cross-Site Scripting (Stored XSS) do dữ liệu đầu vào từ các trường `username` và `comment` được lưu trực tiếp vào database mà không được sanitize hoặc output encoding đúng cách. Khi dữ liệu được hiển thị lại trên trang, payload JavaScript sẽ được thực thi trên trình duyệt của người dùng truy cập.

- PoC:

+ Intercept request gửi comment tới: `POST /OhMyCrab/modules/xss/stored.php`
  
+ Thay giá trị `username` và `comment` bằng payload: `<script>alert(1)</script>`
  
+ Forward request để lưu comment vào database.
  
+ Kết quả PoC cho lỗ hổng Stored XSS:

<img width="922" height="572" alt="image" src="https://github.com/user-attachments/assets/6cb93c1a-7662-47a7-aa16-9e7b6f745765" />

- Phân tích source code

```
<!DOCTYPE html>
<html>
    <head>
        <title>Stored XSS</title>
        <link rel="stylesheet" href="../../assets/css/style.css">
    </head>
    <body>
        <?php require_once "../../includes/navbar.php"; ?>
        <?php require_once "../../includes/sidebar.php"; ?>
        <div class="content">
            <h1>Stored XSS</h1>
            <form method="POST">
                <input type="text" name="username" placeholder="Username">
                <br><br>
                <textarea name="comment" placeholder="Comment"></textarea>
                <br><br>
                <button type="submit">Post</button>
            </form>
            <hr>
            <h2>Comments</h2>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
            <div class="comment">
                <strong>
                    <?php echo $row['username']; ?>
                </strong>
                <br>
                <?php echo $row['comment']; ?>
            </div>
            <hr>
            <?php endwhile; ?>
            <a href="reset.php">Reset Comments</a>
        </div>
        <div class="content">
            <button><a href="/OhMyCrab/modules/xss/fixed/stored_fix.php">View Fixed</a></button>
        </div>
    </body>
</html>
```

Hai trường `username` và `comment` được lưu thô vào database và chèn trực tiếp vào trang HTML khi hiển thị, không có bước HTML-escaping hay lọc input. Do đó payload `<script>alert(1)</script>` sẽ được lưu vào DB và thực thi trên trình duyệt của người dùng.

- Cách khắc phục

```
<!DOCTYPE html>
<html>
    <head>
        <title>Stored XSS</title>
        <link rel="stylesheet" href="../../../assets/css/style.css">
    </head>
    <body>
        <?php require_once "../../../includes/navbar.php"; ?>
        <?php require_once "../../../includes/sidebar.php"; ?>
        <div class="content">
            <h1>Stored XSS</h1>
            <form method="POST">
                <input type="text" name="username" placeholder="Username">
                <br><br>
                <textarea name="comment" placeholder="Comment"></textarea>
                <br><br>
                <button type="submit">Post</button>
            </form>
            <hr>
            <h2>Comments</h2>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
            <div class="comment">
                <strong>
                    <?php
                        echo htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8');
                    ?>
                </strong>
                <br>
                <?php
                    echo htmlspecialchars($row['comment'], ENT_QUOTES, 'UTF-8');
                ?>
            </div>
            <hr>
            <?php endwhile; ?>
            <a href="reset.php">Reset Comments</a>
        </div>
        <div class="content">
            <button>
                <a href="/OhMyCrab/modules/xss/stored.php">
                    Back To Vulnerable
                </a>
            </button>
        </div>
    </body>
</html>
```

Sau khi encode, các ký tự đặc biệt như `<` và `>` sẽ được chuyển thành entity HTML, khiến payload không thực thi được và hiển thị dưới dạng văn bản.

- Script khai thác

```
from selenium import webdriver
from selenium.webdriver.common.by import By
import time

driver = webdriver.Chrome()
driver.get("http://127.0.0.1/OhMyCrab/modules/xss/stored.php")
time.sleep(2)

username = driver.find_element(By.NAME, "username")
comment = driver.find_element(By.NAME, "comment")

username.send_keys("<script>alert(1)</script>")
comment.send_keys("<script>alert(1)</script>")

driver.find_element(By.TAG_NAME, "button").click()

time.sleep(10)

driver.quit()
```

Script sử dụng Selenium để tự động gửi payload XSS vào 2 trường `username`, `comment` và trigger lỗ hổng Stored XSS.

3. DOM-based XSS

- URL: `http://127.0.0.1/OhMyCrab/modules/xss/dom.php`

- Ứng dụng tồn tại lỗ hổng DOM-based XSS do dữ liệu từ URL query string (window.location.search) được xử lý trực tiếp bằng JavaScript và chèn vào DOM thông qua document.write() mà không có bất kỳ cơ chế sanitize hoặc output encoding nào. Attacker có thể kiểm soát tham số book để chèn payload độc hại.

- PoC:

+ Truy cập URL: `http://127.0.0.1/OhMyCrab/modules/xss/dom_select.php?book=<script>alert(1)</script>`

+ Nhấn enter

+ Browser parse HTML và thực thi JavaScript.

+ Kết quả PoC cho lỗ hổng DOM XSS:

<img width="1264" height="655" alt="image" src="https://github.com/user-attachments/assets/b6bd7bf2-c033-4ba7-b6db-0adf0217197f" />

- Phân tích source code

```
<!DOCTYPE html>
<html>
    <head>
        <title>DOM XSS (DVWA Style)</title>
        <link rel="stylesheet" href="../../assets/css/style.css">
    </head>

    <body>
        <?php require_once "../../includes/sidebar.php"; ?>
        <div class="content">
            <h1>DOM XSS</h1>
            <h2>Hãy chọn sách bạn muốn</h2>
            <form name="allbook" method="GET">
                <select name="book">
                    <script>
                        var lang = decodeURI(window.location.search.toprop || window.location.search);

                        if (lang.indexOf("book=") >= 0) {
                            var res = lang.substring(lang.indexOf("book=") + 5);
                            
                            document.write("<option value='" + res + "'>" + res + "</option>");
                        } else {
                            document.write("<option value='OhMyCrab'>OhMyCrab</option>");
                        }
                    </script>
                    <option value="Foxie">Foxie</option>
                    <option value="Pophello">Pophello</option>
                    <option value="Naru">Naru</option>
                </select>
                <input type="submit" style="margin-top: 20px;" value="Chọn">
            </form>

            <div id="output" style="margin-top: 20px;"></div>
        </div>
    </body>
</html>
```

Dữ liệu lấy từ URL query string: `location.search`, attacker kiểm soát hoàn toàn input
trích xuất tham số
if (lang.indexOf("book=") >= 0) {
    var res = lang.substring(lang.indexOf("book=") + 5);
}
Cắt trực tiếp chuỗi từ book=
Không validate input
Không encode
3. Sink gây XSS
document.write("<option value='" + res + "'>" + res + "</option>");

Đây là dangerous sink vì:

inject HTML trực tiếp vào DOM
không escape ký tự đặc biệt như:
<
>
'
"
