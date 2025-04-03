<?php
require_once('connexion.php');

function getTotalClients() {
    global $pdo;
    $stmt = $pdo->query("SELECT COUNT(*) AS total FROM clients");
    return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
}

function getTotalFactures() {
    global $pdo;
    $stmt = $pdo->query("SELECT COUNT(*) AS total FROM factures");
    return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
}

function getTotalConsommation() {
    global $pdo;
    $stmt = $pdo->query("SELECT SUM(valeur_compteur) AS total FROM consommations_mensuelles");
    return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
}

function getReclamationsByType() {
    global $pdo;
    $stmt = $pdo->query("
        SELECT type, COUNT(*) AS total
        FROM reclamations
        GROUP BY type
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getStatistiquesPaiement() {
    global $pdo;
    $stmt = $pdo->query("
        SELECT statut_paiement, COUNT(*) AS total
        FROM factures
        GROUP BY statut_paiement
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
