// File: profile_client.js
document.addEventListener('DOMContentLoaded', () => {
  const profileForm = document.getElementById('profileForm');
  const passwordForm = document.getElementById('passwordForm');
  const successDiv = document.getElementById('successMessage');
  const errorDiv = document.getElementById('errorMessage');
  const historyTable = document.getElementById('historyTable').querySelector('tbody');

  // Helper to show messages
  function showMessage(type, msg) {
    if (type === 'success') {
      successDiv.textContent = msg;
      successDiv.style.display = 'block';
      errorDiv.style.display = 'none';
    } else {
      errorDiv.textContent = msg;
      errorDiv.style.display = 'block';
      successDiv.style.display = 'none';
    }
  }

  // 1) On load, fetch client data
  async function loadClientData() {
    try {
      const response = await fetch('../Traitement/GetClientProfile.php');
      const result = await response.json();
      if (result.status === 'success') {
        // Fill form
        const c = result.client;
        profileForm.querySelector('input[name="client_id"]').value = c.id; // if you want
        profileForm.querySelector('input[name="nom"]').value = c.nom;
        profileForm.querySelector('input[name="prenom"]').value = c.prenom;
        profileForm.querySelector('input[name="adresse"]').value = c.adresse || '';
        profileForm.querySelector('input[name="email"]').value = c.email;

        // Fill hidden client_id in password form
        passwordForm.querySelector('input[name="client_id"]').value = c.id;

        // Populate history table
        const hist = result.history || [];
        historyTable.innerHTML = ''; // clear old
        if (hist.length === 0) {
          // No data
          const row = document.createElement('tr');
          const cell = document.createElement('td');
          cell.colSpan = 3;
          cell.textContent = 'Aucune donnée de consommation disponible.';
          row.appendChild(cell);
          historyTable.appendChild(row);
        } else {
          hist.forEach(h => {
            const row = document.createElement('tr');
            // annee
            let cellAnnee = document.createElement('td');
            cellAnnee.textContent = h.annee;
            row.appendChild(cellAnnee);

            // consommation
            let cellCons = document.createElement('td');
            cellCons.textContent = h.consommation;
            row.appendChild(cellCons);

            // ecart
            let cellEcart = document.createElement('td');
            const ecart = h.ecart || 0;
            if (ecart >= 0) {
              cellEcart.style.color = '#27ae60';
              cellEcart.textContent = `+${ecart} kWh`;
            } else {
              cellEcart.style.color = '#e74c3c';
              cellEcart.textContent = `${ecart} kWh`;
            }
            row.appendChild(cellEcart);

            historyTable.appendChild(row);
          });
        }

      } else {
        showMessage('error', 'Impossible de charger les données du profil.');
      }
    } catch (err) {
      console.error(err);
      showMessage('error', 'Erreur lors du chargement des données.');
    }
  }

  // Call the loading function once
  loadClientData();

  // 2) Handle profile form submission
  if (profileForm) {
    profileForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      showMessage('success', '');
      showMessage('error', '');

      // Collect data
      // (We can ignore client_id or rely on it, but the server uses session anyway.)
      const nom = profileForm.querySelector('input[name="nom"]').value.trim();
      const prenom = profileForm.querySelector('input[name="prenom"]').value.trim();
      const adresse = profileForm.querySelector('input[name="adresse"]').value.trim();
      const email = profileForm.querySelector('input[name="email"]').value.trim();

      // Basic checks
      if (!nom || !prenom || !adresse || !email) {
        showMessage('error', 'Veuillez remplir tous les champs obligatoires.');
        return;
      }

      // optional email check
      if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        showMessage('error', 'Adresse email invalide.');
        return;
      }

      // Build request payload
      const payload = {
        action: 'update_profile',
        nom,
        prenom,
        adresse,
        email
      };

      try {
        const res = await fetch('../Traitement/UpdateClientProfile.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });
        const result = await res.json();

        if (result.status === 'success') {
          showMessage('success', result.message || 'Profil mis à jour.');
        } else {
          switch (result.message) {
            case 'missing_fields':
              showMessage('error', 'Champs requis manquants.');
              break;
            case 'invalid_email_format':
              showMessage('error', 'Format email invalide.');
              break;
            case 'email_already_used':
              showMessage('error', 'Email déjà utilisé.');
              break;
            case 'update_failed':
              showMessage('error', 'Échec de la mise à jour.');
              break;
            default:
              showMessage('error', result.message);
              break;
          }
        }
      } catch (error) {
        console.error(error);
        showMessage('error', 'Erreur de communication avec le serveur.');
      }
    });
  }

  // 3) Handle password form submission
  if (passwordForm) {
    passwordForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      showMessage('success', '');
      showMessage('error', '');

      const oldPassword = passwordForm.querySelector('input[name="old_password"]').value.trim();
      const newPassword = passwordForm.querySelector('input[name="new_password"]').value.trim();

      if (!oldPassword || !newPassword) {
        showMessage('error', 'Veuillez remplir les champs du mot de passe.');
        return;
      }
      if (oldPassword === newPassword) {
        showMessage('error', 'Le nouveau mot de passe ne peut pas être identique à l’ancien.');
        return;
      }

      const payload = {
        action: 'update_password',
        old_password: oldPassword,
        new_password: newPassword
      };

      try {
        const res = await fetch('../Traitement/UpdateClientProfile.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });
        const result = await res.json();
        if (result.status === 'success') {
          showMessage('success', result.message || 'Mot de passe mis à jour.');
          // Reset the form fields if you want
          passwordForm.reset();
        } else {
          switch (result.message) {
            case 'missing_password_fields':
              showMessage('error', 'Champs du mot de passe manquants.');
              break;
            case 'same_as_old':
              showMessage('error', 'Le nouveau mot de passe est identique à l\'ancien.');
              break;
            case 'wrong_old_password':
              showMessage('error', 'Ancien mot de passe incorrect.');
              break;
            default:
              showMessage('error', 'Erreur: ' + result.message);
              break;
          }
        }
      } catch (error) {
        console.error(error);
        showMessage('error', 'Erreur de communication avec le serveur.');
      }
    });
  }
});
