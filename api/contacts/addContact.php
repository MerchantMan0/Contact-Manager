<?php
// api/addContact.php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

require_once __DIR__.'/../config/database.php';
require_once __DIR__.'/../utils/auth.php';

try {
    //Get user from token
    $user = getUserFromToken();
    
    //Get request data
    $data = json_decode(file_get_contents("php://input"));
    
    //Validate
    if (empty($data->name)) {
        http_response_code(400);
        echo json_encode(['error' => 'Name is required']);
        exit();
    }
    
    //Connect to database
    $database = new Database();
    $db = $database->getConnection();
    
    //Insert contact
    $query = "INSERT INTO contacts (userId, name, email, phone, createdAt) VALUES (:userId, :name, :email, :phone, :createdAt)";
    $stmt = $db->prepare($query);
    error_log($data->createdAt);
    
    $stmt->bindParam(':userId', $user['userId']);
    $stmt->bindParam(':name', $data->name);
    $stmt->bindParam(':email', $data->email);
    $stmt->bindParam(':phone', $data->phone);
    // $data->createdAt is already taken?
    $stmt->bindParam(':createdAt', $data->createdAt);
    
    if ($stmt->execute()) {
        $contactId = $db->lastInsertId();
        
        http_response_code(201);
        echo json_encode([
            'success' => true,
            'contact' => [
                'id' => $contactId,
                'name' => $data->name,
                'email' => $data->email,
                'phone' => $data->phone,
		'createdAt' => $data->createdAt
            ]
        ]);
    } else {
        throw new Exception('Failed to create contact');
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>
