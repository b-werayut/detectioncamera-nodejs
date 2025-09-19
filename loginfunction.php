<?php
session_start();
include 'config/db_connection.php';

if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
        $query = $conn->prepare("SELECT * FROM Users WHERE username = ?");
        $query->execute([$username]);

        if ($query->rowCount()) {
            $row = $query->fetch(PDO::FETCH_ASSOC);

            $passworddb = $row['password'];
            $role = $row['role'];

            if (password_verify($password, $passworddb)) {

                $_SESSION['username'] = $row['username'];
                $_SESSION['role'] = $role;
                $_SESSION['login_time'] = time();
                $_SESSION['timeout'] = 60 * 1;
                $_SESSION['success'] = "<div>Login Success</div>";
                echo 1;
                // if($role === 'ADMIN'){
                //     echo 0;
                // } else {
                //     echo 1;
                // }

            } else {
                echo 3;
            }

        } else {
            echo 2;
        }

    } catch (PDOException $e) {
        echo 'เกิดข้อผิดพลาด: ' . $e->getMessage();
    }

} else {
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    header("Expires: Sat, 01 Jan 2000 00:00:00 GMT");
    header("Location: login.php?v=1");
    exit();
}
