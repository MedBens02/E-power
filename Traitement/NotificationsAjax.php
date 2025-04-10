<?php
session_start();
header('Content-Type: application/json');

// Ensure only a client can access
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'client') {
    echo json_encode(['status' => 'error', 'message' => 'unauthorized']);
    exit();
}

require_once __DIR__ . '/../DB/models/Notification.php';

// The client ID from session
$clientId = $_SESSION['user_id'];

// Determine action from GET or POST
$action = $_GET['action'] ?? ($_POST['action'] ?? null);
if (!$action) {
    echo json_encode(['status' => 'error', 'message' => 'no_action']);
    exit();
}

if ($action === 'list') {
    // We can accept a 'limit' param if we want, or default to 5
    $limit = $_GET['limit'] ?? 5;
    $notifications = Notification::getLatestByClient($clientId, (int)$limit);
    echo json_encode([
        'status' => 'success',
        'notifications' => $notifications
    ]);
    exit();
}
elseif ($action === 'mark_read') {
    $notifId = $_POST['notif_id'] ?? null;
    if (!$notifId) {
        echo json_encode(['status' => 'error', 'message' => 'missing_notif_id']);
        exit();
    }
    // Mark as read
    $updated = Notification::markAsRead($notifId, $clientId);
    if ($updated) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'update_failed']);
    }
    exit();
}

echo json_encode(['status' => 'error', 'message' => 'unknown_action']);
exit();
