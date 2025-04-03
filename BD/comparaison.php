<?php
require_once('facture_plus.php'); // pour appeler existeFacturePlus()

function getTableauComparaison($annee) {
    global $pdo;

    $sql = "
      SELECT ca.client_id, c.nom, c.prenom, ca.annee, ca.consommation AS conso_agent
      FROM consommations_annuelles ca
      JOIN clients c ON c.id = ca.client_id
      WHERE ca.annee = :annee
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['annee' => $annee]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $result = [];

    foreach ($rows as $row) {
        $clientId = $row['client_id'];

        // Récupérer la somme mensuelle
        $sqlMensuel = "
            SELECT SUM(valeur_compteur) as total_client
            FROM consommations_mensuelles
            WHERE client_id = :cid AND annee = :annee
        ";
        $stmt2 = $pdo->prepare($sqlMensuel);
        $stmt2->execute(['cid'=>$clientId, 'annee'=>$row['annee']]);
        $totalClient = (int) $stmt2->fetchColumn();

        // Calcul de l'écart
        $ecart = (int)$row['conso_agent'] - $totalClient;

        // Vérifier si facture_plus existe déjà
        $dejaEnvoyee = existeFacturePlus($clientId, $row['annee']); // true/false

        $result[] = [
            'client_id'    => $clientId,
            'nom'          => $row['nom'],
            'prenom'       => $row['prenom'],
            'annee'        => $row['annee'],
            'conso_agent'  => $row['conso_agent'],
            'conso_client' => $totalClient,
            'ecart'        => $ecart,
            'facture_plus_existe' => $dejaEnvoyee
        ];
    }

    return $result;
}
