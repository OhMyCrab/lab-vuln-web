<?php
require_once "../../../includes/auth.php";

$clients = [
    "client_1" => [
        "redirect_uri" => "http://127.0.0.1/OhMyCrab/modules/oauth/callback.php"
    ]
];

$message = "";
$success_message = "";

if (isset($_GET['redirect_uri'])) {
    $client_id = "client_1"; 
    $response_type = "code";
    $redirect_uri = trim($_GET['redirect_uri']);
    $parsed_url = parse_url($redirect_uri);
    $host = $parsed_url['host'] ?? '';
    if ($clients[$client_id]['redirect_uri'] !== $redirect_uri) {
        $message = "Chặn đứng! URL chuyển hướng không khớp chính xác với whitelist!";
    } 
    elseif ($host !== '127.0.0.1' && $host !== 'localhost') {
        $message = "Tên miền không nằm trong phạm vi cho phép!";
    }
    else {
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
    <link rel="stylesheet" href="../../../assets/css/style.css">
</head>

<body>
    <?php require_once "../../../includes/navbar.php"; ?>
    <?php require_once "../../../includes/sidebar.php"; ?>
    
    <div class="content">
        <h1>OAuth 2.0 Authorization Code Flow</h1>

        <hr>

        <?php if (!empty($message)): ?>
            <div style="background-color: #ffcccc; color: #cc0000; padding: 12px; margin-bottom: 15px; border-radius: 5px; border: 1px solid #ff9999; font-weight: bold;">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="GET">
            <label for="redirect_uri"><strong>Nhập đường dẫn:</strong></label><br><br>
            <input type="text" name="redirect_uri"  value="http://127.0.0.1/OhMyCrab/modules/oauth/callback.php" style="width:500px;" required>
            <br><br>
            <button type="submit" style="background-color: #2e7d32; color: white;">Chuyển hướng</button>
        </form>
        <hr>
    </div>

    <div class="content">
        <button>    
            <a href="/OhMyCrab/modules/oauth/redirect_uri.php">
                Back to lab
            </a>
        </button>
    </div>
</body>
</html>