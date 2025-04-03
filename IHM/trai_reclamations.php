<?php
session_start();

// Récupération des réclamations depuis la session
$reclamations = $_SESSION['reclamations'] ?? [];

/** 
 * Fonctions internes pour l'affichage
 * (sans accès BDD direct).
 */
function getTypeClass($type) {
    return match($type) {
        'Fuite externe' => 'type-leak',
        'Facture'       => 'type-bill',
        default         => 'type-other',
    };
}

function getTypeIcon($type) {
    return match($type) {
        'Fuite externe' => 'fa-tint',
        'Facture'       => 'fa-file-invoice',
        default         => 'fa-question-circle',
    };
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des réclamations</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f8f9fa;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .header {
            background: #ffffff;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .complaints-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }
        .complaint-item {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            transition: transform 0.2s;
        }
        .complaint-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .complaint-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        .complaint-type {
            font-weight: 600;
            font-size: 0.9rem;
            padding: 4px 12px;
            border-radius: 15px;
        }
        .type-leak {
            background: #fff3e0; color: #ef6c00;
        }
        .type-bill {
            background: #e3f2fd; color: #1976d2;
        }
        .type-other {
            background: #f5f5f5; color: #616161;
        }
        .btn {
            padding: 8px 16px;
            background-color: #007bff;
            color: white; border: none; border-radius: 5px;
            cursor: pointer; text-decoration: none;
        }
        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h2><?= count($reclamations) ?> Réclamations en attente</h2>
    </div>

    <div class="complaints-grid">
        <?php foreach ($reclamations as $reclamation): ?>
            <div class="complaint-item">
                <div class="complaint-meta">
                    <span class="complaint-type <?= getTypeClass($reclamation['type']) ?>">
                        <i class="fas <?= getTypeIcon($reclamation['type']) ?>"></i>
                        <?= htmlspecialchars($reclamation['type']) ?>
                    </span>
                    <span><?= htmlspecialchars($reclamation['date_creation']) ?></span>
                </div>
                <p><?= htmlspecialchars($reclamation['description']) ?></p>
                <div style="display:flex;justify-content:space-between;align-items:center;">
                    <small>Client ID: <?= htmlspecialchars($reclamation['client_id']) ?></small>
                    <!-- Bouton "Traiter" : on va vers routeur pour charger la réclamation -->
                    <a class="btn"
                       href="../traitement/routeur.php?action=load_form_reclamation&id=<?= $reclamation['id'] ?>&type=<?= urlencode($reclamation['type']) ?>">
                       Traiter
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
</body>
</html>
