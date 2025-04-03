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

        try {
            // Vérifier si la consommation annuelle existe déjà pour ce client et cette année
            $checkStmt = $pdo->prepare("
                SELECT COUNT(*) FROM consommations_annuelles 
                WHERE client_id = ? AND annee = ?
            ");
            $checkStmt->execute([$clientId, $annee]);
            $exists = $checkStmt->fetchColumn();

            if ($exists) {
                // Faire un update si l'enregistrement existe déjà
                $stmt = $pdo->prepare("
                    UPDATE consommations_annuelles 
                    SET agent_id = ?, consommation = ?, date_saisie = ? 
                    WHERE client_id = ? AND annee = ?
                ");
                $stmt->execute([$agentId, $consommation, $dateSaisie, $clientId, $annee]);
            } else {
                // Faire un insert si l'enregistrement n'existe pas
                $stmt = $pdo->prepare("
                    INSERT INTO consommations_annuelles 
                    (client_id, agent_id, annee, consommation, date_saisie)
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmt->execute([$clientId, $agentId, $annee, $consommation, $dateSaisie]);
            }
            $count++;
        } catch (Exception $e) {
            // Gestion des erreurs
            error_log("Erreur lors de l'importation de la ligne : " . $e->getMessage());
            continue;
        }
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


