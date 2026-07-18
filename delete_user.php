<?php
// delete_user.php - Prevent deleting admin
session_start();
header("Content-Type: application/json");
include "db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit();
}

$id = $_GET['id'] ?? 0;

// Don't allow deleting yourself
if ($id == $_SESSION['user_id']) {
    echo json_encode(["status" => "error", "message" => "Cannot delete your own account"]);
    exit();
}

// Check if this is the admin account
$check = $conn->prepare("SELECT email, role FROM users WHERE id = ?");
$check->bind_param("i", $id);
$check->execute();
$user = $check->get_result()->fetch_assoc();

if ($user && ($user['email'] === 'admin@gmail.com' || $user['role'] === 'admin')) {
    echo json_encode(["status" => "error", "message" => "Cannot delete admin account"]);
    exit();
}

$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to delete user"]);
}
?>