<?php
// ============================================================
// php/get_student_complaints.php
// Returns complaints for the logged-in student
// ============================================================

session_start();
header('Content-Type: application/json');

if (!file_exists(__DIR__ . '/../config/db.php')) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database configuration file not found.']);
    exit();
}

require_once '../config/db.php';

if (!isset($_SESSION['student_logged_in']) || !$_SESSION['student_logged_in']) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized. Please login.']);
    exit;
}

$student_id = intval($_SESSION['student_id']);

$sql = "SELECT 
            c.complaint_code,
            c.category,
            c.subject,
            c.status,
            c.is_anonymous,
            c.admin_remarks,
            c.submitted_at,
            d.name AS department
        FROM complaints c
        JOIN departments d ON c.department_id = d.id
        WHERE c.student_id = $student_id
        ORDER BY c.submitted_at DESC";

$result = $conn->query($sql);
$complaints = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $complaints[] = $row;
    }
}

echo json_encode(['success' => true, 'complaints' => $complaints]);
$conn->close();
?>
