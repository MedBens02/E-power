<?php
session_start();
header('Content-Type: application/json');

// Must be a client
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'client') {
    echo json_encode(['status' => 'error', 'message' => 'not_authorized']);
    exit();
}

require_once __DIR__ . '/../DB/models/Compteur.php';
require_once __DIR__ . '/../DB/models/ConsommationMensuelle.php';
require_once __DIR__ . '/../DB/models/Tarif.php';
require_once __DIR__ . '/../DB/models/Facture.php';

// The client ID from session
$clientId = $_SESSION['user_id'];

// 1) Check 'action'
$action = $_GET['action'] ?? ($_POST['action'] ?? null);
if (!$action) {
    echo json_encode(['status' => 'error', 'message' => 'no_action']);
    exit();
}

// --------------------------------------
// 2) List compteurs
// --------------------------------------
if ($action === 'list_compteurs') {
    $compteurs = Compteur::getByClientId($clientId);
    echo json_encode([
        'status' => 'success',
        'compteurs' => $compteurs
    ]);
    exit();
}

// --------------------------------------
// 3) Last-month data
// --------------------------------------
if ($action === 'last_month_data') {
    $compteurId = $_GET['compteur_id'] ?? null;
    if (!$compteurId) {
        echo json_encode(['status' => 'error', 'message' => 'compteur_id_missing']);
        exit();
    }
    // fetch last month record
    $lastMonthRow = ConsommationMensuelle::getLastMonthData($clientId, $compteurId);
    $total = Compteur::getTotalConsumption($compteurId);

    echo json_encode([
        'status' => 'success',
        'lastMonthValue' => $lastMonthRow['valeur_compteur'] ?? 0,
        'previousPhoto' => $lastMonthRow['photo_compteur'] ?? null,
        'totalConsumption' => $total
    ]);
    exit();
}

// --------------------------------------
// 4) Save monthly consumption
// --------------------------------------
if ($action === 'save_monthly_consumption') {
    // We'll read from $_POST and $_FILES
    $compteurId   = $_POST['compteur_id']   ?? null;
    $currentValue = (int)($_POST['currentValue'] ?? 0);
    $difference   = (int)($_POST['difference']   ?? 0);

    // If file is uploaded
    $uploadedPhotoPath = null;
    if (!empty($_FILES['photo']['name'])) {
        // Move to some folder, e.g. 'uploads/'
        $tmpName = $_FILES['photo']['tmp_name'];
        $fileName = time() . '_' . $_FILES['photo']['name'];
        $destination = __DIR__ . '/../Uploads/' . $fileName;
        move_uploaded_file($tmpName, $destination);

        // We'll store just the filename in DB
        $uploadedPhotoPath = $fileName;
    }

    // 1) Insert the new consommation row
    $newId = ConsommationMensuelle::create(
        $clientId,
        $compteurId,
        $difference,
        $uploadedPhotoPath
    );
    if (!$newId) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'db_insert_failed'
        ]);
        exit();
    }

    // 2) Update compteur total
    Compteur::updateConsumption($compteurId, $difference);

    // 3) If you want to create a Facture with front-end computed prices:
    $prixHt  = $_POST['prix_ht']  ?? null;

    // If for some reason those values aren't provided, you can skip or do a server calc
    if ($prixHt !== null) {
        // Insert the Facture referencing the new consumption
        // Notice we use `$newId` as the consommation ID
        $factureId = Facture::createDirectValues(
            $clientId,
            $newId,
            $prixHt
        );
        if (!$factureId) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Erreur création facture'
            ]);
            exit();
        }
        // Return the factureId if needed
        echo json_encode([
            'status'    => 'success',
            'message'   => 'Consommation et facture créées avec succès.',
            'factureId' => $factureId
        ]);
        exit();
    }

    // Otherwise, if no direct price, or you're handling invoice creation differently
    // then just return success for the consumption part
    echo json_encode([
        'status'  => 'success',
        'message' => 'Consommation créée (pas de facture).'
    ]);
    exit();
}


if ($action === 'compute_tarif') {
    $difference = $_GET['difference'] ?? null;
    if ($difference === null) {
        echo json_encode(['status' => 'error', 'message' => 'missing_difference']);
        exit();
    }

    $difference = (int)$difference;
    if ($difference < 0) {
        echo json_encode(['status' => 'error', 'message' => 'invalid_difference']);
        exit();
    }

    // Use the Tarif model
    $tarifRow = Tarif::getTarifForConsumption($difference);
    if (!$tarifRow) {
        echo json_encode(['status' => 'error', 'message' => 'no_tarif_found']);
        exit();
    }

    // Return success with the info
    echo json_encode([
        'status' => 'success',
        'unit_price' => $tarifRow['unit_price'],
        'tranche_label' => $tarifRow['tranche_label']
    ]);
    exit();
}

echo json_encode(['status' => 'error', 'message' => 'unknown_action']);
exit();
