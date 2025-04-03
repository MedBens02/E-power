<?php
session_start();

$reclamation = $_SESSION['reclamation_to_edit'] ?? null;
$facture = $_SESSION['facture_to_edit'] ?? null;

if (!$reclamation) {
    echo "Réclamation introuvable dans la session.";
    exit;
}

$id = $reclamation['id'];
$type = $reclamation['type'];
$description = $reclamation['description'];
$client_id = $reclamation['client_id'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Traiter Réclamation</title>
    <!-- Inclusion de Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <style>
        #cneterrr {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f0f8ff;
            color: #333;
        }

        h3 {
            color: #ff6600;
            border-bottom: 2px solid #007bff;
            padding-bottom: 5px;
        }

        form {
            background-color: #fff;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            margin: auto;
        }

        label, p {
            color: #333;
            margin: 5px 0;
        }

        textarea, input[type="number"] {
            width: 100%;
            padding: 8px;
            margin: 5px 0 10px 0;
            border: 1px solid #ddd;
            border-radius: 3px;
            background-color: #f9f9f9;
        }

        button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 15px;
            margin: 5px;
            border-radius: 3px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }

        button:active {
            background-color: #003f7f;
        }

        #orange {
            background-color: #ff6600;
            color: #fff;
            border: none;
            padding: 10px 15px;
            margin: 5px;
            border-radius: 3px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        /* Style de l'icône de retour avec un cercle */
        .back-arrow {
            margin-left: 250px;
            position: absolute;
            top: 10px;
            left: 10px;
            text-decoration: none;
            font-size: 24px;
            color: #007bff;
            display: flex;
            align-items: center;
        }

        .back-arrow:hover {
            color: #0056b3;
        }

        .arrow-icon {
            margin-right: 10px;
            font-size: 28px; /* Plus grand pour la visibilité */
        }
    </style>
</head>

<body>
    <!-- Icône de retour avec un cercle -->
    <a href="../ihm/tableau_bord_admin.php?action=load_reclamations" class="back-arrow">
        <i class="fas fa-arrow-circle-left arrow-icon"></i> Retour
    </a>

    <h3 id="cneterrr">Traiter la réclamation #<?= htmlspecialchars($id) ?></h3>

    <form method="post" action="../traitement/routeur.php?action=traiter_reclamation">
        <input type="hidden" name="reclamation_id" value="<?= htmlspecialchars($id) ?>">

        <p><strong>Type :</strong> <?= htmlspecialchars($type) ?></p>
        <p><strong>Description :</strong> <?= htmlspecialchars($description) ?></p>

        <label>Message de notification :</label><br>
        <textarea name="message" required></textarea><br><br>

        <?php if ($type === 'Facture'): ?>
            <p>
                <strong>Prix actuel :</strong>
                <?= $facture ? htmlspecialchars($facture['prix_ht']) : 'N/A' ?>
            </p>
            <label>Nouveau prix (si tu souhaites corriger) :</label><br>
            <input type="number" step="0.01" name="nouveau_prix" placeholder="Montant corrigé"><br><br>

            <button type="submit" name="action_facture" value="valider_prix">Valider la correction</button>
            <button id="orange" type="submit" name="action_facture" value="annuler_renouveler">Annuler & Renouveler la facture</button>
        <?php else: ?>
            <button type="submit">Valider</button>
        <?php endif; ?>
    </form>
</body>
</html>
