<?php
session_start();
header('Content-Type: application/json');

// Must be a client
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'client') {
    echo json_encode(['status' => 'error', 'message' => 'unauthorized']);
    exit();
}

require_once __DIR__ . '/../DB/models/Reclamation.php';
require_once __DIR__ . '/../DB/models/Compteur.php';
require_once __DIR__ . '/../DB/models/Facture.php';

$clientId = $_SESSION['user_id'];

$action = $_GET['action'] ?? ($_POST['action'] ?? null);
if (!$action) {
    echo json_encode(['status' => 'error', 'message' => 'no_action']);
    exit();
}

// 1) list_reclamations
if ($action === 'list_reclamations') {
    $recs = Reclamation::getAllByClient($clientId);
    echo json_encode([
        'status' => 'success',
        'reclamations' => $recs
    ]);
    exit();
}

// 2) create_reclamation
if ($action === 'create_reclamation') {
    $type        = $_POST['type'] ?? '';
    $description = $_POST['description'] ?? '';

    $compteurId  = $_POST['compteur_id'] ?? null;
    $factureId   = $_POST['facture_id']  ?? null;

    // Convert empty strings to null
    if (!$compteurId) {
        $compteurId = null;
    }
    if (!$factureId) {
        $factureId = null;
    }

    // For "Fuite externe" or "Fuite interne", a compteur_id is required.
    if (($type === 'Fuite externe' || $type === 'Fuite interne') && !$compteurId) {
        echo json_encode(['status' => 'error', 'message' => 'compteur_id_required']);
        exit();
    }

    // Insert the reclamation
    $recId = Reclamation::create($clientId, $type, $description, $compteurId, $factureId);
    if (!$recId) {
        echo json_encode(['status' => 'error', 'message' => 'create_failed']);
        exit();
    }
    echo json_encode(['status' => 'success', 'reclamationId' => $recId]);
    exit();
}


// 3) list_compteurs
if ($action === 'list_compteurs') {
    $compteurs = Compteur::getByClientId($clientId);
    echo json_encode([
        'status' => 'success',
        'compteurs' => $compteurs
    ]);
    exit();
}

// 4) list_factures
if ($action === 'list_factures_by_compteur') {
    // We expect ?compteur_id=XYZ
    $compteurId = $_GET['compteur_id'] ?? null;
    if (!$compteurId) {
        echo json_encode(['status' => 'error', 'message' => 'missing_compteur_id']);
        exit();
    }

    // Use a method that fetches factures for this client & compteur
    $factures = Facture::getAllByCompteur($clientId, $compteurId);
    echo json_encode([
        'status' => 'success',
        'factures' => $factures
    ]);
    exit();
}

echo json_encode(['status' => 'error', 'message' => 'unknown_action']);
exit();
