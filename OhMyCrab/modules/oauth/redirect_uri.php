<?php
require_once "../../includes/auth.php";

$clients = [
    "client_1" => [
        "redirect_uri" => "http://127.0.0.1/OhMyCrab/modules/oauth/callback.php"
    ]
];

$message = "";
if (isset($_GET['redirect_uri'])) {
    $client_id = "client_1"; 
    $response_type = "code";
    $redirect_uri = $_GET['redirect_uri'];
    if (strpos($redirect_uri, $clients[$client_id]['redirect_uri']) !== 0) {
        $message = "Đường dẫn chuyển hướng (redirect_uri) không hợp lệ!";
    } else {
        $code = bin2hex(random_bytes(16));
        $_SESSION['auth_code'] = $code;
        header("Location: $redirect_uri?code=$code");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>OAuth 2.0 Authorization Code Flow</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>
    <?php require_once "../../includes/navbar.php"; ?>
    <?php require_once "../../includes/sidebar.php"; ?>
    
    <div class="content">
        <h1>OAuth 2.0 Authorization Code Flow</h1>
        <hr>
        <?php if (!empty($message)): ?>
            <p style="color:red; font-weight: bold;">
                <?php echo htmlspecialchars($message); ?>
            </p>
        <?php endif; ?>

        <form method="GET">
            <label for="redirect_uri"><strong>Nhập đường dẫn:</strong></label><br><br>
            <input type="text" name="redirect_uri" placeholder="ví dụ: http://hack.com" style="width:500px;" required>
            <br><br>
            <button type="submit">Chuyển hướng</button>
        </form>
        <hr>
    </div>

    <div class="content">
        <button>    
            <a href="/OhMyCrab/modules/oauth/fixed/redirect_uri_fix.php">Bản fix</a>
        </button>
    </div>
</body>
</html>