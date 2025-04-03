


<?php
require_once('../bd/client.php'); // Charger la classe Client
session_start();
$clients = $_SESSION['clients'] ?? [];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion Clients</title>
    <style>
        * {
            margin: 0; padding: 0; box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        .container {
            max-width: 1200px;
            margin: 2rem auto;                                                                          
            padding: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .search-bar {       
            position: relative;
            width: 300px;
        }

        .search-input {
            width: 100%;
            padding: 10px 40px 10px 20px;
            border: 2px solid #007bff;
            border-radius: 25px;
            font-size: 16px;
        }

        .search-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #007bff;
        }

        .btn {
            padding: 10px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .btn-primary {
            background: #007bff;
            color: white;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-edit {
            background: #28a745;
            color: white;
        }

        .client-table {
            width: 100%;
            border-collapse: collapse;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }

        .client-table th,
        .client-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .client-table th {
            background: #007bff;
            color: white;
        }

        .client-table tr:hover {
            background-color: #f5f5f5;
        }

        .actions-cell {
            display: flex;
            gap: 10px;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 10px;
            width: 500px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Gestion des Clients</h1>
            <div class="search-bar">
                <input type="text" class="search-input" placeholder="Rechercher par nom ou ID...">
                <i class="search-icon">🔍</i>
            </div>
            <a href="ajouter_client.php" class="btn btn-primary">+ Ajouter Client</a>

          
        </div>

        <table class="client-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Adresse</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clients as $client): ?>
                    <tr>
                        <td>#<?= htmlspecialchars($client->id) ?></td>
                        <td><?= htmlspecialchars($client->nom) ?></td>
                        <td><?= htmlspecialchars($client->prenom) ?></td>
                        <td><?= htmlspecialchars($client->adresse) ?></td>
                        <td><?= htmlspecialchars($client->email) ?></td>
                        <td class="actions-cell">
                         <form method="post" action="../traitement/routeur.php?action=supprimer_client" style="display:inline;">
                          <input type="hidden" name="id" value="<?= $client->id ?>">
                          <button type="submit" class="btn btn-danger">Supprimer</button>
                          </form>
                          <a href="../traitement/routeur.php?action=load_modif_client&id=<?= $client->id ?>" class="btn btn-edit">Modifier</a>
                          
                            </td>

                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Modal Ajout Client -->
        <div id="modal" class="modal">
            <div class="modal-content">
                <h2 style="margin-bottom: 20px;">Nouveau Client</h2>
                <form method="post" action="../traitement/routeur.php?action=ajouter_client">
                    <div class="form-group">
                        <label>Nom:</label>
                        <input type="text" name="nom" required>
                    </div>
                    <div class="form-group">
                        <label>Prénom:</label>
                        <input type="text" name="prenom" required>
                    </div>
                    <div class="form-group">
                        <label>Adresse:</label>
                        <input type="text" name="adresse" required>
                    </div>
                    <div class="form-group">
                        <label>Email:</label>
                        <input type="email" name="email" required>
                    </div>
                    <div style="display: flex; gap: 10px; margin-top: 20px;">
                        <button type="button" class="btn btn-danger" onclick="closeModal()">Annuler</button>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('modal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('modal').style.display = 'none';
        }
    </script>
</body>
</html>
