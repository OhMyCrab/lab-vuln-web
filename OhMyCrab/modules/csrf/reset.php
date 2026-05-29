<?php
    require_once "../../includes/auth.php";
    require_once "../../includes/connect.php";
    requireLogin();

    $username = $_SESSION['user'];

    $default_passwords = [
        "admin"  => "admin123",
        "caymai" => "caymai123",
        "kr4v7"  => "crabmeifucan",
        "guest"  => "guest"
    ];
    if (isset($default_passwords[$username])) {
        $default_password = $default_passwords[$username];
        $hash = password_hash($default_password, PASSWORD_DEFAULT);

        mysqli_query($conn, " UPDATE users SET password='$hash' WHERE username='$username'");
    }
    header("Location: change_password.php");
    exit;
?>