<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Authroization');

//Success (OK)
if($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

//Only POST requests
if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

require_once 'config/database.php';
require_once 'utils/jwt.php';

//Login logic
try {
    $data = json_decode(file_get_contents("php://input"));

    //Check if username and password were provided
    if(empty($data->username) || empty($data->password)) {
        http_response_code(400);
        echo json_encode(["error"=> "Username and password are required"]);
        exit();
    }

    $database = new Database();
    $db = $database->getConnection();

    //Find user by username
    $query = "SELECT id, userFirstName, userLastName, username, password FROM users WHERE username = :username LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':username', $data->username);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    //Check if uesr exists (and if password matches)
    if(!password_verify($data->password, $user['password'])) {
        http_response_code(401);
        echo json_encode(['error'=> 'Invalid username or password']);
        exit();
    }

    //Create JWT token
    $token = JWT::encode([
        'userId' => $user['id'],
        'username' => $user['username']
    ]);

    http_response_code(200);
    echo json_encode([
        'success' => true,
        'token'=> $token,
        'user' => [
            'id'=> $user['id'],
            'firstName' => $user['userFirstName'],
            'lastName' => $user['userLastName'],
            'username' => $user['username']
            ]
        ]);

} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>
