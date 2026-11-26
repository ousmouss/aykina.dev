<?php
/**
 * api/widget_categories.php
 * GET -> [{ "name": "...", "color": "#...", "total": 3 }, ...]
 */
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/session.php';

$userId = require_login();
header('Content-Type: application/json');

$sql = "SELECT tc.name, tc.color, COUNT(t.id) AS total
        FROM task_categories tc
        LEFT JOIN todo t ON t.category_id = tc.id AND t.user_id = :uid
        WHERE tc.user_id = :uid
        GROUP BY tc.id, tc.name, tc.color
        HAVING total > 0
        ORDER BY total DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute(['uid' => $userId]);

echo json_encode($stmt->fetchAll());