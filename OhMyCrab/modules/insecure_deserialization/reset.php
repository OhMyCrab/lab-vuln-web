<?php
    require_once "../../includes/auth.php";
    require_once "../../includes/connect.php";
    requireLogin();

    /* Clear logs */
    mysqli_query(
        $conn,
        "
        DELETE FROM attack_logs
        WHERE lab_id = 11
        "
    );
    /* Remove vulnerable cookie */
    setcookie(
        "session",
        "",
        time() - 3600,
        "/"
    );
    header("Location: index.php");
    exit;
?>