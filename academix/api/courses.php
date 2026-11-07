<?php
require_once '../includes/auth.php';
require_once '../config/database.php';
requireLogin();

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

switch ($method) {
    case 'GET':
        $stmt = $pdo->query("SELECT * FROM courses ORDER BY name");
        echo json_encode($stmt->fetchAll());
        break;
        
    case 'POST':
        if (!isset($input['name']) || empty(trim($input['name']))) {
            http_response_code(400);
            echo json_encode(['error' => 'Course name is required']);
            exit;
        }
        $name = trim($input['name']);
        $description = isset($input['description']) ? trim($input['description']) : null;
        $schedule = isset($input['schedule']) ? trim($input['schedule']) : null;
        
        $stmt = $pdo->prepare("INSERT INTO courses (name, description, schedule) VALUES (?, ?, ?)");
        $stmt->execute([$name, $description, $schedule]);
        echo json_encode(['id' => $pdo->lastInsertId(), 'name' => $name, 'description' => $description, 'schedule' => $schedule]);
        break;
        
    case 'PUT':
        $id = $_GET['id'] ?? null;
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'Course ID required']);
            exit;
        }
        if (!isset($input['name']) || empty(trim($input['name']))) {
            http_response_code(400);
            echo json_encode(['error' => 'Course name is required']);
            exit;
        }
        $name = trim($input['name']);
        $description = isset($input['description']) ? trim($input['description']) : null;
        $schedule = isset($input['schedule']) ? trim($input['schedule']) : null;
        
        $stmt = $pdo->prepare("UPDATE courses SET name = ?, description = ?, schedule = ? WHERE id = ?");
        $stmt->execute([$name, $description, $schedule, $id]);
        echo json_encode(['success' => true]);
        break;
        
    case 'DELETE':
        $id = $_GET['id'] ?? null;
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'Course ID required']);
            exit;
        }
        $stmt = $pdo->prepare("DELETE FROM courses WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
}