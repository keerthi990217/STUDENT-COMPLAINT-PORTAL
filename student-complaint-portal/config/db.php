<?php
// ============================================================
// config/db.php
// Database Connection Configuration
// ============================================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');          // Change to your MySQL username
define('DB_PASS', '');              // Change to your MySQL password
define('DB_NAME', 'complaint_portal');

// Create connection
$conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    // Log error for debugging
    error_log('Database connection failed: ' . $conn->connect_error);
    
    // Return JSON error response
    header('Content-Type: application/json', true, 500);
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed. Please ensure MySQL is running and the database exists.'
    ]);
    exit();
}

// Set charset to utf8
$conn->set_charset('utf8');
?>
