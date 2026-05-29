<?php
    class UserData
    {
        public $username;
        public $role;
        public function __construct($username = "guest", $role = "user") {
            $this->username = $username;
            $this->role = $role;
        }
        function __destruct()
        {
            if ($this->role === "admin") {
                echo "<p style='color:lime'>Đã thay đổi quyền từ user sang admin</p>";
            }
        }
    }
    require_once "../../includes/auth.php";
    require_once "../../includes/connect.php";
    require_once "../../includes/logger.php";
    $current_username = "guest";
    $current_role = "user";
    if (isset($_SESSION['user'])) {
        $current_username = $_SESSION['user'];
    }
    if (isset($_SESSION['role'])) {
        $current_role = $_SESSION['role'];
    }
    $currentUserObject = new UserData($current_username, $current_role);
    $current_serialized_data = serialize($currentUserObject);
    $output = "";
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $payload = $_POST['payload'];
        logAttack(11, $payload, 200);
        ob_start();
        $unserialized_obj = @unserialize($payload); 
        unset($unserialized_obj); 
        $captured_output = ob_get_clean();
        if ($captured_output === "" && $payload !== 'b:0;') {
            $output = "<p style='color:red'>Deserialize thất bại</p>";
        } else {
            $output = $captured_output;
        }
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Insecure Deserialization</title>
        <link rel="stylesheet" href="../../assets/css/style.css">
    </head>

    <body>
        <?php require_once "../../includes/navbar.php"; ?>
        <?php require_once "../../includes/sidebar.php"; ?>
        <div class="content">
            <h1>Insecure Deserialization</h1>
            <p><strong>Serialization data hiện tại</strong></p>
            <pre><?php echo htmlspecialchars($current_serialized_data); ?></pre>
            
            <form method="POST">
                <textarea name="payload" rows="6" cols="80" placeholder="Serialized object" style="margin-top: 20px;"></textarea>
                <br><br>
                <button type="submit">Deserialize</button>
            </form>
            <br>
            <a href="reset.php">Reset Lab</a>
            <hr>
            <?php echo $output; ?>
        </div>
        <div class="content">
            <button>
                <a href="fixed/form_fix.php">Bản fix</a>
            </button>
        </div>
    </body>
</html>