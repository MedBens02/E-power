<?php
require_once('connexion.php');

function ajouterNotification($client_id, $message) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO notifications (client_id, message) VALUES (?, ?)");
    return $stmt->execute([$client_id, $message]);
}

?>
