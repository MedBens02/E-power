<?php
session_start();

// Ensure only clients can access this page
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'client') {
    header('Location: login.php');
    exit();
}

// DO NOT load models or fetch DB data here. Just do the session check above.
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Client</title>
    <link rel="stylesheet" href="Styles/Global.css">
    <link rel="stylesheet" href="Styles/ProfileClient.css">
</head>
<body>
    <!-- We'll show success/error messages here -->
    <div id="successMessage"></div>
    <div id="errorMessage"></div>

    <div class="profile-container">
        <!-- Section Informations personnelles -->
        <div class="profile-card">
            <h2 class="section-title">Informations personnelles</h2>

            <!-- 
                No 'action' or 'method'. We'll let JS handle everything.
                We'll give it an ID to be recognized in JS.
            -->
            <form id="profileForm">
                <input type="hidden" name="client_id" value="" />

                <div class="form-group">
                    <label for="nom">Nom</label>
                    <input type="text" id="nom" name="nom" required />
                </div>
                <div class="form-group">
                    <label for="prenom">Prénom</label>
                    <input type="text" id="prenom" name="prenom" required />
                </div>
                <div class="form-group">
                    <label for="adresse">Adresse</label>
                    <input type="text" id="adresse" name="adresse" required />
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required />
                </div>
                <button class="btn" type="submit">Enregistrer les modifications</button>
            </form>
        </div>

        <!-- Section Historique et Mot de passe -->
        <div class="right-column">
            <div class="consumption-history">
                <h2 class="section-title">Historique de consommation</h2>
                <!-- We'll create an empty table that JS will fill -->
                <table class="history-table" id="historyTable">
                    <thead>
                        <tr>
                            <th>Année</th>
                            <th>Consommation (kWh)</th>
                            <th>Écart</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- JS will populate rows here -->
                    </tbody>
                </table>
            </div>

            <div class="password-change">
                <h3>Changer le mot de passe</h3>
                <form id="passwordForm">
                    <input type="hidden" name="client_id" value="" />
                    <div class="form-group">
                        <label for="old_password">Ancien mot de passe</label>
                        <input type="password" id="old_password" name="old_password" required />
                    </div>
                    <div class="form-group">
                        <label for="new_password">Nouveau mot de passe</label>
                        <input type="password" id="new_password" name="new_password" required />
                    </div>
                    <button class="btn" type="submit">Mettre à jour</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Link to your new JavaScript file -->
    <script src="../Scripts/ProfileClient.js"></script>
</body>
</html>
