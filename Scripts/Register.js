// File: Scripts/Register.js
document.addEventListener('DOMContentLoaded', () => {
    const registerForm = document.getElementById('registerForm');
    const errorDiv     = document.getElementById('errorMessage');
    const successDiv   = document.getElementById('successMessage');
  
    // A helper to show messages
    function showError(msg) {
      if (errorDiv) {
        errorDiv.textContent = msg;
        errorDiv.style.color = 'red';
        errorDiv.style.display = 'block';
      }
      if (successDiv) {
        successDiv.style.display = 'none';
      }
    }
  
    function showSuccess(msg) {
      if (successDiv) {
        successDiv.textContent = msg;
        successDiv.style.color = 'green';
        successDiv.style.display = 'block';
      }
      if (errorDiv) {
        errorDiv.style.display = 'none';
      }
    }
  
    if (registerForm) {
      registerForm.addEventListener('submit', async (e) => {
        e.preventDefault(); // Stop normal form POST
  
        // Clear messages
        showError('');
        showSuccess('');
  
        // Gather the fields
        const nom      = registerForm.querySelector('input[name="nom"]').value.trim();
        const prenom   = registerForm.querySelector('input[name="prenom"]').value.trim();
        const email    = registerForm.querySelector('input[name="email"]').value.trim();
        const password = registerForm.querySelector('input[name="password"]').value.trim();
        const type     = registerForm.querySelector('select[name="type"]').value;
  
        // Basic client-side checks
        if (!nom || !prenom || !email || !password) {
          showError('Veuillez remplir tous les champs.');
          return;
        }
  
        // Optional email format check
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
          showError('Format d\'email invalide.');
          return;
        }
  
        // Build the payload
        const payload = {
          nom,
          prenom,
          email,
          password,
          type
        };
  
        try {
          // Send to RegisterAjax.php
          const response = await fetch('../Traitement/Register.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
          });
          const result = await response.json();
  
          if (result.status === 'success') {
            // Show success message
            if (result.message === 'registered_admin') {
              showSuccess('Compte administrateur créé avec succès!');
            } else if (result.message === 'registered_client') {
              showSuccess('Compte client créé avec succès!');
            } else {
              showSuccess('Inscription réussie.');
            }
  
            // If we want to redirect automatically
            if (result.redirect) {
              // Wait a short moment so user sees the message
              setTimeout(() => {
                window.location.href = result.redirect;
              }, 1500);
            }
          } else {
            // Show error
            switch (result.message) {
              case 'invalid_request':
                showError('Requête invalide.');
                break;
              case 'missing_fields':
                showError('Veuillez remplir tous les champs.');
                break;
              case 'invalid_email_format':
                showError('Adresse email invalide.');
                break;
              case 'registration_failed':
                showError('Échec de l\'inscription. Email déjà utilisé ?');
                break;
              default:
                showError('Erreur: ' + result.message);
                break;
            }
          }
        } catch (err) {
          console.error('Erreur réseau ou serveur', err);
          showError('Impossible de communiquer avec le serveur.');
        }
      });
    }
  });
  