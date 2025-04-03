<?php
session_start();

// Optionally ensure only the client or an admin can view the PDF
if ($_SESSION['user_type'] !== 'client' && $_SESSION['user_id'] !== $_GET['client_id']) {
    // Or implement your own access check
    die("Accès refusé.");
}

require_once __DIR__ . '/../DB/DB.php';
require_once __DIR__ . '/../DB/models/Facture.php';
require_once __DIR__ . '/../DB/models/ConsommationMensuelle.php';

// 1) Load Dompdf library
require_once __DIR__ . '/../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// 2) Get facture_id from GET
$factureId = $_GET['facture_id'] ?? null;
if (!$factureId) {
    die("No facture ID specified.");
}

// 3) Load invoice data from DB
$facture = Facture::getById($factureId);
if (!$facture) {
    die("Facture introuvable.");
}

$consommation = ConsommationMensuelle::getConsommationById($facture['consommation_id']);
if (!$consommation) {
    die("Consommation introuvable.");
}

// Build path for compteur photo if available
$photoCompteur = !empty($consommation['photo_compteur']) 
    ? '../Uploads/' . $consommation['photo_compteur']
    : '';

// 4) Build HTML for the PDF
$html = "
<html>
<head>
  <meta charset='utf-8'>
  <style>
    body {
      font-family: 'DejaVu Sans', sans-serif;
      margin: 30px;
      font-size: 13px;
      color: #333;
    }
    .header {
      text-align: center;
      margin-bottom: 30px;
    }
    .header h1 {
      font-size: 24px;
      margin-bottom: 5px;
      color: #3498db;
    }
    .header p {
      font-size: 14px;
      color: #666;
    }
    .facture-info {
      margin-bottom: 20px;
      border: 1px solid #ddd;
      padding: 15px;
      border-radius: 8px;
    }
    .facture-info h2 {
      margin-bottom: 15px;
      font-size: 20px;
      border-bottom: 2px solid #3498db;
      padding-bottom: 5px;
      color: #2c3e50;
    }
    .facture-info p {
      margin: 8px 0;
      line-height: 1.5;
    }
    .facture-info p strong {
      color: #2c3e50;
    }
    .compteur-photo {
      margin-top: 20px;
      text-align: center;
    }
    .compteur-photo img {
      max-width: 300px;
      border: 1px solid #ddd;
      border-radius: 8px;
    }
    .footer {
      text-align: center;
      margin-top: 30px;
      font-style: italic;
      color: #888;
    }
  </style>
</head>
<body>
  <div class='header'>
    <h1>Facture #{$facture['id']}</h1>
    <p>Date: " . date('d-m-Y') . "</p>
  </div>

  <div class='facture-info'>
    <h2>Informations Client</h2>
    <p><strong>Client:</strong> {$facture['client_nom']} {$facture['client_prenom']}</p>
    <p><strong>Facture ID:</strong> {$facture['id']}</p>
    <p><strong>Consommation ID:</strong> {$facture['consommation_id']}</p>
    <p><strong>Compteur ID:</strong> {$consommation['compteur_id']}</p>
    <p><strong>Consommation ce mois:</strong> {$consommation['valeur_compteur']} kWh</p>
    <p><strong>Prix HT:</strong> {$facture['prix_ht']} DH</p>
    <p><strong>TVA:</strong> {$facture['tva']}%</p>
    <p><strong>Prix TTC:</strong> {$facture['prix_ttc']} DH</p>
  </div>
";

if (!empty($photoCompteur)) {
  $html .= "
  <div class='compteur-photo'>
    <p><strong>Photo du compteur:</strong></p>
    <img src='{$photoCompteur}' alt='Photo du compteur'>
  </div>
  ";
}

$html .= "
  <div class='footer'>
    <p>Merci pour votre confiance.</p>
  </div>
</body>
</html>
";

// 5) Generate PDF with Dompdf
$options = new Options();
$options->set('defaultFont', 'DejaVu Sans'); 
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// 6) Stream inline to the browser
$dompdf->stream("Facture-{$factureId}.pdf", ["Attachment" => false]);
