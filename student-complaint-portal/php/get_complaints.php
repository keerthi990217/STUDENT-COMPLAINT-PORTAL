<?php
// ============================================================
// php/get_complaints.php
// Admin: Fetch all complaints with optional filters
// ============================================================

session_start();
header('Content-Type: application/json');

if (!file_exists(__DIR__ . '/../config/db.php')) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database configuration file not found.']);
    exit();
}

require_once '../config/db.php';

// Protect: admin only
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

// Optional filters
$status_filter = $_GET['status'] ?? '';
$dept_filter   = intval($_GET['department_id'] ?? 0);

$where = [];
if (!empty($status_filter) && in_array($status_filter, ['pending','in_progress','resolved','rejected'])) {
    $where[] = "c.status = '$status_filter'";
}
if ($dept_filter > 0) {
    $where[] = "c.department_id = $dept_filter";
}

$where_clause = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';

$sql = "SELECT 
            c.id,
            c.complaint_code,
            c.category,
            c.subject,
            c.description,
            c.status,
            c.is_anonymous,
            c.admin_remarks,
            c.submitted_at,
            c.updated_at,
            d.name AS department,
            CASE WHEN c.is_anonymous = 1 THEN 'Anonymous' ELSE s.name END AS student_name,
            CASE WHEN c.is_anonymous = 1 THEN '---' ELSE s.student_id END AS student_roll
        FROM complaints c
        JOIN departments d ON c.department_id = d.id
        JOIN students s ON c.student_id = s.id
        $where_clause
        ORDER BY c.submitted_at DESC";

$result = $conn->query($sql);

$complaints = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $complaints[] = $row;
    }
}

// Stats summary
$stats_sql = "SELECT 
    COUNT(*) AS total,
    SUM(status='pending') AS pending,
    SUM(status='in_progress') AS in_progress,
    SUM(status='resolved') AS resolved,
    SUM(status='rejected') AS rejected
FROM complaints";

$stats_result = $conn->query($stats_sql);
$stats = $stats_result->fetch_assoc();

echo json_encode([
    'success'    => true,
    'complaints' => $complaints,
    'stats'      => $stats
]);

$conn->close();
?>
