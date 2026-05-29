<?php
    require_once "../includes/auth.php";
    requireLogin();
    if (!isAdmin()) {
        die("Access denied");
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>TRANG QUẢN TRỊ</title>
        <link rel="stylesheet" href="../assets/css/style.css">
    </head>
    <body>
        <div class="admin-content">
            
            <div class="admin-sticky-header">
                <h1>TRANG QUẢN TRỊ</h1>
                <a href="/OhMyCrab/admin/logout.php" class="btn-back">
                    Đăng xuất
                </a>
            </div>

            <p>Chào mừng đến với trang quản trị. Tại đây bạn có thể quản lý người dùng, xem logs hoặc reset.</p>
            
            <div class="admin-grid-menu">
                <a href="/OhMyCrab/admin/users.php" class="menu-card">
                    <h3>Manage Users</h3>
                    <span>Quản lý tài khoản hệ thống</span>
                </a>

                <a href="/OhMyCrab/admin/logs.php" class="menu-card">
                    <h3>Attack Logs</h3>
                    <span>Xem lịch sử và payload tấn công</span>
                </a>

                <a href="/OhMyCrab/admin/reset.php" class="menu-card">
                    <h3>Reset</h3>
                    <span>Khôi phục dữ liệu</span>
                </a>
            </div>

            <div class="admin-grid-menu" style="margin-top: 30px;">
                <a href="/OhMyCrab/modules/access_control/" class="menu-card">
                    <h3>Access Control</h3>
                    <span>Lỗ hổng kiểm soát truy cập</span>
                </a>

                <a href="/OhMyCrab/modules/auth/auth_bypass.php" class="menu-card">
                    <h3>Auth Bypass</h3>
                    <span>Vượt qua cơ chế xác thực</span>
                </a>

                <a href="/OhMyCrab/modules/command_injection/" class="menu-card">
                    <h3>Command Injection</h3>
                    <span>OS Command Injection</span>
                </a>

                <a href="/OhMyCrab/modules/csrf/" class="menu-card">
                    <h3>CSRF</h3>
                    <span>Cross-Site Request Forgery</span>
                </a>

                <a href="/OhMyCrab/modules/insecure_deserialization/form.php" class="menu-card">
                    <h3>Insecure Deserialization</h3>
                    <span>Deserialization</span>
                </a>

                <a href="/OhMyCrab/modules/jwt/" class="menu-card">
                    <h3>JWT Vulnerability</h3>
                    <span>Khai thác lỗ hổng JSON Web Token</span>
                </a>

                <a href="/OhMyCrab/modules/oauth/" class="menu-card">
                    <h3>OAuth Vuln</h3>
                    <span>Open Authorization</span>
                </a>

                <a href="/OhMyCrab/modules/sqli/" class="menu-card">
                    <h3>SQL Injection</h3>
                    <span>Khai thác lỗ hổng SQL</span>
                </a>

                <a href="/OhMyCrab/modules/ssrf/ssrf.php" class="menu-card">
                    <h3>SSRF</h3>
                    <span>Server-Side Request Forgery</span>
                </a>

                <a href="/OhMyCrab/modules/ssti/" class="menu-card">
                    <h3>SSTI</h3>
                    <span>Server-Side Template Injection</span>
                </a>

                <a href="/OhMyCrab/modules/xss/" class="menu-card">
                    <h3>XSS</h3>
                    <span>Cross-Site Scripting</span>
                </a>

                <a href="/OhMyCrab/modules/xxe/" class="menu-card">
                    <h3>XXE Injection</h3>
                    <span>XML External Entity</span>
                </a>
            </div>
        </div>
    </body>
</html>