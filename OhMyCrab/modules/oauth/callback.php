<?php
session_start();
require_once "../../includes/auth.php";
requireLogin();

$code = $_GET['code'] ?? null;

if (!$code) {
    die("No code received");
}

if ($code === ($_SESSION['auth_code'] ?? null)) {

    $access_token = "token_" . bin2hex(random_bytes(16));

    echo "<h2>Login success</h2>";
    echo "Code: " . htmlspecialchars($code) . "<br>";
    echo "Access Token: " . htmlspecialchars($access_token);

} else {
    echo "Invalid code";
}
?>