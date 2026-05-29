<?php
    ob_start();
    require_once "../../includes/auth.php";
    require_once "../../includes/connect.php";
    require_once "../../includes/logger.php";
    requireLogin();

    $data = null;
    $session_user = $_SESSION['user'] ?? null; 
    $session_id = null;
    if ($session_user) {
        $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $session_user);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            if ($row = mysqli_fetch_assoc($res)) {
                $session_id = $row['id'];
            }
            mysqli_stmt_close($stmt);
        }
    }
    if (!isset($_GET['id']) && $session_id) {
        header("Location: idor.php?id=" . $session_id);
        exit();
    }
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $sql = "SELECT id, username, role FROM users WHERE id='$id'";
        $result = mysqli_query($conn, $sql);
        $data = mysqli_fetch_assoc($result);
        logAttack(10, $id, 200);
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>IDOR Lab</title>
        <link rel="stylesheet" href="../../assets/css/style.css">
    </head>

    <body>
        <?php require_once "../../includes/navbar.php"; ?>
        <?php require_once "../../includes/sidebar.php"; ?>
        <div class="content">
            <h1>IDOR Profile Access</h1>
            <h2>Truy cập hồ sơ người dùng</h2>
            <br>
            <hr>
            <?php if ($data): ?>
            <p><b>ID:</b> <?php echo $data['id']; ?></p>
            <p><b>Username:</b> <?php echo $data['username']; ?></p>
            <p><b>Role:</b> <?php echo $data['role']; ?></p>
            <?php else: ?>
            <p>Không tìm thấy dữ liệu user.</p>
            <?php endif; ?>
        </div>
        <div class="content">
            <button>
                <a href="/OhMyCrab/modules/access_control/fixed/idor_fixed.php">
                    Bản fix
                </a>
            </button>
        </div>
    </body>
</html>