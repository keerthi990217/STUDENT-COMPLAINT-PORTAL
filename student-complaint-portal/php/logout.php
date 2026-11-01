<?php
// ============================================================
// php/logout.php
// Destroys session and logs out user
// ============================================================

session_start();
session_unset();
session_destroy();

// Redirect based on who logged out
$type = $_GET['type'] ?? 'student';
if ($type === 'admin') {
    header('Location: ../admin/login.html');
} else {
    header('Location: ../login.html');
}
exit;
?>
