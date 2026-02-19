<?php
// api/contacts/deleteContact.php

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
    $user = getUserFromToken();
    $data = json_decode(file_get_contents("php://input"));
    
    if (empty($data->id)) {
        http_response_code(400);
        echo json_encode(['error' => 'Contact ID is required']);
        exit();
    }
    
    $database = new Database();
    $db = $database->getConnection();
    
    //Make sure to nclude userId in WHERE, so users can only delete their own contacts
    $query = "DELETE FROM contacts WHERE id = :id AND userId = :userId";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $data->id);
    $stmt->bindParam(':userId', $user['userId']);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Contact deleted']);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Contact not found or unauthorized']);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>
