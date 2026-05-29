<?php
    require_once "../../includes/connect.php";
    mysqli_query(
        $conn,
        "TRUNCATE TABLE xss_stored_comments"
    );
    header("Location: stored.php");
?>