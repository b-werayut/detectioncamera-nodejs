<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['UserId'])) {
    
    $timeout_duration = 3600; 

    if (isset($_SESSION['LAST_ACTIVITY'])) {
        if ((time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
            session_unset();
            session_destroy();
            header("Location: /login.php?timeout=1"); 
            exit();
        }
    }
    
    $_SESSION['LAST_ACTIVITY'] = time();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= (isset($pageTitle) && !empty($pageTitle)) ? $pageTitle . ' : Management System' : 'Management System' ?></title>
    <link rel="icon" type="image/x-icon" href="/assets/favicon/favicon.ico" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./css/styles.css"> 
</head>
<body>
    <button class="btn btn-green text-white shadow d-md-none mobile-toggle position-fixed m-3" 
            style="z-index: 1001; top:0; left:0; display:none;" 
            onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>