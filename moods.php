<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/db.php';

header('Content-Type: application/json');
$user_id = require_login();
$method  = $_SERVER['REQUEST_METHOD'];

switch ($method) {

    // Historique des 14 derniers jours
    case 'GET':
        $stmt = $pdo->prepare(
            'SELECT id, mood_emoji, note, entry_date FROM moods
             WHERE user_id = ? ORDER BY entry_date DESC LIMIT 14'
        );
        $stmt->execute([$user_id]);
        echo json_encode($stmt->fetchAll());
        break;

    // Enregistrer / mettre à jour l'humeur du jour (une entrée par jour)
    case 'POST':
        $data  = json_decode(file_get_contents('php://input'), true) ?? [];
        $emoji = trim($data['mood_emoji'] ?? '🙂');
        $note  = trim($data['note'] ?? '');
        $today = date('Y-m-d');

        $stmt = $pdo->prepare(
            'INSERT INTO moods (user_id, mood_emoji, note, entry_date) VALUES (?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE mood_emoji = VALUES(mood_emoji), note = VALUES(note)'
        );
        $stmt->execute([$user_id, $emoji, $note, $today]);
        echo json_encode(['success' => true, 'entry_date' => $today]);
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Méthode non autorisée.']);
}
