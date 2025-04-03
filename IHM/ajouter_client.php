<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><title>Ajouter Client</title></head>
<body>
<h2>Ajouter un client</h2>
<form method="post" action="../traitement/routeur.php?action=ajouter_client">
    <label>Nom: <input type="text" name="nom" required></label><br><br>
    <label>Prénom: <input type="text" name="prenom" required></label><br><br>
    <label>Adresse: <input type="text" name="adresse" required></label><br><br>
    <label>Email: <input type="email" name="email" required></label><br><br>
    <label>Mot de passe: <input type="password" name="mot_de_passe" required></label><br><br>
    <button type="submit">Enregistrer</button>
</form>
</body>
</html>
