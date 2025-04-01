<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ElectricBill - Connexion</title>
    <link rel="stylesheet" href="Styles/Global.css">
    <link rel="stylesheet" href="Styles/Login.css">
</head>
<body>
    <div class="overlay"></div>
    <div class="container">
        <div class="logo-section">
            <h1>ElectricBill</h1>
            <p>Gestion simplifiée de vos factures d'électricité</p>
        </div>

        <!-- We remove action and method, and add an id="loginForm" -->
        <form id="loginForm" class="auth-form">
            <div id="errorMessage"></div>

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

            <!-- No name="login" needed; JS handles everything -->
            <button type="submit" class="login-btn">Se connecter</button>
        </form>

        <div class="forgot-password">
            <a href="#">Mot de passe oublié ?</a>
        </div>

        <div class="create-account">
            <a href="Register.php">Créer un compte</a>
        </div>
    </div>

    <!-- Include our external JS (relative path depends on folder structure) -->
    <script src="../Scripts/Login.js"></script>
</body>
</html>
