<?php

    function sanitize($data)
    {
        return htmlspecialchars(
            $data,
            ENT_QUOTES,
            'UTF-8'
        );
    }

    function redirect($url)
    {
        header("Location: $url");
        exit;
    }

    function clientIP()
    {
        return $_SERVER['REMOTE_ADDR']
        ?? 'UNKNOWN';
    }

    function userAgent()
    {
        return $_SERVER['HTTP_USER_AGENT']
        ?? 'UNKNOWN';
    }
?>