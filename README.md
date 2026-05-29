# OhMyCrab 🦀

Dự án này là một ứng dụng web được thiết kế có tính dễ bị tổn thương một cách cố ý nhằm mục đích nghiên cứu bảo mật và phân tích lỗ hổng.

Mục tiêu của dự án này là để dựng lại các lỗ hổng web phổ biến, phân tích nguyên nhân lỗi và cung cấp cách khắc phục.

Các tài khoản đăng nhập:

- admin/admin123 (quyền admin)

- guest/guest (quyền user)

- caymai/caymai123 (quyền user)

- kr4v7/crabmeifucan (quyền user)

Các bước để setup:

Bước 1: Cài đặt môi trường chạy PHP & MySQL (XAMPP)

- Tải và cài đặt XAMPP

- Sau khi cài xong, mở XAMPP Control Panel.

- Bấm Start ở 2 dòng Apache (Web Server) và MySQL (Database).

Bước 2: Đưa Source Code dự án vào thư mục web server

- Tải từ gitHub về toàn bộ thư mục OhMyCrab (thư mục chứa các folder con như admin, modules, includes, assets...).

- Copy thư mục OhMyCrab và paste vào đường dẫn mặc định của XAMPP: `C:\xampp\htdocs\`

Bước 3: Cấu hình cơ sở dữ liệu

- File sao lưu cơ sở dữ liệu `\OhMyCrab\database\ohmycrab.sql`

- Mở trình duyệt web truy cập vào địa chỉ: http://localhost/phpmyadmin/

<img width="1378" height="848" alt="image" src="https://github.com/user-attachments/assets/d4069828-e782-44d9-a2bd-d16d890b8793" />

- Bấm vào mục Import (Nhập)

- Bấm Choose File và trỏ tới file ohmycrab.sql

- Kéo xuống cuối trang bấm Import (Nhập) để hệ thống tự động khởi tạo các bảng (users, logs,...).

<img width="1392" height="878" alt="image" src="https://github.com/user-attachments/assets/f565d7bb-917e-4c1b-929a-0c6b2a11f014" />

Bước 4: Truy cập và bắt đầu

Sau khi các bước trên đã hoàn tất, mở trình duyệt và truy cập vào đường dẫn: `http://127.0.0.1/OhMyCrab/login.php`, đăng nhập và test các lỗ hổng như SQLi, XSS, SSTI, XXE... rồi!
