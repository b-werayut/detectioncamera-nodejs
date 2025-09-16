<?php 
include 'config/db_connection.php';

if(isset($_POST['username'])){

    $username = $_POST['username'];
    $password = $_POST['password'];
    $passwordenc = password_hash($password, PASSWORD_DEFAULT);
    $email = $_POST['email'];
    $role = 'ADMIN';
    $active = 1;
    $dateNow = date('Y-m-d H:i:s');
    
    
    try{
    $query = $conn->prepare(" SELECT * FROM Users WHERE username = ? ");
    $query->execute(array($username));
    $rows =  $query->fetch(PDO::FETCH_ASSOC);

    $queryEmail = $conn->prepare(" SELECT * FROM Users WHERE email = ? ");
    $queryEmail->execute(array($email));
    $rowsemail =  $queryEmail->fetch(PDO::FETCH_ASSOC);

    $usernamedb = $rows['username']??NULL;
    $emaildb = $rowsemail['email']??NULL;

        if($username == $usernamedb){
            echo 1;
            return;
        }else if($email == $emaildb){
            echo 4;
            return;
        }else{
        $query = $conn->prepare(" INSERT INTO Users (username, password, email, role, isActive, updatedAt) VALUES (?, ?, ?, ?, ?, ?) ");
        $query->execute(array($username, $passwordenc, $email, $role, $active, $dateNow));

        if($query->rowCount()){
            echo 2;
             return;
        }else{
            echo 3;
             return;
        }
         return;
    }
    } catch(PDOException $e){
        echo 'Error'.$e->getMessage();
    }
}else{
    header("location: /login.php");
}
    
$conn=null;