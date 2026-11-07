<?php
require_once '../includes/auth.php';
require_once '../config/database.php';
requireLogin();

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

switch ($method) {
    case 'GET':
        $stmt = $pdo->query("SELECT * FROM instructors ORDER BY name");
        echo json_encode($stmt->fetchAll());
        break;
    case 'POST':
        $stmt = $pdo->prepare("INSERT INTO instructors (name, department, email, status) VALUES (?, ?, ?, ?)");
        $stmt->execute([$input['name'], $input['department'] ?? null, $input['email'] ?? null, $input['status'] ?? 'Active']);
        echo json_encode(['id' => $pdo->lastInsertId()]);
        break;
    case 'PUT':
        $id = $_GET['id'] ?? null;
        if (!$id) { http_response_code(400); exit; }
        $stmt = $pdo->prepare("UPDATE instructors SET name=?, department=?, email=?, status=? WHERE id=?");
        $stmt->execute([$input['name'], $input['department'] ?? null, $input['email'] ?? null, $input['status'], $id]);
        echo json_encode(['success' => true]);
        break;
    case 'DELETE':
        $id = $_GET['id'] ?? null;
        if (!$id) { http_response_code(400); exit; }
        $stmt = $pdo->prepare("DELETE FROM instructors WHERE id=?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
        break;
}