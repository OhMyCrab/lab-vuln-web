<?php
    require_once "includes/connect.php";

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $sql = "INSERT INTO users(username,password,role) VALUES('$username','$password','user')";
        mysqli_query($conn, $sql);
        header("Location: login.php");
        exit();
    }
?>

<link rel="stylesheet" href="assets/css/auth.css">
<div class="auth-box">
    <h2>Đăng ký</h2>
    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Đăng ký</button>
    </form>
    <p>Đã có tài khoản? <a href="login.php">Đăng nhập</a></p>
</div>