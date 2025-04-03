<?php
session_start();

require_once('../bd/reclamation.php');
require_once('../bd/facture.php');
require_once('../bd/client.php');
require_once('../bd/stats.php');
require_once('../bd/consommation.php');
/**
 * On regarde l'action
 */
if (isset($_GET['action'])) {
    $action = $_GET['action'];


    if ($action === 'load_stats') {
       
        $_SESSION['stats'] = [
            'total_clients' => getTotalClients(),
            'total_factures' => getTotalFactures(),
            'total_consommation' => getTotalConsommation(),
            'reclamations' => getReclamationsByType(),
            'paiement' => getStatistiquesPaiement()
        ];

        
        header("Location: ../ihm/dashboard.php");
        exit;
    }

    


    // ------------------------------
    // 1) Charger la liste de réclamations
    // ------------------------------
    if ($action === 'load_reclamations') {
        $_SESSION['reclamations'] = getAllReclamations(); // BDD
        // Redirection vers la page IHM
        header("Location: ../ihm/tableau_bord_admin.php?action=load_reclamations");
        exit;
    }

    // ------------------------------
    // 2) Préparer le formulaire de réclamation
    // ------------------------------
    if ($action === 'load_form_reclamation') {
        $id   = $_GET['id']   ?? null;
        $type = $_GET['type'] ?? null;
    
        if (!$id || !$type) {
            $_SESSION['error'] = "Paramètres manquants (id ou type).";
            header("Location: ../ihm/tableau_bord_admin.php?action=load_reclamations");
            exit;
        }
    
        $reclamation = getReclamationById($id);
        if (!$reclamation) {
            $_SESSION['error'] = "Réclamation introuvable en BDD.";
            header("Location: ../ihm/tableau_bord_admin.php?action=load_reclamations");
            exit;
        }
        // Stocker la réclamation dans la session
        $_SESSION['reclamation_to_edit'] = $reclamation;
    
        // Charger les 2 dernières consommations pour ce client
        $client_id = $reclamation['client_id'];
        $consos = getDernieresConsommations($client_id, 2); 
        // getDernieresConsommations() = fonction BD qui renvoie les 2 plus récentes 
        // de la table consommations_mensuelles
    
        $_SESSION['consommations_client'] = $consos;
    
        // Rediriger vers le fichier qui affiche le formulaire
        header("Location: ../ihm/tableau_bord_admin.php?action=load_reclamations_form&type=".$reclamation['type']);

        exit;
    }



    
    if ($action === 'update_consommation' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $reclamation_id   = $_POST['reclamation_id'];
        $client_id        = $_POST['client_id'];
        $valeur_compteur  = $_POST['valeur_compteur'];
        $message = $_POST['message'];
    

        // 1. Mettre à jour le compteur total
        //    On peut relire le compteur actuel, et faire la différence, ou 
        //    recalculer la conso totale. Ex.:
        $compteur = getCompteurByClient($client_id); 
        // si tu as la fonction getCompteurByClient(...) => renvoie la ligne de 'compteurs'
        // (id, client_id, consommation_totale, ...)
    
        if ($compteur) {
            $newConsommationTotale = $compteur['consommation_totale'] + ($valeur_compteur); 
            // ou autrement si tu as besoin d'une logique plus précise
            updateCompteurTotal($compteur['id'], $newConsommationTotale);
        }
    
        // 2. Mettre à jour la dernière ligne de consommations_mensuelles
        //    On suppose qu'on modifie la "dernière conso" => ex: id max pour ce client
        $lastConso = getDerniereConso($client_id);
        if ($lastConso) {
            updateConsommation($lastConso['id'], $valeur_compteur);
        }
    
        // 3. Mettre à jour la facture associée
        //    On suppose qu'on récupère la dernière facture pour ce client, 
        //    ou la facture qui correspond à la conso "lastConso".
        $lastFacture = getFactureByConsoId($lastConso['id']);
        if ($lastFacture) {
            // On recalcule le nouveau prix HT :
            $newPrixHT = recalculerPrixHT($valeur_compteur);
            updateFacturePrix($lastFacture['id'], $newPrixHT);
        }
    
        // 4. Optionnel : marquer la réclamation comme résolue
        updateReclamationStatus($reclamation_id, 'résolu');
        $reclamation = getReclamationById($reclamation_id);
        // 2. Envoyer une notification au clien        $reclamation = getReclamationById($id);
        if ($reclamation) {
            ajouterNotification($reclamation['client_id'], $message);
        }
        // 5. Rediriger vers la liste des réclamations 
        $_SESSION['reclamations'] = getAllReclamations();
        header("Location: ../ihm/tableau_bord_admin.php?action=load_reclamations");
        exit;
    }
    
    if ($action === 'load_importation_annuelle') {
        // Charger la liste des consommations existantes
        require_once('../bd/consommation_annuelle.php');
        $all = getAllConsommationsAnnuelles();  // fonction BD
        // Stocker en session pour affichage
        $_SESSION['consommations_annuelles'] = $all;
    
        // Rediriger vers tableau_bord_admin.php?action=importation
        header("Location: ../ihm/tableau_bord_admin.php?action=importation");
        exit;
    }
    
    if ($action === 'importer_consommation_annuelle' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_FILES['fichier_txt']) || $_FILES['fichier_txt']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = "Erreur lors du téléchargement du fichier.";
            header("Location: ../ihm/tableau_bord_admin.php?action=importation");
            exit;
        }
    
        // Déplacer le fichier dans ../uploads
        $tmpName = $_FILES['fichier_txt']['tmp_name'];
        $fileName = basename($_FILES['fichier_txt']['name']);
        $destination = "../uploads/".$fileName;
        move_uploaded_file($tmpName, $destination);
    
        // Importer via fonction BD
        require_once('../bd/consommation_annuelle.php');
        $nbInserts = importerConsommationAnnuelle($destination);
    
        // Recharger la liste après l’import
        $all = getAllConsommationsAnnuelles();
        $_SESSION['consommations_annuelles'] = $all;
    
        $_SESSION['info'] = "Importation réussie ($nbInserts lignes insérées).";
        header("Location: ../ihm/tableau_bord_admin.php?action=importation");
        exit;
    }
    



    // ------------------------------
    // 3) Traiter la réclamation (POST)
    // ------------------------------
    if ($action === 'traiter_reclamation' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $id          = $_POST['reclamation_id'];
        $message     = $_POST['message'];
        $nouveauPrix = $_POST['nouveau_prix'] ?? null;
        
        // Déterminer quelle action de facture est demandée
        $actionFacture = $_POST['action_facture'] ?? null;
    
        // 1. Marquer la réclamation comme résolue
        updateReclamationStatus($id, 'résolu');
        $reclamation = getReclamationById($id);
        // 2. Envoyer une notification au clien        $reclamation = getReclamationById($id);
        if ($reclamation) {
            ajouterNotification($reclamation['client_id'], $message);
        }
    
        // 3. Gérer les différentes actions "facture"
        if ($reclamation && $reclamation['type'] === 'Facture') {
            if ($actionFacture === 'valider_prix') {
                // Correction du prix
                if (!empty($nouveauPrix) && is_numeric($nouveauPrix)) {
                    updateFactureMontant($reclamation['client_id'], $nouveauPrix);
                }
            } elseif ($actionFacture === 'annuler_renouveler') {
                // Annuler la consommation et la facture
                annulerConsommation($reclamation['client_id']);
                supprimerDerniereFacture($reclamation['client_id']);
                // L’utilisateur pourra ressaisir plus tard une nouvelle conso
            }
        }
    
        // Actualiser la session des réclamations
        $_SESSION['reclamations'] = getAllReclamations();
    
        // On revient au tableau de bord
        header("Location: ../ihm/tableau_bord_admin.php?action=load_reclamations");
        exit;
    }
    

    // ------------------------------
    // 4) Gérer l'ajout / suppr / modif de clients
    // ------------------------------
    if ($action === 'ajouter_client' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        ajouterClient($_POST['nom'], $_POST['prenom'], $_POST['email'], $_POST['adresse'], $_POST['mot_de_passe']);
    }

    if ($action === 'supprimer_client' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        supprimerClient($_POST['id']);
    }

    if ($action === 'modifier_client' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        modifierClient(
            $_POST['id'],
            $_POST['nom'],
            $_POST['prenom'],
            $_POST['email'],
            $_POST['adresse'],
            $_POST['mot_de_passe'] ?: null
        );
    }

    // Actualiser la liste de clients dans la session
    $_SESSION['clients'] = getAllClients();

    // On redirige vers le dashboard (affichage clients / réclamations, etc.)
    header("Location: ../ihm/tableau_bord_admin.php?action=clients");
    exit;
}
