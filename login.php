<?php
// login.php - Simple working version
session_start();
header("Content-Type: application/json");
include "db.php";

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
    // Direct comparison (plain text)
    if ($password === $user['password']) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['fullname'] = $user['fullname'];
        $_SESSION['role'] = $user['role'];
        
        echo json_encode([
            "status" => "success",
            "role" => $user['role']
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "Wrong password"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "User not found"]);
}
?>