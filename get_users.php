<?php
// get_users.php - Get all users EXCEPT the main admin
session_start();
header("Content-Type: application/json");
include "db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode([]);
    exit();
}

// Only get users with role 'student' (exclude admin)
$query = "SELECT id, fullname, email, role, created_at 
          FROM users 
          WHERE role = 'student'
          ORDER BY id DESC";

$result = $conn->query($query);
$users = [];

while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

echo json_encode($users);
?>