<?php
// api/contacts/getContacts.php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__.'/../config/database.php';
require_once __DIR__.'/../utils/auth.php';

try {
    //Get user from token
    $user = getUserFromToken();
    
    //Connect to database
    $database = new Database();
    $db = $database->getConnection();
    
    //Get all contacts for user
    $query = "SELECT id, name, email, phone, createdAt FROM contacts WHERE userId = :userId ORDER BY name ASC";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':userId', $user['userId']);
    $stmt->execute();
    
    $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    //Return contacts
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
