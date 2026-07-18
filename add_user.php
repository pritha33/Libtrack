<?php
// add_user.php - Admin can only add students
session_start();
header("Content-Type: application/json");
include "db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit();
}

$fullname = $_POST['fullname'] ?? '';
$email = $_POST['email'] ?? '';
$role = $_POST['role'] ?? 'student';

// Force role to be student only (no admin creation)
$role = 'student';
$password = 'student123'; // Default password for new users

if (empty($fullname) || empty($email)) {
    echo json_encode(["status" => "error", "message" => "All fields required"]);
    exit();
}

// Prevent creating admin account
if ($email === 'admin@gmail.com') {
    echo json_encode(["status" => "error", "message" => "Cannot create admin account"]);
    exit();
}

// Check if email exists
$check = $conn->prepare("SELECT id FROM users WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
if ($check->get_result()->num_rows > 0) {
    echo json_encode(["status" => "error", "message" => "Email already exists"]);
    exit();
}

// Insert as student only
$stmt = $conn->prepare("INSERT INTO users (fullname, email, password, role) VALUES (?, ?, ?, 'student')");
$stmt->bind_param("sss", $fullname, $email, $password);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Student added successfully. Password: student123"]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to add user"]);
}
?>