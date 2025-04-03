
<?php
require_once('connexion.php');

function getDernieresConsommations($clientId, $limit = 2) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT * 
        FROM consommations_mensuelles
        WHERE compteur_id = ?
        ORDER BY annee DESC, mois DESC, id DESC
        LIMIT ?
    ");
    $stmt->bindValue(1, $clientId, PDO::PARAM_INT);
    $stmt->bindValue(2, $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function getCompteurByClient($clientId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM compteurs WHERE client_id = ? LIMIT 1");
    $stmt->execute([$clientId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}


function updateCompteurTotal($compteurId, $newVal) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE compteurs SET consommation_totale = ? WHERE id = ?");
    $stmt->execute([$newVal, $compteurId]);
}


function getDerniereConso($clientId) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT * 
        FROM consommations_mensuelles
        WHERE client_id = ?
        ORDER BY annee DESC, mois DESC, id DESC
        LIMIT 1
    ");
    $stmt->execute([$clientId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}


function updateConsommation($consoId, $newValeur) {
    global $pdo;
    $stmt = $pdo->prepare("
        UPDATE consommations_mensuelles
        SET valeur_compteur = ?
        WHERE id = ?
    ");
    $stmt->execute([$newValeur, $consoId]);
}


function getFactureByConsoId($consoId) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT * 
        FROM factures
        WHERE consommation_id = ?
        ORDER BY id DESC
        LIMIT 1
    ");
    $stmt->execute([$consoId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}


function recalculerPrixHT($valeurCompteur) {
    global $pdo;
    // Chercher la tranche
    $stmt = $pdo->query("
        SELECT prix_unitaire
        FROM tarifs
        WHERE $valeurCompteur BETWEEN tranche_min AND tranche_max
        LIMIT 1
    ");
    $tarif = $stmt->fetch(PDO::FETCH_ASSOC);
    $prixUnitaire = $tarif ? $tarif['prix_unitaire'] : 1.00;
    return $valeurCompteur * $prixUnitaire;
}


function updateFacturePrix($factureId, $prixHT) {
    global $pdo;
    $stmt = $pdo->prepare("
        UPDATE factures
        SET prix_ht = ?
        WHERE id = ?
    ");
    $stmt->execute([$prixHT, $factureId]);
}
