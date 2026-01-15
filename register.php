<?php
include 'config/db_connection.php';
include 'user.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $database = new Database();
  $db = $database->getConnection();
  $user = new User($db);

  $data = json_decode(file_get_contents("php://input"));

  if ($data === null) {
    http_response_code(400);
    echo json_encode(['message' => 'Invalid JSON']);
    exit;
  }

  if (!empty($data->username) && !empty($data->email) && !empty($data->password)) {
    $user->username = $data->username;
    $user->email = $data->email;
    $user->password = $data->password;

    if ($user->emailExists()) {
      http_response_code(400);
      echo json_encode(['message' => 'Email already exists']);
    } else if ($user->register()) {
      http_response_code(201);
      echo json_encode(['message' => 'User registered successfully']);
    } else {
      http_response_code(500);
      echo json_encode(['message' => 'Unable to register user']);
    }
  } else {
    http_response_code(400);
    echo json_encode(['message' => 'Incomplete data']);
  }
}