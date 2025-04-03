<?php
session_start();
$client = $_SESSION['client_a_modifier'] ?? null;

if (!$client) {
    echo "Client à modifier non trouvé.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><title>Modifier Client</title></head>
<body>
<h2>Modifier le client</h2>
<form method="post" action="../traitement/routeur.php?action=modifier_client">
    <input type="hidden" name="id" value="<?= $client['id'] ?>">
    <label>Nom: <input type="text" name="nom" value="<?= htmlspecialchars($client['nom']) ?>" required></label><br><br>
    <label>Prénom: <input type="text" name="prenom" value="<?= htmlspecialchars($client['prenom']) ?>" required></label><br><br>
    <label>Adresse: <input type="text" name="adresse" value="<?= htmlspecialchars($client['adresse']) ?>" required></label><br><br>
    <label>Email: <input type="email" name="email" value="<?= htmlspecialchars($client['email']) ?>" required></label><br><br>
    <label>Nouveau mot de passe (optionnel): <input type="password" name="mot_de_passe"></label><br><br>
    <button type="submit">Modifier</button>
</form>
</body>
</html>
