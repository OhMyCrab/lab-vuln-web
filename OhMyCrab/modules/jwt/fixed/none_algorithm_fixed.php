<?php
    require_once "../../../includes/auth.php";
    require_once "../../../includes/logger.php";
    require_once "../jwt_helper.php";
    requireLogin();

    $header = ["alg" => "HS256", "typ" => "JWT"];
    $payload = ["username" => $_SESSION['user'], "role" => "user"];
    $secret = "ohmycrab_secret";
    $header_encoded = base64url_encode(json_encode($header));
    $payload_encoded = base64url_encode(json_encode($payload));
    $signature = hash_hmac("sha256", "$header_encoded.$payload_encoded", $secret, true);
    $signature_encoded = base64url_encode($signature);
    $jwt = $header_encoded . "." . $payload_encoded . "." . $signature_encoded;
    $message = "";
    if (isset($_POST['token'])) {
        $token = $_POST['token'];
        $parts = explode(".", $token);
        if (count($parts) === 3) {
            $header = json_decode(base64url_decode($parts[0]), true);
            $payload = json_decode(base64url_decode($parts[1]), true);
            $allowed_alg = "HS256";
            if (!isset($header['alg']) || $header['alg'] !== $allowed_alg) {
                $message = "<p style='color:red'>Thuật toán không hợp lệ!</p>";
            } else {
                $expected_signature = hash_hmac("sha256", "$parts[0].$parts[1]", $secret, true);
                $expected_signature_encoded = base64url_encode($expected_signature);
                if (hash_equals($expected_signature_encoded, $parts[2])) {
                    if (isset($payload['role']) && $payload['role'] === 'admin') {
                        $message = "<p style='color:lime'>Chào mừng admin</p>
                        <p>FLAG:OHMYCRAB{jwt_none_pwned}</p>";
                    } else {
                        $message = "<p style='color:red'>Quyền hạn không hợp lệ</p>";
                    }
                } else {
                    logAttack(5, $token, 403);
                    $message = "<p style='color:red'>Token bị từ chối: Chữ ký không hợp lệ!</p>";
                }
            }
        } else {
            $message = "<p style='color:red'>Định dạng Token không hợp lệ!</p>";
        }
    }
?>

<!DOCTYPE html>
<html>
    <head>
    <title>JWT Secure Algorithm</title>
    <link rel="stylesheet" href="../../../assets/css/style.css">
    </head>

    <body>
        <?php require_once "../../../includes/navbar.php"; ?>
        <?php require_once "../../../includes/sidebar.php"; ?>
        <div class="content">
            <h1>JWT None Algorithm</h1>
            <p>Chỉnh sửa token để trở thành admin.</p>
            <hr>
            <h3>JWT của bạn</h3>
            <textarea rows="5" cols="100" readonly><?php echo $jwt; ?></textarea>
            <hr>
            <form method="POST">
                <textarea name="token" rows="5" cols="100" placeholder="Dán mã JWT vào đây"></textarea>
                <br><br>
                <button type="submit">Xác thực</button>
            </form>
            <hr>
            <?php echo $message; ?>
        </div>
        <div class="content">
            <button>
                <a href="/OhMyCrab/modules/jwt/none_algorithm.php">
                    Back to lab
                </a>
            </button>
        </div>
    </body>
</html>