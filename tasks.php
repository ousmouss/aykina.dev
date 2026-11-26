<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/db.php';

header('Content-Type: application/json');
$user_id = require_login();
$method  = $_SERVER['REQUEST_METHOD'];

$allowed_priorities = ['faible', 'moderee', 'haute'];
$allowed_statuses   = ['non_commence', 'en_cours', 'termine'];

switch ($method) {

    case 'GET':
        // Compteurs pour les donuts "Task Status"
        if (isset($_GET['stats'])) {
            $stmt = $pdo->prepare('SELECT status, COUNT(*) AS n FROM todo WHERE user_id = ? GROUP BY status');
            $stmt->execute([$user_id]);
            $counts = ['non_commence' => 0, 'en_cours' => 0, 'termine' => 0];
            foreach ($stmt->fetchAll() as $row) {
                $counts[$row['status']] = (int) $row['n'];
            }
            $counts['total'] = array_sum($counts);
            echo json_encode($counts);
            break;
        }

        $sql = "SELECT t.id, t.title, t.description, t.priority, t.status, t.image_url,
                       t.due_date, t.created_at, t.completed_at, t.category_id,
                       c.name AS category_name, c.color AS category_color
                FROM todo t
                LEFT JOIN task_categories c ON c.id = t.category_id
                WHERE t.user_id = ?";
        $params = [$user_id];

        $scope = $_GET['scope'] ?? '';
        if ($scope === 'today') {
            $sql .= ' AND (t.due_date = CURDATE() OR (t.due_date IS NULL AND DATE(t.created_at) = CURDATE()))';
        } elseif ($scope === 'vital') {
            $sql .= " AND t.priority = 'haute'";
        } elseif ($scope === 'completed') {
            $sql .= " AND t.status = 'termine'";
        }

        if (!empty($_GET['status']) && in_array($_GET['status'], $allowed_statuses, true)) {
            $sql .= ' AND t.status = ?';
            $params[] = $_GET['status'];
        }

        if (!empty($_GET['category_id'])) {
            $sql .= ' AND t.category_id = ?';
            $params[] = (int) $_GET['category_id'];
        }

        if (!empty($_GET['q'])) {
            $sql .= ' AND (t.title LIKE ? OR t.description LIKE ?)';
            $like = '%' . $_GET['q'] . '%';
            $params[] = $like;
            $params[] = $like;
        }

        $sql .= $scope === 'completed'
            ? ' ORDER BY t.completed_at DESC'
            : ' ORDER BY t.created_at DESC';

        if (!empty($_GET['limit'])) {
            $sql .= ' LIMIT ' . max(1, (int) $_GET['limit']);
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        echo json_encode($stmt->fetchAll());
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true) ?? [];
        $title = trim($data['title'] ?? '');

        if ($title === '') {
            http_response_code(400);
            echo json_encode(['error' => 'Le titre est requis.']);
            break;
        }

        $priority = in_array($data['priority'] ?? '', $allowed_priorities, true) ? $data['priority'] : 'moderee';
        $status   = in_array($data['status'] ?? '', $allowed_statuses, true) ? $data['status'] : 'non_commence';
        $description = trim($data['description'] ?? '') ?: null;
        $due_date    = !empty($data['due_date']) ? $data['due_date'] : null;
        $image_url   = !empty($data['image_url']) ? trim($data['image_url']) : null;
        $category_id = !empty($data['category_id']) ? (int) $data['category_id'] : null;
        $completed_at = $status === 'termine' ? date('Y-m-d H:i:s') : null;

        $stmt = $pdo->prepare(
            'INSERT INTO todo (user_id, category_id, title, description, priority, status, image_url, due_date, completed_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([$user_id, $category_id, $title, $description, $priority, $status, $image_url, $due_date, $completed_at]);

        echo json_encode(['id' => (int) $pdo->lastInsertId()]);
        break;

    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true) ?? [];
        $id = (int) ($data['id'] ?? 0);

        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'ID requis.']);
            break;
        }

        // Vérifie que la tâche appartient bien à l'utilisateur
        $check = $pdo->prepare('SELECT status FROM todo WHERE id = ? AND user_id = ?');
        $check->execute([$id, $user_id]);
        $existing = $check->fetch();
        if (!$existing) {
            http_response_code(404);
            echo json_encode(['error' => 'Tâche introuvable.']);
            break;
        }

        $fields = [];
        $params = [];

        if (array_key_exists('title', $data)) {
            $title = trim($data['title']);
            if ($title === '') { http_response_code(400); echo json_encode(['error' => 'Titre vide.']); break; }
            $fields[] = 'title = ?'; $params[] = $title;
        }
        if (array_key_exists('description', $data)) {
            $fields[] = 'description = ?'; $params[] = trim($data['description']) ?: null;
        }
        if (array_key_exists('priority', $data) && in_array($data['priority'], $allowed_priorities, true)) {
            $fields[] = 'priority = ?'; $params[] = $data['priority'];
        }
        if (array_key_exists('status', $data) && in_array($data['status'], $allowed_statuses, true)) {
            $fields[] = 'status = ?'; $params[] = $data['status'];
            if ($data['status'] === 'termine' && $existing['status'] !== 'termine') {
                $fields[] = 'completed_at = NOW()';
            } elseif ($data['status'] !== 'termine' && $existing['status'] === 'termine') {
                $fields[] = 'completed_at = NULL';
            }
        }
        if (array_key_exists('due_date', $data)) {
            $fields[] = 'due_date = ?'; $params[] = $data['due_date'] ?: null;
        }
        if (array_key_exists('image_url', $data)) {
            $fields[] = 'image_url = ?'; $params[] = trim($data['image_url']) ?: null;
        }
        if (array_key_exists('category_id', $data)) {
            $fields[] = 'category_id = ?'; $params[] = $data['category_id'] ? (int) $data['category_id'] : null;
        }

        if (empty($fields)) {
            http_response_code(400);
            echo json_encode(['error' => 'Rien à mettre à jour.']);
            break;
        }

        $params[] = $id;
        $params[] = $user_id;
        $stmt = $pdo->prepare('UPDATE todo SET ' . implode(', ', $fields) . ' WHERE id = ? AND user_id = ?');
        $stmt->execute($params);
        echo json_encode(['success' => true]);
        break;

    case 'DELETE':
        $id = (int) ($_GET['id'] ?? 0);
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'ID requis.']);
            break;
        }
        $stmt = $pdo->prepare('DELETE FROM todo WHERE id = ? AND user_id = ?');
        $stmt->execute([$id, $user_id]);
        echo json_encode(['success' => true]);
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Méthode non autorisée.']);
}
