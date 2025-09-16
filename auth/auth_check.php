<?php
$timeout = 60 * 1;

$role = $_SESSION['role'] ?? null;
$auth = $_SESSION['auth'] ?? null;

if (empty($role) && empty($auth)) {
    header("Location: ../login.php");
    exit();
}

if (isset($_SESSION['login_time']) && time() - $_SESSION['login_time'] > $timeout) {
    session_unset();
    session_destroy();
    header("Location: ../login.php?expired=1");
    exit();
}
