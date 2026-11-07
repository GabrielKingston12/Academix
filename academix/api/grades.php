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
            SELECT g.*, 
                   s.name as student_name, 
                   c.name as course_name, 
                   sub.name as subject_name,
                   i.name as instructor_name
            FROM grades g
            JOIN students s ON g.student_id = s.id
            JOIN courses c ON g.course_id = c.id
            JOIN subjects sub ON g.subject_id = sub.id
            LEFT JOIN instructors i ON g.instructor_id = i.id
            ORDER BY g.created_at DESC
        ");
        echo json_encode($stmt->fetchAll());
        break;
        
    case 'POST':
        $stmt = $pdo->prepare("
            INSERT INTO grades (student_id, course_id, subject_id, instructor_id, grade, semester, academic_year)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $input['student_id'],
            $input['course_id'],
            $input['subject_id'],
            $input['instructor_id'] ?: null,
            $input['grade'],
            $input['semester'],
            $input['academic_year']
        ]);
        echo json_encode(['id' => $pdo->lastInsertId()]);
        break;
        
    case 'PUT':
        $id = $_GET['id'] ?? null;
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'Grade ID required']);
            exit;
        }
        $stmt = $pdo->prepare("
            UPDATE grades SET 
                student_id = ?, course_id = ?, subject_id = ?, instructor_id = ?,
                grade = ?, semester = ?, academic_year = ?
            WHERE id = ?
        ");
        $stmt->execute([
            $input['student_id'],
            $input['course_id'],
            $input['subject_id'],
            $input['instructor_id'] ?: null,
            $input['grade'],
            $input['semester'],
            $input['academic_year'],
            $id
        ]);
        echo json_encode(['success' => true]);
        break;
        
    case 'DELETE':
        $id = $_GET['id'] ?? null;
        $stmt = $pdo->prepare("DELETE FROM grades WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
}