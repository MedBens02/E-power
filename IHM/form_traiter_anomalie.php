<?php
session_start();
// On récupère la réclamation depuis la session
$reclamation = $_SESSION['reclamation_to_edit'] ?? null;
// On récupère le client_id
if (!$reclamation) {
    echo "Réclamation non trouvée dans la session.";
    exit;
}

$client_id = $reclamation['client_id'];
// Récupérer depuis la session les consommations mensuelles (déjà chargées par le routeur)
$consos = $_SESSION['consommations_client'] ?? [];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Traitement Anomalie</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
          
        }

        h2, h3 {
            color: #0056b3;
        }

        .container {
            max-width: 800px;
            margin: auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .info, .consos {
            margin-bottom: 20px;
        }

        .consos {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .consos div {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            background-color: #f9f9f9;
            margin: 5px;
            width: 45%;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .photo-compteur {
            width: 100%; 
            height: auto;
            border: 1px solid #ccc;
            margin: 10px 0;
            border-radius: 5px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-top: 20px;
        }

        label {
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="number"], textarea {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 100%;
            box-sizing: border-box;
            resize: vertical;
        }

        textarea {
            height: 100px;
        }

        button {
            padding: 12px;
            background-color: #0056b3;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            font-size: 16px;
        }

        button:hover {
            background-color: #003d80;
        }

        hr {
            margin: 20px 0;
            border: 0;
            border-top: 1px solid #ddd;
        }

        .notification {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Traitement de la réclamation #<?= htmlspecialchars($reclamation['id']) ?></h2>
        <div class="info">
            <p><strong>Client ID :</strong> <?= htmlspecialchars($client_id) ?></p>
            <p><strong>Type :</strong> <?= htmlspecialchars($reclamation['type']) ?></p>
            <p><strong>Description :</strong> <?= htmlspecialchars($reclamation['description']) ?></p>
        </div>

        <hr>

        <h3>Historique des deux dernières consommations :</h3>
        <div class="consos">
            <?php foreach ($consos as $c): ?>
                <div>
                    <p><strong>Mois :</strong> <?= htmlspecialchars($c['mois']) ?>/<?= htmlspecialchars($c['annee']) ?></p>
                    <p><strong>Valeur compteur :</strong> <?= htmlspecialchars($c['valeur_compteur']) ?></p>
                    <?php if ($c['photo_compteur']): ?>
                        <img class="photo-compteur" src="../uploads/<?= htmlspecialchars($c['photo_compteur']) ?>" alt="photo compteur">
                    <?php else: ?>
                        <p>Aucune photo</p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <form method="post" action="../traitement/routeur.php?action=update_consommation">
            <label for="message">Message de notification :</label>
            <textarea id="message" name="message" placeholder="Entrez votre message ici..." required></textarea>

            <input type="hidden" name="reclamation_id" value="<?= htmlspecialchars($reclamation['id']) ?>">
            <input type="hidden" name="client_id" value="<?= htmlspecialchars($client_id) ?>">

            <label for="valeur_compteur">Nouvelle valeur du compteur :</label>
            <input type="number" id="valeur_compteur" name="valeur_compteur" placeholder="Saisissez la valeur" required>

            <button type="submit">Enregistrer la correction</button>
        </form>
    </div>
</body>
</html>
