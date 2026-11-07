<?php
require_once '../includes/auth.php';
require_once '../config/database.php';
requireLogin();

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

switch ($method) {
    case 'GET':
        $stmt = $pdo->query("
            SELECT s.*, c.name as course_name 
            FROM students s 
            LEFT JOIN courses c ON s.course_id = c.id
            ORDER BY s.name
        ");
        echo json_encode($stmt->fetchAll());
        break;
        
    case 'POST':
        if (!isset($input['name'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Name required']);
            exit;
        }
        $stmt = $pdo->prepare("
            INSERT INTO students (name, course_id, year_level, email, status) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $input['name'],
            $input['course_id'] ?: null,
            $input['year_level'] ?? '1st',
            $input['email'] ?? null,
            $input['status'] ?? 'Active'
        ]);
        echo json_encode(['id' => $pdo->lastInsertId()]);
        break;
        
    case 'PUT':
        $id = $_GET['id'] ?? null;
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'ID required']);
            exit;
        }
        $stmt = $pdo->prepare("
            UPDATE students SET 
                name = ?, course_id = ?, year_level = ?, email = ?, status = ?
            WHERE id = ?
        ");
        $stmt->execute([
            $input['name'],
            $input['course_id'] ?: null,
            $input['year_level'],
            $input['email'] ?? null,
            $input['status'],
            $id
        ]);
        echo json_encode(['success' => true]);
        break;
        
    case 'DELETE':
        $id = $_GET['id'] ?? null;
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'ID required']);
            exit;
        }
        $stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
}