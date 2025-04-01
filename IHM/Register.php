<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ElectricBill - Inscription</title>
    <link rel="stylesheet" href="Styles/Global.css">
    <link rel="stylesheet" href="Styles/Register.css">
</head>
<body>
    <div class="overlay"></div>
    <div class="container">
        <div class="logo-section">
            <h1>ElectricBill</h1>
            <p>Créer un compte utilisateur</p>
        </div>

        <!-- Remove action and method, add id="registerForm" -->
        <form id="registerForm" class="auth-form">
            <!-- We'll place error/success messages here -->
            <div id="errorMessage"></div>
            <div id="successMessage"></div>

            <div class="form-group">
                <label for="nom">Nom</label>
                <input id="nom" name="nom" type="text" required>
            </div>

            <div class="form-group">
                <label for="prenom">Prénom</label>
                <input id="prenom" name="prenom" type="text" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" required>
            </div>

            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input id="password" name="password" type="password" required>
            </div>

            <div class="form-group">
                <label for="type">Vous êtes :</label>
                <select id="type" name="type">
                    <option value="admin">Administrateur</option>
                    <option value="client" selected>Client</option>
                </select>
            </div>

            <!-- Instead of name="register", we rely on JS -->
            <button type="submit" class="login-btn">S'inscrire</button>
        </form>

        <div class="back-to-login">
            <a href="login.php">Retour à la connexion</a>
        </div>
    </div>

    <!-- Include our JS file -->
    <script src="../Scripts/Register.js"></script>
</body>
</html>
