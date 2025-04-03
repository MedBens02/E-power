<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes Réclamations</title>
    <link rel="stylesheet" href="./Styles/Reclamations.css">
</head>
<body>
    <h1>Mes Réclamations</h1>

    <div class="claim-container">

        <!-- Formulaire de réclamation -->
        <div class="claim-form">
            <h2 class="form-title">Nouvelle réclamation</h2>

            <form>
                <div class="form-group">
                    <label for="typeSelect">Type de réclamation</label>
                    <select id="typeSelect" required>
                        <option value="">Sélectionnez un type</option>
                        <option value="Fuite externe">Fuite externe</option>
                        <option value="Fuite interne">Fuite interne</option>
                        <option value="Facture">Facture</option>
                        <option value="Autre">Autre</option>
                    </select>
                </div>

                <div class="form-group" id="compteurSelectGroup" style="display: none;">
                    <label for="compteurSelect">Choisir un compteur:</label>
                    <select id="compteurSelect">
                        <!-- JS populates with user’s compteurs -->
                    </select>
                </div>

                <!-- This dropdown is only shown if type=Facture -->
                <div class="form-group" id="factureSelectGroup" style="display:none;">
                    <label for="factureSelect">Choisir une facture:</label>
                    <select id="factureSelect">
                        <!-- JS will populate with the user’s factures -->
                    </select>
                </div>

                <div class="form-group">
                    <label for="descriptionInput">Description:</label>
                    <textarea id="descriptionInput" rows="3"></textarea>
                </div>

                <!-- If you want file upload:
                <div class="form-group">
                    <label for="pieceJointe">Pièce jointe:</label>
                    <input type="file" id="pieceJointe" accept="image/*,application/pdf">
                </div>
                -->

                <button type="button" class="submit-btn" id="submitReclamationBtn">
                    Envoyer Réclamation
                </button>
            </form>
        </div>

        <!-- Historique des réclamations -->
        <div class="claim-history">
            <h2 class="history-title">Historique des réclamations</h2>
            <div class="reclamation-list" id="reclamationList">
                <!-- JS will dynamically fill this list -->
            </div>
        </div>
    </div>

    <script src="../Scripts/Reclamations.js"></script>
</body>
</html>