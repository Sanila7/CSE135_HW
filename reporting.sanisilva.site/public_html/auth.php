<?php
session_start();

function require_login() {
    if (!isset($_SESSION['user'])) {
        header('Location: /login.php');
        exit();
    }
}

function require_role($roles) {
    require_login();
    if (!in_array($_SESSION['user']['role'], (array)$roles)) {
        header('Location: /403.php');
        exit();
    }
}

function current_user() {
    return $_SESSION['user'] ?? null;
}

function is_superadmin() {
    return ($_SESSION['user']['role'] ?? '') === 'superadmin';
}

function is_analyst() {
    return in_array($_SESSION['user']['role'] ?? '', ['superadmin', 'analyst']);
}

function can_access_report($report) {
    $allowed = explode(',', $_SESSION['user']['allowed_reports'] ?? '');
    return in_array($report, $allowed);
}
?>
