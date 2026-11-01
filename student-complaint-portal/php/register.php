<?php
// ============================================================
// php/register.php
// Handles new student account registration
// ============================================================

session_start();
header('Content-Type: application/json');

if (!file_exists(__DIR__ . '/../config/db.php')) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database configuration file not found.']);
    exit();
}

require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// ── Collect & Trim Inputs ──────────────────────────────────
$name             = trim($_POST['name']             ?? '');
$student_id       = trim($_POST['student_id']       ?? '');
$email            = trim($_POST['email']            ?? '');
$department_id    = intval($_POST['department_id']  ?? 0);
$year             = intval($_POST['year']           ?? 0);
$password         = $_POST['password']              ?? '';
$confirm_password = $_POST['confirm_password']      ?? '';

// ── Validation ─────────────────────────────────────────────
$errors = [];

if (empty($name))
    $errors[] = 'Full name is required.';

if (strlen($name) < 2 || strlen($name) > 100)
    $errors[] = 'Name must be between 2 and 100 characters.';

if (empty($student_id))
    $errors[] = 'Student ID is required.';

if (!preg_match('/^[A-Za-z0-9]+$/', $student_id))
    $errors[] = 'Student ID must contain only letters and numbers (no spaces or symbols).';

if (empty($email))
    $errors[] = 'Email address is required.';

if (!filter_var($email, FILTER_VALIDATE_EMAIL))
    $errors[] = 'Please enter a valid email address.';

if ($department_id <= 0)
    $errors[] = 'Please select a department.';

if ($year < 1 || $year > 4)
    $errors[] = 'Please select a valid year (1–4).';

if (empty($password))
    $errors[] = 'Password is required.';

if (strlen($password) < 6)
    $errors[] = 'Password must be at least 6 characters long.';

if ($password !== $confirm_password)
    $errors[] = 'Passwords do not match. Please re-enter.';

// Return all validation errors at once
if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => implode(' ', $errors)]);
    exit;
}

// ── Sanitize ───────────────────────────────────────────────
$name       = $conn->real_escape_string($name);
$student_id = $conn->real_escape_string(strtoupper($student_id));
$email      = $conn->real_escape_string(strtolower($email));

// ── Check: Student ID already exists ──────────────────────
$check_id = $conn->query("SELECT id FROM students WHERE student_id = '$student_id' LIMIT 1");
if ($check_id && $check_id->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'This Student ID is already registered. Try logging in instead.']);
    exit;
}

// ── Check: Email already exists ───────────────────────────
$check_email = $conn->query("SELECT id FROM students WHERE email = '$email' LIMIT 1");
if ($check_email && $check_email->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'This email address is already registered. Try logging in instead.']);
    exit;
}

// ── Check: Department exists ──────────────────────────────
$check_dept = $conn->query("SELECT id FROM departments WHERE id = $department_id LIMIT 1");
if (!$check_dept || $check_dept->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Selected department is invalid.']);
    exit;
}

// ── Hash Password & Insert ─────────────────────────────────
$hashed_password = password_hash($password, PASSWORD_BCRYPT);

$sql = "INSERT INTO students (student_id, name, email, password, department_id, year)
        VALUES ('$student_id', '$name', '$email', '$hashed_password', $department_id, $year)";

if ($conn->query($sql)) {
    echo json_encode([
        'success' => true,
        'message' => 'Account created successfully! You can now log in.'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Registration failed. Please try again later.'
    ]);
}

$conn->close();
?>
