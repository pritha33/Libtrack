<?php
// reset_password.php - Reset password with code
session_start();
header("Content-Type: application/json");
include "db.php";

$data = json_decode(file_get_contents("php://input"), true);
$email = $data['email'] ?? '';
$token = $data['token'] ?? '';
$new_password = $data['new_password'] ?? '';

if (empty($email) || empty($token) || empty($new_password)) {
    echo json_encode(["status" => "error", "message" => "All fields are required"]);
    exit();
}

if (strlen($new_password) < 6) {
    echo json_encode(["status" => "error", "message" => "Password must be at least 6 characters"]);
    exit();
}

// Verify token
$stmt = $conn->prepare("SELECT id, reset_token, reset_expires FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
    exit();
}

// Check if token matches
if ($user['reset_token'] !== $token) {
    echo json_encode(["status" => "error", "message" => "Invalid reset code"]);
    exit();
}

// Check if token expired
$expires = strtotime($user['reset_expires']);
$now = time();

if ($now > $expires) {
    echo json_encode(["status" => "error", "message" => "Reset code has expired. Please request a new one"]);
    exit();
}

// Update password (plain text)
$update_stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL, last_password_reset = NOW() WHERE email = ?");
$update_stmt->bind_param("ss", $new_password, $email);

if ($update_stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Password reset successful! Redirecting to login..."]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to reset password"]);
}
?>