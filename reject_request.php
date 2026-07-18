<?php
// reject_request.php
session_start();
header("Content-Type: application/json");
include "db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit();
}

$request_id = $_POST['request_id'] ?? 0;

$stmt = $conn->prepare("UPDATE book_requests SET status = 'rejected' WHERE id = ? AND status = 'pending'");
$stmt->bind_param("i", $request_id);

if ($stmt->execute()) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error"]);
}
?>