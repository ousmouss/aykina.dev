<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/db.php';

header('Content-Type: application/json');
$user_id = require_login();
$method  = $_SERVER['REQUEST_METHOD'];

$allowed_types   = ['livre', 'film', 'serie', 'article', 'autre'];
$allowed_statuts = ['a_faire', 'en_cours', 'termine'];

switch ($method) {

    case 'GET':
        $stmt = $pdo->prepare(
            'SELECT id, title, type, status, rating, notes, created_at
             FROM content_tracker WHERE user_id = ? ORDER BY created_at DESC'
        );
        $stmt->execute([$user_id]);
        echo json_encode($stmt->fetchAll());
        break;

    case 'POST':
        $data  = json_decode(file_get_contents('php://input'), true) ?? [];
        $title = trim($data['title'] ?? '');
        $type  = in_array($data['type'] ?? '', $allowed_types, true) ? $data['type'] : 'autre';

        if ($title === '') {
            http_response_code(400);
            echo json_encode(['error' => 'Le titre est requis.']);
            break;
        }

        $stmt = $pdo->prepare(
            "INSERT INTO content_tracker (user_id, title, type, status) VALUES (?, ?, ?, 'a_faire')"
        );
        $stmt->execute([$user_id, $title, $type]);

        echo json_encode([
            'id' => (int) $pdo->lastInsertId(), 'title' => $title, 'type' => $type, 'status' => 'a_faire',
        ]);
        break;

    case 'PUT':
        $data   = json_decode(file_get_contents('php://input'), true) ?? [];
        $id     = (int) ($data['id'] ?? 0);
        $status = $data['status'] ?? null;
        $rating = isset($data['rating']) ? max(0, min(5, (int) $data['rating'])) : null;

        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'ID requis.']);
            break;
        }
        if ($status !== null && !in_array($status, $allowed_statuts, true)) {
            http_response_code(400);
            echo json_encode(['error' => 'Statut invalide.']);
            break;
        }

        $stmt = $pdo->prepare(
            'UPDATE content_tracker SET status = COALESCE(?, status), rating = COALESCE(?, rating)
             WHERE id = ? AND user_id = ?'
        );
        $stmt->execute([$status, $rating, $id, $user_id]);
        echo json_encode(['success' => true]);
        break;

    case 'DELETE':
        $id = (int) ($_GET['id'] ?? 0);

        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'ID requis.']);
            break;
        }

        $stmt = $pdo->prepare('DELETE FROM content_tracker WHERE id = ? AND user_id = ?');
        $stmt->execute([$id, $user_id]);
        echo json_encode(['success' => true]);
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Méthode non autorisée.']);
}
