<?php
    require_once "../../../includes/auth.php";
    require_once "../../../includes/connect.php";
    require_once "../../../includes/logger.php";

    $output = "";
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $xml = $_POST['xml'];
        libxml_disable_entity_loader(true);
        $data = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NONET);
        logAttack( 7, $xml, 200);
        if ($data) {
            $output = htmlspecialchars(
                $data->message
            );
        } else {
            $output = "Invalid XML";
        }
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>XXE</title>
        <link rel="stylesheet" href="../../../assets/css/style.css">
    </head>

<body>
    <?php require_once "../../../includes/navbar.php"; ?>
    <?php require_once "../../../includes/sidebar.php"; ?>
    <div class="content">
        <h1>XXE</h1>
        <p>Parse XML data.</p>
        <hr>
        <form method="POST">
            <textarea name="xml" rows="15" cols="100"><?php echo '<?xml version="1.0"?>'; ?>
      
<data>
    <message>
        Hello OhMyCrab
    </message>
</data></textarea>
            <br><br>
            <button type="submit">Parse XML</button>
        </form>
        <br>
        <a href="reset.php">
            Reset Lab
        </a>
        <hr>
        <pre>
        <?php echo $output; ?>
        </pre>
    </div>
    <div class="content">
        <button>
            <a href="../xxe.php">
                Back to lab
            </a>
        </button>
    </div>
</body>
</html>