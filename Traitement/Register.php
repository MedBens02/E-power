<?php
session_start();

// Return JSON
header('Content-Type: application/json');

require_once __DIR__ . '/../DB/models/Admin.php';
require_once __DIR__ . '/../DB/models/Client.php';

// Grab raw JSON from the request
$rawData = file_get_contents('php://input');
$data = json_decode($rawData, true);

if (!$data) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'invalid_request'
    ]);
    exit();
}

// Extract fields
$nom      = $data['nom']     ?? '';
$prenom   = $data['prenom']  ?? '';
$email    = $data['email']   ?? '';
$password = $data['password'] ?? '';
$type     = $data['type']    ?? 'client';

// Basic checks
if (empty($nom) || empty($prenom) || empty($email) || empty($password)) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'missing_fields'
    ]);
    exit();
}

// (Optional) check email format more strictly
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'invalid_email_format'
    ]);
    exit();
}

// Register
if ($type === 'admin') {
    $adminId = Admin::register($nom, $prenom, $email, $password);
    if ($adminId) {
        // success
        echo json_encode([
            'status'   => 'success',
            'message'  => 'registered_admin',
            'redirect' => 'login.php'
        ]);
        exit();
    } else {
        // failed
        echo json_encode([
            'status'  => 'error',
            'message' => 'registration_failed'
        ]);
        exit();
    }
} else {
    // client
    $clientId = Client::register($nom, $prenom, $email, $password);
    if ($clientId) {
        echo json_encode([
            'status'   => 'success',
            'message'  => 'registered_client',
            'redirect' => 'login.php'
        ]);
        exit();
    } else {
        echo json_encode([
            'status'  => 'error',
            'message' => 'registration_failed'
        ]);
        exit();
    }
}
