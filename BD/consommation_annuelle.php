<?php
require_once('connexion.php');

function importerConsommationAnnuelle($filePath) {
    global $pdo;
    $handle = fopen($filePath, "r");
    if (!$handle) return 0;

    $count = 0;
    while (($line = fgets($handle)) !== false) {
        $line = trim($line);
        if ($line === '') continue;

        // Exemple: client_id,agent_id,consommation,annee,date_saisie
        $parts = explode(',', $line);
        if (count($parts) < 5) {
            // ligne invalide
            continue;
        }

        list($clientId, $agentId, $consommation, $annee, $dateSaisie) = $parts;

        // Insertion en base
        $stmt = $pdo->prepare("
            INSERT INTO consommations_annuelles 
            (client_id, agent_id, annee, consommation, date_saisie)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$clientId, $agentId, $annee, $consommation, $dateSaisie]);
        $count++;
    }
    fclose($handle);
    return $count;
}

function getAllConsommationsAnnuelles() {
    global $pdo;
    $stmt = $pdo->query("
        SELECT * 
        FROM consommations_annuelles
        ORDER BY annee DESC, id DESC
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
