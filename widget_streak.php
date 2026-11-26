<?php
/**
 * api/widget_streak.php
 * GET -> { "streak": 6 }
 */
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/session.php';

$userId = require_login();
header('Content-Type: application/json');

$sql = "SELECT DISTINCT DATE(completed_at) AS d
        FROM todo
        WHERE user_id = :uid AND completed_at IS NOT NULL
        ORDER BY d DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute(['uid' => $userId]);
$dateSet = array_flip(array_column($stmt->fetchAll(), 'd'));

$streak = 0;
$cursor = new DateTime('today');

if (!isset($dateSet[$cursor->format('Y-m-d')])) {
    $cursor->modify('-1 day');
}
while (isset($dateSet[$cursor->format('Y-m-d')])) {
    $streak++;
    $cursor->modify('-1 day');
}

echo json_encode(['streak' => $streak]);