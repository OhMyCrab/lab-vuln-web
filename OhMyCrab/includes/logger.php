<?php

    require_once "connect.php";
    require_once "functions.php";

    function logAttack(
        $lab_id,
        $payload,
        $status_code = 200
    ) {
        global $conn;
        $ip = clientIP();
        $ua = userAgent();
        $method = $_SERVER['REQUEST_METHOD'];
        $endpoint = $_SERVER['REQUEST_URI'];
        $stmt = mysqli_prepare(
            $conn,
            "
            INSERT INTO attack_logs
            (
                lab_id,
                ip_address,
                user_agent,
                request_method,
                endpoint,
                payload,
                status_code
            )
            VALUES
            (
                ?,?,?,?,?,?,?
            )
            "
        );

        mysqli_stmt_bind_param(
            $stmt,
            "isssssi",
            $lab_id,
            $ip,
            $ua,
            $method,
            $endpoint,
            $payload,
            $status_code
        );

        mysqli_stmt_execute($stmt);
    }
?>