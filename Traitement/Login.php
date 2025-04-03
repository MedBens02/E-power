<?php
session_start();

// Return JSON responses
header('Content-Type: application/json');

// 1) Get raw POST body (JSON)
$rawData = file_get_contents('php://input');
$data = json_decode($rawData, true);

// 2) Validate presence of fields
if (!isset($data['email'], $data['password'], $data['type'])) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'missing_fields'
    ]);
    exit();
}

$email    = $data['email'];
$password = $data['password'];
$type     = $data['type'];

// 3) Load models (Admin, Client)
require_once __DIR__ . '/../DB/models/Admin.php';
require_once __DIR__ . '/../DB/models/Client.php';

if ($type === 'admin') {
    $admin = Admin::login($email, $password);
    if ($admin) {
        $_SESSION['user_type'] = 'admin';
        $_SESSION['user_id']   = $admin->getId();
        $_SESSION['user_name'] = $admin->getNom() . ' ' . $admin->getPrenom();

        // Return a JSON success with redirect path
        echo json_encode([
            'status'   => 'success',
            'redirect' => '../IHM/tableau_bord_admin.php'
        ]);
        exit();
    } else {
        echo json_encode([
            'status'  => 'error',
            'message' => 'invalid_credentials'
        ]);
        exit();
    }
} else {
    // Client by default
    $client = Client::login($email, $password);
    if ($client) {
        $_SESSION['user_type'] = 'client';
        $_SESSION['user_id']   = $client->getId();
        $_SESSION['user_name'] = $client->getNom() . ' ' . $client->getPrenom();

        echo json_encode([
            'status'   => 'success',
            'redirect' => '../IHM/ClientDashboard.php'
        ]);
        exit();
    } else {
        echo json_encode([
            'status'  => 'error',
            'message' => 'invalid_credentials'
        ]);
        exit();
    }
}
