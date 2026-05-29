<?php
    require_once "session.php";

    function isLoggedIn()
    {
        return isset($_SESSION['user']);
    }
    function requireLogin()
    {
        if (!isLoggedIn()) {
            header("Location: login.php");
            exit;
        }
    }
    function isAdmin()
    {
        return isset($_SESSION['role']) &&
            $_SESSION['role'] === 'admin';
    }
?>