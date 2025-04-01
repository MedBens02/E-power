<?php
session_start();
header('Content-Type: application/json');

// Must be a client
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'client') {
    echo json_encode([
        'status' => 'error',
        'message' => 'not_authorized'
    ]);
    exit();
}

require_once __DIR__ . '/../DB/models/Client.php';

// The client ID is stored in session
$clientId = $_SESSION['user_id'];

// 1) Fetch the client data
$clientData = Client::getById($clientId);
if (!$clientData) {
    echo json_encode([
        'status' => 'error',
        'message' => 'client_not_found'
    ]);
    exit();
}

// 2) Fetch consumption history (if you need it)
$consumptionHistory = Client::getAnnualConsumption($clientId);

// Return all data as JSON
echo json_encode([
    'status' => 'success',
    'client' => $clientData,
    'history' => $consumptionHistory
]);
exit();
