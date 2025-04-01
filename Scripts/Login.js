// File: Scripts/Login.js
document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('loginForm');
    const errorDiv  = document.getElementById('errorMessage');
  
    // A helper to show error text
    function showError(msg) {
      if (errorDiv) {
        errorDiv.textContent = msg;
        errorDiv.style.color = 'red';
      }
    }
  
    if (loginForm) {
      loginForm.addEventListener('submit', async (e) => {
        e.preventDefault(); // Stop normal form submission
  
        // Clear any old errors
        showError('');
  
        // Collect fields
        const email = loginForm.querySelector('input[name="email"]').value.trim();
        const password = loginForm.querySelector('input[name="password"]').value.trim();
        const type = loginForm.querySelector('select[name="type"]').value;
  
        // Basic client-side checks
        if (!email || !password) {
          showError('Veuillez remplir tous les champs');
          return;
        }
  
        // Prepare data for Ajax
        const payload = {
          email: email,
          password: password,
          type: type
        };
  
        try {
          const response = await fetch('../Traitement/Login.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
          });
          const result = await response.json();
  
          if (result.status === 'success') {
            // If success, redirect to the indicated page
            window.location.href = result.redirect;
          } else {
            // Show the error message from the server
            if (result.message === 'invalid_credentials') {
              showError('Identifiants incorrects. Veuillez réessayer.');
            } else if (result.message === 'missing_fields') {
              showError('Champs requis manquants.');
            } else {
              showError('Une erreur s\'est produite. Réessayez plus tard.');
            }
          }
        } catch (error) {
          console.error('Erreur réseau ou serveur', error);
          showError('Impossible de communiquer avec le serveur.');
        }
      });
    }
  });
  