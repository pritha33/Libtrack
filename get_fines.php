<?php
// get_fines.php - Get fines for logged in user
session_start();
header("Content-Type: application/json");
include "db.php";

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit();
}

$user_id = $_SESSION['user_id'];
$is_admin = ($_SESSION['role'] ?? '') === 'admin';

if ($is_admin) {
    // Admin sees all pending fines
    $query = "SELECT f.*, u.fullname as user_name, b.title as book_title 
              FROM fines f
              JOIN users u ON f.user_id = u.id
              JOIN issued_books ib ON f.issued_id = ib.id
              JOIN books b ON ib.book_id = b.id
              WHERE f.status = 'unpaid'
              ORDER BY f.created_at DESC";
    $result = $conn->query($query);
} else {
    // Student sees only their pending fines
    $stmt = $conn->prepare("SELECT f.*, b.title as book_title 
                            FROM fines f
                            JOIN issued_books ib ON f.issued_id = ib.id
                            JOIN books b ON ib.book_id = b.id
                            WHERE f.user_id = ? AND f.status = 'unpaid'
                            ORDER BY f.created_at DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
}

$fines = [];
while ($row = $result->fetch_assoc()) {
    $fines[] = $row;
}

echo json_encode($fines);
?>