<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'client') {
    echo json_encode(['status' => 'error', 'message' => 'unauthorized']);
    exit();
}

require_once __DIR__ . '/../DB/models/Facture.php';
require_once __DIR__ . '/../DB/models/Compteur.php';

// The client ID
$clientId = $_SESSION['user_id'];

$action = $_GET['action'] ?? null;
if (!$action) {
    echo json_encode(['status' => 'error', 'message' => 'no_action']);
    exit();
}

// 1) List compteurs
if ($action === 'list_compteurs') {
    $compteurs = Compteur::getByClientId($clientId);
    echo json_encode([
        'status'    => 'success',
        'compteurs' => $compteurs
    ]);
    exit();
}

// 2) List factures for a particular compteur
if ($action === 'list_factures') {
    $compteurId = $_GET['compteur_id'] ?? null;
    if (!$compteurId) {
        echo json_encode(['status' => 'error', 'message' => 'missing_compteur_id']);
        exit();
    }

    // You need a method to get all factures for that compteur
    // e.g. Facture::getAllByCompteur($clientId, $compteurId)
    $factures = Facture::getAllByCompteur($clientId, $compteurId);
    echo json_encode([
        'status'   => 'success',
        'factures' => $factures
    ]);
    exit();
}

// 3) get_facture_details
if ($action === 'get_facture_details') {
    $factureId = $_GET['facture_id'] ?? null;
    if (!$factureId) {
        echo json_encode(['status' => 'error', 'message' => 'missing_facture_id']);
        exit();
    }

    $row = Facture::getDetailById($factureId);
    if (!$row) {
        echo json_encode(['status' => 'error', 'message' => 'facture_not_found']);
        exit();
    }

    // If you want to compute Tva or difference, you can do so here
    $tvaCalc = $row['prix_ttc'] - $row['prix_ht'];

    echo json_encode([
        'status'           => 'success',
        'client_nom'       => $row['client_nom'],
        'client_address'   => $row['client_address'],
        'consommation_kwh' => $row['consommation_kwh'],
        'prix_ht'          => $row['prix_ht'],
        'tva_calculated'   => round($tvaCalc, 2),
        'prix_ttc'         => $row['prix_ttc'],
        'photo_path'       => $row['photo_compteur']
        // etc.
    ]);
    exit();
}

echo json_encode(['status' => 'error', 'message' => 'unknown_action']);
exit();
