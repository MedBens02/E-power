<?php
session_start();
$consommations = $_SESSION['consommations_annuelles'] ?? [];
$info = $_SESSION['info'] ?? null;
$error = $_SESSION['error'] ?? null;

// Optionnel si tu veux effacer le message après affichage
unset($_SESSION['info'], $_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Importation Annuelle</title>
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

        .message {
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        form {
            margin: 20px 0;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        input[type="file"] {
            margin: 10px 0;
        }

        button {
            padding: 10px;
            background-color: #0056b3;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #003d80;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        hr {
            margin: 20px 0;
            border: 0;
            border-top: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Importation Annuelle</h2>

        <!-- Affichage des messages d'information ou d'erreur -->
        <?php if ($info): ?>
            <div class="message success"><?= htmlspecialchars($info) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="message error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- Formulaire d'upload -->
        <form method="post" 
              action="../traitement/routeur.php?action=importer_consommation_annuelle" 
              enctype="multipart/form-data">
            <label for="fichier_txt">Fichier TXT :</label>
            <input type="file" id="fichier_txt" name="fichier_txt" accept=".txt" required>
            <button type="submit">Importer</button>
        </form>

        <hr>
        <h3>Liste des Consommations Annuelles</h3>

        <!-- Tableau des consommations -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Client</th>
                    <th>Agent</th>
                    <th>Année</th>
                    <th>Consommation</th>
                    <th>Date Saisie</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($consommations)): ?>
                    <?php foreach ($consommations as $c): ?>
                        <tr>
                            <td><?= htmlspecialchars($c['id']) ?></td>
                            <td><?= htmlspecialchars($c['client_id']) ?></td>
                            <td><?= htmlspecialchars($c['agent_id']) ?></td>
                            <td><?= htmlspecialchars($c['annee']) ?></td>
                            <td><?= htmlspecialchars($c['consommation']) ?></td>
                            <td><?= htmlspecialchars($c['date_saisie']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center;">Aucune consommation annuelle enregistrée</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
