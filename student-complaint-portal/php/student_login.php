<?php
// ============================================================
// php/student_login.php
// Handles student login authentication
// ============================================================

session_start();
header('Content-Type: application/json');

// Ensure config/db.php exists and is readable
if (!file_exists(__DIR__ . '/../config/db.php')) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database configuration file not found.']);
    exit();
}

require_once '../config/db.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// Get and sanitize input
$student_id = trim($_POST['student_id'] ?? '');
$password   = trim($_POST['password'] ?? '');

// Basic validation
if (empty($student_id) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Student ID and password are required.']);
    exit;
}

// Sanitize against SQL injection
$student_id = $conn->real_escape_string($student_id);

// Fetch student from database
$sql = "SELECT s.*, d.name AS dept_name 
        FROM students s 
        LEFT JOIN departments d ON s.department_id = d.id
        WHERE s.student_id = '$student_id' 
        LIMIT 1";

$result = $conn->query($sql);

if ($result && $result->num_rows === 1) {
    $student = $result->fetch_assoc();

    // Verify password
    if (password_verify($password, $student['password'])) {
        // Store session data
        $_SESSION['student_logged_in'] = true;
        $_SESSION['student_id']        = $student['id'];
        $_SESSION['student_name']      = $student['name'];
        $_SESSION['student_roll']      = $student['student_id'];
        $_SESSION['student_dept']      = $student['dept_name'];

        echo json_encode([
            'success' => true,
            'message' => 'Login successful!',
            'name'    => $student['name']
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Incorrect password. Please try again.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Student ID not found. Please check your credentials.']);
}

$conn->close();
?>
