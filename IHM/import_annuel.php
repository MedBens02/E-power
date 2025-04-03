<?php
session_start();
$data = $_SESSION['compare_data'] ?? [];
$annee = $_SESSION['compare_annee'] ?? date('Y');
$info = $_SESSION['info'] ?? null;
$error = $_SESSION['error'] ?? null;

// Optionnel : effacer les messages après affichage
unset($_SESSION['info'], $_SESSION['error']);
?>

<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f6f9;
            margin: 0;
        
        }

        h2 {
            color: #34495e;
            text-align: center;
            margin-bottom: 20px;
        }

        .message {
            padding: 10px 15px;
            margin: 10px 0;
            border-radius: 5px;
            font-size: 1em;
            text-align: center;
        }

        .success-message {
            background-color: #dff0d8;
            color: #3c763d;
        }

        .error-message {
            background-color: #f2dede;
            color: #a94442;
        }

        /* Formulaire */
        form {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
            margin-right: 8px;
        }

        input[type="file"] {
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #2980b9;
        }

        /* Tableau */
        table {
            border-collapse: collapse;
            width: 100%;
            margin: 20px auto;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        th, td {
            padding: 15px;
            text-align: center;
            border: 1px solid #ddd;
        }

        th {
            background-color: #3498db;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .alert {
            color: red;
            font-weight: bold;
        }

        .high-ecart {
            background-color: #ffe6e6;
            font-weight: bold;
        }

        .btn-create {
            background-color: #27ae60;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-create:hover {
            background-color: #219150;
        }

        .center {
            text-align: center;
        }

        .ecart-red {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>

<h2>Importation Annuelle</h2>

<?php if ($info): ?>
    <div class="message success-message"><?= htmlspecialchars($info) ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="message error-message"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<!-- Formulaire d'upload -->
<div class="center">
    <form method="post" 
          action="../traitement/routeur.php?action=importer_consommation_annuelle" 
          enctype="multipart/form-data">
        <label>Fichier TXT :</label>
        <input type="file" name="fichier_txt" accept=".txt" required>
        <button type="submit">Importer</button>
    </form>
</div>

<h2>Comparaison Annuelle Agent vs Client - Année <?= htmlspecialchars($annee) ?></h2>

<table>
    <tr>
        <th>Client</th>
        <th>Conso Agent (KWh)</th>
        <th>Conso Client (KWh)</th>
        <th>Écart (KWh)</th>
        <th>Action</th>
    </tr>
    <?php foreach($data as $row): ?>
<?php
    $ecart = $row['ecart'];
    $ecartStyle = ($ecart > 480) ? 'ecart-red' : '';
    $dejaEnvoyee = $row['facture_plus_existe']; // Nouveau champ ajouté dans la fonction de récupération
?>
<tr>
    <td><?= htmlspecialchars($row['nom'].' '.$row['prenom']) ?></td>
    <td><?= htmlspecialchars($row['conso_agent']) ?></td>
    <td><?= htmlspecialchars($row['conso_client']) ?></td>
    <td class="<?= $ecartStyle ?>"><?= $ecart ?></td>
    <td>
        <?php if ($dejaEnvoyee): ?>
            <span style="color:blue; font-weight:bold;">Facture+ déjà envoyée</span>
        <?php elseif ($ecart > 50): ?>
            <form method="post" action="../traitement/routeur.php?action=creer_facture_plus">
                <input type="hidden" name="client_id" value="<?= $row['client_id'] ?>">
                <input type="hidden" name="annee" value="<?= $row['annee'] ?>">
                <input type="hidden" name="ecart" value="<?= $ecart ?>">
                <button type="submit" class="btn-create">Créer Facture +</button>
            </form>
        <?php else: ?>
            -
        <?php endif; ?>
    </td>
</tr>
<?php endforeach; ?>

</table>

</body>
</html>
