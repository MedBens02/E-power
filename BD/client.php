<?php
require_once('connexion.php');

class ClientA {
    public $id, $nom, $prenom, $email, $adresse, $mot_de_passe, $created_at;

    public function __construct($data) {
        $this->id = $data['id'];
        $this->nom = $data['nom'];
        $this->prenom = $data['prenom'];
        $this->email = $data['email'];
        $this->adresse = $data['adresse'];
        $this->mot_de_passe = $data['mot_de_passe'] ?? null;
        $this->created_at = $data['created_at'] ?? null;
    }
}

function getAllClients() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM clients");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return array_map(fn($row) => new ClientA($row), $rows);
}

function getClientById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM clients WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? new ClientA($row) : null;
}

function ajouterClient($nom, $prenom, $email, $adresse, $mot_de_passe) {
    global $pdo;
    $hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO clients (nom, prenom, email, adresse, mot_de_passe) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$nom, $prenom, $email, $adresse, $hash]);
}

function supprimerClient($id) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM clients WHERE id = ?");
    $stmt->execute([$id]);
}

function modifierClient($id, $nom, $prenom, $email, $adresse, $mot_de_passe = null) {
    global $pdo;
    if ($mot_de_passe) {
        $hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE clients SET nom = ?, prenom = ?, email = ?, adresse = ?, mot_de_passe = ? WHERE id = ?");
        $stmt->execute([$nom, $prenom, $email, $adresse, $hash, $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE clients SET nom = ?, prenom = ?, email = ?, adresse = ? WHERE id = ?");
        $stmt->execute([$nom, $prenom, $email, $adresse, $id]);
    }
}
