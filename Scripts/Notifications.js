document.addEventListener('DOMContentLoaded', () => {
  const notifListDiv    = document.getElementById('notificationList');
  const modalOverlay    = document.getElementById('modalOverlay');
  const notifModal      = document.getElementById('notifModal');
  const modalTitle      = document.getElementById('modalTitle');
  const modalMessage    = document.getElementById('modalMessage');
  const confirmBtn      = document.getElementById('confirmBtn');

  let currentNotifId = null; // track which notification is open

  // 1) Load notifications
  loadNotifications();

  // 2) Render notifications
  function renderNotifications(notifs) {
    notifListDiv.innerHTML = '';
    notifs.forEach(notif => {
      const itemDiv = document.createElement('div');
      itemDiv.style.marginBottom = '10px';

      // Icon: unread => bell, read => check-circle
      const iconClass = notif.lu == 0 ? 'fa-bell' : 'fa-check-circle';

      itemDiv.innerHTML = `
        <i class="fas ${iconClass}" style="margin-right:8px;"></i>
        ${notif.message}
      `;
      // If unread => user can click to show details
      if (notif.lu == 0) {
        itemDiv.style.cursor = 'pointer';
        itemDiv.addEventListener('click', () => openModal(notif));
      } else {
        itemDiv.style.opacity = '0.7'; // or any style for read
      }
      notifListDiv.appendChild(itemDiv);
    });
  }

  // 3) Open the modal with the details of the clicked notification
  function openModal(notif) {
    currentNotifId = notif.id;
    modalTitle.textContent = 'Détails de la notification #' + notif.id;
    modalMessage.textContent = notif.message;
    // Show the modal + overlay
    modalOverlay.style.display = 'block';
    notifModal.style.display   = 'block';
  }

  // 4) Confirm => mark as read, then close
  confirmBtn.addEventListener('click', async () => {
    if (!currentNotifId) return closeModal();

    // Mark as read via Ajax
    try {
      const formData = new FormData();
      formData.append('action', 'mark_read');
      formData.append('notif_id', currentNotifId);

      const res = await fetch('../Traitement/NotificationsAjax.php', {
        method: 'POST',
        body: formData
      });
      const data = await res.json();
      if (data.status === 'success') {
        // reload the list
        loadNotifications();
      } else {
        console.error('Erreur:', data.message);
      }
    } catch (err) {
      console.error('Network error:', err);
    }

    closeModal();
  });

  // 5) Close the modal
  function closeModal() {
    modalOverlay.style.display = 'none';
    notifModal.style.display   = 'none';
    currentNotifId = null; // reset
  }

  // 6) loadNotifications fetch
  async function loadNotifications() {
    try {
      const response = await fetch('../Traitement/NotificationsAjax.php?action=list&limit=5');
      const data = await response.json();
      if (data.status === 'success') {
        renderNotifications(data.notifications);
      } else {
        console.error('Erreur liste notifications:', data.message);
      }
    } catch (err) {
      console.error('Erreur réseau:', err);
    }
  }
});
