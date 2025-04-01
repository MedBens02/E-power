<?php
require_once __DIR__ . '/../DB.php';

class Facture
{
    /**
     * Insert a new facture referencing the given consommation.
     * We'll skip date_facture since your table doesn't have it.
     */
    public static function createDirectValues($clientId, $consommationId, $prixHt)
    {
        try {
            $pdo = DB::connect();
            $mois  = date('n');
            $annee = date('Y');

            $sql = "INSERT INTO factures
                    (client_id, consommation_id, mois, annee, prix_ht, statut_paiement)
                    VALUES
                    (:cid, :consid, :mois, :annee, :pht, 'non payée')";

            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':cid',    $clientId,        \PDO::PARAM_INT);
            $stmt->bindValue(':consid', $consommationId,  \PDO::PARAM_INT);
            $stmt->bindValue(':mois',   $mois,            \PDO::PARAM_INT);
            $stmt->bindValue(':annee',  $annee,           \PDO::PARAM_INT);
            $stmt->bindValue(':pht',    $prixHt);

            $stmt->execute();
            return $pdo->lastInsertId();
        } catch (\Exception $e) {
            // Log or handle error
            return false;
        }
    }

    /**
     * Retrieve a facture by ID, joining the clients table
     * for name/prenom, and optionally skipping consumption details here.
     */
    public static function getById($factureId)
    {
        $pdo = DB::connect();

        $sql = "
            SELECT f.*,
                   c.nom     AS client_nom,
                   c.prenom  AS client_prenom
            FROM factures f
            JOIN clients c ON f.client_id = c.id
            WHERE f.id = :fid
            LIMIT 1
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':fid', $factureId, \PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Return a minimal list of factures for a specific client,
     * e.g. used by "list_factures" in your front-end's invoice list.
     */
    public static function getAllByClient($clientId)
    {
        $pdo = DB::connect();

        $sql = "
            SELECT
                f.id,
                f.prix_ttc,
                f.statut_paiement,
                f.mois,
                f.annee
            FROM factures f
            WHERE f.client_id = :clientId
            ORDER BY f.id DESC
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':clientId', $clientId, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get detailed info about a single invoice, including
     * client name/address and consumption details (kWh, photo, etc.).
     */
    public static function getDetailById($factureId)
    {
        $pdo = DB::connect();

        $sql = "
            SELECT
                f.id           AS facture_id,
                f.client_id,
                f.consommation_id,
                f.mois,
                f.annee,
                f.prix_ht,
                f.tva,
                f.prix_ttc,
                f.statut_paiement,

                c.nom     AS client_nom,
                c.prenom  AS client_prenom,
                c.adresse AS client_address,

                m.valeur_compteur AS consommation_kwh,
                m.photo_compteur  AS photo_compteur

            FROM factures f
            JOIN clients c ON f.client_id = c.id
            JOIN consommations_mensuelles m ON f.consommation_id = m.id
            WHERE f.id = :factureId
            LIMIT 1
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':factureId', $factureId, \PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public static function getAllByCompteur($clientId, $compteurId)
{
    $pdo = DB::connect();
    $sql = "
        SELECT
            f.id,
            f.mois,
            f.annee,
            f.prix_ttc,
            f.statut_paiement
        FROM factures f
        -- possibly join consommations_mensuelles if you want 
        WHERE f.client_id = :clientId
          AND f.consommation_id IN (
            SELECT m.id
            FROM consommations_mensuelles m
            WHERE m.compteur_id = :compteurId
          )
        ORDER BY f.id DESC
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':clientId', $clientId, \PDO::PARAM_INT);
    $stmt->bindValue(':compteurId', $compteurId, \PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}

}
