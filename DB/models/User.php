<?php
require_once __DIR__ . '/../DB.php';

abstract class User
{
    protected $id;
    protected $nom;
    protected $prenom;
    protected $email;
    protected $motDePasse;

    public function __construct($id, $nom, $prenom, $email, $motDePasse)
    {
        $this->id          = $id;
        $this->nom         = $nom;
        $this->prenom      = $prenom;
        $this->email       = $email;
        $this->motDePasse  = $motDePasse;
    }

    abstract public static function login($email, $password);

    /**
     * Check if an email is used by *any* admin or client.
     * Optionally exclude a specific user by ID/type (e.g., 'client' or 'admin').
     *
     * @param string $email        The email to check.
     * @param int|null $excludeId  The user ID to exclude from check.
     * @param string|null $excludeType  'client' or 'admin', if you want to exclude that row.
     *
     * @return bool  True if the email is used by some user (besides exclude), else false.
     */
    public static function isEmailUsed($email, $excludeId = null, $excludeType = null)
    {
        $pdo = DB::connect();

        //
        // 1) Check in the admins table
        //
        $sqlAdmin = "SELECT COUNT(*) AS cnt FROM admins WHERE email = :email";
        if ($excludeType === 'admin' && $excludeId !== null) {
            $sqlAdmin .= " AND id != :excludeId";
        }
        $stmtA = $pdo->prepare($sqlAdmin);
        $stmtA->bindValue(':email', $email);
        if ($excludeType === 'admin' && $excludeId !== null) {
            $stmtA->bindValue(':excludeId', $excludeId, PDO::PARAM_INT);
        }
        $stmtA->execute();
        $rowA = $stmtA->fetch(\PDO::FETCH_ASSOC);
        if ($rowA && $rowA['cnt'] > 0) {
            return true; // email used by an Admin
        }

        //
        // 2) Check in the clients table
        //
        $sqlClient = "SELECT COUNT(*) AS cnt FROM clients WHERE email = :email";
        if ($excludeType === 'client' && $excludeId !== null) {
            $sqlClient .= " AND id != :excludeId";
        }
        $stmtC = $pdo->prepare($sqlClient);
        $stmtC->bindValue(':email', $email);
        if ($excludeType === 'client' && $excludeId !== null) {
            $stmtC->bindValue(':excludeId', $excludeId, PDO::PARAM_INT);
        }
        $stmtC->execute();
        $rowC = $stmtC->fetch(\PDO::FETCH_ASSOC);
        if ($rowC && $rowC['cnt'] > 0) {
            return true; // email used by a Client
        }

        return false;
    }


    public function getId() {
        return $this->id;
    }
    public function getNom() {
        return $this->nom;
    }
    public function getPrenom() {
        return $this->prenom;
    }
    public function getEmail() {
        return $this->email;
    }
    public function getMotDePasse() {
        return $this->motDePasse;
    }
}
?>

