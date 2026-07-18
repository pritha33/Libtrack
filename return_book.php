<?php
// return_book.php - Updated with fine calculation
session_start();
header("Content-Type: application/json");
include "db.php";

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Please login"]);
    exit();
}

$issue_id = intval($_POST['issue_id'] ?? 0);
$user_id = $_SESSION['user_id'];

// Verify ownership
$check = $conn->prepare("SELECT ib.*, b.title, b.copies, b.id as book_id 
                         FROM issued_books ib 
                         JOIN books b ON ib.book_id = b.id 
                         WHERE ib.id = ? AND ib.user_id = ? AND ib.status = 'issued'");
$check->bind_param("ii", $issue_id, $user_id);
$check->execute();
$issue = $check->get_result()->fetch_assoc();

if (!$issue) {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
    exit();
}

// Calculate fine if overdue
$fine_amount = 0;
$due_date = new DateTime($issue['due_date']);
$today = new DateTime();
if ($today > $due_date) {
    $days = $due_date->diff($today)->days;
    $fine_amount = $days * 5; // 5 TK per day
}

// Update issued book
$update = $conn->prepare("UPDATE issued_books SET return_date = CURDATE(), status = 'returned' WHERE id = ?");
$update->bind_param("i", $issue_id);
$update->execute();

// Update available copies
$update_copies = $conn->prepare("UPDATE books SET available_copies = available_copies + 1 WHERE id = ?");
$update_copies->bind_param("i", $issue['book_id']);
$update_copies->execute();

// Create fine if overdue
if ($fine_amount > 0) {
    $fine_stmt = $conn->prepare("INSERT INTO fines (user_id, issued_book_id, amount, status) VALUES (?, ?, ?, 'pending')");
    $fine_stmt->bind_param("iid", $user_id, $issue_id, $fine_amount);
    $fine_stmt->execute();
    echo json_encode(["status" => "success", "message" => "Book returned. Fine: $fine_amount TK", "fine" => $fine_amount]);
} else {
    echo json_encode(["status" => "success", "message" => "Book returned successfully"]);
}
?>