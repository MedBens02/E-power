<?php
require_once __DIR__ . '/../DB.php';

class Compteur
{
    public static function getByClientId($clientId)
    {
        $pdo = DB::connect();
        $sql = "SELECT id, client_id, description, consommation_totale
                FROM compteurs
                WHERE client_id = :clientId";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':clientId', $clientId, \PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $rows;
    }

    public static function getTotalConsumption($compteurId)
    {
        $pdo = DB::connect();
        $sql = "SELECT consommation_totale
                FROM compteurs
                WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $compteurId, \PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ? (int)$row['consommation_totale'] : 0;
    }

    public static function updateConsumption($compteurId, $difference)
    {
        // Add difference to compteur's total
        $pdo = DB::connect();
        $sql = "UPDATE compteurs
                SET consommation_totale = consommation_totale + :diff
                WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':diff', $difference, \PDO::PARAM_INT);
        $stmt->bindValue(':id', $compteurId, \PDO::PARAM_INT);
        return $stmt->execute();
    }
}
