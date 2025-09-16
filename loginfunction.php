<?php
session_start(); 
include 'config/db_connection.php';

if(isset($_POST['username']) && isset($_POST['password'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
        $query = $conn->prepare("SELECT * FROM Users WHERE username = ?");
        $query->execute([$username]);

        if($query->rowCount()){
            $row = $query->fetch(PDO::FETCH_ASSOC);

            $passworddb = $row['password']; 
            $role = $row['role'];

            if(password_verify($password, $passworddb)) {

                $_SESSION['username'] = $row['username'];
                $_SESSION['role'] = $role;
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
    header("Location: login.php");
    exit();
}
