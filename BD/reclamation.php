<?php
require_once('connexion.php');

class Reclamation {
    public $id, $client_id, $type, $description, $statut, $date_creation, $pieces_jointes;

    public function __construct($data) {
        $this->id = $data['id'];
        $this->client_id = $data['client_id'];
        $this->type = $data['type'];
        $this->description = $data['description'];
        $this->statut = $data['statut'];
        $this->date_creation = $data['date_creation'];
        $this->pieces_jointes = explode(',', $data['pieces_jointes'] ?? '');
    }
}

function getAllReclamations() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM reclamations WHERE statut = 'en attente' ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
    
}

function getReclamationById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM reclamations WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function updateReclamationStatus($id, $statut) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE reclamations SET statut = ? WHERE id = ?");
    return $stmt->execute([$statut, $id]);
}




function updateFacture($client_id, $nouveau_prix, $mois, $annee) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE factures SET prix_ht = ? WHERE client_id = ? AND mois = ? AND annee = ?");
    return $stmt->execute([$nouveau_prix, $client_id, $mois, $annee]);
}

function ajouterNotification($client_id, $message) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO notifications (client_id, message) VALUES (?, ?)");
    return $stmt->execute([$client_id, $message]);
}








?>
