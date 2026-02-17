<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

require_once 'config/database.php';
require_once 'utils/jwt.php';

try {
    $data = json_decode(file_get_contents("php://input"));

    //Make sure all fields are provided
    if(empty($data->firstName) || empty($data->lastName) || empty($data->email) || empty($data->password)) {
        http_response_code(400);
        echo json_encode((['error' => 'All fields are required']));
        exit();
    }

    //Validate email
    if(!filter_var($data->email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode((['error'=> 'Invalid email format']));
        exit();
    }

    //Connect to database
    $database = new Database();
    $db = $database->getConnection();

    //Check if email already exists
    $checkQuery = "SELECT id FROM users WHERE email :email";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->bindParam(':email', $data->email);
    $checkStmt->execute();

    if($checkStmt->rowCount() > 0) {
        http_response_code(409);
        echo json_encode((['error'=> 'Email already registered']));
        exit();
    }

    //Hash password
    $hashedPassword = password_hash($data->password, PASSWORD_BCRYPT);

    //Insert new user
    $query = "INSERT INTO users (firstName, lastName, email, password) VALUES (:firstName, :lastName, :email, :password)";
    $stmt = $db->prepare($query);

    $stmt->bindParam('firstName', $data->firstName);
    $stmt->bindParam('lastName', $data->lastName);
    $stmt->bindParam(':email', $data->email);
    $stmt->bindParam('password', $hashedPassword);

    if($stmt->execute()) {
        $userID = $db->lastInsertId();

        //Generate token
        $token = JWT::encode([
            'userId' => $userId,
            'email' => $data->email
        ]);

        http_response_code(201);
        echo json_encode([
            'success' => true,
            'token' => $token,
            'user' => [
                'id' => $userId,
                'firstName' => $data->firstName,
                'lastName' => $data->lastName,
                'email' => $data->email
            ]
        ]);
    } else {
        throw new Exception('Failed to make user');
        }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error'=> $e->getMessage()]);
}
?>