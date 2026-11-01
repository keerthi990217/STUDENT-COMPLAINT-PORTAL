<?php
// ============================================================
// php/admin_login.php
// Handles admin/staff login authentication
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

$email    = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

if (empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Email and password are required.']);
    exit;
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
    exit;
}

$email = $conn->real_escape_string($email);

$sql    = "SELECT * FROM admins WHERE email = '$email' LIMIT 1";
$result = $conn->query($sql);

if ($result && $result->num_rows === 1) {
    $admin = $result->fetch_assoc();

    if (password_verify($password, $admin['password'])) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id']        = $admin['id'];
        $_SESSION['admin_name']      = $admin['name'];
        $_SESSION['admin_role']      = $admin['role'];

        echo json_encode([
            'success' => true,
            'message' => 'Admin login successful!',
            'name'    => $admin['name'],
            'role'    => $admin['role']
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Incorrect password.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Admin account not found.']);
}

$conn->close();
?>
