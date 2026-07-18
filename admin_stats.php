<?php
// admin_stats.php - Fixed version
session_start();
header("Content-Type: application/json");
include "db.php";

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

// Get counts with error handling
$books = 0;
$users = 0;
$issued = 0;
$fines = 0;

// Count books
$result = $conn->query("SELECT COUNT(*) as count FROM books");
if ($result) {
    $books = $result->fetch_assoc()['count'];
}

// Count students (not admin)
$result = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'student'");
if ($result) {
    $users = $result->fetch_assoc()['count'];
}

// Count issued books
$result = $conn->query("SELECT COUNT(*) as count FROM issued_books WHERE status = 'issued'");
if ($result) {
    $issued = $result->fetch_assoc()['count'];
}

// Count total unpaid fines
$result = $conn->query("SELECT COALESCE(SUM(amount), 0) as total FROM fines WHERE status = 'unpaid'");
if ($result) {
    $fines = $result->fetch_assoc()['total'];
}

// Return JSON
echo json_encode([
    "books" => $books,
    "users" => $users,
    "issued" => $issued,
    "fines" => $fines
]);
?>