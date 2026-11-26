<?php
/**
 * api/widget_deadlines.php
 * GET -> [{ "id":1, "title":"...", "due_date":"2026-08-25", "is_late":false }, ...]
 */
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/session.php';

$userId = require_login();
header('Content-Type: application/json');

$sql = "SELECT id, title, due_date,
               (due_date < CURDATE() AND status != 'termine') AS is_late
        FROM todo
        WHERE user_id = :uid
          AND due_date IS NOT NULL
          AND status != 'termine'
        ORDER BY due_date ASC
        LIMIT 5";
$stmt = $pdo->prepare($sql);
$stmt->execute(['uid' => $userId]);

$rows = $stmt->fetchAll();
foreach ($rows as &$r) {
    $r['is_late'] = (bool) $r['is_late'];
}

echo json_encode($rows);