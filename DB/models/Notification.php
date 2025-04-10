<?php
require_once __DIR__ . '/../DB.php';

class Notification
{
    /**
     * Get the latest $limit notifications for a given client.
     * @param int $clientId
     * @param int $limit
     * @return array
     */
    public static function getLatestByClient($clientId, $limit = 5)
    {
        $pdo = DB::connect();
        $sql = "
            SELECT 
                id, client_id, message, date_envoi, lu
            FROM notifications
            WHERE client_id = :clientId
            -- was: ORDER BY date_envoi DESC
            ORDER BY lu ASC, date_envoi DESC
            LIMIT :lim
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':clientId', $clientId, \PDO::PARAM_INT);
        $stmt->bindValue(':lim', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }



    public static function countUnread($clientId)
    {
        $pdo = DB::connect();
        $sql = "
            SELECT COUNT(*) AS count
            FROM notifications
            WHERE client_id = :clientId
              AND lu = 0
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':clientId', $clientId, \PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ? (int)$row['count'] : 0;
    }

    public static function markAsRead($notifId, $clientId)
    {
        $pdo = DB::connect();
        $sql = "
            UPDATE notifications
            SET lu = TRUE
            WHERE id = :notifId
              AND client_id = :clientId
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':notifId', $notifId, \PDO::PARAM_INT);
        $stmt->bindValue(':clientId', $clientId, \PDO::PARAM_INT);
        return $stmt->execute();
    }
}
