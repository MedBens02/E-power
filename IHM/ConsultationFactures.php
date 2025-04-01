<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Consultation des factures</title>
    <link rel="stylesheet" href="Styles/Global.css">
    <link rel="stylesheet" href="Styles/ConsultationFactures.css">
</head>
<body>
    <div class="container">
        <h1>Mes factures </h1>
        <div class="form-group">
            <select id="compteurSelect"></select>
        </div>

        <div class="dashboard-layout">
            <!-- Liste des factures -->
            <div class="invoice-list" id="invoiceList">
                <!-- JS will populate with .invoice-item elements -->
            </div>

            <!-- Détails de la facture -->
            <div class="invoice-details" id="invoiceDetails">
                <div class="detail-header">
                    <h2>Détails de la facture</h2>

                    <div class="detail-item">
                        <span class="detail-label">Client :</span>
                        <span class="detail-value" id="clientName">--</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Adresse :</span>
                        <span class="detail-value" id="clientAddress">--</span>
                    </div>
                </div>

                <div class="detail-item">
                    <span class="detail-label">Consommation :</span>
                    <span class="detail-value" id="consommationKwh">--</span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">Prix HT :</span>
                    <span class="detail-value" id="prixHt">--</span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">TVA (18%) :</span>
                    <span class="detail-value" id="tvaValue">--</span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">Total TTC :</span>
                    <span class="detail-value" id="prixTtc">--</span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">Photo du compteur :</span>
                </div>
                <img src="placeholder-compteur.jpg" alt="Compteur électrique" class="meter-photo" id="compteurPhoto">

                <button class="download-btn" id="downloadPdfBtn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-download" viewBox="0 0 16 16">
                        <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5"/>
                        <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708z"/>
                    </svg>
                    Télécharger PDF
                </button>
            </div>
        </div>
    </div>

    <script src="../Scripts/ConsultationFactures.js"></script>
</body>
</html>
