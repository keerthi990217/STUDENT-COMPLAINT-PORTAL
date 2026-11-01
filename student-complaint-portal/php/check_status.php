<?php
// ============================================================
// php/check_status.php
// Lets students check complaint status using their complaint code
// ============================================================

header('Content-Type: application/json');

if (!file_exists(__DIR__ . '/../config/db.php')) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database configuration file not found.']);
    exit();
}

require_once '../config/db.php';

$complaint_code = strtoupper(trim($_GET['code'] ?? ''));

if (empty($complaint_code)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a complaint code.']);
    exit;
}

// Validate format: CMP-YYYY-XXXX
if (!preg_match('/^CMP-\d{4}-\d{4}$/', $complaint_code)) {
    echo json_encode(['success' => false, 'message' => 'Invalid complaint code format. Use CMP-YYYY-XXXX']);
    exit;
}

$complaint_code = $conn->real_escape_string($complaint_code);

$sql = "SELECT 
            c.complaint_code,
            c.category,
            c.subject,
            c.status,
            c.admin_remarks,
            c.is_anonymous,
            c.submitted_at,
            c.updated_at,
            d.name AS department
        FROM complaints c
        JOIN departments d ON c.department_id = d.id
        WHERE c.complaint_code = '$complaint_code'
        LIMIT 1";

$result = $conn->query($sql);

if ($result && $result->num_rows === 1) {
    $complaint = $result->fetch_assoc();
    echo json_encode(['success' => true, 'complaint' => $complaint]);
} else {
    echo json_encode(['success' => false, 'message' => 'No complaint found with this code. Please check and try again.']);
}

$conn->close();
?>
