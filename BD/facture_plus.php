<?php
require_once('connexion.php');


/**
 * Calcule le prix HT selon l'écart en kWh.
 * Logique similaire à ce que tu fais dans factures / triggers
 */
function calculPrixEcart($ecart) {
    // Ex. tu appliques la tranche max : 1.10
    // ou tu fais un "SELECT prix_unitaire FROM tarifs WHERE $ecart BETWEEN tranche_min AND tranche_max"
    // Pour simplifier, appliquons la plus haute tranche (1.10)
    return $ecart * 1.10;
}

/**
 * Crée une facture_plus dans la table `facture_plus`.
 * Retourne l'ID inséré.
 */
function creerFacturePlus($clientId, $annee, $ecart, $prixHt) {
    global $pdo;
    $stmt = $pdo->prepare("
        INSERT INTO facture_plus
        (client_id, annee, ecart, prix_ht)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([$clientId, $annee, $ecart, $prixHt]);
    return $pdo->lastInsertId();
}

/**
 * Récupère toutes les facture_plus d'un client, ou toutes.
 */
function getAllFacturePlus() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM facture_plus ORDER BY date_creation DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getFacturePlusByClient($clientId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM facture_plus WHERE client_id = ? ORDER BY date_creation DESC");
    $stmt->execute([$clientId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function existeFacturePlus($clientId, $annee) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM facture_plus
        WHERE client_id = ? 
          AND annee = ?
    ");
    $stmt->execute([$clientId, $annee]);
    $count = $stmt->fetchColumn();
    return $count > 0; // true si 1 ou +, false sinon
}
