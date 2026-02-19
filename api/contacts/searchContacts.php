<?php
// api/contacts/searchContacts.php

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
    
    //Get search
    $data = json_decode(file_get_contents("php://input"));
    
    if (empty($data->search)) {
        http_response_code(400);
        echo json_encode(['error' => 'Search term is required']);
        exit();
    }
    
    //Connect to database
    $database = new Database();
    $db = $database->getConnection();
    
    //Search contacts (with partial match)
    $searchTerm = '%' . $data->search . '%';
    $query = "SELECT id, name, email, phone, created_at 
              FROM contacts 
              WHERE userId = :userId 
              AND (name LIKE :search OR email LIKE :search OR phone LIKE :search)
              ORDER BY name ASC";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':userId', $user['userId']);
    $stmt->bindParam(':search', $searchTerm);
    $stmt->execute();
    
    $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'contacts' => $contacts
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>
