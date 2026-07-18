<?php
// register.php - Working version
header("Content-Type: application/json");
include "db.php";

$fullname = trim($_POST['fullname'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');
$role = $_POST['role'] ?? 'student';

// Force role to student only
$role = 'student';

if (empty($fullname) || empty($email) || empty($password)) {
    echo json_encode(["status" => "error", "message" => "All fields required"]);
    exit();
}

// Check minimum 6 characters (not 8)
if (strlen($password) < 8) {
    echo json_encode(["status" => "error", "message" => "Password must be at least 8 characters"]);
    exit();
}

// Check if email exists
$check = $conn->prepare("SELECT id FROM users WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo json_encode(["status" => "error", "message" => "Email already exists"]);
    exit();
}

// Insert user with plain text password
$stmt = $conn->prepare("INSERT INTO users (fullname, email, password, role) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $fullname, $email, $password, $role);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Registration successful!"]);
} else {
    echo json_encode(["status" => "error", "message" => "Registration failed"]);
}
?>