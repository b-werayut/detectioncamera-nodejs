<?php
include 'config/db_connection.php';

if(isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['passwordcf'])) {

    $postusername = $_POST['username'];
    $postemail = $_POST['email'];
    $password = $_POST['password'];
    $passwordcf = $_POST['passwordcf'];

    if($password !== $passwordcf){
        echo 0;
        return;
    }

    $passwordenc = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("SELECT * FROM Users WHERE username = ?");
    $stmt->execute(array($postusername));

    if($stmt->rowCount()){
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $usernamedb = $row['username'];
        $emaildb = $row['email'];

        if($postusername !== $usernamedb){
            echo 1;
            return;
        }else if($postemail !== $emaildb){
            echo 2;
            return;
        }

        $updateStmt = $conn->prepare("UPDATE Users SET password = ? WHERE username = ?");
        if($updateStmt->execute(array($passwordenc, $postusername))){
            echo 3; 
        } else {
            echo 4; 
        }
    } else {
        echo 6; 
    }

} else {
    echo 4; 
}
