<?php
include 'config/db_connection.php';
class User
{
  private $conn;
  private $table = 'users';

  public $id;
  public $username;
  public $email;
  public $password;

  public function __construct($db)
  {
    $this->conn = $db;
  }

  public function register()
  {
    $query = "INSERT INTO " . $this->table . " 
                  SET username = :username, 
                      email = :email, 
                      password = :password";

    $stmt = $this->conn->prepare($query);

    $this->username = htmlspecialchars(strip_tags($this->username));
    $this->email = htmlspecialchars(strip_tags($this->email));
    $this->password = password_hash($this->password, PASSWORD_BCRYPT);

    $stmt->bindParam(':username', $this->username);
    $stmt->bindParam(':email', $this->email);
    $stmt->bindParam(':password', $this->password);

    if ($stmt->execute()) {
      return true;
    }

    return false;
  }

  public function login()
  {
    $query = "SELECT id, username, email, password 
                  FROM " . $this->table . " 
                  WHERE email = :email 
                  LIMIT 1";

    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':email', $this->email);
    $stmt->execute();

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && password_verify($this->password, $row['password'])) {
      $this->id = $row['id'];
      $this->username = $row['username'];
      $this->email = $row['email'];
      return true;
    }

    return false;
  }

  public function emailExists()
  {
    $query = "SELECT id FROM " . $this->table . " 
                  WHERE email = :email 
                  LIMIT 1";

    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':email', $this->email);
    $stmt->execute();

    return $stmt->rowCount() > 0;
  }
}