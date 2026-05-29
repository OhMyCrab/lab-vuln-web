<?php
    require_once "../../includes/auth.php";
    require_once "../../includes/connect.php";
    requireLogin();

    $default_admin_hash = password_hash('admin123', PASSWORD_DEFAULT); 
    
    $stmt = mysqli_prepare($conn, "UPDATE users SET password = ? WHERE username = 'admin'");
    mysqli_stmt_bind_param($stmt, "s", $default_admin_hash);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_query(
        $conn,
        "DELETE FROM attack_logs WHERE lab_id = 12"
    );

    header("Location: auth_bypass.php");
    exit;
?>