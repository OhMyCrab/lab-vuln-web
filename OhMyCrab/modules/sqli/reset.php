<?php
    require_once "../../includes/auth.php";
    require_once "../../includes/connect.php";
    requireLogin();

    mysqli_query(
        $conn,
        "
        DROP TABLE IF EXISTS sqli_accounts
        "
    );

    mysqli_query(
        $conn,
        "
        CREATE TABLE sqli_accounts (

            id INT AUTO_INCREMENT PRIMARY KEY,

            username VARCHAR(100),

            password VARCHAR(100),

            role VARCHAR(50)

        )
        "
    );

    mysqli_query(
        $conn,
        "
        INSERT INTO sqli_accounts
        (
            username,
            password,
            role
        )
        VALUES

        (
            'admin',
            'supersecret',
            'admin'
        ),

        (
            'guest',
            'guest123',
            'user'
        )
        "
    );

    header(
        "Location: index.php"
    );

    exit;
?>