<?php
session_start();
$stats = $_SESSION['stats'] ?? [];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Statistiques</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
         * {
            margin: 0; padding: 0; box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        body {
            background: #f0f2f5; display: flex;
            margin-left: 250px; /* Ensure content isn't hidden behind sidebar */
            padding: 20px;
        }
        /* Sidebar */
        .sidebar {
            background: #2c3e50; color: white;
            width: 250px; min-height: 100vh; padding: 20px; position: fixed;
        }
        .logo {
            font-size: 24px; margin-bottom: 40px; text-align: center;
        }
        .menu-item {
            padding: 15px; margin: 10px 0; border-radius: 8px;
            cursor: pointer; transition: 0.3s;
        }
        .menu-item:hover { background: #34495e; }
        /* Main Content */
        .main-content {
            width: 100%; padding: 30px;
        }
        h2, h3 {
            text-align: center;
            color: #333;
        }
        /* Stats Cards */
        .stats-container {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            justify-content: center;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white; padding: 20px; border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin: 10px; width: 250px;
            text-align: center;
        }
        .stat-card h3 {
            font-size: 18px;
            color: #333;
        }
        .stat-card p {
            font-size: 28px;
            font-weight: 600;
            color: #2c3e50;
        }
        /* Chart Boxes */
        .chart-box {
            background: white; padding: 20px; border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin: 20px auto; width: 100%; max-width: 600px;
        }
        .chart-container {
            position: relative;
            margin: auto;
            height: 300px;
            width: 100%;
        }
        /* Style for the Sidebar */
        .sidebar {
            background: #2c3e50; color: white;
            width: 250px; min-height: 100vh; padding: 20px; position: fixed;
            left: 0;
            top: 0;
        }
        .menu-item {
            font-size: 15px;
            margin-bottom: 17px;
        }
        .chart-row {
        display: flex;
         gap: 20px;
        justify-content: center;
         margin-bottom: 30px;
         }

.chart-box {
    flex: 1;
    max-width: 600px;
}
.menu-item {
            padding: 15px; margin: 10px 0; border-radius: 8px;
            cursor: pointer; transition: 0.3s;
            font-size: 15px;
            margin-bottom: 17px;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <div class="logo">E-Power Admin</div>
    <div class="menu-item" id="btn-gestion-clients">
        <i class="fas fa-users"></i> Gestion clients
    </div>
    <div class="menu-item" id="btn-gestion-reclamations">
        <i class="fas fa-exclamation-circle"></i> Réclamations
    </div>
    
    <div class="menu-item" onclick="window.location.href='../traitement/routeur.php?action=load_stats';">
        <i class="fas fa-chart-bar"></i> Dashboard
    </div>
    <div class="menu-item" id="btn-gestion-import"><i class="fas fa-file-import"></i> Importation annuelle</div>
    <div class="menu-item">
        <a href="../Traitement/Signout.php" style="text-decoration: none; color: inherit;">
            <i class="fas fa-sign-out"></i> Deconnexion
        </a>
    </div>
</div>

<!-- Main Content -->
<div class="main-content">
    <h2>Dashboard - Statistiques</h2>

    <!-- Stats Cards -->
    <div class="stats-container">
        <div class="stat-card">
            <h3>Total Clients</h3>
            <p><?= htmlspecialchars($stats['total_clients'] ?? 0) ?></p>
        </div>
        <div class="stat-card">
            <h3>Total Factures</h3>
            <p><?= htmlspecialchars($stats['total_factures'] ?? 0) ?></p>
        </div>
        <div class="stat-card">
            <h3>Total Consommation (KWH)</h3>
            <p><?= htmlspecialchars($stats['total_consommation'] ?? 0) ?></p>
        </div>
    </div>

    <div class="chart-row">
    <div class="chart-box">
        <h3>Répartition des Réclamations</h3>
        <div class="chart-container">
            <canvas id="reclamationsChart"></canvas>
        </div>
    </div>

    <div class="chart-box">
        <h3>Statistiques de Paiement des Factures</h3>
        <div class="chart-container">
            <canvas id="paiementChart"></canvas>
        </div>
    </div>
</div>


</div>

<script>
    // Vérification des données pour éviter les erreurs
    const reclamationsData = <?= json_encode($stats['reclamations'] ?? []) ?>;
    const paiementData = <?= json_encode($stats['paiement'] ?? []) ?>;

    // Préparation des données pour les réclamations
    const reclamationsLabels = reclamationsData.length > 0 ? reclamationsData.map(item => item.type) : ['Aucune'];
    const reclamationsValues = reclamationsData.length > 0 ? reclamationsData.map(item => parseInt(item.total)) : [0];

    // Création du graphique des réclamations
    const reclamationsChart = new Chart(document.getElementById('reclamationsChart'), {
        type: 'pie',
        data: {
            labels: reclamationsLabels,
            datasets: [{
                label: 'Réclamations',
                data: reclamationsValues,
                backgroundColor: ['#3498db', '#e74c3c', '#f39c12', '#2ecc71']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Préparation des données pour les paiements
    const paiementLabels = paiementData.length > 0 ? paiementData.map(item => item.statut_paiement) : ['Aucune'];
    const paiementValues = paiementData.length > 0 ? paiementData.map(item => parseInt(item.total)) : [0];

    // Création du graphique des paiements
    const paiementChart = new Chart(document.getElementById('paiementChart'), {
        type: 'doughnut',
        data: {
            labels: paiementLabels,
            datasets: [{
                label: 'Factures',
                data: paiementValues,
                backgroundColor: ['#2ecc71', '#e74c3c']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });


    document.getElementById('btn-gestion-clients').addEventListener('click', () => {
        window.location.href = "../traitement/routeur.php?action=clients";
});

document.getElementById('btn-gestion-reclamations').addEventListener('click', () => {
    
    window.location.href = "../traitement/routeur.php?action=load_reclamations";
});

document.getElementById('btn-gestion-import').addEventListener('click', () => {
    
    window.location.href = "../traitement/routeur.php?action=compare_annuel&annee=2025";
});
</script>

</body>
</html>
