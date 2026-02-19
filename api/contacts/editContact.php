<?php

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
    // Get user from JWT token
    $user = getUserFromToken();

    // Get request data
    $data = json_decode(file_get_contents("php://input"));

    // Contact ID is required
    if (empty($data->id)) {
        http_response_code(400);
        echo json_encode(['error' => 'Contact ID is required']);
        exit();
    }

    // At least one field to update must be provided
    if (empty($data->name) && !isset($data->email) && !isset($data->phone)) {
        http_response_code(400);
        echo json_encode(['error' => 'At least one field (name, email, phone) must be provided to update']);
        exit();
    }

    // Connect to database
    $database = new Database();
    $db = $database->getConnection();

    // First, verify the contact exists AND belongs to this user
    $checkQuery = "SELECT id FROM contacts WHERE id = :id AND userId = :userId";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->bindParam(':id', $data->id);
    $checkStmt->bindParam(':userId', $user['userId']);
    $checkStmt->execute();

    if ($checkStmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Contact not found or unauthorized']);
        exit();
    }

    // Build update query dynamically based on which fields were provided
    $setClauses = [];
    $params = [];

    if (!empty($data->name)) {
        $setClauses[] = 'name = :name';
        $params[':name'] = $data->name;
    }

    if (isset($data->email)) {
        $setClauses[] = 'email = :email';
        $params[':email'] = $data->email;
    }

    if (isset($data->phone)) {
        $setClauses[] = 'phone = :phone';
        $params[':phone'] = $data->phone;
    }

    $query = "UPDATE contacts SET " . implode(', ', $setClauses) . " WHERE id = :id AND userId = :userId";
    $stmt = $db->prepare($query);

    // Bind dynamic params
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }

    // Always bind id and userId for the WHERE clause
    $stmt->bindParam(':id', $data->id);
    $stmt->bindParam(':userId', $user['userId']);

    if ($stmt->execute()) {
        // Fetch the updated contact to return it
        $fetchQuery = "SELECT id, name, email, phone, created_at FROM contacts WHERE id = :id AND userId = :userId";
        $fetchStmt = $db->prepare($fetchQuery);
        $fetchStmt->bindParam(':id', $data->id);
        $fetchStmt->bindParam(':userId', $user['userId']);
        $fetchStmt->execute();

        $updatedContact = $fetchStmt->fetch(PDO::FETCH_ASSOC);

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'contact' => $updatedContact
        ]);
    } else {
        throw new Exception('Failed to update contact');
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>
