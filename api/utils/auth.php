<?php

require_once __DIR__ . '/jwt.php';


//Gets user information from JWT token in Authorization header
//Automatically sends 401 error and exits if token is invalid

function getUserFromToken() {
    
    $headers = getallheaders();
    
    //check if auth header exists
    if (!isset($headers['Authorization'])) {

        http_response_code(401);
        echo json_encode(['error' => 'No authorization token provided']);
        exit();
    }
    
    //extract token ("bearer ...")
    $authHeader = $headers['Authorization'];
    
    if (stripos($authHeader, 'Bearer ') === 0) {
        $token = substr($authHeader, 7);
    } else {
        
        http_response_code(401);
        echo json_encode(['error' => 'Invalid authorization header format']);
        exit();
    }
    
    //verify and decode
    $payload = JWT::decode($token);
    
    //check if token is valid
    if (!$payload) {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid or expired token']);
        exit();
    }
    
    //return user info based on token
    return $payload;
}
?>