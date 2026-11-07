<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function getCurrentUser() {
    return $_SESSION['username'] ?? null;
}

function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}
?>