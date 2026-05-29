<?php
    require_once "../../includes/auth.php";
    requireLogin();

    header(
        "Location: index.php"
    );
    exit;
?>