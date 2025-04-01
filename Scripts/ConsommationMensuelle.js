document.addEventListener('DOMContentLoaded', () => {

    const compteurSelect = document.getElementById('compteurSelect');
    const previousDataSection = document.getElementById('previousDataSection');
    const lastMonthConsumption = document.getElementById('lastMonthConsumption');
    const totalConsumption = document.getElementById('totalConsumption');
    const previousPhoto = document.getElementById('previousPhoto');
  
    const currentInputSection = document.getElementById('currentInputSection');
    const currentConsumption = document.getElementById('currentConsumption');
    const compteurPhoto = document.getElementById('compteurPhoto');
    const calculateBtn = document.getElementById('calculateBtn');
  
    const calculationResults = document.getElementById('calculationResults');
    const diffKwh = document.getElementById('diffKwh');
    const trancheLabel = document.getElementById('trancheLabel');
    const prixHt = document.getElementById('prixHt');
    const tvaValue = document.getElementById('tvaValue');
    const prixTtc = document.getElementById('prixTtc');
    const submitConsumptionBtn = document.getElementById('submitConsumptionBtn');
  
    let lastMonthValue = 0;        // For computing difference
    let meterPhotoPath = null;     // For display
    let selectedCompteurId = null; // Which compteur?
    let difference = 0;            // This month's consumption
    let computedHt = 0;            // Computed price HT
  
    // 1) On page load, fetch the client’s compteurs
    async function loadCompteurs() {
      try {
        const response = await fetch('../Traitement/ConsommationAjax.php?action=list_compteurs');
        const result = await response.json();
        if (result.status === 'success') {
          const compteurs = result.compteurs;
          compteurSelect.innerHTML = '';
          compteurs.forEach((c, idx) => {
            let opt = document.createElement('option');
            opt.value = c.id;
            opt.textContent = `Compteur #${c.id} - ${c.description || ''}`;
            compteurSelect.appendChild(opt);
          });
          // By default, select first and trigger load
          if (compteurs.length > 0) {
            selectedCompteurId = compteurs[0].id;
            loadPreviousData(selectedCompteurId);
          }
        } else {
          alert('Impossible de charger vos compteurs.');
        }
      } catch (err) {
        console.error(err);
        alert('Erreur réseau lors du chargement des compteurs.');
      }
    }
  
    // 2) When user changes the select, fetch that compteur’s previous data
    compteurSelect.addEventListener('change', async (e) => {
      selectedCompteurId = e.target.value;
      await loadPreviousData(selectedCompteurId);
    });
  
    // Load the last-month data for a specific compteur
    async function loadPreviousData(compteurId) {
      previousDataSection.style.display = 'none';
      currentInputSection.style.display = 'none';
      calculationResults.style.display = 'none';
  
      try {
        const response = await fetch(`../Traitement/ConsommationAjax.php?action=last_month_data&compteur_id=${compteurId}`);
        const result = await response.json();
        if (result.status === 'success') {
          // Show the previous data
          previousDataSection.style.display = 'block';
          currentInputSection.style.display = 'block';
  
          lastMonthValue = result.totalConsumption || 0;
          lastMonthConsumption.textContent = result.lastMonthValue;
          totalConsumption.textContent = result.totalConsumption || 0;
  
          if (result.previousPhoto) {
            previousPhoto.style.display = 'block';
            previousPhoto.src = '../Uploads/' + result.previousPhoto;
          } else {
            previousPhoto.style.display = 'none';
          }
        } else {
          console.log(result.message);
        }
      } catch (err) {
        console.error(err);
      }
    }
  
    // 3) On “Calculer” => compute difference, find tranche, compute pricing
    calculateBtn.addEventListener('click', async (e) => {
      e.preventDefault();
      const currentVal = parseInt(currentConsumption.value || '0', 10);
      difference = currentVal - lastMonthValue;
      if (difference < 0) {
        alert('La valeur actuelle est inférieure à la dernière valeur : vérifiez votre entrée.');
        return;
      }
      // Determine which tranche
      try {
        const tarifResponse = await fetch(`../Traitement/ConsommationAjax.php?action=compute_tarif&difference=${difference}`);
        const tarifData = await tarifResponse.json();
        if (tarifData.status === 'success') {
          let unitPrice = tarifData.unit_price;
          let tranche = tarifData.tranche_label;
    
          computedHt = difference * unitPrice;
          const tva = computedHt * 0.18;
          const totalTtc = computedHt + tva;
    
          calculationResults.style.display = 'block';
          diffKwh.textContent = difference + 'kWh';
          trancheLabel.textContent = tranche;
          prixHt.textContent = computedHt.toFixed(2) + 'DH';
          tvaValue.textContent = tva.toFixed(2) + 'DH';
          prixTtc.textContent = totalTtc.toFixed(2) + 'DH';
    
        } else {
          alert('Erreur: ' + tarifData.message);
        }
      } catch (err) {
        console.error(err);
        alert('Erreur lors de la récupération du tarif.');
      }
    });
  
    // 4) On “Enregistrer” => send data (compteur_id, difference, current_val, photo) to server
    submitConsumptionBtn.addEventListener('click', async (e) => {
      e.preventDefault();
  
      // If difference <= 0 => error
      if (difference <= 0) {
        alert('Aucune différence calculée, vérifiez vos valeurs.');
        return;
      }
  
      // We'll upload the photo as multipart/form-data, because we have a file
      const formData = new FormData();
      formData.append('action', 'save_monthly_consumption');
      formData.append('compteur_id', selectedCompteurId);
      formData.append('currentValue', currentConsumption.value.trim());
      formData.append('difference', difference);
      formData.append('photo', compteurPhoto.files[0] || '');

      // Also pass the front-end computed prices
      const htValue = parseFloat(prixHt.textContent);

      formData.append('prix_ht', htValue);
  
      try {
        const response = await fetch('../Traitement/ConsommationAjax.php', {
          method: 'POST',
          body: formData
        });
        const result = await response.json();
        if (result.status === 'success') {
          alert('Consommation mensuelle enregistrée avec succès!');
          if (result.factureId) {
            window.open(`../Traitement/FacturePdf.php?facture_id=${result.factureId}`, '_blank');
          }
          // Optionally reset form / reload data
          currentConsumption.value = '';
          compteurPhoto.value = '';
          calculationResults.style.display = 'none';
          loadPreviousData(selectedCompteurId);
        } else {
          alert('Erreur: ' + result.message);
        }
      } catch (err) {
        console.error(err);
        alert('Erreur réseau lors de la sauvegarde.');
      }
    });
  
    // Finally, initial load
    loadCompteurs();
  });
  