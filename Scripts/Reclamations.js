document.addEventListener('DOMContentLoaded', () => {
    // DOM elements
    const typeSelect            = document.getElementById('typeSelect');
    const compteurSelectGroup   = document.getElementById('compteurSelectGroup');
    const compteurSelect        = document.getElementById('compteurSelect');
    const factureSelectGroup    = document.getElementById('factureSelectGroup');
    const factureSelect         = document.getElementById('factureSelect');
    const descriptionInput      = document.getElementById('descriptionInput');
    const submitReclamationBtn  = document.getElementById('submitReclamationBtn');
  
    const reclamationListDiv    = document.getElementById('reclamationList');
  
    // 1) On page load, load existing reclamations
    loadReclamations();
  
    // 2) When the user changes the "type"
    typeSelect.addEventListener('change', async () => {
      const selectedType = typeSelect.value;
  
      // Reset/hide everything by default
      compteurSelectGroup.style.display = 'none';
      factureSelectGroup.style.display  = 'none';
  
      if (selectedType === 'Fuite externe' || selectedType === 'Fuite interne') {
        // Show compteur group
        compteurSelectGroup.style.display = 'block';
        // Load compteurs for user
        await loadCompteurs();
      } 
      else if (selectedType === 'Facture') {
        // Show compteur group first, wait for user to pick, then load factures
        compteurSelectGroup.style.display = 'block';
        // Clear the facture dropdown for now
        factureSelect.innerHTML = '';
        // We'll load compteurs
        await loadCompteurs();
      } 
      // else if "Autre" => hide both, do nothing
    });
  
    // 3) If type=Facture, after user picks a compteur => load factures by that compteur
    compteurSelect.addEventListener('change', async () => {
      const selectedType = typeSelect.value;
      if (selectedType === 'Facture') {
        const compteurId = compteurSelect.value;
        if (compteurId) {
          // Load factures for that compteur
          await loadFacturesByCompteur(compteurId);
          // Now show the facture group
          factureSelectGroup.style.display = 'block';
        }
      }
    });
  
    // 4) Submit the reclamation
    submitReclamationBtn.addEventListener('click', async () => {
      const selectedType = typeSelect.value;
      const desc         = descriptionInput.value.trim();
  
      if (!selectedType) {
        alert('Veuillez sélectionner un type.');
        return;
      }
      if (!desc) {
        alert('Veuillez saisir une description.');
        return;
      }
  
      let selectedCompteurId = null;
      let selectedFactureId  = null;
  
      if (selectedType === 'Fuite externe' || selectedType === 'Fuite interne') {
        // Must pick a compteur
        selectedCompteurId = compteurSelect.value;
        if (!selectedCompteurId) {
          alert('Veuillez choisir un compteur.');
          return;
        }
      } 
      else if (selectedType === 'Facture') {
        // Must pick a compteur and then a facture
        selectedCompteurId = compteurSelect.value;
        if (!selectedCompteurId) {
          alert('Veuillez choisir un compteur.');
          return;
        }
        selectedFactureId = factureSelect.value;
        if (!selectedFactureId) {
          alert('Veuillez choisir une facture.');
          return;
        }
      }
      // If "Autre," we have no compteur/facture
  
      // Build formData
      const formData = new FormData();
      formData.append('action', 'create_reclamation');
      formData.append('type', selectedType);
      formData.append('description', desc);
      if (selectedCompteurId) formData.append('compteur_id', selectedCompteurId);
      if (selectedFactureId)  formData.append('facture_id', selectedFactureId);
  
      try {
        const res = await fetch('../Traitement/ReclamationsAjax.php', {
          method: 'POST',
          body: formData
        });
        const data = await res.json();
        if (data.status === 'success') {
          alert('Réclamation créée avec succès!');
          // Reset
          typeSelect.value = '';
          compteurSelectGroup.style.display = 'none';
          factureSelectGroup.style.display  = 'none';
          compteurSelect.innerHTML          = '';
          factureSelect.innerHTML           = '';
          descriptionInput.value            = '';
  
          // Reload the reclamations
          loadReclamations();
        } else {
          alert('Erreur: ' + data.message);
        }
      } catch (err) {
        console.error(err);
        alert('Impossible de contacter le serveur.');
      }
    });
  
  
    // ================== HELPER FUNCTIONS ==================
  
    async function loadReclamations() {
      reclamationListDiv.innerHTML = '';
      try {
        const res = await fetch('../Traitement/ReclamationsAjax.php?action=list_reclamations');
        const data = await res.json();
        if (data.status === 'success') {
          renderReclamationList(data.reclamations);
        } else {
          console.error('Erreur chargement réclamations:', data.message);
        }
      } catch (error) {
        console.error('Erreur réseau:', error);
      }
    }
  
    function renderReclamationList(reclamations) {
      reclamations.forEach((rec) => {
        const item = document.createElement('div');
        item.classList.add('claim-item');
  
        const topLine = document.createElement('p');
        topLine.innerHTML = `<strong>${rec.type}</strong> - ${rec.date_creation}`;
        item.appendChild(topLine);

        if (rec.type === "Facture") {
            const bottomLine = document.createElement('p');
            
            // Create a clickable text using an anchor element
            const factureLink = document.createElement('a');
            factureLink.textContent = `Facture #${rec.facture_id}`;
            factureLink.href = `../Traitement/FacturePdf.php?facture_id=${rec.facture_id}`;
            factureLink.target = '_blank'; 
        
        
            bottomLine.appendChild(factureLink);
            
            // Append additional info about the compteur
            const compInfo = document.createElement('span');
            compInfo.innerHTML = ` - <strong>Compteur #${rec.compteur_id}</strong>`;
            bottomLine.appendChild(compInfo);
        
            item.appendChild(bottomLine);
        }
        
        

        if (rec.type === "Fuite externe" || rec.type === "Fuite interne") {
            const bottomLine = document.createElement('p');
            bottomLine.innerHTML = `<strong>Compteur #${rec.compteur_id}</strong>`;
            item.appendChild(bottomLine);

        }

        const descLine = document.createElement('p');
        descLine.innerHTML = `<strong>Description: </strong> ${rec.description}`;
        item.appendChild(descLine);
  
        // status
        const statusSpan = document.createElement('span');
        statusSpan.classList.add('status');
        if (rec.statut === 'en attente') {
          statusSpan.classList.add('pending');
          statusSpan.textContent = 'En attente';
        } else {
          statusSpan.classList.add('resolved');
          statusSpan.textContent = 'Résolu';
        }
        item.appendChild(statusSpan);
  
        reclamationListDiv.appendChild(item);
      });
    }
  
    async function loadCompteurs() {
      try {
        const res = await fetch('../Traitement/ReclamationsAjax.php?action=list_compteurs');
        const data = await res.json();
        if (data.status === 'success') {
          compteurSelect.innerHTML = '';
          data.compteurs.forEach((cmp) => {
            const opt = document.createElement('option');
            opt.value = cmp.id;
            opt.textContent = `Compteur #${cmp.id} (${cmp.description || 'N/A'})`;
            compteurSelect.appendChild(opt);
          });
        } else {
          console.error('Erreur chargement compteurs:', data.message);
        }
      } catch (err) {
        console.error(err);
      }
    }
  
    async function loadFacturesByCompteur(compteurId) {
      try {
        const res = await fetch(`../Traitement/ReclamationsAjax.php?action=list_factures_by_compteur&compteur_id=${compteurId}`);
        const data = await res.json();
        if (data.status === 'success') {
          factureSelect.innerHTML = '';
          data.factures.forEach((fact) => {
            const opt = document.createElement('option');
            opt.value = fact.id;
            opt.textContent = `Facture #${fact.id} - ${fact.prix_ttc} DH`;
            factureSelect.appendChild(opt);
          });
        } else {
          console.error('Erreur chargement factures:', data.message);
        }
      } catch (err) {
        console.error(err);
      }
    }
  });
  