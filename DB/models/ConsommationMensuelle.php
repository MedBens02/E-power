<?php
require_once __DIR__ . '/../DB.php';

class ConsommationMensuelle
{
    public static function getLastMonthData($clientId, $compteurId)
    {
        $pdo = DB::connect();
        $sql = "SELECT valeur_compteur, photo_compteur
                FROM consommations_mensuelles
                WHERE client_id = :clientId
                  AND compteur_id = :compteurId
                ORDER BY id DESC
                LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':clientId', $clientId, \PDO::PARAM_INT);
        $stmt->bindValue(':compteurId', $compteurId, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    public static function create($clientId, $compteurId, $valeurCompteur, $photoPath)
    {
        $pdo = DB::connect();
        $mois = date('n');   // e.g. 1–12
        $annee = date('Y');

        $sql = "INSERT INTO consommations_mensuelles
                (client_id, compteur_id, mois, annee, valeur_compteur, photo_compteur)
                VALUES
                (:clientId, :compteurId, :mois, :annee, :valeur, :photo)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':clientId', $clientId, \PDO::PARAM_INT);
        $stmt->bindValue(':compteurId', $compteurId, \PDO::PARAM_INT);
        $stmt->bindValue(':mois', $mois, \PDO::PARAM_INT);
        $stmt->bindValue(':annee', $annee, \PDO::PARAM_INT);
        $stmt->bindValue(':valeur', $valeurCompteur, \PDO::PARAM_INT);
        $stmt->bindValue(':photo', $photoPath);

        if ($stmt->execute()) {
            return $pdo->lastInsertId();
        }
        return false;
    }

    public static function getConsommationById($consoId)
    {
        $pdo = DB::connect();
        $sql = "SELECT *
                FROM consommations_mensuelles
                WHERE id = :consoId
                LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':consoId', $consoId, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

}
