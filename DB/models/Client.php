<?php
require_once __DIR__ . '/User.php';

class Client extends User
{
    /**
     * Login method for client.
     * Returns a Client instance on success, or null on failure.
     */
    public static function login($email, $password)
    {
        $pdo = DB::connect();

        $sql = "SELECT * FROM clients WHERE email = :email LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':email', $email);
        $stmt->execute();

        $clientRow = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$clientRow) {
            return null;
        }

        if (!password_verify($password, $clientRow['mot_de_passe'])) {
            return null;
        }

        return new Client(
            $clientRow['id'],
            $clientRow['nom'],
            $clientRow['prenom'],
            $clientRow['email'],
            $clientRow['mot_de_passe']
        );
    }

    public static function register($nom, $prenom, $email, $password)
    {
        try {
            $pdo = DB::connect();

            $checkSql = "SELECT COUNT(*) AS cnt FROM clients WHERE email = :email";
            $checkStmt = $pdo->prepare($checkSql);
            $checkStmt->bindValue(':email', $email);
            $checkStmt->execute();
            $row = $checkStmt->fetch(PDO::FETCH_ASSOC);
            if ($row['cnt'] > 0) {
                return false;
            }

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $insertSql = "INSERT INTO clients (nom, prenom, email, mot_de_passe, adresse) 
                          VALUES (:nom, :prenom, :email, :mot_de_passe, :adresse)";
            $stmt = $pdo->prepare($insertSql);
            $stmt->bindValue(':nom', $nom);
            $stmt->bindValue(':prenom', $prenom);
            $stmt->bindValue(':email', $email);
            $stmt->bindValue(':mot_de_passe', $hashedPassword);

            $stmt->bindValue(':adresse', 'N/A');

            $stmt->execute();
            return $pdo->lastInsertId();

        } catch (Exception $e) {
            return false;
        }
    }

    public static function getById($clientId)
    {
        $pdo = DB::connect();
        $sql = "SELECT * FROM clients WHERE id = :clientId LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':clientId', $clientId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Update a client's profile (name, prenom, address, email)
     */
    public static function updateProfile($clientId, $nom, $prenom, $adresse, $email)
    {
        $pdo = DB::connect();
        $sql = "UPDATE clients
                SET nom = :nom,
                    prenom = :prenom,
                    adresse = :adresse,
                    email = :email
                WHERE id = :clientId";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':nom', $nom);
        $stmt->bindValue(':prenom', $prenom);
        $stmt->bindValue(':adresse', $adresse);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':clientId', $clientId, \PDO::PARAM_INT);

        return $stmt->execute();
    }


    /**
     * Change a client's password (if old password matches)
     */
    public static function updatePassword($clientId, $oldPassword, $newPassword)
    {
        $pdo = DB::connect();

        // 1) Check the current hashed password in DB
        $checkSql = "SELECT mot_de_passe FROM clients WHERE id = :clientId";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->bindValue(':clientId', $clientId, PDO::PARAM_INT);
        $checkStmt->execute();
        $row = $checkStmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return false; // client not found
        }

        // 2) Verify the old password
        if (!password_verify($oldPassword, $row['mot_de_passe'])) {
            return false; // old password mismatch
        }

        // 3) Hash the new password
        $hashedNew = password_hash($newPassword, PASSWORD_DEFAULT);

        // 4) Update DB
        $updateSql = "UPDATE clients SET mot_de_passe = :newPass WHERE id = :clientId";
        $updateStmt = $pdo->prepare($updateSql);
        $updateStmt->bindValue(':newPass', $hashedNew);
        $updateStmt->bindValue(':clientId', $clientId, PDO::PARAM_INT);
        return $updateStmt->execute();
    }

    /**
     * Example: getAnnualConsumption
     * Return an array of [ 'annee' => int, 'consommation' => int, 'ecart' => int ]
     * or whatever your DB structure is.
     */
    public static function getAnnualConsumption($clientId)
    {
        $pdo = DB::connect();
        $sql = "SELECT annee, consommation,
                (consommation - LAG(consommation, 1, consommation) OVER (ORDER BY annee)) as ecart
                FROM consommations_annuelles
                WHERE client_id = :clientId
                ORDER BY annee DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':clientId', $clientId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
