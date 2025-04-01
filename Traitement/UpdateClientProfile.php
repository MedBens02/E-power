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
require_once __DIR__ . '/../DB/models/User.php'; // for isEmailUsed

// Read the raw JSON from fetch
$rawData = file_get_contents('php://input');
$data = json_decode($rawData, true);

if (!$data || !isset($data['action'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'invalid_request'
    ]);
    exit();
}

$action   = $data['action'];
$clientId = $_SESSION['user_id']; // we trust session ID, ignoring any posted 'client_id'

// ===========================
// 1. Updating Profile
// ===========================
if ($action === 'update_profile') {
    $nom     = $data['nom']     ?? '';
    $prenom  = $data['prenom']  ?? '';
    $adresse = $data['adresse'] ?? '';
    $email   = $data['email']   ?? '';

    // Basic validations
    if (empty($nom) || empty($prenom) || empty($adresse) || empty($email)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'missing_fields'
        ]);
        exit();
    }

    // Check email format if you want
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'invalid_email_format'
        ]);
        exit();
    }

    // Check if email used by another user
    if (User::isEmailUsed($email, $clientId, 'client')) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'email_already_used'
        ]);
        exit();
    }

    $updated = Client::updateProfile($clientId, $nom, $prenom, $adresse, $email);
    if ($updated) {
        echo json_encode([
            'status'  => 'success',
            'message' => 'Profil mis à jour avec succès.'
        ]);
        exit();
    } else {
        echo json_encode([
            'status'  => 'error',
            'message' => 'update_failed'
        ]);
        exit();
    }
}

// ===========================
// 2. Updating Password
// ===========================
if ($action === 'update_password') {
    $oldPassword = $data['old_password'] ?? '';
    $newPassword = $data['new_password'] ?? '';

    if (empty($oldPassword) || empty($newPassword)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'missing_password_fields'
        ]);
        exit();
    }

    // If you want to forbid reusing old password:
    if ($oldPassword === $newPassword) {
        echo json_encode([
            'status' => 'error',
            'message' => 'same_as_old'
        ]);
        exit();
    }

    $updatedPass = Client::updatePassword($clientId, $oldPassword, $newPassword);
    if ($updatedPass) {
        echo json_encode([
            'status'  => 'success',
            'message' => 'Mot de passe mis à jour avec succès.'
        ]);
        exit();
    } else {
        echo json_encode([
            'status'  => 'error',
            'message' => 'wrong_old_password'
        ]);
        exit();
    }
}

// If none of the recognized actions
echo json_encode([
    'status' => 'error',
    'message' => 'unknown_action'
]);
exit();
