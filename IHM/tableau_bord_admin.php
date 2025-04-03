<?php
session_start();

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    // Redirect to login if not a client
    header('Location: login.php');
    exit();
}

?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de bord Fournisseur</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0; padding: 0; box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        body {
            background: #f0f2f5; display: flex;
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
            font-size: 15px;
            margin-bottom: 17px;
        }
        .menu-item:hover { background: #34495e; }
        /* Contenu principal */
        .main-content {
            margin-left: 250px; padding: 30px;
            width: calc(100% - 250px);
        }
        /* Cartes de stats */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px; margin-bottom: 30px;
        }
        .stat-card {
            background: white; padding: 20px; border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .stat-card i {
            font-size: 24px; margin-bottom: 10px;
        }
        .stat-value {
            font-size: 28px; font-weight: 600; color: #2c3e50;
        }
        .stat-title {
            color: #7f8c8d; font-size: 14px;
        }
        /* Graphiques */
        .charts-container {
            display: grid; grid-template-columns: 1fr 1fr; gap: 20px;
        }
        .chart-box {
            background: white; padding: 20px; border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            min-height: 300px;
        }
        .chart-title {
            margin-bottom: 15px; color: #2c3e50; font-weight: 600;
        }
        .card-1 { color: #e74c3c; }
        .card-2 { color: #27ae60; }
        .card-3 { color: #2980b9; }
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

    <div class="menu-item" id="btn-gestion-import" ><i class="fas fa-file-import"></i> Importation annuelle</div>
  

    <div class="menu-item">
        <a href="../Traitement/Signout.php" style="text-decoration: none; color: inherit;">
            <i class="fas fa-sign-out"></i> Deconnexion
        </a>
    </div>
</div>



<!-- Contenu principal -->
<div class="main-content" id="main-content">
    <h2>Bienvenue sur le tableau de bord</h2>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    
// Au clic sur "Gestion clients"
document.getElementById('btn-gestion-clients').addEventListener('click', () => {
    window.location.href = "../traitement/routeur.php?action=clients";
});

// Au clic sur "Réclamations"
document.getElementById('btn-gestion-reclamations').addEventListener('click', () => {
    window.location.href = "../traitement/routeur.php?action=load_reclamations";
});


document.getElementById('btn-gestion-import').addEventListener('click', () => {
    window.location.href = "../traitement/routeur.php?action=compare_annuel&annee=2024";
});



// On vérifie l'action dans l'URL
const params = new URLSearchParams(window.location.search);
const action = params.get('action');
const recType = params.get('type');
const mainContent = document.getElementById('main-content');
if (action === 'statas') {
    fetch('dashboard.php')
        .then(r => r.text())
        .then(html => { mainContent.innerHTML = html; })
        .catch(err => console.error(err));
}else if (action === 'clients') {
    fetch('clients.php')
        .then(r => r.text())
        .then(html => { mainContent.innerHTML = html; })
        .catch(err => console.error(err));
}
else if (action === 'load_reclamations') {
    fetch('trai_reclamations.php')
        .then(r => r.text())
        .then(html => { mainContent.innerHTML = html; })
        .catch(err => console.error(err));
}
else if (action === 'edit_reclamation') {
    // Récupère ?id=... & ?type=...
    const id   = params.get('id');
    const type = params.get('type');

    fetch(`formulaire_traitement.php?id=${id}&type=${encodeURIComponent(type)}`)
        .then(r => r.text())
        .then(html => {
            document.getElementById('main-content').innerHTML = html;
        })
        .catch(err => console.error('Erreur chargement formulaire:', err));
}else if (action === 'load_reclamations_form') {
    if (recType === "Fuite externe" || recType === "Fuite interne" || recType === "Autre") {
        fetch('formulaire_traitement.php')
        .then(r => r.text())
        .then(html => { mainContent.innerHTML = html; })
        .catch(err => console.error(err));
    } else if (recType === "Facture") {
        fetch('form_traiter_anomalie.php')
        .then(r => r.text())
        .then(html => { mainContent.innerHTML = html; })
        .catch(err => console.error(err));
    }
}else if (action === 'show_compare') {
  fetch('import_annuel.php')
    .then(r => r.text())
    .then(html => { mainContent.innerHTML = html; })
    .catch(console.error);
}





</script>
</body>
</html>



