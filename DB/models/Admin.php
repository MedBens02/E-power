<?php
require_once __DIR__ . '/User.php';

class Admin extends User
{
    /**
     * Login method for admin.
     * Returns an Admin instance on success, or null on failure.
     */
    public static function login($email, $password)
    {
        $pdo = DB::connect();
        
        $sql = "SELECT * FROM admins WHERE email = :email LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':email', $email);
        $stmt->execute();

        $adminRow = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$adminRow) {
            return null;
        }

        if (!password_verify($password, $adminRow['mot_de_passe'])) {
            return null;
        }

        return new Admin(
            $adminRow['id'],
            $adminRow['nom'],
            $adminRow['prenom'],
            $adminRow['email'],
            $adminRow['mot_de_passe']
        );
    }

    public static function register($nom, $prenom, $email, $password)
    {
        try {
            $pdo = DB::connect();

            $checkSql = "SELECT COUNT(*) AS cnt FROM admins WHERE email = :email";
            $checkStmt = $pdo->prepare($checkSql);
            $checkStmt->bindValue(':email', $email);
            $checkStmt->execute();
            $row = $checkStmt->fetch(PDO::FETCH_ASSOC);
            if ($row['cnt'] > 0) {
                return false;
            }

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $insertSql = "INSERT INTO admins (nom, prenom, email, mot_de_passe)
                          VALUES (:nom, :prenom, :email, :mot_de_passe)";
            $stmt = $pdo->prepare($insertSql);
            $stmt->bindValue(':nom', $nom);
            $stmt->bindValue(':prenom', $prenom);
            $stmt->bindValue(':email', $email);
            $stmt->bindValue(':mot_de_passe', $hashedPassword);

            $stmt->execute();
            return $pdo->lastInsertId();

        } catch (Exception $e) {
            return false;
        }
    }
}
?>
