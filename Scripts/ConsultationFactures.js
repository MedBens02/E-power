document.addEventListener('DOMContentLoaded', () => {
    const compteurSelect   = document.getElementById('compteurSelect');
    const invoiceListDiv   = document.getElementById('invoiceList');
  
    const clientName       = document.getElementById('clientName');
    const clientAddress    = document.getElementById('clientAddress');
    const consommationKwh  = document.getElementById('consommationKwh');
    const prixHt           = document.getElementById('prixHt');
    const tvaValue         = document.getElementById('tvaValue');
    const prixTtc          = document.getElementById('prixTtc');
    const compteurPhoto    = document.getElementById('compteurPhoto');
    const downloadBtn      = document.getElementById('downloadPdfBtn');
  
    let currentFactureId   = null;
    let selectedCompteurId = null;
    let currentFactureClientId   = null;
  
    // 1) Load all compteurs for this client
    async function loadCompteurs() {
      try {
        const res = await fetch('../Traitement/FacturesAjax.php?action=list_compteurs');
        const data = await res.json();
        if (data.status === 'success') {
          const compteurs = data.compteurs;
          populateCompteurSelect(compteurs);
        } else {
          console.error('Erreur chargement compteurs:', data.message);
        }
      } catch (error) {
        console.error('Network error loading compteurs:', error);
      }
    }
  
    // Fill the <select> with the user's compteurs
    function populateCompteurSelect(compteurs) {
      compteurSelect.innerHTML = '';
      if (compteurs.length === 0) {
        // if user has no compteurs, handle it
        const opt = document.createElement('option');
        opt.value = '';
        opt.textContent = 'Aucun compteur';
        compteurSelect.appendChild(opt);
        return;
      }
      compteurs.forEach((c, idx) => {
        const opt = document.createElement('option');
        opt.value = c.id;
        opt.textContent = `Compteur #${c.id} - ${c.description || ''}`;
        compteurSelect.appendChild(opt);
      });
      // By default, select first
      selectedCompteurId = compteurs[0].id;
      compteurSelect.value = selectedCompteurId;
  
      // Then load the factures for that compteur
      loadFacturesForCompteur(selectedCompteurId);
    }
  
    // 2) When user changes the compteur in the dropdown
    compteurSelect.addEventListener('change', (e) => {
      selectedCompteurId = e.target.value;
      loadFacturesForCompteur(selectedCompteurId);
    });
  
    // Load the factures for the chosen compteur
    async function loadFacturesForCompteur(compteurId) {
      invoiceListDiv.innerHTML = '';
      // Clear detail panel
      clearFactureDetails();
  
      try {
        const res = await fetch(`../Traitement/FacturesAjax.php?action=list_factures&compteur_id=${compteurId}`);
        const data = await res.json();
        if (data.status === 'success') {
          renderFactureList(data.factures);
        } else {
          console.error('Erreur de chargement des factures:', data.message);
        }
      } catch (error) {
        console.error('Network error loading factures:', error);
      }
    }
  
    function renderFactureList(factures) {
      invoiceListDiv.innerHTML = '';
  
      factures.forEach((fact) => {
        const invoiceItem = document.createElement('div');
        invoiceItem.classList.add('invoice-item');
  
        // We'll do Mois/Annee or just Facture #...
        const leftDiv = document.createElement('div');
        const dateDiv = document.createElement('div');
        dateDiv.classList.add('invoice-date');
        dateDiv.textContent = `Mois ${fact.mois}/${fact.annee}`;
  
        const amountDiv = document.createElement('div');
        amountDiv.classList.add('invoice-amount');
        amountDiv.textContent = fact.prix_ttc + ' DH TTC';
  
        leftDiv.appendChild(dateDiv);
        leftDiv.appendChild(amountDiv);
  
        // Right part: status
        const statusDiv = document.createElement('div');
        statusDiv.classList.add('invoice-status');
        if (fact.statut_paiement === 'payée') {
          statusDiv.classList.add('status-paid');
          statusDiv.textContent = 'Payée';
        } else {
          statusDiv.classList.add('status-unpaid');
          statusDiv.textContent = 'Impayée';
        }
  
        invoiceItem.appendChild(leftDiv);
        invoiceItem.appendChild(statusDiv);
  
        invoiceItem.addEventListener('click', () => {
          loadFactureDetails(fact.id);
        });
  
        invoiceListDiv.appendChild(invoiceItem);
      });
  
      // Optionally auto-load the first invoice
      if (factures.length > 0) {
        loadFactureDetails(factures[0].id);
      }
    }
  
    // Clear detail panel
    function clearFactureDetails() {
      clientName.textContent      = '--';
      clientAddress.textContent   = '--';
      consommationKwh.textContent= '--';
      prixHt.textContent         = '--';
      tvaValue.textContent       = '--';
      prixTtc.textContent        = '--';
      compteurPhoto.src          = 'placeholder-compteur.jpg';
      currentFactureId           = null;
    }
  
    async function loadFactureDetails(factureId) {
      try {
        const res = await fetch(`../Traitement/FacturesAjax.php?action=get_facture_details&facture_id=${factureId}`);
        const data = await res.json();
        if (data.status === 'success') {
          currentFactureId           = factureId;
          currentFactureClientId     = data.client_id;
          clientName.textContent     = data.client_nom;
          clientAddress.textContent  = data.client_address;
          consommationKwh.textContent= data.consommation_kwh + ' KWH';
          prixHt.textContent         = data.prix_ht + ' DH';
          tvaValue.textContent       = data.tva_calculated + ' DH';
          prixTtc.textContent        = data.prix_ttc + ' DH';
  
          if (data.photo_path) {
            compteurPhoto.src = '../Uploads/' + data.photo_path;
          } else {
            compteurPhoto.src = 'placeholder-compteur.jpg';
          }
        } else {
          console.error('Erreur chargement details facture:', data.message);
        }
      } catch (error) {
        console.error('Network error:', error);
      }
    }
  
    // Download PDF => open new tab
    downloadBtn.addEventListener('click', () => {
      if (!currentFactureId) {
        alert('Aucune facture sélectionnée.');
        return;
      }
      window.open(`../Traitement/FacturePdf.php?facture_id=${currentFactureId}&client_id=${currentFactureClientId}`, '_blank');
    });
  
    // Initial load: 1) load compteurs, then we will pick the first compteur’s factures
    loadCompteurs();
  });
  