<?php
// ============================================================
// php/update_status.php
// Admin: Update complaint status and add remarks
// ============================================================

session_start();
header('Content-Type: application/json');

if (!file_exists(__DIR__ . '/../config/db.php')) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database configuration file not found.']);
    exit();
}

require_once '../config/db.php';

// Admin only
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$complaint_id = intval($_POST['complaint_id'] ?? 0);
$status       = trim($_POST['status'] ?? '');
$remarks      = trim($_POST['remarks'] ?? '');

// Validate
if ($complaint_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid complaint ID.']);
    exit;
}

$allowed_statuses = ['pending', 'in_progress', 'resolved', 'rejected'];
if (!in_array($status, $allowed_statuses)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status value.']);
    exit;
}

$remarks = $conn->real_escape_string($remarks);

$sql = "UPDATE complaints 
        SET status = '$status', admin_remarks = '$remarks', updated_at = NOW()
        WHERE id = $complaint_id";

if ($conn->query($sql) && $conn->affected_rows > 0) {
    echo json_encode(['success' => true, 'message' => 'Complaint status updated successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Update failed or no changes made.']);
}

$conn->close();
?>
