<?php
require_once __DIR__ . '/../DB.php';

class Reclamation
{
    public static function create($clientId, $type, $description, $compteurId = null, $factureId = null)
    {
        try {
            $pdo = DB::connect();
            $sql = "
                INSERT INTO reclamations
                (client_id, compteur_id, facture_id, type, description, statut, pieces_jointes)
                VALUES
                (:cid, :cmpid, :factid, :rtype, :descr, 'en attente', NULL)
            ";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':cid',    $clientId, \PDO::PARAM_INT);
            $stmt->bindValue(':cmpid',  $compteurId ? $compteurId : null, \PDO::PARAM_INT);
            $stmt->bindValue(':factid', $factureId ? $factureId : null,   \PDO::PARAM_INT);
            $stmt->bindValue(':rtype',  $type);
            $stmt->bindValue(':descr',  $description);

            $stmt->execute();
            return $pdo->lastInsertId();
        } catch (\Exception $e) {
            // log or handle
            return false;
        }
    }

    /**
     * Return all reclamations for a client
     */
    public static function getAllByClient($clientId)
    {
        $pdo = DB::connect();
        $sql = "
            SELECT
                id,
                client_id,
                compteur_id,
                facture_id,
                type,
                description,
                statut,
                date_creation
            FROM reclamations
            WHERE client_id = :cid
            ORDER BY date_creation DESC
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':cid', $clientId, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
