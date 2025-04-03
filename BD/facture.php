
<?php
require_once('connexion.php');

function getFactureByClientId($client_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM factures WHERE client_id = ?");
    $stmt->execute([$client_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function updateFactureMontant($id, $nouveau_prix) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE factures SET prix_ht = ? WHERE id = ?");
    return $stmt->execute([$nouveau_prix, $id]);
}


function getFactureActuelleByClient($client_id) {
    // Récupère la dernière facture du client (ex : la plus récente)
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT * 
        FROM factures
        WHERE client_id = ?
        ORDER BY annee DESC, mois DESC
        LIMIT 1
    ");
    $stmt->execute([$client_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}


function annulerConsommation($clientId) {
    global $pdo;
    // Supprime la dernière consommation mensuelle liée au client
    $stmt = $pdo->prepare("
        DELETE FROM consommations_mensuelles
        WHERE id = (
            SELECT cm.id
            FROM consommations_mensuelles cm
            WHERE cm.client_id = ?
            ORDER BY cm.annee DESC, cm.mois DESC, cm.id DESC
            LIMIT 1
        )
    ");
    $stmt->execute([$clientId]);
}


function supprimerDerniereFacture($clientId) {
    global $pdo;
    // Supprime la dernière facture associée au client
    $stmt = $pdo->prepare("
        DELETE FROM factures
        WHERE id = (
            SELECT f.id
            FROM factures f
            WHERE f.client_id = ?
            ORDER BY f.annee DESC, f.mois DESC, f.id DESC
            LIMIT 1
        )
    ");
    $stmt->execute([$clientId]);
}


?>
