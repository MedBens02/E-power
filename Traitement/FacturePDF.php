<?php
session_start();

// Optionally ensure only the client or an admin can view the PDF
// if (!isset($_SESSION['user_type'])) {
//     die("Unauthorized");
// }

require_once __DIR__ . '/../DB/DB.php';  // your DB connection
require_once __DIR__ . '/../DB/models/Facture.php';
require_once __DIR__ . '/../DB/models/ConsommationMensuelle.php';

// 1) Dompdf library
require_once __DIR__ . '/../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// 2) Get facture_id from GET
$factureId = $_GET['facture_id'] ?? null;
if (!$factureId) {
    die("No facture ID specified.");
}

// 3) Load the invoice data from DB
$facture = Facture::getById($factureId);
if (!$facture) {
    die("Facture introuvable.");
}

$consommation = ConsommationMensuelle::getConsommationById($facture['consommation_id']);
if (!$consommation) {
    die("Consommation introuvable.");
}

// (Optional) Check user is allowed to see this invoice
// if ($_SESSION['user_type'] === 'client' && $_SESSION['user_id'] !== $facture['client_id']) {
//     die("Accès refusé.");
// }

// 4) Build the HTML for the PDF
$html = "
<html>
<head>
  <meta charset='utf-8'>
  <style>
    body {
      font-family: DejaVu Sans, sans-serif;
      margin: 20px;
      font-size: 12px;
    }
    .header {
      text-align: center;
      margin-bottom: 20px;
    }
    .facture-info {
      margin-top: 15px;
      line-height: 1.5;
    }
    .facture-info h2 {
      margin-bottom: 10px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }
    th, td {
      border: 1px solid #ddd;
      padding: 8px;
    }
    th {
      background: #f2f2f2;
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

<hr>
<p>Merci pour votre confiance.</p>

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
