<?php
require_once __DIR__ . '/../DB.php';

class Tarif
{
    /**
     * Given a consumption amount (kWh), returns an array with:
     *  - 'unit_price' => float
     *  - 'tranche_label' => string, e.g. "0-100" or "101-150" or "151+"
     */
    public static function getTarifForConsumption($consumption)
    {
        $pdo = DB::connect();

        $sql = "SELECT tranche_min, tranche_max, prix_unitaire
                FROM tarifs
                WHERE :consumption BETWEEN tranche_min AND tranche_max
                LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':consumption', (int)$consumption, \PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($row) {
            // Build a label like "0-100", "101-150", or "151+" if desired
            $min = (int)$row['tranche_min'];
            $max = (int)$row['tranche_max'];

            $label = ($max >= 9999)
                ? "{$min}+"
                : "{$min}-{$max}";

            return [
                'unit_price' => (float)$row['prix_unitaire'],
                'tranche_label' => $label
            ];
        }

        // If not found, you could default to something or return null
        return null;
    }
}
