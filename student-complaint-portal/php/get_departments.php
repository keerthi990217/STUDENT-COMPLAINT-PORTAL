<?php
// ============================================================
// php/get_departments.php
// Returns list of departments as JSON
// ============================================================

header('Content-Type: application/json');
require_once '../config/db.php';

$departments = [];
$success = false;
$message = 'Unable to fetch departments.';

$result = $conn->query("SELECT id, name, description FROM departments ORDER BY name ASC");

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $departments[] = $row;
    }
    $success = true;
    $message = 'Departments fetched successfully.';
} else {
    error_log('Database query error: ' . $conn->error);
}

echo json_encode([
    'success' => $success,
    'message' => $message,
    'departments' => $departments
]);
$conn->close();
?>
