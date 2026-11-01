<?php
// ============================================================
// php/submit_complaint.php
// Handles new complaint submission by student
// ============================================================

session_start();
header('Content-Type: application/json');

if (!file_exists(__DIR__ . '/../config/db.php')) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database configuration file not found.']);
    exit();
}

require_once '../config/db.php';

// Check if student is logged in
if (!isset($_SESSION['student_logged_in']) || !$_SESSION['student_logged_in']) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized. Please login first.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// Get inputs
$student_id    = $_SESSION['student_id'];
$department_id = intval($_POST['department_id'] ?? 0);
$category      = trim($_POST['category'] ?? '');
$subject       = trim($_POST['subject'] ?? '');
$description   = trim($_POST['description'] ?? '');
$is_anonymous  = isset($_POST['is_anonymous']) ? 1 : 0;

// Validate
$errors = [];
if ($department_id <= 0)    $errors[] = 'Please select a department.';
if (empty($category))       $errors[] = 'Please select a complaint category.';
if (empty($subject))        $errors[] = 'Complaint subject is required.';
if (strlen($subject) > 255) $errors[] = 'Subject must be under 255 characters.';
if (empty($description))    $errors[] = 'Complaint description is required.';
if (strlen($description) < 20) $errors[] = 'Description must be at least 20 characters.';

$allowed_categories = ['academics', 'faculty', 'facilities', 'hostel', 'other'];
if (!in_array($category, $allowed_categories)) $errors[] = 'Invalid category selected.';

if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => implode(' ', $errors)]);
    exit;
}

// Verify department exists
$dept_check = $conn->query("SELECT id FROM departments WHERE id = $department_id");
if (!$dept_check || $dept_check->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid department selected.']);
    exit;
}

// Generate unique complaint code: CMP-YEAR-XXXX
$year = date('Y');
$count_result = $conn->query("SELECT COUNT(*) AS total FROM complaints");
$count_row    = $count_result->fetch_assoc();
$next_num     = str_pad($count_row['total'] + 1, 4, '0', STR_PAD_LEFT);
$complaint_code = "CMP-$year-$next_num";

// Sanitize text inputs
$subject     = $conn->real_escape_string($subject);
$description = $conn->real_escape_string($description);

// Insert complaint
$sql = "INSERT INTO complaints 
        (complaint_code, student_id, department_id, category, subject, description, is_anonymous, status)
        VALUES 
        ('$complaint_code', $student_id, $department_id, '$category', '$subject', '$description', $is_anonymous, 'pending')";

if ($conn->query($sql)) {
    echo json_encode([
        'success'        => true,
        'message'        => 'Complaint submitted successfully!',
        'complaint_code' => $complaint_code
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to submit complaint. Please try again.']);
}

$conn->close();
?>
