<?php
session_start();

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'client') {
    // Redirect to login if not a client
    header('Location: login.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord Client</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="Styles/Global.css">
    <link rel="stylesheet" href="Styles/ClientDash.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="logo">Mon Énergie</div>
            <div class="menu-item">
                <a href="ConsultationFactures.php" style="text-decoration: none; color: inherit;">
                    <i class="fas fa-file-invoice"></i> Consulter les factures
                </a>
            </div>
            <div class="menu-item">
                <a href="InputConsommation.php" style="text-decoration: none; color: inherit;">
                    <i class="fas fa-tachometer-alt"></i> Saisir la Consommation
                </a>
            </div>
            <div class="menu-item">
                <i class="fas fa-exclamation-circle"></i> Réclamations
            </div>
            <div class="menu-item">
                <a href="ProfileClient.php" style="text-decoration: none; color: inherit;">
                    <i class="fas fa-user-circle"></i> Profil
                </a>
            </div>

            <div class="menu-item">
                <a href="../Traitement/Signout.php" style="text-decoration: none; color: inherit;">
                    <i class="fas fa-sign-out"></i> Deconnexion
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Résumé -->
            <div class="summary-grid">
                <div class="summary-card">
                    <div class="card-title">Consommation du mois</div>
                    <div class="consumption-value">
                        145 KWH
                    </div>
                    <div class="progress-bar" style="margin-top: 15px;">
                    </div>
                </div>

                <div class="summary-card">
                    <div class="card-title">Dernière facture</div>
                    <div class="bill-status">
                        <i class="fas fa-receipt"></i>
                        <span>148.60 DH</span>
                        <span class="unpaid">(Non payée)</span>
                    </div>
                </div>

                <div class="summary-card">
                    <div class="card-title">Notifications</div>
                    <div class="notification-item">
                        <span class="notification-badge">2 nouvelles</span>
                        <div style="margin-top: 10px;">
                            <div><i class="fas fa-check-circle"></i> Réclamation #45 résolue</div>
                            <div><i class="fas fa-exclamation-triangle"></i> Anomalie détectée</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Graphique -->
            <div class="graph-placeholder">
                Graphique d'évolution de la consommation (intégrer Chart.js ou autre ici)
            </div>
        </div>
    </div>
</body>
</html>
