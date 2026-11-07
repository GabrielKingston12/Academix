<?php
require_once '../includes/auth.php';
require_once '../config/database.php';
requireLogin();

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

switch ($method) {
    case 'GET':
        $stmt = $pdo->query("SELECT * FROM subjects ORDER BY name");
        echo json_encode($stmt->fetchAll());
        break;
        
    case 'POST':
        if (!isset($input['name']) || empty(trim($input['name']))) {
            http_response_code(400);
            echo json_encode(['error' => 'Subject name is required']);
            exit;
        }
        $name = trim($input['name']);
        
        $check = $pdo->prepare("SELECT id FROM subjects WHERE name = ?");
        $check->execute([$name]);
        if ($check->fetch()) {
            http_response_code(409);
            echo json_encode(['error' => 'Subject already exists']);
            exit;
        }
        
        $stmt = $pdo->prepare("INSERT INTO subjects (name) VALUES (?)");
        $stmt->execute([$name]);
        echo json_encode(['id' => $pdo->lastInsertId(), 'name' => $name]);
        break;
        
    case 'PUT':
        $id = $_GET['id'] ?? null;
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'Subject ID required']);
            exit;
        }
        if (!isset($input['name']) || empty(trim($input['name']))) {
            http_response_code(400);
            echo json_encode(['error' => 'Subject name is required']);
            exit;
        }
        $name = trim($input['name']);
        $stmt = $pdo->prepare("UPDATE subjects SET name = ? WHERE id = ?");
        $stmt->execute([$name, $id]);
        echo json_encode(['success' => true]);
        break;
        
    case 'DELETE':
        $id = $_GET['id'] ?? null;
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'Subject ID required']);
            exit;
        }
        $check = $pdo->prepare("SELECT id FROM grades WHERE subject_id = ? LIMIT 1");
        $check->execute([$id]);
        if ($check->fetch()) {
            http_response_code(409);
            echo json_encode(['error' => 'Cannot delete subject because it has associated grades']);
            exit;
        }
        $stmt = $pdo->prepare("DELETE FROM subjects WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
}