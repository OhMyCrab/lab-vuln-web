<?php
    require_once "../../includes/auth.php";
    require_once "../../includes/connect.php";
    requireLogin();

    mysqli_query(
        $conn,
        "
        DELETE FROM attack_logs
        WHERE lab_id = 8
        "
    );

    header("Location: index.php");

    exit;
?>