<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/db.php';

header('Content-Type: application/json');
$user_id = require_login();
$method  = $_SERVER['REQUEST_METHOD'];

switch ($method) {

    case 'GET':
        $stmt = $pdo->prepare(
            "SELECT c.id, c.name, c.color,
                    (SELECT COUNT(*) FROM todo t WHERE t.category_id = c.id) AS task_count
             FROM task_categories c
             WHERE c.user_id = ?
             ORDER BY c.created_at DESC"
        );
        $stmt->execute([$user_id]);
        echo json_encode($stmt->fetchAll());
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true) ?? [];
        $name  = trim($data['name'] ?? '');
        $color = trim($data['color'] ?? '') ?: '#F4645C';

        if ($name === '') {
            http_response_code(400);
            echo json_encode(['error' => 'Le nom est requis.']);
            break;
        }

        $stmt = $pdo->prepare('INSERT INTO task_categories (user_id, name, color) VALUES (?, ?, ?)');
        $stmt->execute([$user_id, $name, $color]);
        echo json_encode(['id' => (int) $pdo->lastInsertId(), 'name' => $name, 'color' => $color]);
        break;

    case 'DELETE':
        $id = (int) ($_GET['id'] ?? 0);
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'ID requis.']);
            break;
        }
        $stmt = $pdo->prepare('DELETE FROM task_categories WHERE id = ? AND user_id = ?');
        $stmt->execute([$id, $user_id]);
        echo json_encode(['success' => true]);
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Méthode non autorisée.']);
}
