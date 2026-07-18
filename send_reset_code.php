<?php
// send_reset_code.php - Simple version (shows code on screen)
session_start();
header("Content-Type: application/json");
include "db.php";

$data = json_decode(file_get_contents("php://input"), true);
$email = $data['email'] ?? '';

if (empty($email)) {
    echo json_encode(["status" => "error", "message" => "Email is required"]);
    exit();
}

// Check if email exists
$stmt = $conn->prepare("SELECT id, fullname FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo json_encode(["status" => "error", "message" => "Email not found in our records"]);
    exit();
}

// Generate 6-digit reset code
$reset_token = sprintf("%06d", mt_rand(1, 999999));
$expires = date('Y-m-d H:i:s', strtotime('+30 minutes'));

// Save token to database
$update_stmt = $conn->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE email = ?");
$update_stmt->bind_param("sss", $reset_token, $expires, $email);

if ($update_stmt->execute()) {
    // For testing - show code directly (since email might not work)
    echo json_encode([
        "status" => "success", 
        "message" => "Your reset code is: " . $reset_token,
        "demo_code" => $reset_token
    ]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to generate reset code"]);
}
?>