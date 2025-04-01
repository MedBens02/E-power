<?php
session_start();

// Ensure only a client can access
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'client') {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saisie Consommation</title>
    <link rel="stylesheet" href="Styles/Global.css">
    <link rel="stylesheet" href="Styles/InputConsommation.css">
</head>
<body>
    <div class="container">
        <h2>Consommation Mensuelle</h2>

        <!-- Dropdown to select compteur -->
        <div class="form-group">
            <label for="compteurSelect">Choisir un compteur:</label>
            <select id="compteurSelect" name="compteurSelect">
            </select>
        </div>

        <!-- Display of last month’s data -->
        <div class="previous-month">
        <div id="previousDataSection" style="display: none; margin-top: 20px;">
            <h2>Informations du mois précédent</h2>
            <h4>Consommation mensuelle (mois dernier): <span id="lastMonthConsumption">--</span> kWh</h4>
            <h4>Consommation totale actuelle: <span id="totalConsumption">--</span> kWh</h4>
            <h4>Photo compteur précédente:</h4>
            <img id="previousPhoto" src="#" alt="Photo précédente" style="max-width: 300px; display: none;">
        </div>
        </div>

        <!-- Form to input current consumption -->
        <div id="currentInputSection" style="display: none; margin-top: 20px;">
            <h2>Nouvelle Consommation</h2>
            <div class="form-group">
                <label for="currentConsumption">Valeur compteur actuelle (kWh):</label>
                <input type="number" id="currentConsumption" name="currentConsumption">
            </div>
            <div class="form-group">
                <label for="compteurPhoto">Photo compteur actuelle:</label>
                <input type="file" id="compteurPhoto" name="compteurPhoto" accept="image/*">
            </div>
            <button id="calculateBtn">Calculer</button>
        </div>

        <!-- Display of computed difference and pricing -->
        <!-- Price Preview Container -->
        <div id="calculationResults" class="price-preview" style="display: none; margin-top: 20px;">

            <!-- Consommation mensuelle -->
            <div class="price-item">
                <span>Consommation ce mois-ci:</span>
                <span id="diffKwh">--</span>
            </div>

            <!-- Tranche -->
            <div class="price-item">
                <span>Tranche:</span>
                <span id="trancheLabel">--</span>
            </div>

            <!-- Prix HT -->
            <div class="price-item">
                <span>Prix HT estimé:</span>
                <span id="prixHt">0.00</span>
            </div>

            <!-- TVA -->
            <div class="price-item">
                <span>TVA (18%):</span>
                <span id="tvaValue">0.00</span>
            </div>

            <!-- Prix TTC -->
            <div class="price-item total">
                <span>Prix TTC:</span>
                <span id="prixTtc">0.00</span>
            </div>

            <button id="submitConsumptionBtn">Enregistrer</button>
        </div>

    </div>

    <script src="../Scripts/ConsommationMensuelle.js"></script>
</body>
</html>